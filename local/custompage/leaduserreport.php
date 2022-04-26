<script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/autofill/2.3.5/css/autoFill.dataTables.min.css">

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">
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
 * For a given question type, list the number of
 *
 * @package    report
 * @subpackage rslscore
 * @copyright  2008 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/questionlib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
global $DB,$USER,$COURSE,$CFG,$PAGE,$OUTPUT;

$PAGE->set_title("Lead User Report");
$PAGE->set_heading('Lead User Report'); 
$PAGE->navbar->add('Lead User Report', new moodle_url($CFG->wwwroot.'/local/custompage/leaduserreport.php'));

$course_id = optional_param('course_id', '', PARAM_RAW);
$batch_id = optional_param('batch_id', '', PARAM_RAW);

$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 10, PARAM_INT);
$PAGE->set_url('/local/custompage/btuserreport.php', array('course_id' => $course_id));
$data='';
require_login();
$context = context_system::instance();
$companyid = iomad::get_my_companyid($context);

//admin_externalpage_setup('reportrslscore', '', null, '', array('pagelayout'=>'report'));
echo $OUTPUT->header();
// $mform = new btuserreport_form();
// $mform->display();
// $drives = $DB->get_records_sql("SELECT id,name FROM  {bt_recruitment_drive} ");
$courses = $DB->get_records_sql("SELECT c.* from {course} as c JOIN {company_course} cc where c.id = cc.courseid AND cc.companyid = $companyid");
echo '<label>Select a course : </label>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
echo '<select type="" name="id_course_select" id="id_course_select" class="custom-select"><option value="" selected disabled>---- Choose a Courses ----</option>';
foreach($courses as $key => $course){
    echo '<option value =' .$course->id.'>' .$course->fullname.'</option>';
}  
echo '</select></br></br>';
echo '<label>Select a batch : </label>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
echo '<select type="select" name="id_batch_select" id="id_batch_select" class="custom-select"><option value="" disabled>---- Choose a Batch ----</option>';
// if($course_id != ""){
//     $batches = $DB->get_records_sql("SELECT * from {lead_batches} lb join {lead_batch_assigned_courses} lbc on lb.id = lbc.batchid where lbc.courseid = $course_id");
//     foreach($batches as $keys => $batch){
//         echo '<option value =' .$batch->id.'>' .$batch->name.'</option>';
//     }
// }  
echo '</select></br></br>';
if($batch_id != ""){
    // echo $block_content = "&nbsp &nbsp&nbsp &nbsp<button type='button' class='btn btn-primary share_button' data-toggle='modal' data-target='#share_modal'>Share Details</button>&nbsp &nbsp</br></br>";  
    $qstn_ctry = array();
    $ctry_data = $DB->get_record_sql("SELECT questions FROM {lead_test} as lt join {test_questioncategory} as tq where lt.test_id = tq.test_id and lt.courseid = $course_id ")->questions;
    $ctry_ids = explode(',' , $ctry_data);
    foreach($ctry_ids as $key => $value){
        $data_set = explode('-',$value);
        if($data_set[1] > 0){
                $qst_ctry = $DB->get_record_sql("SELECT name FROM {question_categories} where id = '$data_set[0]' ")->name;
                array_push($qstn_ctry,$qst_ctry);
            }
        }
        // print_r($qstn_ctry);exit();
    $courses = $DB->get_records_sql("SELECT c.* from {course} as c JOIN {company_course} cc where c.id = cc.courseid AND cc.companyid = $companyid");
    $proctoring =$DB->get_record_sql("SELECT eproctoringrequired From {quizaccess_eproctoring} ep join {lead_test} lt on ep.quizid = lt.test_id and lt.courseid=$course_id")->eproctoringrequired;
    // $proctoring =$DB->get_record_sql("SELECT eproctoringrequired From {quizaccess_eproctoring} ep join {lead_test} lt where ep.quizid = tq.test_id and lt.courseid=$course_id")->eproctoringrequired;
    $userheading = $DB->get_records_sql("SELECT f.id,f.name,f.shortname FROM {user_info_field} f where f.categoryid = $companyid");
    // print_r($proctoring);exit();
    $data .= '<div class="card"><div id="tableContainer" class="card-body table-responsive">
	<table id="user_data" class="table table-striped table-inverse table-bordered table-hover no-footer" cellspacing="0" width="100%">
	<thead>
        <tr>
            <th class="header c3" scope="col">First Name</th>
            <th class="header c3" scope="col">Last Name</th>';
        // <th style="text-align:center"><input type="checkbox" id="mainselect" value="1"> All</th>';
            foreach($userheading as $key => $val){
                $data .= '<th style="text-align:center">' . $val->name .'</th>';
            }
            $data .='<th style="text-align:center">Obtained Grade/Total Grade </th>';
            foreach($qstn_ctry as $key => $value){
                $data .= '<th style="text-align:center">' . $value .'</th>';
            }
            if($proctoring == 1){
                $data .='<th class="header c2" scope="col">Background Noise</th>
                <th class="header c3" scope="col">Tab Change</th>
                <th class="header c4" scope="col">Window Change</th>
                <th class="header c5" scope="col">Mouth Open Count</th>
                <th class="header c5" scope="col">Mobile Phone Count </th>
                <th class="header c5" scope="col">More Person</th>
                <th class="header c5" scope="col">No Person</th>
                <th class="header c5" scope="col">Head Up</th>
                <th class="header c5" scope="col">Head Down</th>
                <th class="header c5" scope="col">Head Left</th>
                <th class="header c5" scope="col">Head Right</th>
                <th class="header c5" scope="col">Face Recognition </th>';
            }
            $data .= '<th style="text-align:center">Status</th>
		</tr>
	</thead>
	<tbody>';
    
 
        // $enrolled_users =$DB->get_records_sql("select ru.*,g.name AS groupname,u.email,rrd.interview,ru.userid AS userid,rrd.test As testid ,(select round(((10/b.sumgrades) * a.sumgrades)* 10) quizper from mdl_quiz_attempts a join mdl_quiz b on a.quiz = b.id where b.id = rrd.test and userid = ru.userid order by a.timemodified desc limit 1) as testper, case when rrd.interview = 1 then (select round(((10/b.sumgrades) * a.sumgrades)* 10) quizper from mdl_quiz_attempts a join mdl_quiz b on a.quiz = b.id where b.id = ru.interview_id order by a.timemodified desc limit 1) else 'No Interview' end interviewper from mdl_rsl_user_detail ru INNER JOIN mdl_user u ON u.id=ru.userid JOIN mdl_groups g ON g.id=ru.test_groupid JOIN mdl_rsl_recruitment_drive rrd ON rrd.id=ru.recruitment_id WHERE ru.recruitment_id =$course_id");

        // $enrolled_users = $DB->get_records_sql("select mqa.id as attemptid,u.firstname,u.lastname,u.email,bu.userid,mqa.sumgrades,qz.sumgrades as sumg,
        // (select round((10/b.sumgrades) * a.sumgrades) quizper from mdl_quiz_attempts a join mdl_quiz b 
        // on a.quiz = b.id where b.id = brd.test and userid = bu.userid order by a.timemodified desc limit 1)
        // as testper from mdl_bt_user_detail bu INNER JOIN mdl_user u ON u.id=bu.userid JOIN mdl_groups g 
        // ON g.id=bu.test_groupid JOIN mdl_bt_recruitment_drive brd ON brd.id=bu.recruitment_id join mdl_quiz_attempts mqa on mqa.userid = bu.userid join {quiz} as qz on qz.id = mqa.quiz
        // WHERE bu.recruitment_id =$course_id");
        
        // $datas = $DB->get_records_sql("SELECT lu.userid FROM {lead_user_detail} lu join {lead_batch_user_assignments} lba on lu.userid = lba.userid join {lead_test} lt on lt. where lba.batchid = $batch_id");
        $datas = $DB->get_records_sql("SELECT lu.userid FROM {lead_user_detail} lu join {lead_batch_user_assignments} lba on lu.userid = lba.userid where lba.batchid = $batch_id");
        // print_r($datas);exit();

        // print_r($enrolled_users);
        // $t_count = count($enrolled_users);
        //     $start = $page * $perpage;
        //     if ($start > $t_count) {
        //         $page = 0;
        //         $start = 0;
        //     }
        //     $i = 1;
        //     if($page != 0){
        //         $i = ($page * $perpage)+1;

        //     }
        
    //  print_r($datas);exit; 
        foreach($datas as $key => $value){
            $enrolled_users = $DB->get_record_sql("select mqa.id as attemptid,lu.userid,mcm.id as quizid,u.firstname,u.lastname,
            mqa.sumgrades,qz.sumgrades as sumg from mdl_lead_user_detail lu JOIN mdl_user u ON u.id=lu.userid join mdl_quiz_attempts mqa on
            mqa.userid = lu.userid join {quiz} as qz on qz.id = mqa.quiz join mdl_course_modules as mcm on mcm.instance = qz.id
            WHERE u.id = $value->userid");
            // $str_status = "Not Attemted;
            // if($value->testper >= 7){
            //     $str_status = "Passed";
            // }elseif($value->testper > 0 && $value->testper < 7){
            //     $str_status = "Failed";
            // }else{
            //     $str_status = "Not Attemted";
            // }

            if($enrolled_users){
                // print_r($enrolled_users);exit();
                $data .= '<tr>';
                $data .= '<td style="text-align:center">'.$enrolled_users->firstname.' </td>';
                $data .= '<td style="text-align:center">'.$enrolled_users->lastname.' </td>';
                // $data .= '<td style="text-align:center"><input type="checkbox" class="userid" id= ' .$value->id. '></td>';
                    foreach($userheading as $key1 => $val){
                        $userdata = $DB->get_record_sql("SELECT d.data,f.datatype FROM {user_info_data} d join {user_info_field} f on f.id = d.fieldid where d.fieldid = $val->id and d.userid = $value->userid");
                        // $userdata = $DB->get_record_sql("SELECT d.data FROM {user_info_data} d  where d.fieldid = $val->id and d.userid = $value->id");
                        if($val->id && $userdata){
                            if($userdata->datatype == 'datetime'){
                                $data .= '<td style="text-align:center">'.date('d/m/Y', $userdata->data).' </td>';
                            }else{
                                $data .= '<td style="text-align:center">'.$userdata->data.' </td>';
                            }
                        }else{
                            $data .= '<td style="text-align:center"> NA </td>';
                        }
                        
                    }
                $userid = $enrolled_users->userid;  // user id
                $attemptid=$enrolled_users->attemptid;  // Need to take from mdl_quiz_attempts with proper userid and quiz id , latest attempt can be consider so orderby id desc limit 1

                //Take attempt object of that user in that particular quiz attempt

                $attemptobj = quiz_create_attempt_handling_errors($attemptid);
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

                // print_r($byGroup_category);
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
                // print_r($final_result);
                // $str_category = "";
                // foreach($final_result as $cat_score => $result){
                //     $category_data = $DB->get_record_sql("SELECT id,name FROM {question_categories} where id = $cat_score");
                //     $str_category .= $category_data->name .'   -   '. $result .'% </br>';
                // }
           
            
            // $data .='<td style="text-align:center"> '.round($enrolled_users->sumgrades,2). '/' .round($enrolled_users->sumg,2).'</td>';   
            if($enrolled_users){
                $totalgarde = $enrolled_users->sumgrades / $enrolled_users->sumg * 100;
                $data .='<td style="text-align:center"> '.round($totalgarde,2).'%</td>'; 
                foreach($final_result as $cat_score => $result){
                    $data .='<td style="text-align:center"> '.$result.' % </td>'; 
                }  
                if($proctoring == 1){
                    $voicedata = $DB->get_record_sql("SELECT * from {proctoringvoicewindow} where userid = $value->userid and quizid = $enrolled_users->quizid ");
                    $proctoringdata = $DB->get_record_sql("SELECT * from {proctoringdetails} where userid = $value->userid and quizid = $enrolled_users->quizid ");
                    $data .='<td class="header c2" scope="col">'. $voicedata->background_noise .'</td>';
                    $data .='<td class="header c2" scope="col">'. $voicedata->tab_change .'</td>';
                    $data .='<td class="header c2" scope="col">'. $voicedata->window_change .'</td>';
                    $data .='<td class="header c2" scope="col">'. $proctoringdata->mouth_open_count .'</td>';
                    $data .='<td class="header c2" scope="col">'. $proctoringdata->mobile_phone_count .'</td>';
                    $data .='<td class="header c2" scope="col">'. $proctoringdata->more_person .'</td>';
                    $data .='<td class="header c2" scope="col">'. $proctoringdata->no_person .'</td>';
                    $data .='<td class="header c2" scope="col">'. $proctoringdata->head_up .'</td>';
                    $data .='<td class="header c2" scope="col">'. $proctoringdata->head_down .'</td>';
                    $data .='<td class="header c2" scope="col">'. $proctoringdata->head_left .'</td>';
                    $data .='<td class="header c2" scope="col">'. $proctoringdata->head_right .'</td>';
                    $data .='<td class="header c2" scope="col">'. $proctoringdata->face_recognition .'</td>';
                }
                
            }
            $data .='<td style="text-align:center"> Attempted </td>';
            $data .= '</tr>';
            }
      
        }

        $data .='</tbody></table></div></div>';

    }else{
        $data .= '<div class="card"><div id="tableContainer" class="card-body table-responsive">
        <table id="user_data" class="table table-striped table-inverse table-bordered table-hover no-footer" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th style="text-align:center">First Name</th>
                <th style="text-align:center">Last Name</th>
                <th style="text-align:center">Email</th>
                <th style="text-align:center">Obtained Grade/Total Grade </th>
                <th style="text-align:center">Status</th>
            </tr>
        </thead>
        <tbody>
        </tbody></table></div></div>';
    }
    echo $data;


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
echo '<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/autofill/2.3.5/js/dataTables.autoFill.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.flash.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>';
echo $OUTPUT->footer();
        $modal_content = '
        <div class="modal fade" id="share_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> Share Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
            
                        <div style="padding-left: 46px;"><label for="batches">Enter Email Id</label></div>
                        <div style="padding-left: 46px;"><textarea class="form-control" rows="3" id="data_mail"></textarea></div>                           
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary share_button" data-dismiss="modal">Close</button>
                        <button type="button" id="share_data" class="btn btn-primary">Share</button>
                    </div>
                </div>
            </div>
        </div>';
    echo $modal_content;
?>

<script>
$(document).ready( function () {
    var courseid = "<?php echo $course_id ?>";
    var batchid = "<?php echo $batch_id ?>";
    dropdown(courseid);
    // alert(batchid);
    $("#id_course_select").val(courseid);
    $("#id_batch_select").val(batchid);
    var table = $("#user_data").DataTable({
        "serverside": false,
            "lengthMenu": [
                [10, 40, 60, -1],
                [10, 40, 60, "All"]
            ],
            dom: 'lBfrtip',
            buttons: [
                'csv', 'excel'
        ],
        initComplete: function () {
        var table = $('#user_data').DataTable();
        this.api().columns().every( function () {
            var column = this;
            var colCount = $("#user_data th").length; 
            if (column.index() == colCount-1) {
                $('<span style="margin-left: 10px; margin-right: 10px;"></span>   ').appendTo( '#user_data_length' );
                    var select = $('<select id="class_select" class="custom-select"></select>')
                    select.append( '<option value="" >All</option>' )
                    .appendTo( '#user_data_length' )
                    .on( 'change', function () {
                        
                        var val = $(this).val()
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );

                column.data().unique().sort().each( function ( d, j ) {
    
                        select.append( '<option value="'+d+'" >'+d+'</option>' )
                
                } );
                $('<span style="margin-left: 10px; margin-right: 10px;"></span>   ').appendTo( '#user_data_length' );
           }
        //     if (column.index() == 4) {
                
        //     $('<span style="margin-left: 10px;"></span> ').appendTo( '#user_data_length' );
        //     var select = $('<select type="text" id="class_select" class=""></select>')
        //     .appendTo( '#user_data_length' )
        //     .on( 'change', function () {

        //     var val = $(this).val()
        //     column
        //     .search( val ? '^'+val+'$' : '', true, false )
        //     .draw();
        //     } );

        //     column.data().unique().sort().each( function ( d, j ) {
        //     var reg = /<a[^>]*>([^<]+)<\/a>/g
        //     var d_text = reg.exec(d)[1];            
        //     quotations.push(d_text);
        //     } );
        //     $.each($.unique(quotations), function(i, value){
        //     //$('div').eq(1).append(value  + ' ');
        //     select.append( '<option value="'+value+'" >'+value+'</option>' )
        //     });

        // }


        } );
        
    }

    });
    $('#search-datatable').keyup(function(){
      table.search($(this).val()).draw() ;
    });


    $('#mainselect').on('click', function() {
        var rows = table.rows({
            'search': 'applied'
        }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
    });

    $('#table tbody').on('change', 'input[type="checkbox"]', function() {

        if (!this.checked) {
            var el = $('#mainselect').get(0);
            if (el && el.checked && ('indeterminate' in el)) {
                el.indeterminate = true;
            }
        }
    });

// $('#mainselect').click(function(e){
//     var table= $(e.target).closest('table');
//     $('td input:checkbox',table).prop('checked',this.checked);
// });

$('#share_data').click(function(){
    var mail_id = $('#data_mail').val();
    var batch_id = $('#id_batch_select').val();
    idlist = [];
        table.$('.userid').each(function() {
            if (this.checked) {
                idlist.push(this.id);
            }
        });
    // $("#user_data input[class=userid]:checked").each(function () {
    //     idlist.push(this.id);
    // });
    if(idlist == ""){
        alert('Choose at least one..');
    }else if(mail_id == ""){
        alert('Enter atleast one mail');
    }else{
        // $("#share_modal").modal('hide');
        $.ajax({
            type: "post",
            url: "btmail_function.php",
            data: {
                'idlist': idlist,
                'mail_id': mail_id,
                'batch_id' : batch_id,
            },
            success: function(data) {
                alert(data);
                // location.reload();
            }
        });
        // alert("val---" + idlist.join(", "));
        // alert(mail_id);

    }
})


	$("#id_course_select" ).change(function() {
        dropdown(this.value);
    });


	$( "#id_batch_select" ).change(function() {
        var batch_id = document.getElementById("id_batch_select").value;
        var course_id = document.getElementById("id_course_select").value;
        var num = "<?php echo $url ?>";
        if(batch_id == ''){
            window.location.href= num; 
        }else{
            window.location.href= num+'?course_id='+course_id+'&&batch_id='+batch_id; 
        }
        // alert(course_id);
    });
    function dropdown(course){
        $.ajax({
            type: "POST",
            url: "datafile.php",
            data: {
                'lead_type' : 1,
                'courseid': course,
            },
            success: function(data) {
                $("#id_batch_select").html(data);
            }
        });
    }
});
</script>

