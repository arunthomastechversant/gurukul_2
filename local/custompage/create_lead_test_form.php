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
require_once('create_lead_test.php');
require_login();
?>
<script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
<?php

$PAGE->set_pagelayout('admin');
$PAGE->set_title("Create Lead Test");
$PAGE->set_heading("Create Lead Test");
$PAGE->set_url($CFG->wwwroot.'/local/custompage/create_lead_test_form.php');
$coursenode = $PAGE->navbar->add('Create Lead Test', new moodle_url($CFG->wwwroot.'/local/custompage/create_lead_test_form.php'));
$PAGE->set_context(context_system::instance());
$url = $CFG->wwwroot.'/local/custompage/create_lead_test_form.php';
$return = $CFG->wwwroot.'/my';
echo $OUTPUT->header();


$mform = new create_lead_test_form();
if ($mform->is_cancelled()) {
    redirect($return);

} else if ($record = $mform->get_data()) {
    // print_r($_REQUEST['category']);exit;
    $data_product=array();
    $request=$_REQUEST['category'];
	foreach($request as $key => $val){
        if($val){
            $string = $key.'-'.$val; 
        }else{
            $string = $key.'-0'; 
        }
        
        array_push($data_product,$string);
    }
    $questionsvar=implode(',', $data_product);


    $quizzz = $DB->get_record_sql("SELECT * FROM {modules} WHERE name = 'quiz'");
    $position = 0;
    $skipcheck = false;
    $courseid=$record->course;
    $not=$record->not;
    $duration=$record->duration * 60;
    $questions=$record->questions;
    // print_r($courseid);exit();
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
    $quizdata = new stdClass();
    if($record->proctoring == 1){
        $quizdata->eproctoringrequired = 1;
    }
    $quizdata->name = $not;
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
    $quizdata->attempts = $record->noa;
    $quizdata->grademethod = 1;
    $quizdata->questionsperpage = $record->qpp;
    $quizdata->navmethod = 'free';
    $quizdata->shuffleanswers = 1;
    $quizdata->preferredbehaviour = 'deferredfeedback';
    $quizdata->canredoquestions = 0;
    $quizdata->attemptonlast = 0;
    $quizdata->marksimmediately = 1;
    $quizdata->overallfeedbackimmediately = 1;
    $quizdata->marksopen = 1;
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




        $includesubcategories = 0;
        $tagids = array();

        foreach($request as $rkey => $rval){
            quiz_add_random_questions($quiz, $record->qpp, $rkey, $rval, $includesubcategories, $tagids); 
        } 

        $cd = new stdClass();
        $cd->test_id   = $quiz->id;
        $cd->questions  = $questionsvar;
        $cd->proctoring = $record->proctoring;
        $sectionid = $DB->insert_record("test_questioncategory", $cd);

        $leadrecord = new stdClass();
        $leadrecord->name = $not;
        $leadrecord->test_id = $quiz->id;  
        $leadrecord->courseid=$courseid;      
        // print_r($leadrecord);exit();
        $insert_record = $DB->insert_record('lead_test', $leadrecord);

    $sql = 'UPDATE {quiz}
    SET sumgrades = COALESCE((
        SELECT SUM(maxmark)
        FROM {quiz_slots}
        WHERE quizid = {quiz}.id
    ), 0)
    WHERE id = ?';
    $DB->execute($sql, array($quiz->id));
    $quiz->sumgrades = $DB->get_field('quiz', 'sumgrades', array('id' => $quiz->id));

    $urlto = $CFG->wwwroot.'/local/custompage/create_lead_test_form.php';
    redirect($urlto, 'Test Created Successfully ', 8);

}

   
$mform->display();



function password_generate($chars) 
{
  $data = '1234567890abcefghijklmnopqrstuvwxyz';
  return substr(str_shuffle($data), 0, $chars);
}

?>
<script type="text/javascript">
	
	$(document).ready(function(){	

		$("#id_course" ).change(function() {
            $("#id_qcategories").remove();
            $("#id_question_category").empty();
            $.ajax({
            type: "POST",
            url: "datafile.php",
            data: {
                'type' : 1,
                'course_id': this.value,
                },
                success: function(data) {
                    // alert(data);
                    $("#id_question_category").html(data);
                }
            });
		});
        var quizid = $('#id_question_category').val();
            $.ajax({
                type: "POST",
                url: "datafile.php",
                data: {
                    'quizid': quizid,  
                },
                success: function(data) {
                    $(".fcontainer").append(data); 
                }
            });
        $("#id_question_category").change(function() {
            $("#id_qcategories").remove();
            $.ajax({
                type: "POST",
                url: "datafile.php",
                data: {
                    'quizid': $(this).val(),  
                },
                success: function(data) {
                    $(".fcontainer").append(data); 
                }
            });
        });
	});

    $(document).on('change','.category',function(){
        var category = this.id;
        var label = "#error_category_"+category;
        $.ajax({
            type: "POST",
            url: "datafile.php",
            data: {
                'category': this.id,
                'value' : this.value,
            },
            success: function(data) {
                if(data == 200){
                    document.getElementById(category).value = "";
                    $(label).html('Only Numbers Allowed').fadeIn();
                }else if(data == 201){
                    document.getElementById(category).value = "";
                    $(label).html('Enterd number exceed the total question').fadeIn();
                }else{
                    $(label).fadeOut();
                }
            }
        });
    })

	</script>

<?php

echo $OUTPUT->footer();







