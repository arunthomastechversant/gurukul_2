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
 * Vplquestion renderer class.
 * @package    qtype_vplquestion
 * @copyright  Astor Bizard, 2019
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../../config.php');
require_once(__DIR__.'/locallib.php');

require_login();

/**
 * Generates HTML output for vplquestion.
 * @copyright  Astor Bizard, 2019
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_vplquestion_renderer extends qtype_renderer {

    public function formulation_and_controls(question_attempt $qa, question_display_options $options) {

        $question = $qa->get_question();

        global $USER, $COURSE;
        $userid = $USER->id;
        $qid = $question->id;
        $vplid = $question->templatevpl;

        $inputname = $qa->get_qt_field_name('answer');
        $lastanswer = $qa->get_last_qt_var('answer');
        if ($lastanswer == null) {
            $lastanswer = $question->answertemplate;
        }

        $html = parent::formulation_and_controls($qa, $options) . $this->output->box_start();

        global $CFG;
        require_once($CFG->dirroot .'/mod/vpl/vpl.class.php');
        try {
            $vpl = new mod_vpl($vplid);
            list($course, $cm) = get_course_and_cm_from_cmid($vplid, 'vpl');
            if ($course->id != $COURSE->id) {
                $html .= $this->output->notification(get_string('vplnotincoursewarning', QVPL), 'warning');
            } else if (!$cm->visible) {
                $html .= $this->output->notification(get_string('vplnotavailablewarning', QVPL), 'warning');
            }
        } catch (moodle_exception $e) {
            // Something went wrong instantiating the VPL, the question is badly configured.
            $html .= $this->output->notification(get_string('vplnotfounderror', QVPL, $e->getMessage()), 'error');
            return $html . $this->output->box_end();
        }

        $acetheme = get_user_preferences('vpl_acetheme', get_config('mod_vpl')->editor_theme);

        $this->output->page->requires->strings_for_js(
                array('compilation', 'evaluation', 'evaluationerror', 'execerror', 'execerrordetails', 'execution'),
                QVPL);
        $this->output->page->requires->js_call_amd(QVPL.'/studentanswer', 'setup',
                array($qid, $vplid, $userid, $acetheme, $inputname));

        if (!$options->readonly) {
            // Add Correction|Reset buttons.
            $canshowcorrection = has_capability('moodle/course:update', context_course::instance($COURSE->id));
            $html .= '<div class="qvpl-set-text">
                         <span>'.
                            ($canshowcorrection ?
                                self::print_set_text_button('qvpl_correction_q'.$qid,
                                    $question->teachercorrection,
                                    get_string('correction', QVPL)).' | '
                                : '').
                            self::print_set_text_button('qvpl_reset_q'.$qid,
                                $question->answertemplate,
                                get_string('reset')).
                         '</span>
                      </div>';
        }

        // Add answer field (editor).
        $html .= '<div class="code-editor" contenteditable="true" spellcheck="false">
                     <textarea name="'.$inputname.'" rows="1"'.($options->readonly ? ' readonly="readonly"' : '').
                     ' data-template="'.htmlspecialchars($question->templatecontext).'"'.
                     ' data-templatelang="'.$question->templatelang.'">'.
                         htmlspecialchars($lastanswer).
                     '</textarea>
                 </div>';

        $run = $vpl->get_instance()->run;
        $precheck = $question->precheckpreference != 'none';
        if (!$options->readonly) {
            // Add Run and Pre-Check buttons, terminal and result field.
            $runbutton = self::print_qvpl_button('run', get_string('run', QVPL));
            $precheckbutton = self::print_qvpl_button($question->precheckpreference == 'dbg' ? 'debug' : 'evaluate',
                get_string('precheck', QVPL), get_string('precheckhelp', QVPL));
            $html .= '<div id="qvpl_buttons_q'.$qid.'" class="qvpl-buttons">'.
                         ($run ? $runbutton : '').
                         ($precheck ? $precheckbutton : '').
                     '</div>
                     <pre id="terminal_wrapper_q'.$qid.'" class="qvpl-terminal-wrapper"></pre>
                     <pre id="vpl_result_q'.$qid.'" class="vpl-result" style="display:none;"></pre>';
        }

        if (!$options->readonly && $precheck) {
            // Add execution files for Pre-check.
            $execfilesdata = $question->precheckpreference == 'diff' ? $question->precheckexecfiles : $question->execfiles;
            $execfiles = json_encode(format_execution_files(json_decode($execfilesdata)));
            $html .= '<input name="execfiles_q'.$qid.'" value="'.htmlspecialchars($execfiles).'" type="hidden">';
        }

        if (!$options->readonly) {
            // Add execution files for Run.
            $filestokeep = $vpl->get_execution_fgm()->getfilekeeplist();
            $execfilesrun = json_encode(format_execution_files(json_decode($question->execfiles), $filestokeep));
            $html .= '<input name="execfiles_run_q'.$qid.'" value="'.htmlspecialchars($execfilesrun).'" type="hidden">';
        }

        $html .= $this->output->box_end();

        return $html;
    }

    /**
     * Builds a button to set editor content.
     * @param string $name The name of the button (HTML id attribute).
     * @param string $textdata The content to apply to the editor upon click.
     * @param string $text The text to display on the button.
     * @return string The HTML code for the button.
     */
    private static function print_set_text_button($name, $textdata, $text) {
        return '<a id="'.$name.'" href="#" data-text="'.htmlspecialchars($textdata).'">'.$text.'</a>';
    }

    /**
     * Builds an execution button for the question attempt (run / pre-check).
     * @param string $action The action this button will execute (should be "run", "debug" or "evaluate").
     * @param string $text The text to display on the button.
     * @param string $tooltip The tooltip of the button.
     * @return string The HTML code for the button.
     */
    private static function print_qvpl_button($action, $text, $tooltip="") {
        return '<button class="qvpl-'.$action.' btn btn-secondary" type="button" title="'.$tooltip.'">'.$text.'</button>';
    }

    public function specific_feedback(question_attempt $qa) {
        $feedback = '';
        if ($qa->get_state()->is_finished()) {
            $feedback = '<div class="correctness '.$qa->get_state_class(true).' badge">'.
                            $qa->get_state()->default_string(true).
                        '</div>';
        }
        if ($qa->get_state()->is_graded()) {
            $displayid = 'vpl_eval_details_q'.$qa->get_question()->id;
            $feedback .= '<div class="gradingdetails">
                            <br>
                            <h5>'.get_string('evaluationdetails', QVPL).'</h5>
                            <pre id="'.$displayid.'" class="vpl-result"
                                data-result="'.htmlspecialchars($qa->get_response_summary()).'">
                            </pre>
                         </div>';
            $this->output->page->requires->js_call_amd(QVPL.'/studentanswer', 'displayResult',
                array($displayid, null));
        }
        return $feedback;
    }

    public function correct_response(question_attempt $qa) {
        if (!$qa->get_question()->teachercorrection) {
            return '';
        }
        return '<h5>'.get_string('possiblesolution', QVPL).'</h5>'.
               '<pre class="possiblesolution">'.htmlspecialchars($qa->get_question()->teachercorrection).'</pre>';
    }
}
