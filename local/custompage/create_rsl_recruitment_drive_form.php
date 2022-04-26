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


require_once('create_rsl_recruitment_drive.php');
require_login();
$PAGE->set_pagelayout('admin');
$PAGE->set_title("Add RSL Recruitment Drive");
$PAGE->set_heading("Add RSL Recruitment Drive");
$PAGE->set_url($CFG->wwwroot.'/local/custompage/create_rsl_recruitment_drive_form.php');
$coursenode = $PAGE->navbar->add('Add RSL Recruitment Drive', new moodle_url($CFG->wwwroot.'/local/custompage/create_rsl_recruitment_drive_form.php'));
$PAGE->navbar->add('Add RSL User', new moodle_url($CFG->wwwroot.'/local/custompage/create_ru_form.php'));
$PAGE->set_context(context_system::instance());
//~ $url = $CFG->wwwroot.'/local/custompage/get_data.php';
$return = $CFG->wwwroot.'/local/custompage/rsl_recruitment_drive_list.php';
echo $OUTPUT->header();

$mform = new create_rsl_recruitment_drive_form();
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
    $record->interview  = $data->interview; 

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
    $insert_record = $DB->insert_record('rsl_recruitment_drive', $record);
// print_r( $record);exit;
// print_r($record );exit;
// $record->intertview    =  $data->test;
//  $record->rounds    =  $data->rounds;

// if($data->interview == 1){

    // $record->intertview    =  $data->test;
    // // $record->rounds    =  $data->rounds;
    // $courseid=$data->courseid;
    // $section_details = course_create_section($courseid);       
    // $section = $section_details->section;
    // // print_r($sectionid);exit;
    // $sql_cate = "SELECT * FROM {grade_categories} where courseid=$courseid";
    // $res_cate = $DB->get_record_sql($sql_cate);
    // $grade_cat = $res_cate->id;

//     $add ='quiz';
//     list($module, $context, $cw, $cm, $data) = prepare_new_moduleinfo_data($course, $add, $section);
//     // print_r($cm);exit;
//     $data->return = 0;
//     $data->sr = $sectionreturn;
//     $rrg->add = $add;
//     $sectionreturn =$section;	

//     $sectionname = get_section_name($course, $cw);
//     $fullmodulename = get_string('modulename', $module->name);
//     if ($data->section && $course->format != 'site') {
//     $heading = new stdClass();
//     $heading->what = $fullmodulename;
//     $heading->to   = $sectionname;
//     $pageheading = get_string('addinganewto', 'moodle', $heading);
//     } else {
//     $pageheading = get_string('addinganew', 'moodle', $fullmodulename);
//     }
//     $navbaraddition = $pageheading;
//     $mformclassname = 'mod_quiz_mod_form';
//      $mform = new mod_quiz_mod_form($data, $cw->section, $cm, $course);
     
