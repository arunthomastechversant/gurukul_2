<script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/autofill/2.3.5/css/autoFill.dataTables.min.css">
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

$PAGE->set_title("BT User Scores");
$PAGE->set_heading("BT User Scores");
$PAGE->navbar->add('BT User Scores', new moodle_url($CFG->wwwroot.'/local/custompage/bu_drive_list_detail.php'));

$drive_id = optional_param('drive_id', '', PARAM_RAW);

$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 10, PARAM_INT);
$PAGE->set_url('/local/custompage/bu_drive_list_detail.php', array('drive_id' => $drive_id));
$data='';
require_login();

//admin_externalpage_setup('reportrslscore', '', null, '', array('pagelayout'=>'report'));
echo $OUTPUT->header();
$baseurl = $CFG->wwwroot."/local/custompage/bu_drive_list_detail.php.php?drive_id=".$drive_id;
$assi = $CFG->wwwroot."/local/custompage/bu_drive_list_detail.php?drive_id=".$drive_id;

if(is_siteadmin() || user_has_role_assignment($USER->id, 10)){
 ?>
	<a href="<?php echo $CFG->wwwroot;?>/local/custompage/bu_drive_list.php" class="btn btn-primary"> Back </a> 

<?php

    echo $block_content = "&nbsp &nbsp&nbsp &nbsp<button type='button' class='btn btn-primary share_button' data-toggle='modal' data-target='#share_modal'>Share Details</button>&nbsp &nbsp</br></br>";  
    $qstn_ctry = array();
    $ctry_data = $DB->get_record_sql("SELECT questions FROM {bt_recruitment_drive} as brd join {test_questioncategory} as tq where brd.test = tq.test_id and brd.id = $drive_id ")->questions;
    $ctry_ids = explode(',' , $ctry_data);
    foreach($ctry_ids as $key => $value){
        $data_set = explode('-',$value);
        if($data_set[1] > 0){
                $qst_ctry = $DB->get_record_sql("SELECT name FROM {question_categories} where id = '$data_set[0]' ")->name;
                array_push($qstn_ctry,$qst_ctry);
            }
        }
    // print_r($qstn_ctry);
    echo "<input type='hidden' value='$drive_id' id='driveid'>";
    $data .= '<div id="tableContainer">
	<table id="user_data" class="table table-striped table-inverse table-bordered table-hover no-footer" cellspacing="0" width="100%">
	<thead>
        <tr>
            <th style="text-align:center"><input type="checkbox" id="mainselect" value="1"> All</th>
            <th style="text-align:center">First Name</th>
            <th style="text-align:center">Last Name</th>
            <th style="text-align:center">Email</th>
            <th style="text-align:center">Obtained Grade/Total Grade </th>';
            foreach($qstn_ctry as $key => $value){
                $data .= '<th style="text-align:center">' . $value .'</th>';
            }
            $data .= '<th style="text-align:center">Status</th>
		</tr>
	</thead>
	<tbody>';
    
 
        // $enrolled_users =$DB->get_records_sql("select ru.*,g.name AS groupname,u.email,rrd.interview,ru.userid AS userid,rrd.test As testid ,(select round(((10/b.sumgrades) * a.sumgrades)* 10) quizper from mdl_quiz_attempts a join mdl_quiz b on a.quiz = b.id where b.id = rrd.test and userid = ru.userid order by a.timemodified desc limit 1) as testper, case when rrd.interview = 1 then (select round(((10/b.sumgrades) * a.sumgrades)* 10) quizper from mdl_quiz_attempts a join mdl_quiz b on a.quiz = b.id where b.id = ru.interview_id order by a.timemodified desc limit 1) else 'No Interview' end interviewper from mdl_rsl_user_detail ru INNER JOIN mdl_user u ON u.id=ru.userid JOIN mdl_groups g ON g.id=ru.test_groupid JOIN mdl_rsl_recruitment_drive rrd ON rrd.id=ru.recruitment_id WHERE ru.recruitment_id =$drive_id");

        // $enrolled_users = $DB->get_records_sql("select mqa.id as attemptid,u.firstname,u.lastname,u.email,bu.userid,mqa.sumgrades,qz.sumgrades as sumg,
        // (select round((10/b.sumgrades) * a.sumgrades) quizper from mdl_quiz_attempts a join mdl_quiz b 
        // on a.quiz = b.id where b.id = brd.test and userid = bu.userid order by a.timemodified desc limit 1)
        // as testper from mdl_bt_user_detail bu INNER JOIN mdl_user u ON u.id=bu.userid JOIN mdl_groups g 
        // ON g.id=bu.test_groupid JOIN mdl_bt_recruitment_drive brd ON brd.id=bu.recruitment_id join mdl_quiz_attempts mqa on mqa.userid = bu.userid join {quiz} as qz on qz.id = mqa.quiz
        // WHERE bu.recruitment_id =$drive_id");
        
        $datas = $DB->get_records_sql("SELECT u.* FROM  {bt_user_detail} as bu join {user} as u WHERE bu.userid = u.id and bu.recruitment_id = $drive_id");
        // print_r($datas);

        // print_r($enrolled_users);
        $t_count = count($enrolled_users);
            $start = $page * $perpage;
            if ($start > $t_count) {
                $page = 0;
                $start = 0;
            }
            $i = 1;
            if($page != 0){
                $i = ($page * $perpage)+1;

            }
        
    //  print_r($enrolled_users);exit; 
        foreach($datas as $key => $value){
            $enrolled_users = $DB->get_record_sql("select mqa.id as attemptid,u.firstname,u.lastname,u.email,bu.userid,
            mqa.sumgrades,qz.sumgrades as sumg from mdl_bt_user_detail bu JOIN mdl_user u ON u.id=bu.userid JOIN mdl_groups g
            ON g.id=bu.test_groupid JOIN mdl_bt_recruitment_drive brd ON brd.id=bu.recruitment_id join mdl_quiz_attempts mqa on
            mqa.userid = bu.userid join {quiz} as qz on qz.id = mqa.quiz
            WHERE bu.recruitment_id =$drive_id and u.id = $value->id");
            // $str_status = "Not Attemted";
            // if($value->testper >= 7){
            //     $str_status = "Passed";
            // }elseif($value->testper > 0 && $value->testper < 7){
            //     $str_status = "Failed";
            // }else{
            //     $str_status = "Not Attemted";
            // }
            if($enrolled_users){
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
            }
            
            $data .= '<tr>';
            $data .= '<td style="text-align:center"><input type="checkbox" class="userid" id= ' .$value->id. '></td>';
            $data .='<td style="text-align:center">'.$value->firstname.'</td>';
            $data .='<td style="text-align:center">'.$value->lastname.'</td>';
            $data .='<td style="text-align:center">'.$value->email.'</td>';
            // $data .='<td style="text-align:center"> '.round($enrolled_users->sumgrades,2). '/' .round($enrolled_users->sumg,2).'</td>';   
            if($enrolled_users){
                $data .='<td style="text-align:center"> '.round($enrolled_users->sumgrades,2). '/' .round($enrolled_users->sumg,2).'</td>'; 
                foreach($final_result as $cat_score => $result){
                    $data .='<td style="text-align:center"> '.$result.' % </td>'; 
                }  
                $data .='<td style="text-align:center"> Attempted </td>';   
            }else{
                $data .='<td style="text-align:center"> NA </td>'; 
                foreach($qstn_ctry as $key2 => $value){
                    $data .='<td style="text-align:center"> NA </td>'; 
                }  
                $data .='<td style="text-align:center"> Not Attempted </td>';   
            }

            $data .= '</tr>';
      
        }


        $data .='</tbody></table></div></div>';
        echo $data;


    
}else{

    redirect($CFG->wwwroot.'/my',"Acess Denied.");
}

echo $OUTPUT->paging_bar($t_count, $page, $perpage, $baseurl);

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
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/autofill/2.3.5/js/dataTables.autoFill.min.js"></script>';
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
    var table = $("#user_data").DataTable({
        'columnDefs': [{
            'targets': -1,
            'searchable': false,
            'orderable': false,
        }],
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
$('.share_button').click(function(){
    document.getElementById("data_mail").value   = "";
})

$('#share_data').click(function(){
    var mail_id = $('#data_mail').val();
    var driveid = $('#driveid').val();
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
                'driveid' : driveid,
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

});

</script>
