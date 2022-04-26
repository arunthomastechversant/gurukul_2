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
 * List all files in a course.
 *
 * @package    local_listcoursefiles
 * @copyright  2017 Martin Gauk (@innoCampus, TU Berlin)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
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
require_once('locallib.php');
global $DB, $USER, $COURSE, $PAGE, $CFG,$OUTPUT;
$courseid = required_param('courseid', PARAM_INT);
$actionid = optional_param('actionid','', PARAM_INT);


$PAGE->set_context(context_system::instance());
// echo $_POST['interviewer'];exit;
// print_r($recruiter);exit;
$page = optional_param('page', 0, PARAM_INT);
$limit = optional_param('limit', 1, PARAM_INT);
if ($page < 0) {
    $page = 0;
}
if ($limit < 1 || $limit > LOCAL_LISTCOURSEFILES_MAX_FILES) {
    $limit = LOCAL_LISTCOURSEFILES_MAX_FILES;
}
$component = optional_param('component', 'all_wo_submissions', PARAM_ALPHAEXT);
$filetype = optional_param('filetype', 'document', PARAM_ALPHAEXT);
$action = optional_param('action', '', PARAM_ALPHAEXT);
$chosenfiles = optional_param_array('file', array(), PARAM_INT);
$recruiterid = optional_param('recruiter','', PARAM_INT);
$bulkactionid = optional_param('bulkaction','', PARAM_INT);
$interviewerid = optional_param('interviewer','', PARAM_INT);
// print_r($interviewer);
// print_r($chosenfiles);exit;

// $context = context_course::instance($courseid);
$title = get_string('pluginname', 'local_listcoursefiles');
$url = new moodle_url('/local/listcoursefiles/index.php',
        array('courseid' => $courseid));
$datas = $DB->get_record_sql("SELECT * FROM {rsl_recruitment_drive} where id=$courseid");

$PAGE->set_title($datas->name);
$PAGE->set_heading($datas->name);

$PAGE->navbar->add('RSL Recruitment Drive List', new moodle_url('/local/listcoursefiles/drivelist.php'));
$PAGE->navbar->add($datas->name, new moodle_url('/local/listcoursefiles/index.php?courseid='.$courseid));

// old

// require_login($courseid);
// require_capability('local/listcoursefiles:view', $context);
// $changelicenseallowed = has_capability('local/listcoursefiles:change_license', $context);
// $downloadallowed = has_capability('local/listcoursefiles:download', $context);


// $files = new local_listcoursefiles\course_files($courseid, $context, $component, $filetype);

// if ($action === 'change_license' && $changelicenseallowed) {
//     require_sesskey();
//     $license = required_param('license', PARAM_ALPHAEXT);
//     try {
//         $files->set_files_license($chosenfiles, $license);
//     } catch (moodle_exception $e) {
//         \core\notification::add($e->getMessage(), \core\output\notification::NOTIFY_ERROR);
//     }
// } else if ($action === 'download' && $downloadallowed) {
//     require_sesskey();
//     try {
//         $files->download_files($chosenfiles);
//     } catch (moodle_exception $e) {
//         \core\notification::add($e->getMessage(), \core\output\notification::NOTIFY_ERROR);
//     }
// }



$r_user = $DB->get_records_sql("SELECT rud.userid,u.username,u.firstname,u.lastname ,u.email,u.deleted,u.suspended,rud.rsl_due   FROM {rsl_user_detail} rud    JOIN {user} u ON u.id = rud.userid where u.deleted=0 AND rud.recruitment_id=$courseid");
	// print_r($r_user);exit;
// $filelist = $files->get_file_list($page * $limit, $limit);
// $licenses = local_listcoursefiles\course_files::get_available_licenses();

$tpldata = new stdClass();
// $tpldata->course_selection_html = local_listcoursefiles_get_course_selection($url, $courseid);
// $tpldata->component_selection_html = local_listcoursefiles_get_component_selection($url, $files->get_components(), $component);
// $tpldata->file_type_selection_html = local_listcoursefiles_get_file_type_selection($url, $filetype);
//  $tpldata->paging_bar_html = $OUTPUT->paging_bar(count($r_user), $page , $limit, $url, 'page');



