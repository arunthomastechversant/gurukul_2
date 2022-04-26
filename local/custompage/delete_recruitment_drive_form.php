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

$url=$CFG->wwwroot.'/local/custompage/edit_recruitment_drive_form.php';
$delete = optional_param('delete', 0, PARAM_INT);    // course_sections.id
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$PAGE->set_pagelayout('admin');
$PAGE->set_title("Delete Recruitment Drive Details");
$PAGE->set_heading("Delete Recruitment Drive Details");
$PAGE->set_url($CFG->wwwroot.'/local/custompage/delete_recruitment_drive_form.php');
$coursenode = $PAGE->navbar->add('Edit Recruitment Drive Details', new moodle_url($CFG->wwwroot.'/local/custompage/delete_recruitment_drive_form.php'));

$PAGE->set_context(context_system::instance());

echo $OUTPUT->header();

if(!empty($delete)){

    $return = new moodle_url('/local/custompage/recruitment_drive.php');
    if (!$confirm or !confirm_sesskey()) {
        $drivename = $DB->get_record('rsl_recruitment_drive',array('id' => $delete))->name;
        // print_r($drivename);exit();
        $optionsyes = array('confirm'=>1, 'delete'=>$delete, 'sesskey'=>sesskey());
        $strparams = (object)array('type' => 'Drive', 'name' => $drivename);
        $strdeletechecktypename = get_string('deletechecktypename', '', $strparams);
        echo $OUTPUT->box_start('noticebox');
        $formcontinue = new single_button(new moodle_url("$CFG->wwwroot/local/custompage/delete_recruitment_drive_form.php", $optionsyes), get_string('yes'));
        $formcancel = new single_button($return, get_string('no'));
        echo $OUTPUT->confirm($strdeletechecktypename, $formcontinue, $formcancel);
        echo $OUTPUT->box_end();
        exit;
    }
    $systemcontext = context_system::instance();
    $companyid = iomad::get_my_companyid($systemcontext);
    $userids = "";
    $driveid = $delete;
    if($companyid == 1){   
        if($driveid){
            $userids =  $DB->get_records_sql("select userid from {rsl_user_detail} where recruitment_id = $driveid");
            $DB->delete_records('rsl_user_detail',array('recruitment_id' => $driveid));
            $DB->delete_records('rsl_user',array('drive_id' => $driveid));
            $DB->delete_records('rsl_recruitment_drive',array('id' => $driveid));
            userstatussetingup($userids);
        } 
    }else if($companyid == 2){   
        if($driveid){
            $userids =  $DB->get_records_sql("select userid from {urdc_user_detail} where recruitment_id = $driveid");
            $DB->delete_records('urdc_user_detail',array('recruitment_id' => $driveid));
            $DB->delete_records('urdc_user',array('drive_id' => $driveid));
            $DB->delete_records('urdc_recruitment_drive',array('id' => $driveid));
            userstatussetingup($userids);
        } 
    }else if($companyid == 4){
        if($driveid){
            $userids =  $DB->get_records_sql("select userid from {bt_user_detail} where recruitment_id = $driveid");
            $DB->delete_records('bt_user_detail',array('recruitment_id' => $driveid));
            $DB->delete_records('bt_user',array('drive_id' => $driveid));
            $DB->delete_records('bt_recruitment_drive',array('id' => $driveid));
            userstatussetingup($userids);
        }
    }else{
        if($driveid){
            $userids =  $DB->get_records_sql("select userid from {user_detail} where recruitment_id = $driveid");
            $DB->delete_records('user_detail',array('recruitment_id' => $driveid));
            $DB->delete_records('drive_users',array('drive_id' => $driveid));
            $DB->delete_records('recruitment_drive',array('id' => $driveid));
            userstatussetingup($userids);
        }
    }
    redirect(new moodle_url('/local/custompage/recruitment_drive.php'),'Recruitment drive deleted Successfully', 3);

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
// print_r($userid);exit();
     list($in, $params) = $DB->get_in_or_equal($userid);
     
     $users = $DB->get_recordset_select('user', "deleted = 0 and id $in", $params);
     foreach($users as $user)
     {
        delete_user($user);
        $result++;
     }

}
?>
<script>
	

	</script>
<?php

echo $OUTPUT->footer();