// $quizarray=array();
// // for ($x = 0; $x <  $rounds; $x++) {  
//     for ($x = 0; $x < 1; $x++) {
//         // create quiz
//     $quizdata = new stdClass();
//     $quizdata->name = "Interview".time();
//     $quizdata->introeditor = Array('text' =>'','format' => 1,'itemid' => 924801990);
//     $quizdata->showdescription = 0;
//     $quizdata->timeopen = 0;
//     $quizdata->timeclose = 0;
//     $quizdata->timelimit = '';
//     $quizdata->overduehandling = 'autosubmit';
//     $quizdata->graceperiod = 0;
//     $quizdata->gradecat = $grade_cat;
//     $quizdata->gradepass =null; 
//     $quizdata->grade = 0;
//     $quizdata->attempts = 0;
//     $quizdata->grademethod = 1;
//     $quizdata->questionsperpage = 1;
//     $quizdata->navmethod = 'free';
//     $quizdata->shuffleanswers = 1;
//     $quizdata->preferredbehaviour = 'deferredfeedback';
//     $quizdata->canredoquestions = 0;
//     $quizdata->attemptonlast = 0;
//     $quizdata->attemptimmediately = 1;
//     $quizdata->correctnessimmediately = 1;
//     $quizdata->marksimmediately = 1;
//     $quizdata->specificfeedbackimmediately = 1;
//     $quizdata->generalfeedbackimmediately = 1;
//     $quizdata->rightanswerimmediately = 1;
//     $quizdata->overallfeedbackimmediately = 1;
//     $quizdata->attemptopen = 1;
//     $quizdata->correctnessopen = 1;
//     $quizdata->marksopen = 1;
//     $quizdata->specificfeedbackopen = 1;
//     $quizdata->generalfeedbackopen = 1;
//     $quizdata->rightansweropen = 1;
//     $quizdata->overallfeedbackopen = 1;
//     $quizdata->showuserpicture = 0;
//     $quizdata->decimalpoints = 2;
//     $quizdata->questiondecimalpoints = -1;
//     $quizdata->showblocks = 0;
//     $quizdata->quizpassword =''; 
//     $quizdata->seb_requiresafeexambrowser = 0;	
//     $quizdata->filemanager_sebconfigfile = 783218603;	
//     $quizdata->seb_showsebdownloadlink = 1	;
//     $quizdata->seb_linkquitseb ='';
//     $quizdata->seb_userconfirmquit = 1;
//     $quizdata->seb_allowuserquitseb = 1;	
//     $quizdata->seb_quitpassword ='';	
//     $quizdata->seb_allowreloadinexam = 1;	
//     $quizdata->seb_showsebtaskbar = 1;	
//     $quizdata->seb_showreloadbutton = 1;	
//     $quizdata->seb_showtime = 1;	
//     $quizdata->seb_showkeyboardlayout = 1;	
//     $quizdata->seb_showwificontrol = 0;	
//     $quizdata->seb_enableaudiocontrol = 0;	
//     $quizdata->seb_muteonstartup = 0;	
//     $quizdata->seb_allowspellchecking = 0;	
//     $quizdata->seb_activateurlfiltering = 0;	
//     $quizdata->seb_filterembeddedcontent = 0;	
//     $quizdata->seb_expressionsallowed ='';	
//     $quizdata->seb_regexallowed ='';	
//     $quizdata->seb_expressionsblocked ='';	
//     $quizdata->seb_regexblocked =''; 	
//     $quizdata->seb_allowedbrowserexamkeys = '';
//     $quizdata->subnet = '';
//     $quizdata->delay1 = 0;
//     $quizdata->delay2 = 0;
//     $quizdata->browsersecurity = '-';
//     $quizdata->boundary_repeats = 1;
//     $quizdata->feedbacktext = Array(Array('text' => '','format' => 1, 'itemid' => 321687703),Array('text' =>'', 'format' => 1,'itemid' => 44625097));
//     $quizdata->feedbackboundaries = Array();
//     $quizdata->visible = 1;
//     $quizdata->visibleoncoursepage = 1;
//     $quizdata->cmidnumber = null;
//     $quizdata->groupmode = 0;
//     $quizdata->groupingid = 0;
//     $quizdata->availabilityconditionsjson ='{"op":"&","c":[],"showc":[]}';
//     $quizdata->completionunlocked = 1;
//     $quizdata->completion = 1;
//     $quizdata->completionpass = 0;
//     $quizdata->completionattemptsexhausted = 0;
//     $quizdata->completionexpected = 0;
//     $quizdata->tags = Array();
//     $quizdata->course = $courseid;
//     $quizdata->coursemodule = 0;
//     $quizdata->section = $section;
//     $quizdata->module = $quizzz->id;
//     $quizdata->modulename = 'quiz';
//     $quizdata->instance = 0;
//     $quizdata->add = 'quiz';
//     $quizdata->update = 0;
//     $quizdata->return = 0;
//     $quizdata->sr = 0;
//     $quizdata->competencies = Array();
//     $quizdata->competency_rule = 0;
//     $quizdata->submitbutton2 = 'Save and return to course';	
//     $fromform = add_moduleinfo($quizdata, $course, $mform);
//     $cmid = $fromform->coursemodule;
//     $quizid = $fromform->instance;
//     array_push($quizarray,$quizid);
//     $record->rounds    =  0;


// }
    
//     $quizes=implode(',', $quizarray);
//     $record->quizid    =  $quizes;
//         // print_r($record);exit;
//     $systemcontext = context_system::instance();
//     $companyid = iomad::get_my_companyid($systemcontext);