$tpldata->url = $url;
$tpldata->sesskey = sesskey();
$tpldata->files = array();
$tpldata->files_exist = count($r_user) > 0;
// $tpldata->change_license_allowed = $changelicenseallowed;
// $tpldata->download_allowed = $downloadallowed;
// $tpldata->license_select_html = html_writer::select($licenses, 'license');
// old

$driveid=$DB->get_record_sql("SELECT * FROM {rsl_recruitment_drive}  where id=$courseid ");
$urlto = $CFG->wwwroot.'/local/listcoursefiles/index.php?courseid='.$courseid ;
if($bulkactionid == 1 ){
    
    foreach ($chosenfiles as $key => $val) {
        
        $gradedetail = $DB->get_record_sql(" select round(((10/b.sumgrades) * a.sumgrades)* 10) quizper,a.timemodified as timemodified from mdl_quiz_attempts a join mdl_quiz b on a.quiz = b.id where b.id = $driveid->test and userid = $key order by a.timemodified desc limit 1");
        $questioncategory=$DB->get_record_sql("SELECT * FROM {test_questioncategory}  where test_id=$driveid->test  ");
        $userstatus=$DB->get_record_sql("SELECT * FROM {user}  where id=$key ");

        $record1 = new stdClass();
        $record1->userid = $key;
        $record1->interviewerid =  $interviewerid;		
        $record1->testscore = $gradedetail->quizper;	
       
        $record1->remark = ''; 
        $record1->interviewscore = ''; 
        $record1->category =  $questioncategory->questions;        
        $record1->categoryscores = ''; 

        if($datas->interview == 0){
            $record1->interviewtype = 1;
            $record1->interviewstatus = 'Pending';
        }           
        $recordstatus=$DB->get_records_sql("SELECT * FROM {interview}  where userid=$key AND driveid=$courseid");
        $count=count($recordstatus);
        if($datas->interview == 1){

            if($count == 0){
                $record1->interviewtype = 1; 
                $record1->interviewstatus = 'Pending for 1st Round';
            }else if($count == 1){
                $record1->interviewtype = 2; 
                $record1->interviewstatus = 'Pending for 2nd Round';
            }else if($count == 2){
                $record1->interviewtype = 3; 
                $record1->interviewstatus = 'Pending for 3rd Round';
            }

        }
        
     
        $record1->driveid = $courseid; 
        $record1->upcomingstatus = '';
        $record1->activestatus = $userstatus->suspended;  

        $lastinsertid = $DB->insert_record('interview', $record1);
        // print_r($record1);
        // print_r($interviewerid );  
        // print_r($key );exit;
        // print_r($key );  
        // exit;    
       

        $record2 = new stdClass();
        $record2->userid = $key;
        $record2->recruitment_id =  $courseid;
        
        if($datas->interview == 0){
            $record2->userstatus =  'Assigned For RSL Interview';	
        }
        if($datas->interview == 1){

            if($count == 0){
                
                $record2->userstatus = 'Assigned for 1st Round';
            }else if($count == 1){
                
                $record2->userstatus  = 'Assigned for 2nd Round';
            }else if($count == 2){
                
                $record2->userstatus  = 'Assigned for 3rd Round';
            }

        }

        $record2->timestamp =  time();	
        $DB->insert_record('userstatus', $record2);	
    }

    
    redirect($urlto, 'Users Assigned to Interview Sucessfully ', 8);
}
if($bulkactionid == 2 ){
    foreach ($chosenfiles as $key => $val) {
        $driveid=$DB->get_record_sql("SELECT * FROM {rsl_recruitment_drive}  where id=$courseid ");
        $gradedetail = $DB->get_record_sql(" select round(((10/b.sumgrades) * a.sumgrades)* 10) quizper,a.timemodified as timemodified from mdl_quiz_attempts a join mdl_quiz b on a.quiz = b.id where b.id = $driveid->test and userid = $key order by a.timemodified desc limit 1");
        $questioncategory=$DB->get_record_sql("SELECT * FROM {test_questioncategory}  where test_id=$driveid->test  ");
        $userstatus=$DB->get_record_sql("SELECT * FROM {user}  where id=$key ");

        $record1 = new stdClass();
        $record1->userid = $key;
        $record1->hrid =  $recruiterid;	
        $record1->testscore = $gradedetail->quizper;
        $record1->remark = ''; 
        $record1->driveid = $courseid; 	
        $record1->category_score = '';
        $record1->total_score = '';
        $record1->status = 'pending';
        $record1->timestamp = time();

// print_r( $record1);exit;

        $lastinsertid = $DB->insert_record('hrinterview', $record1);

        $record2 = new stdClass();
        $record2->userid = $key;
        $record2->recruitment_id =  $courseid;	
        $record2->userstatus =  'Assigned For HR Interview';	
        $record2->timestamp =  time();	
        $DB->insert_record('userstatus', $record2);	
        // print_r("test");exit;

        // print_r($interviewerid );  
        // print_r($key );exit;
        // print_r($key );  
        // exit;    
    }
    redirect($urlto, 'Users Assigned to HR Interview Sucessfully ', 8);
}
if($bulkactionid == 3 ){
   
    // print_r($enrol);exit;
    $nodays=15;
    foreach ($chosenfiles as $key => $val) {
       
        $rslquest = $DB->get_record_sql("SELECT rrd.test,tq.questions  FROM {rsl_recruitment_drive} rrd  JOIN {test_questioncategory} tq ON tq.test_id = rrd.test where rrd.id=$courseid");
        // print_r($rslquest);exit;
		$pro=explode(',',$rslquest->questions);
		if($pro){
            $enrol = enrol_get_plugin('manual'); 
            $categoryarray=array();
           
			foreach($pro as $val) {
                $implode_data=explode('-',$val);
                if($implode_data[1] != 0){
                    $qcourse=$DB->get_record_sql("SELECT * FROM {question_category_mapping}  where question_categoryid=$implode_data[0] ");
                    $ccid=$qcourse->courseid;
                    $instances = $DB->get_records_sql("SELECT * FROM mdl_enrol WHERE courseid =$ccid AND status = 0 AND enrol = 'manual'");
                  
                    foreach($instances as $instance){
                      $plugin = enrol_get_plugin($instance->enrol);
                      $end_date = strtotime("+$nodays day", time());
                      $result= $plugin->enrol_user($instance, $key,5,time(),$end_date);

                      $updateuser = $DB->get_record_sql("SELECT * FROM {rsl_user_detail} WHERE userid =$key ");
                    //   print_r( $updateuser);exit;
                      $up = new stdClass();
                      $up->id  = $updateuser->id;
                      $up->due_date  = $end_date;
                    //  print_r($up);
                     $DB->execute("UPDATE {rsl_user_detail} SET rsl_due =  $end_date WHERE userid =$key");
                    


                    }
                }


                $record2 = new stdClass();
                $record2->userid = $key;
                $record2->recruitment_id =  $courseid;	
                $record2->userstatus =  'Assigned For RSL';	
                $record2->timestamp =  time();	
                $DB->insert_record('userstatus', $record2);	

			}
		}
        


    }  
    redirect($urlto, 'Users Assigned to RSL Sucessfully ', 8);  
}
if($bulkactionid == 4 ){
    
    foreach ($chosenfiles as $key => $val) {
        
        $gradedetail = $DB->get_record_sql(" select round(((10/b.sumgrades) * a.sumgrades)* 10) quizper,a.timemodified as timemodified from mdl_quiz_attempts a join mdl_quiz b on a.quiz = b.id where b.id = $driveid->test and userid = $key order by a.timemodified desc limit 1");
        $questioncategory=$DB->get_record_sql("SELECT * FROM {test_questioncategory}  where test_id=$driveid->test  ");
        $userstatus=$DB->get_record_sql("SELECT * FROM {user}  where id=$key ");

        $record1 = new stdClass();
        $record1->userid = $key;
        $record1->interviewerid =  $interviewerid;		
        $record1->testscore = $gradedetail->quizper;	
       
        $record1->remark = ''; 
        $record1->interviewscore = ''; 
        $record1->category =  $questioncategory->questions;        
        $record1->categoryscores = ''; 

        if($datas->interview == 0){
            $record1->interviewtype = 1;
            $record1->interviewstatus = 'Pending For RSL Interview';
        }           
        $recordstatus=$DB->get_records_sql("SELECT * FROM {interview}  where userid=$key AND driveid=$courseid");
        $count=count($recordstatus);
        if($datas->interview == 1){

            if($count == 0){
                $record1->interviewtype = 1; 
                $record1->interviewstatus = 'Pending For RSL Interview';
            }else if($count == 1){
                $record1->interviewtype = 2; 
                $record1->interviewstatus = 'Pending For RSL Interview';
            }else if($count == 2){
                $record1->interviewtype = 3; 
                $record1->interviewstatus = 'Pending For RSL Interview';
            }

        }
        
     
        $record1->driveid = $courseid; 
        $record1->upcomingstatus = '';
        $record1->activestatus = $userstatus->suspended;  
        $record1->isrsl=1;  

        $lastinsertid = $DB->insert_record('interview', $record1);
        // print_r($record1);
        // print_r($interviewerid );  
        // print_r($key );exit;
        // print_r($key );  
        // exit;    
       

        $record2 = new stdClass();
        $record2->userid = $key;
        $record2->recruitment_id =  $courseid;
        
        if($datas->interview == 0){
            $record2->userstatus =  'Assigned For RSL Interview';	
        }
        if($datas->interview == 1){

            if($count == 0){
                
                $record2->userstatus = 'Assigned for RSL Interview';
            }else if($count == 1){
                
                $record2->userstatus  = 'Assigned for RSL Interview';
            }else if($count == 2){
                
                $record2->userstatus  = 'Assigned for  RSL Interview';
            }

        }

        $record2->timestamp =  time();	
        $DB->insert_record('userstatus', $record2);	
    }

    
    redirect($urlto, 'Users Assigned to Interview Sucessfully ', 8);
}       
if($driveid->interview == 1){
    $actionarray = array(
        "" => " ---- Choose Action ---",
        "1" => "Assign To Intereview",
        "2" => "Assign To HR",
        "3" => "Assign To RSL",
        "4" => "Assign To RSL Intereview"
    );
}else{
    $actionarray = array(
        "" => " ---- Choose Action ---",
        "4" => "Assign To RSL Intereview",
        "2" => "Assign To HR",
        "3" => "Assign To RSL",
    );
}


    $actions=array();
    foreach ($actionarray as $key =>$val ) {
            $actions[$key] = $val;
        
    }

    // echo $OUTPUT->single_select($url, 'actionid', $actions, $currentcourseid, null, 'actionselector');exit;
    $tpldata->action_html = $OUTPUT->single_select($url, 'actionid', $actions, $courseid, null, 'actionselector');

    $bulk.='<select name="bulkaction" style="width: 222px;margin-top: 20px;" class="custom-select" id="bulkaction">';
    foreach ($actionarray as $key =>$val ) {
        $bulk.=' <option value="'.$key.'">'.$val.'</option>';
    }
    $bulk.='</select>';

    $inter = $DB->get_records_sql("SELECT u.id,u.username,u.email,u.firstname,u.lastname  FROM {user} u JOIN {role_assignments} ra ON ra.userid = u.id where roleid=3");
    $interviewer.='<select name="interviewer" class="custom-select" style="width: 222px;margin-top: 20px;" id="interviewer"> <option value=" "> ---- Select Interviewer  ----</option>';
    foreach ($inter as $ikey =>$ival ) {
        $interviewer.=' <option value="'.$ival->id.'">'.$ival->username.'</option>';
    }
    $interviewer.='</select>';

    $recruit = $DB->get_records_sql("SELECT u.id,u.username,u.email,u.firstname,u.lastname  FROM {user} u JOIN {role_assignments} ra ON ra.userid = u.id where roleid=16");
    $recruiter.='<select name="recruiter" class="custom-select" style="width: 222px;margin-top: 20px;" id="recruiter"><option value=" "> ---- Select RHR  ----</option>';
    foreach ($recruit as $ikey =>$ival ) {
        $recruiter.=' <option value="'.$ival->id.'">'.$ival->username.'</option>';
    }
    $recruiter.='</select>';


