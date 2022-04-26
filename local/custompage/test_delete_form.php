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
require_login();

$url=$CFG->wwwroot.'/local/custompage/test_delete_form.php';
$delete = optional_param('delete', 0, PARAM_INT);    // course_sections.id
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$PAGE->set_pagelayout('admin');
$PAGE->set_title("Delete Quiz");
$PAGE->set_heading("Delete Quiz");
$PAGE->set_url($CFG->wwwroot.'/local/custompage/test_delete_form.php');
$coursenode = $PAGE->navbar->add('Delete Quiz', new moodle_url($CFG->wwwroot.'/local/custompage/test_delete_form.php'));

$PAGE->set_context(context_system::instance());

$return = $CFG->wwwroot.'/my';
echo $OUTPUT->header();

if(!empty($delete)){
    // $delete =  $DB->get_record_sql("select * from {course_modules} where instance = $quizid")->id;

    $cm     = get_coursemodule_from_id('', $delete, 0, true, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

    // require_login($course, false, $cm);
    $modcontext = context_module::instance($cm->id);
    require_capability('moodle/course:manageactivities', $modcontext);
    // $mform = new test_delete_form();

    $return = new moodle_url('/local/custompage/test.php');
    if (!$confirm or !confirm_sesskey()) {
        $fullmodulename = get_string('modulename', $cm->modname);

        $optionsyes = array('confirm'=>1, 'delete'=>$cm->id, 'sesskey'=>sesskey());
        $strdeletecheck = get_string('deletecheck', '', $fullmodulename);
        $strparams = (object)array('type' => $fullmodulename, 'name' => $cm->name);
        $strdeletechecktypename = get_string('deletechecktypename', '', $strparams);
        // print_r($strparams);exit();
        echo $OUTPUT->box_start('noticebox');
        $formcontinue = new single_button(new moodle_url("$CFG->wwwroot/local/custompage/test_delete_form.php", $optionsyes), get_string('yes'));
        $formcancel = new single_button($return, get_string('no'));
        // print_r($formcontinue);exit();
        echo $OUTPUT->confirm($strdeletechecktypename, $formcontinue, $formcancel);
        echo $OUTPUT->box_end();
        exit;
    }
    $systemcontext = context_system::instance();
    $companyid = iomad::get_my_companyid($systemcontext);
    $quizid = $cm->instance;
    if($companyid == 1)
    {    
       $drives =  $DB->get_records_sql("select id from {rsl_recruitment_drive} where test = $quizid");
        // print_r($drives);exit();
       if($drives){    
            foreach($drives as $drive){
                $driveid = $drive->id;
                $userids =  $DB->get_records_sql("select userid from {rsl_user_detail} where recruitment_id = $driveid");
                $DB->delete_records('rsl_user_detail',array('recruitment_id' => $driveid));
                $DB->delete_records('rsl_user',array('drive_id' => $driveid));
                $DB->delete_records('rsl_recruitment_drive',array('id' => $driveid));
                userstatussetingup($userids);
            }
        }
       
    }else if($companyid == 2){    
       $drives =  $DB->get_records_sql("select id from {urdc_recruitment_drive} where test = $quizid");
        // print_r($drives);exit();
       if($drives){    
            foreach($drives as $drive){
                $driveid = $drive->id;
                $userids =  $DB->get_records_sql("select userid from {urdc_user_detail} where recruitment_id = $driveid");
                $DB->delete_records('urdc_user_detail',array('recruitment_id' => $driveid));
                $DB->delete_records('urdc_user',array('drive_id' => $driveid));
                $DB->delete_records('urdc_recruitment_drive',array('id' => $driveid));
                userstatussetingup($userids);
            }

       }
    }else if($companyid == 4){
        $drives =  $DB->get_records_sql("select id from {bt_recruitment_drive} where test = $quizid");
        // print_r($drives);exit();
        if($drives){    
            foreach($drives as $drive){
                $driveid = $drive->id;
                $userids =  $DB->get_records_sql("select userid from {bt_user_detail} where recruitment_id = $driveid");
                $DB->delete_records('bt_user_detail',array('recruitment_id' => $driveid));
                $DB->delete_records('bt_user',array('drive_id' => $driveid));
                $DB->delete_records('bt_recruitment_drive',array('id' => $driveid));
                userstatussetingup($userids);
            }
        }

    }else{
        $drives =  $DB->get_records_sql("select id from {recruitment_drive} where test = $quizid");
        // print_r($drives);exit();
        if($drives){    
            foreach($drives as $drive){
                $driveid = $drive->id;
                $userids =  $DB->get_records_sql("select userid from {user_detail} where recruitment_id = $driveid");
                $DB->delete_records('user_detail',array('recruitment_id' => $driveid));
                $DB->delete_records('drive_users',array('drive_id' => $driveid));
                $DB->delete_records('recruitment_drive',array('id' => $driveid));
                userstatussetingup($userids);
            }
        }
    }
    course_delete_module($cm->id);
    redirect(new moodle_url('/local/custompage/test.php'),'Quiz deleted Successfully', 3);
}



function userstatussetingup($userids)
{
    global $DB;
        $userid = array();
        foreach($userids as $user)
        {
            array_push($userid,$user->userid);
        }
   
     $result = 0;
     list($in, $params) = $DB->get_in_or_equal($userid);
     
     $users = $DB->get_recordset_select('user', "deleted = 0 and id $in", $params);
     foreach($users as $user)
     {
        delete_user($user);
        $result++;
     }

}


echo $OUTPUT->footer();







