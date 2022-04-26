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
require_once('create_ru.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/course/modlib.php');
require_once($CFG->dirroot.'/mod/quiz/mod_form.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot.'/group/lib.php');
require_once($CFG->dirroot.'/group/group_form.php');
require_once($CFG->dirroot. '/group/lib.php');
require_once($CFG->dirroot . '/user/selector/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

require_login();

$PAGE->set_pagelayout('admin');
$PAGE->set_title("Add RSL User");
$PAGE->set_heading("Add RSL User");
$PAGE->set_url($CFG->wwwroot.'/local/custompage/create_ru_form.php');
$coursenode = $PAGE->navbar->add('Add RSL User');
$PAGE->set_context(context_system::instance());
//~ $url = $CFG->wwwroot.'/local/custompage/get_data.php';
$return = $CFG->wwwroot.'/local/custompage/ru_list.php';
echo $OUTPUT->header();

$mform = new create_ru_form();
if ($mform->is_cancelled()) {
    redirect($return);

} else if ($record = $mform->get_data()) {
	//print_r($record);exit();
    $drive=$DB->get_record_sql("select *  from {rsl_recruitment_drive} where id =$record->drive_id");
    $quizzz = $DB->get_record_sql("SELECT * FROM {modules} WHERE name = 'quiz'");
    //$nodays = $record->days;
    $systemcontext = context_system::instance();
    $companyid = iomad::get_my_companyid($systemcontext);
    $company = new company($companyid);
    $companyname = $company->get_name();
    $parentlevel = company::get_company_parentnode($company->id);
    $companydepartment = $parentlevel->id;
    $systemcontext = \context_system::instance();
    
    if (\iomad::has_capability('block/iomad_company_admin:edit_all_departments', $systemcontext)) {
        $userhierarchylevel = $parentlevel->id;
    } else {
        $userlevel = $company->get_userlevel($USER);
        $userhierarchylevel = $userlevel->id;
    }

    if ($departmentid == 0) {
        $departmentid = $userhierarchylevel;
    } else {
        $departmentid =$departmentid;
    }

    $rec = new stdClass();
	$rec->no_of_users = $record->nofu;
	//$rec->days = $record->days;		
	$rec->days = 0;
	// $rec->rsl_recruiter = $record->rsl_recruiter;	
    $rec->drive_id = $record->drive_id;
    $rec->timestamp =  time();
 
    
    $DB->insert_record('rsl_user', $rec);


for ($x = 1; $x <= $record->nofu; $x++) {
    $data='';
    $userdata = new stdClass();
    $userdata->firstname = 'RSL';
    $userdata->lastname = time();
    $userdata->username = 'rsl'.rand(10,100).password_generate(5);
    $userdata->userdepartment=$departmentid; 
    $userdata->email='rsl'.rand(10,100).time().'@gmail.com'; 
 
    $userdata->newpassword='Rsl@1'.password_generate(4);
	$systemcontext = context_system::instance();
	$companyid = iomad::get_my_companyid($systemcontext);
    $userdata->userid = $USER->id;
    if ($companyid > 0) {
        $userdata->companyid = $companyid;
    }
 
    if (!$userid = company_user::create($userdata)) {
        $this->verbose("Error inserting a new user in the database!");
        if (!$this->get('ignore_errors')) {
            die();
        }
    }
    // print_r($userid);exit;
    $userdetail=$DB->get_record_sql("select *  from {user} where id =$userid");
    $drive=$DB->get_record_sql("select *  from {rsl_recruitment_drive} where id =$record->drive_id");
    $user = $DB->get_record('user', array('id' => $userid, 'deleted' => 0), '*', MUST_EXIST);
    $enrol = enrol_get_plugin('manual');


    if($drive->interview == 0 ){
        // print_r("test");
        $cids=$DB->get_record_sql("select GROUP_CONCAT(DISTINCT courseid) AS courseid from {company_course} where companyid=$companyid ");
        // print_r($cids);exit;
        $course_id=$DB->get_records_sql("select *  from {course} where id IN($cids->courseid)"); 
        
        foreach ($course_id as $key => $course){
            $enrolmethod='manual';   
            $course = $DB->get_record('course', array('id' => $course->id), '*', MUST_EXIST);      
            $instance = $DB->get_record('enrol', array('enrol' => 'manual','courseid' => $course->id));
            //$end_date = strtotime("+$nodays day", $drive->enddate);
	    $end_date = $drive->enddate;
            $enrol->enrol_user($instance, $user->id, 5,$drive->startdate,$end_date);

        }
        
        if (!groups_add_member($drive->test_groupid, $user->id)) {
            print_error('erroraddremoveuser', 'group', $returnurl);
        }
        $record1 = new stdClass();
        $record1->userid = $userid;
        $record1->username =  $userdata->username;		
        $record1->password = $userdata->newpassword;	
        $record1->companyid = $companyid;
        $record1->recruitment_id = $record->drive_id; 
        $record1->timestamp =  time();
        $record1->test_groupid =  $drive->test_groupid;
        $record1->interview_id=  '';
        $record1->rsl_due=  '';
        $lastinsertid = $DB->insert_record('rsl_user_detail', $record1);


        $record2 = new stdClass();
        $record2->userid = $userid;
        $record2->recruitment_id =  $record->drive_id;	
        $record2->userstatus =  'User Created';	
        $record2->timestamp =  time();	
        $DB->insert_record('userstatus', $record2);
    }else{
       
        $cids=$DB->get_record_sql("select GROUP_CONCAT(DISTINCT courseid) AS courseid from {company_course} where companyid=$companyid ");
        // print_r($cids);exit;
        $course_id=$DB->get_records_sql("select *  from {course} where id IN($cids->courseid)"); 
       
        foreach ($course_id as $key => $course){
            $enrolmethod='manual';   
            $course = $DB->get_record('course', array('id' => $course->id), '*', MUST_EXIST);      
            $instance = $DB->get_record('enrol', array('enrol' => 'manual','courseid' => $course->id));
            $end_date = strtotime("+$nodays day", $drive->startdate);
            $enrol->enrol_user($instance, $user->id, 5,$drive->startdate,$end_date);

        }
        
        if (!groups_add_member($drive->test_groupid, $user->id)) {
            print_error('erroraddremoveuser', 'group', $returnurl);
        }
        $record1 = new stdClass();
        $record1->userid = $userid;
        $record1->username =  $userdata->username;		
        $record1->password = $userdata->newpassword;	
        $record1->companyid = $companyid;
        $record1->recruitment_id = $record->drive_id; 
        $record1->timestamp =  time();
        $record1->test_groupid =  $drive->test_groupid;
        $record1->interview_id=  '';
        $record1->rsl_due=  '';
        $lastinsertid = $DB->insert_record('rsl_user_detail', $record1);


        $record2 = new stdClass();
        $record2->userid = $userid;
        $record2->recruitment_id =  $record->drive_id;	
        $record2->userstatus =  'User Created';	
        $record2->timestamp =  time();	
        $DB->insert_record('userstatus', $record2);

        

    }

}

    $urlto = $CFG->wwwroot.'/local/custompage/ru_list.php';
    redirect($urlto, 'Users Created Successfully ', 8);

}

   
$mform->display();



function password_generate($chars) 
{
  $data = '1234567890abcefghijklmnopqrstuvwxyz';
  return substr(str_shuffle($data), 0, $chars);
}

echo $OUTPUT->footer();






