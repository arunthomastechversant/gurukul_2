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


require_once('create_new_recruitment_drive_form.php');
require_login();
$systemcontext = context_system::instance();
$companyid = iomad::get_my_companyid($systemcontext);
$company = $DB->get_record('company',array('id' => $companyid))->shortname;
$PAGE->set_pagelayout('admin');
$PAGE->set_title("Add ".$company." Recruitment Drive");
$PAGE->set_heading("Add ".$company." Recruitment Drive");
$PAGE->set_url($CFG->wwwroot.'/local/custompage/create_new_recruitment_drive.php');
$coursenode = $PAGE->navbar->add('Add '.$company.' Recruitment Drive', new moodle_url($CFG->wwwroot.'/local/custompage/create_new_recruitment_drive.php'));
$PAGE->navbar->add('Add '.$company.' Users', new moodle_url($CFG->wwwroot.'/local/custompage/create_users.php'));
$PAGE->set_context(context_system::instance());
//~ $url = $CFG->wwwroot.'/local/custompage/get_data.php';
$return = $CFG->wwwroot.'/local/custompage/recruitment_drive_list.php';
echo $OUTPUT->header();

$mform = new create_new_recruitment_drive_form();
if ($mform->is_cancelled()) {
    redirect($return);

} else if ($data = $mform->get_data()) {
    $quizzz = $DB->get_record_sql("SELECT * FROM {modules} WHERE name = 'quiz'");
    $groupname= $data->name;
    // print_r($data );exit;
    $record = new stdClass();
    $record->name           =  $data->name;
    $record->startdate         =  $data->startdate;
    $record->enddate           =  $data->enddate;
    $record->test    =  $data->test;
    $record->quizid    =  $data->test;

    $courseid=$data->courseid;
    $course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);


    $context = context_course::instance($course->id);
    require_capability('moodle/course:managegroups', $context);
    $editoroptions = array('maxfiles'=>EDITOR_UNLIMITED_FILES, 'maxbytes'=>$course->maxbytes, 'trust'=>false, 'context'=>$context, 'noclean'=>true);
    if (!empty($group->id)) {
        $editoroptions['subdirs'] = file_area_contains_subdirs($context, 'group', 'description', $group->id);
        $group = file_prepare_standard_editor($group, 'description', $editoroptions, $context, 'group', 'description', $group->id);
    } else {
        $editoroptions['subdirs'] = false;
        $group = file_prepare_standard_editor($group, 'description', $editoroptions, $context, 'group', 'description', null);
    }

    $editform = new group_form(null, array('editoroptions'=>$editoroptions));
    $gp = new stdClass();
    $gp->name=$groupname;
    $gp->courseid=$course->id;
    $gp->description_editor=array("text"=>"","format"=>1); 
    $id = groups_create_group($gp, $editform, $editoroptions);
    $record->test_groupid=$id;
    $record->company_id = $companyid;
    // print_r($record);exit();
    $insert_record = $DB->insert_record('recruitment_drive', $record);


$urlto = $CFG->wwwroot.'/local/custompage/recruitment_drive_list.php';
redirect($urlto, 'Recruitment Drive Created Successfully ', 8);

}

   
$mform->display();
echo $OUTPUT->footer();
