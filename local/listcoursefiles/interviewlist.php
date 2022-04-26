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
require_once('locallib.php');
global $DB, $USER, $COURSE, $PAGE, $CFG,$OUTPUT;
$actionid = optional_param('actionid','', PARAM_INT);


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


$title = get_string('pluginname', 'local_listcoursefiles');
$url = new moodle_url('/local/listcoursefiles/index.php',
        array('courseid' => $courseid));


$PAGE->set_title('Interview List');
$PAGE->set_heading('Interview List');

$PAGE->navbar->add('Interview List', new moodle_url('/local/listcoursefiles/drivelist.php'));
$PAGE->navbar->add("Interview List", new moodle_url('/local/listcoursefiles/index.php?courseid='.$courseid));

// userid
$r_user = $DB->get_records_sql("SELECT i.*,u.username,u.firstname,u.lastname ,u.email,u.deleted,u.suspended,rd.name FROM {interview} i  JOIN {rsl_recruitment_drive} rd ON rd.id=i.driveid JOIN {user} u ON u.id = i.userid where i.interviewerid=$USER->id");

// $r_user = $DB->get_records_sql("SELECT i.*,u.username,u.firstname,u.lastname ,u.email,u.deleted,u.suspended,rd.name FROM {interview} i  JOIN {rsl_recruitment_drive} rd ON rd.id=i.driveid JOIN {user} u ON u.id = i.userid where i.interviewerid=328");


$tpldata = new stdClass();




$tpldata->url = $url;
$tpldata->sesskey = sesskey();
$tpldata->files = array();
$tpldata->files_exist = count($r_user) > 0;
$tpldata->change_license_allowed = $changelicenseallowed;
$tpldata->download_allowed = $downloadallowed;
// $tpldata->license_select_html = html_writer::select($licenses, 'license');
// old


if($bulkactionid == 1 ){
    foreach ($chosenfiles as $key => $val) {
        $driveid=$DB->get_record_sql("SELECT * FROM {rsl_recruitment_drive}  where id=$courseid ");
        $gradedetail = $DB->get_record_sql(" select round(((10/b.sumgrades) * a.sumgrades)* 10) quizper,a.timemodified as timemodified from mdl_quiz_attempts a join mdl_quiz b on a.quiz = b.id where b.id = $driveid->test and userid = $key order by a.timemodified desc limit 1");
        $questioncategory=$DB->get_record_sql("SELECT * FROM {test_questioncategory}  where test_id=$driveid->test  ");
        $userstatus=$DB->get_record_sql("SELECT * FROM {user}  where id=$key ");

        $record1 = new stdClass();
        $record1->userid = $key;
        $record1->interviewerid =  $interviewerid;		
        $record1->testscore = $gradedetail->quizper;	
        $record1->interviewstatus = 'pending';

        $record1->remark = ''; 
        $record1->interviewscore = ''; 

        $record1->category =  $questioncategory->questions;        
        $record1->categoryscores = ''; 
        $record1->interviewtype = 1; 
        $record1->driveid = $courseid; 
        $record1->upcomingstatus = '';
        $record1->activestatus = $userstatus->suspended;  

        $lastinsertid = $DB->insert_record('interview', $record1);

        // print_r($interviewerid );  
        // print_r($key );exit;
        // print_r($key );  
        // exit;    
    }

 
}
if($bulkactionid == 2 ){
    
}

    $actionarray = array(
        "" => " ---- Choose Action ---",
        "1" => "Assign To Intereview",
        "2" => "Assign To HR",
    );
    $actions=array();


    $bulk.='<select name="bulkaction" id="bulkaction">';
    foreach ($actionarray as $key =>$val ) {
        $bulk.=' <option value="'.$key.'">'.$val.'</option>';
    }
    $bulk.='</select>';



// print_r( $inter );exit;
$tpldata->bulk = $bulk;

