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
$deleteid  = optional_param('deleteid', '', PARAM_TEXT);
$updateid  = optional_param('update', '', PARAM_TEXT);
if ($deleteid) {
	    	           
    $result= $DB->delete_records('ru', array('id'=>$deleteid));     
     redirect(new moodle_url('/local/custompage/ru_list.php'),'recruitment_drive Deleted Sucessfully', 3);
}

if($updateid){
   $update = $DB->get_record('ru', array('id'=>$updateid), '*', MUST_EXIST);

    if ($update) {
       $updaterecord = new stdClass();
   
       $updaterecord->id =$updateid;
       
       if($update->status == 0){
           $status = 1;
       }else{
           $status = 0;
           }
       
       $updaterecord->status =  $status;
                      
       $DB->update_record('ru', $updaterecord);

       redirect(new moodle_url('/local/custompage/ru_list.php'),'recruitment_drive Updated Sucessfully', 3);
   }

}

$PAGE->set_pagelayout('admin');
$PAGE->set_title("Add RSL User");
$PAGE->set_heading("Add RSL User");
$PAGE->set_url($CFG->wwwroot.'/local/custompage/create_ru_form.php');
$coursenode = $PAGE->navbar->add('Add RSL User', new moodle_url($CFG->wwwroot.'/local/custompage/create_ru_form.php'));
$PAGE->set_context(context_system::instance());
//~ $url = $CFG->wwwroot.'/local/custompage/get_data.php';
$return = $CFG->wwwroot.'/local/custompage/ru_list.php';
echo $OUTPUT->header();

