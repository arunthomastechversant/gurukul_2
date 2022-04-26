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
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/course/modlib.php');
require_once($CFG->dirroot.'/mod/quiz/mod_form.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/course/lib.php');

require_once($CFG->dirroot.'/group/lib.php');
require_once($CFG->dirroot.'/group/group_form.php');


require_once('create_lead_batch.php');
require_login();
$PAGE->set_pagelayout('admin');
$PAGE->set_title("Create Lead Batch");
$PAGE->set_heading("Create Lead Batch");
$PAGE->set_url($CFG->wwwroot.'/local/custompage/create_lead_batch_form.php');
$coursenode = $PAGE->navbar->add('Create Lead Batch', new moodle_url($CFG->wwwroot.'/local/custompage/create_lead_batch_form.php'));
$PAGE->set_context(context_system::instance());

$return = $CFG->wwwroot.'/local/custompage/lead_batch_list.php';
echo $OUTPUT->header();

$deleteid  = optional_param('deleteid', '', PARAM_TEXT);
if ($deleteid) {
                   
    $result= $DB->delete_records('lead_batches', array('id'=>$deleteid));     
     redirect(new moodle_url('/local/custompage/create_lead_batch_form.php'),'Batch Deleted Successfully', 3);
}


$mform = new create_lead_batch_form();
if ($mform->is_cancelled()) {
    redirect($return);

} 
else if ($data = $mform->get_data()) 
{

    $batch = new stdClass();
    $batch->name = $data->name;
    $batch->created_by = $USER->id;           
    $batch->created_at = time(); 
// print_r($record);exit;
    $insert_record = $DB->insert_record('lead_batches', $batch);

	$urlto = $CFG->wwwroot.'/local/custompage/create_lead_batch_form.php';
	redirect($urlto, 'Batch Created Successfully ', 8);

}

   
$mform->display();
echo $OUTPUT->footer();

?>

