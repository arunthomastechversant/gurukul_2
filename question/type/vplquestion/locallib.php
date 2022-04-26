<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Lib for vplquestion question type.
 * @package    qtype_vplquestion
 * @copyright  Astor Bizard, 2019
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

define('QVPL', 'qtype_vplquestion');

/**
 * Format and filter execution files provided by the user.
 * This method adds a suffix (_qvpl) to file names, and filters out files specified as UNUSED.
 * @param array $execfiles The files to format and filter.
 * @param array $selector If specified, only the files with name contained in this array will be considered.
 * @return array The resulting files array.
 */
function format_execution_files($execfiles, $selector=null) {
    $formattedfiles = array();
    foreach ($execfiles as $name => $content) {
        if ($selector === null || in_array($name, $selector)) {
            if (substr($content, 0, 6) != 'UNUSED') {
                $formattedfiles[$name.'_qvpl'] = $content;
            }
        }
    }
    return $formattedfiles;
}

/**
 * Evaluate an answer to a question by submitting it to the VPL and requesting an evaluate.
 * @param string $answer The answer to evaluate.
 * @param object $question The question data.
 * @param bool $deletesubmissions Whether user submissions should be discarded at the end of the operation.
 * @return object The evaluation result.
 */
function evaluate($answer, $question, $deletesubmissions) {
    global $USER, $CFG;
    require_once($CFG->dirroot .'/mod/vpl/vpl.class.php');
    require_once($CFG->dirroot .'/mod/vpl/forms/edit.class.php');
    require_once(__DIR__.'/classes/util/lock.php');
    $userid = $USER->id;
    $vpl = new mod_vpl($question->templatevpl);

    $reqfiles = $vpl->get_required_fgm()->getAllFiles();
    $reqfilename = array_keys($reqfiles)[0];
    // Escape all backslashes, as following operation deletes them.
    $answer = preg_replace('/\\\\/', '$0$0', $answer);
    // Replace the {{ANSWER}} tag, propagating indentation.
    $answeredreqfile = preg_replace('/([ \t]*)(.*)\{\{ANSWER\}\}/i',
            '$1$2'.implode("\n$1", explode("\n", $answer)),
            $question->templatecontext);
    $files = array($reqfilename => $answeredreqfile) + format_execution_files(json_decode($question->execfiles));

    // Try to evaluate several times (as internal evaluation errors may occur).
    $tries = 0;
    do {
        $tries++;
        try {
            // Forbid simultaneous evaluations (as the VPL won't allow multiple executions at once).
            $sem = semaphor_get($userid);
            semaphor_acquire($sem);

            mod_vpl_edit::save($vpl, $userid, $files);

            $coninfo = mod_vpl_edit::execute($vpl, $userid, "evaluate");

            // Set up a curl execution to listen to VPL execution server,
            // so we can stop as soon as we get the 'retrieve:' message (meaning that evaluation is complete).
            $ch = curl_init($coninfo->server.':'.$coninfo->port.'/'.$coninfo->monitorPath);

            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            "Connection: Upgrade",
                            "Upgrade: websocket",
                            "Host:".$coninfo->server.":".$coninfo->port,
                            "Sec-WebSocket-Key: ".base64_encode(uniqid()),
                            "Sec-WebSocket-Version: 13"
            ));

            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($chdummy, $data){
                if (strpos($data, 'retrieve:') !== false) {
                    // Interrupt curl exec.
                    return -1;
                }
                return strlen($data);
            });

            curl_exec($ch);
            curl_close($ch);

            $result = mod_vpl_edit::retrieve_result($vpl, $userid);
            $retry = false;

        } catch (Exception $e) {
            // There was an error during evaluation - retry.
            $result = new StdClass();
            $retry = true;
        }

        semaphor_release($sem);

        // Retry up to 10 times.
    } while ($retry && $tries < 10);

    if ($deletesubmissions) {
        try {
            require_once($CFG->dirroot.'/mod/vpl/vpl_submission.class.php');
            $subids = $vpl->user_submissions($userid);
            if ($subids) {
                foreach ($subids as $subid) {
                    $submission = new mod_vpl_submission($vpl, $subid);
                    $submission->delete();
                }
            }
        } catch (Exception $e) {
            // Something went wrong while deleting submissions - do nothing more.
            return $result;
        }
    }

    return $result;
}

/**
 * Compute the fraction (grade between 0 and 1) from the result of an evaluation.
 * @param object $result The evaluation result.
 * @param int $templatevpl The ID of the VPL this evaluation has been executed on.
 * @return float|null The fraction if any, or null if there was no grade.
 */
function extract_fraction($result, $templatevpl) {
    if ($result->grade) {
        global $CFG;
        require_once($CFG->dirroot .'/mod/vpl/vpl.class.php');
        $maxgrade = (new mod_vpl($templatevpl))->get_grade();
        $fraction = floatval(preg_replace('/.*: (.*) \/.*/', '$1', $result->grade)) / $maxgrade;
        return $fraction;
    } else {
        return null;
    }
}