$mform = new create_ru_form;
if ($mform->is_cancelled()) {
    redirect($return);

} else if ($record = $mform->get_data()) {
    $drive=$DB->get_record_sql("select *  from {rsl_recruitment_drive} where id =$record->drive_id");
    $quizzz = $DB->get_record_sql("SELECT * FROM {modules} WHERE name = 'quiz'");
    $nodays = $record->days;
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
	$rec->days = $record->days;		
	// $rec->rsl_recruiter = $record->rsl_recruiter;	
    $rec->drive_id = $record->drive_id;
    $rec->timestamp =  time();
 
    
    $DB->insert_record('rsl_user', $rec);


for ($x = 1; $x <= $record->nofu; $x++) {
    $data='';
    $userdata = new stdClass();
    $userdata->firstname = 'RSL';
    $userdata->lastname = time();
    $userdata->username = 'rsl'.password_generate(4).time();
    $userdata->userdepartment=$departmentid; 
    $userdata->email='rsl'.time().'@gmail.com'; 
 
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
        $lastinsertid = $DB->insert_record('rsl_user_detail', $record1);
    }else{
       
        $cids=$DB->get_record_sql("select GROUP_CONCAT(DISTINCT courseid) AS courseid from {company_course} where companyid=$companyid ");       
        $course_id=$DB->get_records_sql("select *  from {course} where id IN($cids->courseid) AND shortname !='rsl-learning'"); 
        
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


        // create interview for user
        $course=$DB->get_record_sql("select *  from {course} WHERE shortname ='rsl-interview' ");
        $courseid = $course->id;
        $section_details = course_create_section($courseid);       
        $section = $section_details->section;
        // print_r($sectionid);exit;
        $sql_cate = "SELECT * FROM {grade_categories} where courseid=$courseid";
        $res_cate = $DB->get_record_sql($sql_cate);
        $grade_cat = $res_cate->id;
        $course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
        $add ='quiz';
        list($module, $context, $cw, $cm, $data) = prepare_new_moduleinfo_data($course, $add, $section);
        // print_r($cm);exit;
        $data->return = 0;
        $data->sr = $sectionreturn;
        $rrg->add = $add;
        $sectionreturn =$section;	
    
        $sectionname = get_section_name($course, $cw);
        $fullmodulename = get_string('modulename', $module->name);
        if ($data->section && $course->format != 'site') {
        $heading = new stdClass();
        $heading->what = $fullmodulename;
        $heading->to   = $sectionname;
        $pageheading = get_string('addinganewto', 'moodle', $heading);
        } else {
        $pageheading = get_string('addinganew', 'moodle', $fullmodulename);
        }
        $navbaraddition = $pageheading;
        $mformclassname = 'mod_quiz_mod_form';
         $mform = new mod_quiz_mod_form($data, $cw->section, $cm, $course);

        // create quiz
        $duration=1000;
        $quizdata = new stdClass();
            $quizdata->name =$userdetail->username;
            $quizdata->introeditor = Array('text' =>'','format' => 1,'itemid' => 924801990);
            $quizdata->showdescription = 0;
            $quizdata->timeopen = 0;
            $quizdata->timeclose = 0;
            $quizdata->timelimit = $duration;
            $quizdata->overduehandling = 'autosubmit';
            $quizdata->graceperiod = 0;
            $quizdata->gradecat = $grade_cat;
            $quizdata->gradepass =7; 
            $quizdata->grade = 10;
            // $quizdata->sumgrade = 10;
            $quizdata->attempts = 0;
            $quizdata->grademethod = 1;
            $quizdata->questionsperpage = 1;
            $quizdata->navmethod = 'free';
            $quizdata->shuffleanswers = 1;
            $quizdata->preferredbehaviour = 'deferredfeedback';
            $quizdata->canredoquestions = 0;
            $quizdata->attemptonlast = 0;
            $quizdata->attemptimmediately = 1;
            $quizdata->correctnessimmediately = 1;
            $quizdata->marksimmediately = 1;
            $quizdata->specificfeedbackimmediately = 1;
            $quizdata->generalfeedbackimmediately = 1;
            $quizdata->rightanswerimmediately = 1;
            $quizdata->overallfeedbackimmediately = 1;
            $quizdata->attemptopen = 1;
            $quizdata->correctnessopen = 1;
            $quizdata->marksopen = 1;
            $quizdata->specificfeedbackopen = 1;
            $quizdata->generalfeedbackopen = 1;
            $quizdata->rightansweropen = 1;
            $quizdata->overallfeedbackopen = 1;
            $quizdata->showuserpicture = 0;
            $quizdata->decimalpoints = 2;
            $quizdata->questiondecimalpoints = -1;
            $quizdata->showblocks = 0;
            $quizdata->quizpassword =''; 
            $quizdata->seb_requiresafeexambrowser = 0;	
            $quizdata->filemanager_sebconfigfile = 783218603;	
            $quizdata->seb_showsebdownloadlink = 1	;
            $quizdata->seb_linkquitseb ='';
            $quizdata->seb_userconfirmquit = 1;
            $quizdata->seb_allowuserquitseb = 1;	
            $quizdata->seb_quitpassword ='';	
            $quizdata->seb_allowreloadinexam = 1;	
            $quizdata->seb_showsebtaskbar = 1;	
            $quizdata->seb_showreloadbutton = 1;	
            $quizdata->seb_showtime = 1;	
            $quizdata->seb_showkeyboardlayout = 1;	
            $quizdata->seb_showwificontrol = 0;	
            $quizdata->seb_enableaudiocontrol = 0;	
            $quizdata->seb_muteonstartup = 0;	
            $quizdata->seb_allowspellchecking = 0;	
            $quizdata->seb_activateurlfiltering = 0;	
            $quizdata->seb_filterembeddedcontent = 0;	
            $quizdata->seb_expressionsallowed ='';	
            $quizdata->seb_regexallowed ='';	
            $quizdata->seb_expressionsblocked ='';	
            $quizdata->seb_regexblocked =''; 	
            $quizdata->seb_allowedbrowserexamkeys = '';
            $quizdata->subnet = '';
            $quizdata->delay1 = 0;
            $quizdata->delay2 = 0;
            $quizdata->browsersecurity = '-';
            $quizdata->boundary_repeats = 1;
            $quizdata->feedbacktext = Array(Array('text' => '','format' => 1, 'itemid' => 321687703),Array('text' =>'', 'format' => 1,'itemid' => 44625097));
            $quizdata->feedbackboundaries = Array();
            $quizdata->visible = 1;
            $quizdata->visibleoncoursepage = 1;
            $quizdata->cmidnumber = null;
            $quizdata->groupmode = 0;
            $quizdata->groupingid = 0;
            $quizdata->availabilityconditionsjson ='{"op":"&","c":[],"showc":[]}';
            $quizdata->completionunlocked = 1;
            $quizdata->completion = 2;      
            $quizdata->completionusegrade = 1;
            $quizdata->completionpass = 1;
            $quizdata->completionattemptsexhausted = 0;
            $quizdata->completionexpected = 0;
            $quizdata->tags = Array();
            $quizdata->course = $courseid;
            $quizdata->coursemodule = 0;
            $quizdata->section = $section;
            $quizdata->module = $quizzz->id;
            $quizdata->modulename = 'quiz';
            $quizdata->instance = 0;
            $quizdata->add = 'quiz';
            $quizdata->update = 0;
            $quizdata->return = 0;
            $quizdata->sr = 0;
            $quizdata->competencies = Array();
            $quizdata->competency_rule = 0;
            $quizdata->submitbutton2 = 'Save and return to course';	
            
             $fromform = add_moduleinfo($quizdata, $course, $mform);
             $cmid = $fromform->coursemodule;
             $quizid = $fromform->instance;
             $quiz = $DB->get_record('quiz', array('id' => $quizid), '*', MUST_EXIST);

             $sql = 'UPDATE {quiz}
             SET sumgrades = COALESCE((
                 SELECT SUM(maxmark)
                 FROM {quiz_slots}
                 WHERE quizid = {quiz}.id
             ), 0)
             WHERE id = ?';
             $DB->execute($sql, array($quiz->id));
             $quiz->sumgrades = $DB->get_field('quiz', 'sumgrades', array('id' => $quiz->id));

            $record1 = new stdClass();
            $record1->userid = $userid;
            $record1->username =  $userdata->username;		
            $record1->password = $userdata->newpassword;	
            $record1->companyid = $companyid;
            $record1->recruitment_id = $record->drive_id; 
            $record1->timestamp =  time();
            $record1->test_groupid =  $drive->test_groupid;
            $record1->interview_id=  $fromform->instance;
            $lastinsertid = $DB->insert_record('rsl_user_detail', $record1);


            // print_r($lastinsertid);exit;

    }

}

    $urlto = $CFG->wwwroot.'/local/custompage/ru_list.php';
    redirect($urlto, 'Users Created Sucessfully ', 8);

}

   
$mform->display();



function password_generate($chars) 
{
  $data = '1234567890abcefghijklmnopqrstuvwxyz';
  return substr(str_shuffle($data), 0, $chars);
}

echo $OUTPUT->footer();