//     $cids=$DB->get_record_sql("select GROUP_CONCAT(DISTINCT courseid) AS courseid from {company_course} where companyid=$companyid ");
//     $course_id=$DB->get_records_sql("select *  from {course} where id IN($cids->courseid) "); 
//     foreach ($course_id as $key => $course){
//     // print_r($course);

    //     $context = context_course::instance($course->id);
    //     require_capability('moodle/course:managegroups', $context);

    //     $strgroups = get_string('groups');
    //     $PAGE->set_title($strgroups);
    //     $PAGE->set_heading($course->fullname . ': '.$strgroups);
    //     $PAGE->set_pagelayout('admin');
    //     navigation_node::override_active_url(new moodle_url('/group/index.php', array('id' => $course->id)));

    //     $returnurl = $CFG->wwwroot.'/group/index.php?id='.$course->id.'&group='.$id;

    //     // Prepare the description editor: We do support files for group descriptions
    //     $editoroptions = array('maxfiles'=>EDITOR_UNLIMITED_FILES, 'maxbytes'=>$course->maxbytes, 'trust'=>false, 'context'=>$context, 'noclean'=>true);
    //     if (!empty($group->id)) {
    //         $editoroptions['subdirs'] = file_area_contains_subdirs($context, 'group', 'description', $group->id);
    //         $group = file_prepare_standard_editor($group, 'description', $editoroptions, $context, 'group', 'description', $group->id);
    //     } else {
    //         $editoroptions['subdirs'] = false;
    //         $group = file_prepare_standard_editor($group, 'description', $editoroptions, $context, 'group', 'description', null);
    //     }

    //     $editform = new group_form(null, array('editoroptions'=>$editoroptions));
    //     $gp = new stdClass();
    //     $gp->name=$groupname;
    //     $gp->courseid=$course->id;

    //     $gp->description_editor=array("text"=>"","format"=>1); 
    // // echo $course->shortname;
    //     $id = groups_create_group($gp, $editform, $editoroptions);
        // if($course->shortname =='rsl-interview'){
        //     $record->interview_groupid=0;    
        // }else{
        //     $record->test_groupid=$id; 
        // }

//     }
// }else{
//     $record->intertview    =  0;
//     $record->rounds    =  0;
//             // print_r($record);exit;
//             $systemcontext = context_system::instance();
//             $companyid = iomad::get_my_companyid($systemcontext);
        
//             $cids=$DB->get_record_sql("select GROUP_CONCAT(DISTINCT courseid) AS courseid from {company_course} where companyid=$companyid ");
//             $course_id=$DB->get_records_sql("select *  from {course} where id IN($cids->courseid)"); 
//             foreach ($course_id as $key => $course){
//             // print_r($course);
        
//                 $context = context_course::instance($course->id);
//                 require_capability('moodle/course:managegroups', $context);
        
//                 $strgroups = get_string('groups');
//                 $PAGE->set_title($strgroups);
//                 $PAGE->set_heading($course->fullname . ': '.$strgroups);
//                 $PAGE->set_pagelayout('admin');
//                 navigation_node::override_active_url(new moodle_url('/group/index.php', array('id' => $course->id)));
        
//                 $returnurl = $CFG->wwwroot.'/group/index.php?id='.$course->id.'&group='.$id;
        
//                 // Prepare the description editor: We do support files for group descriptions
//                 $editoroptions = array('maxfiles'=>EDITOR_UNLIMITED_FILES, 'maxbytes'=>$course->maxbytes, 'trust'=>false, 'context'=>$context, 'noclean'=>true);
//                 if (!empty($group->id)) {
//                     $editoroptions['subdirs'] = file_area_contains_subdirs($context, 'group', 'description', $group->id);
//                     $group = file_prepare_standard_editor($group, 'description', $editoroptions, $context, 'group', 'description', $group->id);
//                 } else {
//                     $editoroptions['subdirs'] = false;
//                     $group = file_prepare_standard_editor($group, 'description', $editoroptions, $context, 'group', 'description', null);
//                 }
        
//                 $editform = new group_form(null, array('editoroptions'=>$editoroptions));
//                 $gp = new stdClass();
//                 $gp->name=$groupname;
//                 $gp->courseid=$course->id;
        
//                 $gp->description_editor=array("text"=>"","format"=>1);
//             // echo $course->shortname;
//                 $id = groups_create_group($gp, $editform, $editoroptions);
//                 if($course->shortname =='rsl-interview'){
//                     $record->interview_groupid=0;    
//                 }else{
//                     $record->test_groupid=$id; 
//                 }
        
//             }
// }










$urlto = $CFG->wwwroot.'/local/custompage/rsl_recruitment_drive_list.php';
redirect($urlto, 'Recruitment Drive Created Successfully ', 8);

}

   
$mform->display();
echo $OUTPUT->footer();
