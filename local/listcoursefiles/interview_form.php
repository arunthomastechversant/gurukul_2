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
require_once('interview.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

require_login();
?>
<script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
<?php
$interviewuser  = optional_param('userid', '', PARAM_INT);
$userid=$interviewuser;

$PAGE->set_pagelayout('admin');
$PAGE->set_title("Interview Form");
$PAGE->set_url($CFG->wwwroot.'/local/listcoursefiles/interviewlist.php');
$coursenode = $PAGE->navbar->add('Interviewlist', new moodle_url($CFG->wwwroot.'/local/listcoursefiles/interviewlist.php'));
$PAGE->set_context(context_system::instance());
echo $OUTPUT->header();
$mform = new interview_form();
//echo '<pre>';print_r($mform);die;
if ($mform->is_cancelled()) {
	$return = $CFG->wwwroot.'/local/listcoursefiles/interviewlist.php';
    redirect($return);

} else if ($data = $mform->get_data()) {
	
	//    echo '<pre>';print_R($data);exit;
	
	foreach($data->category as $dck => $dcv){
		if(!empty($dcv)){
			$marks[] = $dcv;
			
		}
		$catscore[] = $dck.'-'.$dcv;
	}
	$average = array_sum($marks) / count($marks);
	// print_r($average);exit;
	$parsedatatoupdate = array();
	$average=$average*10;
	$parsedatatoupdate['userid'] = $data->userid;
	$parsedatatoupdate['interviewtype'] = $data->interviewtypeid;
	// $parsedatatoupdate['interviewscore'] = $data->average;
	$parsedatatoupdate['interviewscore'] = $average;
	$parsedatatoupdate['remark'] = $data->remark;
	$award=$data->award;
	$parsedatatoupdate['award'] =$award['award'];


	$parsedatatoupdate['categoryscore'] =implode(',',$catscore);
	// echo '<pre>';print_R($parsedatatoupdate);die;
	$checkinterviwexists = $DB->get_record('interview',['userid' => $data->userid,'interviewtype' => $data->interviewtype]);

	

    $record1 = new stdClass();
    $record1->id = $data->interviewid;
	$record1->interviewscore = $average;
	$record1->categoryscores = implode(',',$catscore);
	if($data->award['award']){
		$record1->interviewstatus = $data->award['award'];
	}else{
		$catsql = $DB->get_record_sql("select userid,category,COALESCE(round((sum(interviewscore)+ max(testscore))/count(id)),0) as average from mdl_interview where userid = ".$data->userid." group by userid,category order by id desc");
		// print_r($catsql);
		// print_r($data->average);exit;
		// $interviewaverage=($data->average+$catsql->average);	
		// $interviewaverage=$interviewaverage/2;
		$interviewaverage=$data->average;
		if($data->interviewtypeid == 0){
			if($interviewaverage >=70){
				$record1->interviewstatus ='Moved To HR';
			}else{
				$record1->interviewstatus ='Failed';
			}
		}else{
			
			if($interviewaverage >=70){
				$record1->interviewstatus ='Moved To HR';
			}else if($interviewaverage < 50){
				$record1->interviewstatus ='Failed';
			}else if($interviewaverage <70 && $interviewaverage >= 50 ){
				$record1->interviewstatus ='RSL Candidate';
				
			}
		}
//  print_r($interviewaverage);exit;

		
		
	}
	$record1->remark =  $data->remark;

	// print_r($record1);exit;
	$DB->update_record('interview', $record1);


	
	// $userstatus = $average >= 70 ? 'Moved to HR round' : (($average >= 50 && $average <= 70) ? 'Assign to RSL course' : 'Failed to clear interview');
	$user_interviewstatus = $DB->get_record('interview',(['id' => $data->interviewid]));


	if($data->award['award']){
		$awarddata=$data->award['award'];
		if($data->award['award']== 'Moved To HR'){
			$awarddata='Waiting For HR Interview';
		}else if($data->award['award']== 'Failed'){
			$awarddata='Failed';
		}else if($data->award['award']== 'Assign To Second Interview'){
			$awarddata='Waiting For Second Interview';
		}else if($data->award['award']== 'Assign To Third Interview'){
			$awarddata='Waiting For Third Interview';
		}
	}else{
		if($record1->interviewstatus == 'Moved To HR'){
			$awarddata='Waiting For HR Interview';
		}else{
			$awarddata=$record1->interviewstatus;
		}
		
	}

	 
	$record2 = new stdClass();
	$record2->userid = $data->userid;
	$record2->recruitment_id =  $user_interviewstatus->driveid;	
	$record2->userstatus =  $awarddata;	
	$record2->timestamp =  time();	
	// print_R($record2);die;
	$inserted = $DB->insert_record('userstatus', $record2);

	if($inserted){
		redirect($CFG->wwwroot.'/local/listcoursefiles/interviewlist.php','Interview score updated');
	}
	
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
	$table[$key][$testdetail->name]=round($cv,2).' % ';

}
$table[$key]['Interviewscore']= round($gradedetail->quizper,2).' % ';
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
			if(isset($val->categoryscores)){
					$category = explode(',',$val->categoryscores); 
					foreach($category as $ck => $cv) {
					
						$explodedata=explode('-',$cv); 
						if($explodedata[0]){
						$testdetail= $DB->get_record_sql(" select id,name from {question_categories} where id=$explodedata[0]");
						$table[$key][$testdetail->name]=$explodedata[1] * 10 .' % ';
						}
					
					}
				}
            
        }
        $table[$key]['Score']= round($val->interviewscore,2).' % ';
        $table[$key]['Remark']= $val->remark;


    }
    
	// echo'<pre>'; print_r($table);exit;
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