// echo '<pre>';print_r($r_user );exit;
foreach ($r_user as $userdata) {
    $teststatus='';
    // echo '<pre>'; print_r($userdata);exit;

//  ----------------
    // $r_user = $DB->get_records_sql("SELECT qa.*,rrd.name FROM {rsl_recruitment_drive} rrd   JOIN {quiz_attempts} qa ON qa.id = rrd.test where qa.userid=$userdata->userid ");
    $driveid=$DB->get_record_sql("SELECT * FROM {rsl_recruitment_drive}  where id=$userdata->driveid ");
    // if($driveid)
    $gradedetail = $DB->get_record_sql(" select round(((10/b.sumgrades) * a.sumgrades)* 10) quizper,a.timemodified as timemodified from mdl_quiz_attempts a join mdl_quiz b on a.quiz = b.id where b.id = $driveid->test and userid = $userdata->userid order by a.timemodified desc limit 1");
    // if($gradedetail){
        
    //     $timeoftest=date('d/m/Y H:i:s', $gradedetail->timemodified);
    // }else{
    //     $timeoftest='Not Yet Started';
    // }
    // if($gradedetail->quizper){

    //     if($gradedetail->quizper >= 70){
    //         $teststatus='Pass';
    //     }else if($gradedetail->quizper < 70 && $gradedetail->quizper > 50){
    //         $teststatus='RSl Candidate';
    //     }else{
    //         $teststatus='Fail';
    //     }
    //     $grade= $gradedetail->quizper.' %';
    // }else{
    //     $grade= "Not Yet Started";
    //     $teststatus='Not Yet Started';
    // }
    // if($userdata->suspended == 1 ){
    //     $userstatus='Disabled';
    // }elseif($userdata->suspended == 0){
    //     $userstatus='Enabled';
    // }
    // $drivestatus='Assigned For Interview';
    // $drivedata=$DB->get_record_sql("SELECT * FROM {interview}  where userid=$userdata->userid ");
    // if($drivedata->interviewtype == 1  ){
    //     if($drivedata->interviewstats == 'Pending'){
    //         $drivestatus='Assigned For Interview'; 
    //     }
    //     if($drivedata->interviewstats == 'Pending'){
    //         $drivestatus='Assigned For Interview'; 
    //     }

    // }
    // echo $userstatus;exit;
    $tplfile = new stdClass();

$takeinterview="<a href='interview_form.php?interviewid=$userdata->id&userid=$userdata->userid'>Take Interview</a>";
$remark='';
if($userdata->remark){
    $remark=$userdata->remark;
}else{
    $remark=' - ';
}
    $tplfile->file_id=$userdata->userid;
    $tplfile->username=$userdata->username;
    $tplfile->testscore=$gradedetail->quizper;
    $tplfile->interviewstatus=$userdata->interviewstatus; 
    $tplfile->drive=$driveid->name;
    $tplfile->takeinterview=$takeinterview;
    $tplfile->remark=$remark;

    $tpldata->files[] = $tplfile;
}
//   echo '<pre>';print_r($tpldata);exit;
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_listcoursefiles/interview', $tpldata);
echo '	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
';
?>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {

    var dtable = $("#dashboard_report").DataTable({
        initComplete: function () {
        var table = $('#dashboard_report').DataTable();
        this.api().columns().every( function () {
            var column = this;

            if (column.index() == 3) {
                $('<span style="margin-left: 10px; margin-right: 10px;"></span>   ').appendTo( '#dashboard_report_length' );
                    var select = $('</br><select id="class_select" class="custom-select"></select>')
                    select.append( '<option value="" >All</option>' )
                    .appendTo('#dashboard_report_length' )
                    .on( 'change', function () {
                        
                        var val = $(this).val()
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );

                column.data().unique().sort().each( function ( d, j ) {
    
                        select.append( '<option value="'+d+'" >'+d+'</option>' )
                
                } );
                $('<span style="margin-left: 10px; margin-right: 10px;"></span>   ').appendTo( '#dashboard_report_length' );
           }
           if (column.index() == 4) {
                // $('<span style="margin-left: 10px; margin-right: 10px;"></span>   ').appendTo( '#dashboard_report_length' );
                    var select = $('<select id="class_select1" class="custom-select"></select>')
                    select.append( '<option value="" >All</option>' )
                    .appendTo('#dashboard_report_length' )
                    .on( 'change', function () {
                        
                        var val = $(this).val()
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );

                column.data().unique().sort().each( function ( d, j ) {
    
                        select.append( '<option value="'+d+'" >'+d+'</option>' )
                
                } );
                // $('<span style="margin-left: 10px; margin-right: 10px;"></span>   ').appendTo( '#dashboard_report_length' );
           }


        } );
        
    }

    });
    $('#search-datatable').keyup(function(){
      dtable.search($(this).val()).draw() ;
    });
});
</script>
<?php
echo $OUTPUT->footer();