// print_r( $inter );exit;
$tpldata->bulk = $bulk;
$tpldata->inter = $interviewer;
$tpldata->recruiter = $recruiter;
// echo '<pre>';print_r($r_user );exit;
foreach ($r_user as $userdata) {
    $teststatus='';$grade='';
    // print_r($userdata);exit;


    // $r_user = $DB->get_records_sql("SELECT qa.*,rrd.name FROM {rsl_recruitment_drive} rrd   JOIN {quiz_attempts} qa ON qa.id = rrd.test where qa.userid=$userdata->userid ");
    $driveid=$DB->get_record_sql("SELECT * FROM {rsl_recruitment_drive}  where id=$courseid ");
    if($driveid)
    $gradedetail = $DB->get_record_sql(" select round(((10/b.sumgrades) * a.sumgrades)* 10) quizper,a.timemodified as timemodified from mdl_quiz_attempts a join mdl_quiz b on a.quiz = b.id where b.id = $driveid->test and userid = $userdata->userid order by a.timemodified desc limit 1");
    if($gradedetail){
        if($gradedetail->timemodified){
            $timeoftest=date('d/m/Y H:i:s', $gradedetail->timemodified);
        }else{
            $timeoftest='-';
        }
        
        if($driveid->interview == 1){
            if($gradedetail->quizper){

                if($gradedetail->quizper >= 50){
                    $teststatus='Pass';
                }else{
                    $teststatus='Fail';
                }
                $grade= $gradedetail->quizper.' %';
            }else{
                $grade= "-";
                $teststatus='';
            }
        }else{
            if($gradedetail->quizper){

                if($gradedetail->quizper < 50){
                    $teststatus='Fail';
                }else{
                    $teststatus='Pass';
                }
                $grade= $gradedetail->quizper.' %';
            }else{
                $grade= "-";
                $teststatus='Not Yet Started';
            }
        }

    }else{
        $timeoftest='Not Yet Started';
    }

    if($userdata->suspended == 1 ){
        $userstatus='Disabled';
    }elseif($userdata->suspended == 0){
        $userstatus='Enabled';
    }
    $drivestatus='Assigned For Interview';
    $drivedata=$DB->get_record_sql("SELECT * FROM {userstatus}  where userid=$userdata->userid ORDER BY id DESC ");
    if($timeoftest != 'Not Yet Started' &&  empty($teststatus)){
        $teststatus='Fail';
    }
    if($userdata->rsl_due){
        $rsl_due=date('d/m/Y H:i:s', $userdata->rsl_due);
    }else{
        $rsl_due='-';
    }

    $remarkvar='';
    $remarksdata = $DB->get_records_sql("SELECT id,remark,interviewtype,isrsl FROM {interview} WHERE userid = $userdata->userid");
    // echo'<pre>';print_r($remarksdata);
    if($remarksdata){
        $remarkarray=array();
        foreach ($remarksdata as $rkey => $rval) {
            if($rval->isrsl == 1){
                $remarks='RSL - '.$rval->remark;
            }else if($rval->interviewtype == 1 && $rval->remark){
                $remarks='Interview 1 - '.$rval->remark;
            }else if($rval->interviewtype == 2 && $rval->remark){
                $remarks='Interview 2 - '.$rval->remark;
            }else if($rval->interviewtype == 3 && $rval->remark){
                $remarks='Interview 3 - '.$rval->remark;
            }
    
            array_push($remarkarray,$remarks);
        }
        $remarkvar=implode(',', $remarkarray);
    }

    $tplfile = new stdClass();
 

    $tplfile->file_id=$userdata->userid;
    $tplfile->username=$userdata->username;
    $tplfile->timeoftest=$timeoftest;
    $tplfile->grade=$grade;
    $tplfile->teststatus=$teststatus;
    $tplfile->userstatus=$userstatus;
    $tplfile->drivestatus = $drivedata->userstatus;
    $tplfile->file_type=$userdata->email;
    $tplfile->rsl_due=$rsl_due;
    $tplfile->remarks=$remarkvar;


    $tpldata->files[] = $tplfile;
}
//   echo '<pre>';print_r($tpldata);exit;
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_listcoursefiles/view', $tpldata);
echo '	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
';
?>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    // $('#dashboard_report').DataTable();
    // oTable = $('#dashboard_report').dataTable();

    var table = $('#dashboard_report').DataTable();
    
} );
</script>
<?php
echo $OUTPUT->footer();
