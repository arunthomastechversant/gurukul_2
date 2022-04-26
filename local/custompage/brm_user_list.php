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
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/questionlib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
global $DB,$USER,$COURSE,$CFG,$PAGE,$OUTPUT;

$PAGE->set_title("User Report");
$PAGE->navbar->add('BT Scores', new moodle_url($CFG->wwwroot.'/local/custompage/brm_user_list.php'));
$PAGE->navbar->add('User report');
$PAGE->set_context(context_system::instance());
$drive_id = optional_param('drive_id', '', PARAM_RAW);

$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 10, PARAM_INT);
$PAGE->set_url('/local/custompage/brm_user_list.php');
$data='';
// require_login();

//admin_externalpage_setup('reportrslscore', '', null, '', array('pagelayout'=>'report'));
echo $OUTPUT->header();
$baseurl = $CFG->wwwroot."/local/custompage/brm_user_list.php.php?drive_id=".$drive_id;
$assi = $CFG->wwwroot."/local/custompage/brm_user_list.php?drive_id=".$drive_id;

    $data .= '<div id="tableContainer">
	<table id="" class="table table-striped table-inverse table-bordered table-hover no-footer" cellspacing="0" width="100%">
	<thead>
        <tr>
            <th style="text-align:center">First Name</th>
            <th style="text-align:center">Last Name</th>
            <th style="text-align:center">Email</th>
            <th style="text-align:center">Test Percentage</th>
            <th style="text-align:center">Status</th>          
		</tr>
	</thead>
	<tbody>';
    
 
        // $enrolled_users =$DB->get_records_sql("select ru.*,g.name AS groupname,u.email,rrd.interview,ru.userid AS userid,rrd.test As testid ,(select round(((10/b.sumgrades) * a.sumgrades)* 10) quizper from mdl_quiz_attempts a join mdl_quiz b on a.quiz = b.id where b.id = rrd.test and userid = ru.userid order by a.timemodified desc limit 1) as testper, case when rrd.interview = 1 then (select round(((10/b.sumgrades) * a.sumgrades)* 10) quizper from mdl_quiz_attempts a join mdl_quiz b on a.quiz = b.id where b.id = ru.interview_id order by a.timemodified desc limit 1) else 'No Interview' end interviewper from mdl_rsl_user_detail ru INNER JOIN mdl_user u ON u.id=ru.userid JOIN mdl_groups g ON g.id=ru.test_groupid JOIN mdl_rsl_recruitment_drive rrd ON rrd.id=ru.recruitment_id WHERE ru.recruitment_id =$drive_id");
        // $enrolled_users = $DB->get_records_sql("select * from {bt_user_detail} as btu INNER JOIN {user} as u where u.id = btu.userid and btu.recruitment_id='$drive_id'");
        // $data01 = $DB->get_records_sql("select round(((10/b.sumgrades) * a.sumgrades)* 10) quizper from mdl_quiz_attempts a join mdl_quiz b on a.quiz = b.id where b.id = rrd.test and userid = ru.userid order by a.timemodified desc limit 1) as testper");    
        // echo "select bu.*,g.name AS groupname,u.email,bu.userid AS userid,brd.test As testid ,(select round(((10/b.sumgrades) * a.sumgrades) * 10) quizper from mdl_quiz_attempts a join mdl_quiz b on a.quiz = b.id where b.id = brd.test and userid = bu.userid order by a.timemodified desc limit 1) as testper from mdl_bt_user_detail bu INNER JOIN mdl_user u ON u.id=bu.userid JOIN mdl_groups g ON g.id=bu.test_groupid JOIN mdl_bt_recruitment_drive brd ON brd.id=bu.recruitment_id WHERE bu.recruitment_id =$drive_id";
        $enrolled_users = $DB->get_records_sql("select u.firstname,u.lastname,u.email,bu.userid, 
        (select round(((10/b.sumgrades) * a.sumgrades) * 10) quizper from mdl_quiz_attempts a 
        join mdl_quiz b on a.quiz = b.id where b.id = brd.test and userid = bu.userid 
        order by a.timemodified desc limit 1) as testper from mdl_bt_user_detail bu 
        INNER JOIN mdl_user u ON u.id=bu.userid JOIN mdl_groups g ON g.id=bu.test_groupid 
        JOIN mdl_bt_recruitment_drive brd ON brd.id=bu.recruitment_id JOIN mdl_brm_assignements bss ON bss.userid = bu.id and bss.brmid = 449");
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
        foreach($enrolled_users as $key => $value){

            if($value->testper > 0 ){
                $testper=$value->testper.' % ';
            }else{
                $testper=$value->testper;
            }
            $str_status = "Not Attemted";
            if($value->testper >= 70){
                $str_status = "Passed";
            }elseif($value->testper > 0 && $value->testper < 70){
                $str_status = "Failed";
            }else{
                $str_status = "Not Attemted";
            }
            
            $data .= '<tr>';
            $data .='<td style="text-align:center">'.$value->firstname.'</td>';
            $data .='<td style="text-align:center">'.$value->lastname.'</td>';
            $data .='<td style="text-align:center">'.$value->email.'</td>';
            $data .='<td style="text-align:center"> '.$testper.'</td>';   
            $data .='<td style="text-align:center"> '.$str_status.'</td>';   
            $data .= '</tr>';
      
        }


        $data .='</tbody></table></div></div>';
        echo $data;


// echo $OUTPUT->paging_bar($t_count, $page, $perpage, $baseurl);
// echo $OUTPUT->download_dataformat_selector(get_string('userbulkdownload', 'admin'), 'rslscore_report.php','dataformat', array('cid' => $cid ,'report' => $report,'orgid' => $orgid) );

echo $OUTPUT->footer();
?>