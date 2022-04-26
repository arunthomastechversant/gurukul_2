<style>
table {
  width: 100%;
  display: inline-table !important;
}
</style>
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
$userid = optional_param('userid','', PARAM_INT);

$userdata=$DB->get_record_sql("SELECT * FROM {user}  where id=$userid ");
// print_r("etststs");exit;
require_once('hrinterview.php');
// print_r("etststs");exit;
require_login();

$PAGE->set_pagelayout('admin');
$PAGE->set_title("HR Interview");
$PAGE->set_heading("HR Interview");
$PAGE->set_url($CFG->wwwroot.'/local/listcoursefiles/hrinterview_form.php');


$PAGE->navbar->add('HR Interview List', new moodle_url('/local/listcoursefiles/hrinterviewlist.php'));
$PAGE->navbar->add($userdata->username, new moodle_url($CFG->wwwroot.'/local/listcoursefiles/hrinterview_form.php',array('userid'=>$userid )));
$PAGE->set_context(context_system::instance());
//~ $url = $CFG->wwwroot.'/local/listcoursefiles/get_data.php';
$return = $CFG->wwwroot.'/local/listcoursefiles/hrinterviewlist.php';
echo $OUTPUT->header();

$mform = new hrinterview_form();
if ($mform->is_cancelled()) {
    redirect($return);

} else if ($data = $mform->get_data()) {
    $interviewdata = $DB->get_record_sql("SELECT * FROM {hrinterview} WHERE userid = $data->userid");
    $interdata = $DB->get_records_sql("SELECT * FROM {interview} WHERE userid =$data->userid");	
    $drivedetail=$DB->get_record_sql("SELECT * FROM {rsl_recruitment_drive}  where id=$interviewdata->driveid ");	
    $record1 = new stdClass();
    $record1->id = $interviewdata->id;
    $record1->remark = $data->remark;
    $data_product=array();
    // print_r(count($data->name));exit;
    $avg=0;
    foreach ($data->name as $key =>$val) {
        if($val){
            $string = $key.'-'.$val; 
        }else{
            $string = $key.'-0'; 
        }
        $avg=$avg+($val*10);
        array_push($data_product,$string);
    }
   
    $average=$avg/count($data->name);
    $score=0;
    if($interdata ){
        foreach ($interdata as $userdata =>$val) {
            $interavg=$score+$val->interviewscore;          
        }
        $interaverage=$interavg/count($interdata);
        $interviewscore=$interaverage+$average;
        $total_user=$interviewscore/2;
    }else{
        $interviewscore=$average;
        $total_user=$interviewscore;
    }

    
    $gradedetail = $DB->get_record_sql(" select round(((10/b.sumgrades) * a.sumgrades)* 10) quizper,a.timemodified as timemodified from mdl_quiz_attempts a join mdl_quiz b on a.quiz = b.id where b.id = $drivedetail->test and userid = $data->userid order by a.timemodified desc limit 1");
    $usertotal=$gradedetail->quizper + $total_user;
    $usertotal=$usertotal/2;
    // print_r($usertotal);exit;
   
   
    $questionsvar=implode(',', $data_product);
    
    $record1->category_score =  $questionsvar;	
    $record1->total_score =  $average;
    if($interviewscore >=70){
        $record1->status =  'Selected'; 
    }else{
        $record1->status =  'Rejected';   
    }
    if($data->status){
        $record1->status =  $data->status; 
    }
    $finaluserstatus=$record1->status;
    // print_r($record1);exit;
    $DB->update_record('hrinterview', $record1);
    $record2 = new stdClass();
    $record2->userid = $data->userid;
    $record2->recruitment_id =  $interviewdata->driveid;	
    $record2->userstatus =  $finaluserstatus;	
    $record2->timestamp =  time();	
    $DB->insert_record('userstatus', $record2);	

    // print_r($record1);exit;
  
  	
    // $DB->update_record('hrinterview', $record1);
    




   
$urlto = $CFG->wwwroot.'/local/listcoursefiles/hrinterviewlist.php';
redirect($urlto, 'Interview Taken Successfully ', 8);

}else{
    $course=$DB->get_record_sql("select *  from {course} where  shortname ='rsl-test' ");
    $quizzz = $DB->get_record_sql("SELECT * FROM {modules} WHERE name = 'quiz'");
    $instance = $DB->get_record_sql("SELECT rud.recruitment_id,rrd.test FROM {rsl_user_detail} rud Join {rsl_recruitment_drive} rrd ON rrd.id= rud.recruitment_id where rud.userid=$userid");
    $cmiddata = $DB->get_record_sql("SELECT * FROM {course_modules} WHERE course = $course->id AND module = $quizzz->id AND instance = $instance->test");
    $attemptdata = $DB->get_record_sql("SELECT * FROM {quiz_attempts} WHERE userid = $userid AND quiz = $instance->test");
    
    // category based scores
    $attemptid=$attemptdata->id;  // Need to take from mdl_quiz_attempts with proper userid and quiz id , latest attempt can be consider so orderby id desc limit 1
    $cmid=$cmiddata->id;  // course module id of that quiz
    
    //Take attempt object of that user in that particular quiz attempt
    
    $attemptobj = quiz_create_attempt_handling_errors($attemptid, $cmid);
    $attempt = $attemptobj->get_attempt();
    
    $uniqueid = $attempt->uniqueid;
    $quiz = $attempt->quiz;
    $state = $attempt->state;
    $sumgrades = $attempt->sumgrades;
    
    $array_result = array();
    
    //Taken mark for each question
    
    $sql_question_attempts = "SELECT qa.id,qa.slot,q.category,qa.questionid,qa.maxmark,qas.fraction FROM {$CFG->prefix}question_attempts qa
    JOIN {$CFG->prefix}question_attempt_steps qas ON qas.questionattemptid = qa.id
    JOIN {$CFG->prefix}question q ON q.id = qa.questionid
    JOIN {$CFG->prefix}question_categories qc ON qc.id = q.category
    WHERE qa.questionusageid=$uniqueid order by qa.slot";
    $res_question_attempts = $DB->get_records_sql($sql_question_attempts);
    
    foreach($res_question_attempts as $attempt_data)
    {
        $percentage = (floor($attempt_data->fraction)/floor($attempt_data->maxmark))*100;
        $array_result[] = array('category'=>$attempt_data->category,'mark'=>$percentage,'question'=>$attempt_data->questionid,'slot'=>$attempt_data->slot);
    }
    
    //print_r($array_result);
    //sort it by category
    $byGroup_category = group_by("category", $array_result);
    
    $final_result = array();
    //Saving for final analysis into an array on the basis of category id
    foreach($byGroup_category as $key => $subcats)
    {
        $category_id  = $key;
        $count = 0;
        $total = 0;
        foreach($subcats as $sub)
        {
            $total += $sub['mark'];
            $count++;
        }
        $final_percentage = $total/$count;
        $final_result[$category_id] = round($final_percentage,2);
        
    }
    $gradedetail = $DB->get_record_sql(" select round(((10/b.sumgrades) * a.sumgrades)* 10) quizper,a.timemodified as timemodified from mdl_quiz_attempts a join mdl_quiz b on a.quiz = b.id where b.id = $instance->test and userid = $userid order by a.timemodified desc limit 1");
    $table =array();
    $table[$key]['Name']='Test';
    // print_r($final_result);exit;
    
    foreach($final_result as $ck => $cv) {
                
        
        $testdetail= $DB->get_record_sql(" select id,name from {question_categories} where id=$ck ");
        $table[$key][$testdetail->name]=$cv.' % ';
    
    }
    $table[$key]['Score']= $gradedetail->quizper.' % ';
    $table[$key]['Remark']= ' - ';
    
    
    
    
    
    
    
    
    
    
    // category based scores end

        
        $testdetail= $DB->get_records_sql(" select * from {interview} where userid=$userid");
    
        // print_r($category);exit;
        foreach($testdetail as $key => $val) {
    
           
                if($val->isrsl == 1){
                    $table[$key]['Name']='RSL Interview';
                }else if($val->interviewtype == 1 ){
                    $table[$key]['Name']='Interview 1';
                }else if($val->interviewtype == 2 ){
                    $table[$key]['Name']='Interview 2';
                }else if($val->interviewtype == 3 ){
                    $table[$key]['Name']='Interview 3';
                }
    
            $category = explode(',',$val->categoryscores); 
            foreach($category as $ck => $cv) {
                
                $explodedata=explode('-',$cv); 
                $testdetail= $DB->get_record_sql(" select id,name from {question_categories} where id=$explodedata[0]");
                $table[$key][$testdetail->name]=$explodedata[1] * 10 .' % ';
                
            
    
    
            }
            $table[$key]['Interviewscore']= round($val->interviewscore,2).' % ';
            $table[$key]['Remark']= $val->remark;
    
    
        }
        $filepath = "";
        $context = context_user::instance($userid, MUST_EXIST);
        $fs = get_file_storage();
		if ($files = $fs->get_area_files($context->id, 'local_custompage', 'resume',false, 'sortorder', false)) 
		{
           
            foreach ($files as $file) 
            { 
                $filepath = moodle_url::make_pluginfile_url($context->id, 'local_custompage', 'resume', $file->get_itemid(), $file->get_filepath(), $file->get_filename());
            }
            $filepath = $filepath->__toString();
		}
        
        // echo'<pre>'; print_r($table);exit;
        
    // echo'<pre>'; print_r($table);exit;

   echo '<table style="width:100%" class="table table-striped">
    <tr>';
    foreach($table as $key => $val) {
        foreach($val as $k => $v) {
        echo '<th>'.$k .'</th>';   
        } break; 
    }
     echo '</tr>';
     
     foreach($table as $key => $val) {
        echo ' <tr>';
         foreach($val as $k => $v) {
            echo '<th>'.$v .'</th>';   
         }  
         echo '</tr>';
     }
    
      echo '</table>';
      if($filepath != ""){
	    echo "<br/><br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a target='_blank' href='$filepath'><button class='btn btn-primary'>View Resume</button></a><br/><br/>";
      }

  $mform->display();
}

   
function group_by($key, $data) {
    $result = array();

    foreach($data as $val) {
        if(array_key_exists($key, $val)){
            $result[$val[$key]][] = $val;
        }else{
            $result[""][] = $val;
        }
    }

    return $result;
}

echo $OUTPUT->footer();
