<?php
// This file is part of Moodle - http://moodle.org/
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
 * Adds new instance of enrol_payu to specified course
 * or edits current instance.
 *
 * @package    enrol_payu
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
//require_once('create_test.php');
require_once('test_edit.php');
require_login();
?>
<script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
<?php
$url=$CFG->wwwroot.'/local/custompage/test_edit_form.php';


$PAGE->set_pagelayout('admin');
$PAGE->set_title("Edit test");
$PAGE->set_heading("Edit Test");
$PAGE->set_url($CFG->wwwroot.'/local/custompage/test_edit.php');
$coursenode = $PAGE->navbar->add('Edit test', new moodle_url($CFG->wwwroot.'/local/custompage/test_edit_form.php'));

$PAGE->set_context(context_system::instance());

$return = $CFG->wwwroot.'/local/custompage/test.php';
echo $OUTPUT->header();

$mform = new test_edit_form();
if ($mform->is_cancelled()) {
    redirect($return);
} else if ($record = $mform->get_data()) {
	// print_r($record);exit();
	$data = new stdclass();
	$data->id = $record->quizid;
	$data->name = $record->not;
	$data->timelimit = $record->duration * 60;
	$data->attempts = $record->noa;
	$data->questionsperpage = $record->qpp;
	$DB->update_record('quiz',$data);
	$proctoring_data=$DB->get_record_sql("SELECT * FROM {quizaccess_eproctoring} where quizid = $record->quizid");
	if($record->proctoring ==1){
		if($proctoring_data == ""){
			$proctoring = new stdclass();
			$proctoring->quizid = $record->quizid;
			$proctoring->eproctoringrequired = $record->proctoring;
			$DB->insert_record('quizaccess_eproctoring',$proctoring);
		}
	}else{
		if($proctoring_data){
			$DB->delete_records('quizaccess_eproctoring',array('quizid' => $record->quizid));
		}
	}
	$urlto = $CFG->wwwroot.'/local/custompage/test.php';
    redirect($urlto, 'Test Updated Successfully ', 8);
}
   
$mform->display();


?>
<script>
	

	</script>
<?php

echo $OUTPUT->footer();







