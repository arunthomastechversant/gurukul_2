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
require_once('edit_recruitment_drive.php');
require_login();
?>
<script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
<?php

$url=$CFG->wwwroot.'/local/custompage/edit_recruitment_drive_form.php';


$PAGE->set_pagelayout('admin');
$PAGE->set_title("Edit Recruitment Drive Details");
$PAGE->set_heading("Edit Recruitment Drive Details");
$PAGE->set_url($CFG->wwwroot.'/local/custompage/edit_recruitment_drive_form.php');
$coursenode = $PAGE->navbar->add('Edit Recruitment Drive Details', new moodle_url($CFG->wwwroot.'/local/custompage/edit_recruitment_drive_form.php'));

$PAGE->set_context(context_system::instance());

$return = new moodle_url('/local/custompage/recruitment_drive.php');
echo $OUTPUT->header();

$mform = new edit_recruitment_drive_form();
if ($mform->is_cancelled()) 
{
    redirect($return);

} 
else if ($record = $mform->get_data()) 
{
		$systemcontext = context_system::instance();
		$companyid = iomad::get_my_companyid($systemcontext);
		if($companyid == 1){
			$drive_data = $DB->get_record_sql("SELECT startdate,enddate FROM {rsl_recruitment_drive} where id=$record->driveid");
        }else if($companyid == 2){
			$drive_data = $DB->get_record_sql("SELECT startdate,enddate FROM {urdc_recruitment_drive} where id=$record->driveid");
        }else if($companyid == 4){
			$drive_data = $DB->get_record_sql("SELECT startdate,enddate FROM {bt_recruitment_drive} where id=$record->driveid");
        }else{
			$drive_data = $DB->get_record_sql("SELECT startdate,enddate FROM {recruitment_drive} where id=$record->driveid");
        }
		
		if(($drive_data->startdate != $record->startdate) || ($drive_data->enddate != $record->enddate)){
			// print_r($record);exit();
			if($companyid == 1){
				$users = $DB->get_records_sql("SELECT rd.userid,ue.id as enrolmentid from {rsl_user_detail} as rd join {user_enrolments} as ue on rd.userid = ue.userid where rd.recruitment_id = $record->driveid");
			}else if($companyid == 2){
				$users = $DB->get_records_sql("SELECT rd.userid,ue.id as enrolmentid from {urdc_user_detail} as rd join {user_enrolments} as ue on rd.userid = ue.userid where rd.recruitment_id = $record->driveid");
			}else if($companyid == 4){
				$users = $DB->get_records_sql("SELECT rd.userid,ue.id as enrolmentid from {bt_user_detail} as rd join {user_enrolments} as ue on rd.userid = ue.userid where rd.recruitment_id = $record->driveid");
			}else{
				$users = $DB->get_records_sql("SELECT rd.userid,ue.id as enrolmentid from {user_detail} as rd join {user_enrolments} as ue on rd.userid = ue.userid where rd.recruitment_id = $record->driveid");
			}
			// print_r($users);exit();
			foreach($users as $user){
				$enroldata 				= new stdclass();
				$enroldata->id 			= $user->enrolmentid;
				$enroldata->timestart 	= $record->startdate;
				$enroldata->timeend 	= $record->enddate;
				$DB->update_record('user_enrolments',$enroldata);
			}		
		}

		$data 				= new stdclass();
		$data->id 			= $record->driveid;
		$data->name 		= $record->name;
		$data->startdate	= $record->startdate;
		$data->enddate		= $record->enddate;
		$data->test			= $record->test;
		if($companyid == 1){
			$data->interview	= $record->interview;
			$DB->update_record('rsl_recruitment_drive',$data);
		}else if($companyid == 2){
			$DB->update_record('urdc_recruitment_drive',$data);
		}else if($companyid == 4){
			$DB->update_record('bt_recruitment_drive',$data);
		}else{
			$DB->update_record('recruitment_drive',$data);
		}

	redirect(new moodle_url('/local/custompage/recruitment_drive.php'),'Recruitment Drive Updated Successfully', 3);

}
   
$mform->display();





?>
<script>
	

	</script>
<?php

echo $OUTPUT->footer();







