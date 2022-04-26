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
 * Vplquestion definition class.
 * @package    qtype_vplquestion
 * @copyright  Astor Bizard, 2019
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../../config.php');
require_once(__DIR__.'/locallib.php');

require_login();

/**
 * Represents a vplquestion.
 * @copyright  Astor Bizard, 2019
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_vplquestion_question extends question_graded_automatically {

    /**
     * Html text containing evaluation (test cases) details for an attempt.
     * This is filled during grade_response and used in summarise_response
     * @var string $evaldetails
     */
    private $evaldetails;

    /**
     * {@inheritDoc}
     * @see question_definition::get_expected_data()
     */
    public function get_expected_data() {
        return array('answer' => PARAM_RAW);
    }

    /**
     * {@inheritDoc}
     * @see question_definition::get_correct_response()
     */
    public function get_correct_response() {
        return array('answer' => $this->teachercorrection);
    }

    /**
     * Wrapper to get the answer in a response object, handling unset variable.
     * @param array $response the response object
     * @return string the answer
     */
    private function get_answer(array $response) {
        return isset($response['answer']) ? $response['answer'] : '';
    }

    public function summarise_response(array $response) {
        if ($this->evaldetails != null) {
            // Small hack to send evaluation details to review page.
            // This is called for responsesummary -> evaluation details.
            return $this->evaldetails;
        } else {
            // This is called for something else -> return response.
            return $this->get_answer($response);
        }
    }

    public function is_complete_response(array $response) {
        return $this->get_answer($response) != $this->answertemplate;
    }

    public function get_validation_error(array $response) {
        if ($this->is_gradable_response($response)) {
            return '';
        }
        return get_string('pleaseanswer', QVPL);
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        return question_utils::arrays_same_at_key_missing_is_blank($prevresponse, $newresponse, 'answer');
    }

    public function grade_response(array $response) {
        require_sesskey();
        $deletesubmissions = get_config(QVPL, 'deletevplsubmissions') == '1';
        $result = evaluate($this->get_answer($response), $this, $deletesubmissions);
        $grade = extract_fraction($result, $this->templatevpl);

        if ($grade !== null) {
            if ($this->gradingmethod == 0) {
                // All or nothing.
                $grade = floor($grade);
            }
        } else {
            $result->evaluationerror = get_string('nogradeerror', QVPL);
            $grade = 0;
        }

        $this->evaldetails = json_encode($result);

        return array($grade, question_state::graded_state_for_fraction($grade));
    }
}