?>
<script>
	
	$(document).ready(function(){	
		$( "#id_intro" ).click(function() {
			var user =  $('#id').val();
			var interscore =  $('#interscore').val();
			var interviewid =  $('#interviewid').val();
			var edit = 0;
			var array = 0;
			var count = 0;
			var oldaverage = 0;
			var average = 0;
			var interviewaverage = 0;
			if(interscore){
				edit = interviewid;
			}
			// alert(user);
			$.ajax({
			  url: 'ajax.php',
			  type: 'post',
			  data:{user:user,edit:edit,},
			  success: function(data) {
				//   alert("araj");
				// alert(data);
				  var obj =  $.parseJSON(data);
				 
				  Object.keys(obj).forEach(function(key){
					// alert("1234");
					// alert(key);
					  if(key != 'average'){
							var value = obj[key];
							var textmarks =  $('#'+value+'').val();
							// alert(textmarks);
							if(parseFloat(textmarks) >= 0){
								array += parseFloat(textmarks);
								count += 1;
							}
						}else{
							oldaverage = obj[key];
						}
						
					});
					// alert('oldaverage');
					// alert(array);
					// alert(count);
					 if(array != 0 || count != 0){
						
						average = (array / count) * 10;
						interviewaverage = average;
						var interviwaveragehtml =  $('#id_error_average').val(interviewaverage);
						// alert(oldaverage+'---'+average);
						if(oldaverage != 0){
							// alert('oldaverage');
							// alert(oldaverage);
							average = ((parseFloat(average) + parseFloat(oldaverage)) / 2);
						}
					 }
					
					// alert(oldaverage+'---'+average);
					
					var data = Math.round((average + Number.EPSILON) * 100) / 100
					var averagehtml =  $('#id_average').val(data);
					
					//~ if (average >= 70)
						//~ $('#id_awardingType_award_').find(':radio[name=awarding Type[award]][value="2"]').prop('checked', true);
					  //~ else if(average >= 50 && average <= 70)
						//~ $('#id_awardingType_award_').find(':radio[name=awarding Type[award]][value="1"]').prop('checked', true);
					  //~ else 
						//~ $('#id_awardingType_award_').find(':radio[name=awarding Type[award]][value="0"]').prop('checked', true);

					//~ }
			  }
			});
		});

	$("text, .form-control").on( "keyup keydown click focus", function()
	    {
			var t1 = '';
			var id = $(this).attr('id');
			var user2 = id.match(/\d+/);
			//alert(user2);
			
			$.ajax({
							url: 'ajax2.php',
							type: 'post',
							data:{user2:user2,},
							success: function(data) 
							{
							
							
							JSON.parse(data, function (key, value) 
									{
											if (key == "name") 
											{
												t1  = value;
											} 
										
									});
																
								

								$("#fitem_id_help").text(t1+" score is out of  10")

							}
					})
		})

	
	});
</script>
<?php
echo $OUTPUT->footer();

?>

<style>
table {
  width: 100%;
  display: inline-table !important;
}
</style>






