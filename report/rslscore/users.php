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
$PAGE->navbar->add('RSL Scores', new moodle_url($CFG->wwwroot.'/report/rslscore/index.php'));
$PAGE->navbar->add('User report');

$id = optional_param('id', '', PARAM_RAW);
$did  = optional_param('did', '', PARAM_RAW);


$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 10, PARAM_INT);
$PAGE->set_url('/report/rslscore/users.php', array('id' => $id,'did' => $did));
$data='';
require_login();

//admin_externalpage_setup('reportrslscore', '', null, '', array('pagelayout'=>'report'));
echo $OUTPUT->header();
$baseurl = $CFG->wwwroot."/report/rslscore/users.php?id=".$id."&did=".$did;
$assi = $CFG->wwwroot."/report/rslscore/assignuser.php?id=".$id."&did=".$did;

if(is_siteadmin() || user_has_role_assignment($USER->id, 10)){
 ?>
	<a href="<?php echo $CFG->wwwroot;?>/report/rslscore/index.php" class="btn btn-primary"> Back </a> 
    <?php if($id == 2){ ?>
    <a href="<?php echo $assi; ?>" class="btn btn-primary"> Assign All Users </a>

    <?php } ?>

<?php

    $data .= '<div id="tableContainer">
	<table id="" class="table table-striped table-inverse table-bordered table-hover no-footer" cellspacing="0" width="100%">
	<thead>
        <tr>
            <th style="text-align:center">Username</th>
            <th style="text-align:center">Email</th>
            <th style="text-align:center">Test Percentage</th>
            <th style="text-align:center">Interview Percentage</th>';
            if($id == 2){
                $data .= '<th style="text-align:center">Assign</th>'; 
            }
                $data .= '          
		</tr>
	</thead>
	<tbody>';
    
 
        // $compleated = $DB->get_record_sql("select GROUP_CONCAT(cc.userid) AS userid from mdl_course_completions cc INNER JOIN mdl_company_users cu ON cc.userid = cu.userid where cc.timestarted != 0 AND  cu.companyid = '" . $orgid . "' AND cc.timecompleted IS NULL  AND cc.course = '" . $cid . "'");
        
// 
        // $enrolled_users =$DB->get_records_sql("select ru.*,g.name AS groupname,u.email,rrd.interview,ru.userid AS userid,rrd.test As testid ,(select round(grade * 10) from mdl_quiz_grades where quiz = rrd.test and userid = ru.userid) as testper, case when rrd.interview = 1 then (select round(grade * 10) from mdl_quiz_grades where quiz = ru.interview_id and userid = ru.userid) else 'No Interview' end interviewper from mdl_rsl_user_detail ru INNER JOIN mdl_user u ON u.id=ru.userid JOIN mdl_groups g ON g.id=ru.test_groupid JOIN mdl_rsl_recruitment_drive rrd ON rrd.id=ru.recruitment_id WHERE ru.recruitment_id =$did");
        $enrolled_users =$DB->get_records_sql("select ru.*,g.name AS groupname,u.email,rrd.interview,ru.userid AS userid,rrd.test As testid ,(select round(((10/b.sumgrades) * a.sumgrades)* 10) quizper from mdl_quiz_attempts a join mdl_quiz b on a.quiz = b.id where b.id = rrd.test and userid = ru.userid order by a.timemodified desc limit 1) as testper, case when rrd.interview = 1 then (select round(((10/b.sumgrades) * a.sumgrades)* 10) quizper from mdl_quiz_attempts a join mdl_quiz b on a.quiz = b.id where b.id = ru.interview_id order by a.timemodified desc limit 1) else 'No Interview' end interviewper from mdl_rsl_user_detail ru INNER JOIN mdl_user u ON u.id=ru.userid JOIN mdl_groups g ON g.id=ru.test_groupid JOIN mdl_rsl_recruitment_drive rrd ON rrd.id=ru.recruitment_id WHERE ru.recruitment_id =$did");




        
        //  echo '<pre>';print_r($enrolled_users);exit;
            //pagination starts
           
            //print_r($baseurl);exit;
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
            // $enrolled_users =$DB->get_records_sql("select ru.*,g.name AS groupname,u.email,rrd.interview,rrd.test As testid ,(select round(grade * 10) from mdl_quiz_grades where quiz = rrd.test and userid = ru.userid) as testper, case when rrd.interview = 1 then (select round(grade * 10) from mdl_quiz_grades where quiz = ru.interview_id and userid = ru.userid) else 'No Interview' end interviewper from mdl_rsl_user_detail ru INNER JOIN mdl_user u ON u.id=ru.userid JOIN mdl_groups g ON g.id=ru.test_groupid JOIN mdl_rsl_recruitment_drive rrd ON rrd.id=ru.recruitment_id", array() ,$start, $perpage );
      

    




        
    //  print_r($enrolled_users);exit; 
        foreach($enrolled_users as $key => $value){

            // above 70

            if($id == 3){
                if($value->interview == 1){
                    if($value->testper > 70 && $value->interviewper > 70){
                        if($value->testper > 0 ){
                            $testper=$value->testper.' % ';
                        }else{
                            $testper=$value->testper;
                        }
            
                        if($value->interviewper > 0 ){
                            $interviewper=$value->interviewper.' % ';
                        }else{
                            $interviewper=$value->interviewper;
                        }
                        // if($page)
                        //      $assignuser=$CFG->wwwroot.'/report/rslscore/assignuser.php?id='.$id.'&did='.$did;
                        // else
                        //     $assignuser=$CFG->wwwroot.'/report/rslscore/assignuser.php?id='.$id.'&did='.$did;
                        $assignuser=$CFG->wwwroot.'/report/rslscore/assignuser.php?id='.$id.'&did='.$did.'&uid='.$value->userid;
                        
                            $data .= '<tr>';
                            $data .='<td style="text-align:center">'.$value->username.'</td>';
                            $data .='<td style="text-align:center">'.$value->email.'</td>';
                            $data .='<td style="text-align:center"> '.$testper.'</td>';   
                            $data .='<td style="text-align:center"> '.$interviewper.'</td>';   
                            $data .= '</tr>';
                    }
    
                }else{
                    if($value->testper > 70 ){
                        if($value->testper > 0 ){
                            $testper=$value->testper.' % ';
                        }else{
                            $testper=$value->testper;
                        }
            
                        if($value->interviewper > 0 ){
                            $interviewper=$value->interviewper.' % ';
                        }else{
                            $interviewper=$value->interviewper;
                        }
                        // if($page)
                        //      $assignuser=$CFG->wwwroot.'/report/rslscore/assignuser.php?id='.$id.'&did='.$did.'&uis='.$value->userid;
                        // else
                        //     $assignuser=$CFG->wwwroot.'/report/rslscore/assignuser.php?id='.$id.'&did='.$did;
                        $assignuser=$CFG->wwwroot.'/report/rslscore/assignuser.php?id='.$id.'&did='.$did.'&uid='.$value->userid;
                            $data .= '<tr>';
                            $data .='<td style="text-align:center">'.$value->username.'</td>';
                            $data .='<td style="text-align:center">'.$value->email.'</td>';
                            $data .='<td style="text-align:center"> '.$testper.'</td>';   
                            $data .='<td style="text-align:center"> '.$interviewper.'</td>'; 
                            $data .= '</tr>';
    
                    }
                }
            }
            
            // 50 to 70 per

            if($id == 2){
                if($value->interview == 1){
                    // if($value->testper <= 70 && $value->interviewper <= 70  && ($value->testper >= 50 || $value->interviewper >= 50) && ($value->testper < 70 && $value->interviewper < 70) ){

                        if(($value->testper <= 70 || $value->interviewper <=70) && ($value->testper != '') && ($value->testper >= 50 || $value->interviewper >= 50)){
                        
                        if(($value->testper < 70 && $value->testper > 50)  || ( $value->interviewper < 70 && $value->interviewper > 50))  {


                        if($value->testper > 0 ){
                            $testper=$value->testper.' % ';
                        }else{
                            $testper=$value->testper;
                        }
            
                        if($value->interviewper > 0 ){
                            $interviewper=$value->interviewper.' % ';
                        }else{
                            $interviewper=$value->interviewper;
                        }
                        // if($page)
                        //      $assignuser=$CFG->wwwroot.'/report/rslscore/assignuser.php?id='.$id.'&did='.$did;
                        // else
                        //     $assignuser=$CFG->wwwroot.'/report/rslscore/assignuser.php?id='.$id.'&did='.$did;
                        $assignuser=$CFG->wwwroot.'/report/rslscore/assignuser.php?id='.$id.'&did='.$did.'&uid='.$value->userid;
                        
                            $data .= '<tr>';
                            $data .='<td style="text-align:center">'.$value->username.'</td>';
                            $data .='<td style="text-align:center">'.$value->email.'</td>';
                            $data .='<td style="text-align:center"> '.$testper.'</td>';   
                            $data .='<td style="text-align:center"> '.$interviewper.'</td>';   
                            $data .='<td style="text-align:center"><a href="'.$assignuser.'" class="btn btn-primary"> Assign Course </a></td>';
                            $data .= '</tr>';
                    }
                    }
    
                }else{
                    if($value->testper <= 70 && $value->testper >= 50 ){
                        if($value->testper > 0 ){
                            $testper=$value->testper.' % ';
                        }else{
                            $testper=$value->testper;
                        }
            
                        if($value->interviewper > 0 ){
                            $interviewper=$value->interviewper.' % ';
                        }else{
                            $interviewper=$value->interviewper;
                        }
                        // if($page)
                        //      $assignuser=$CFG->wwwroot.'/report/rslscore/assignuser.php?id='.$id.'&did='.$did;
                        // else
                        //     $assignuser=$CFG->wwwroot.'/report/rslscore/assignuser.php?id='.$id.'&did='.$did;
                        $assignuser=$CFG->wwwroot.'/report/rslscore/assignuser.php?id='.$id.'&did='.$did.'&uid='.$value->userid;
                        
                            $data .= '<tr>';
                            $data .='<td style="text-align:center">'.$value->username.'</td>';
                            $data .='<td style="text-align:center">'.$value->email.'</td>';
                            $data .='<td style="text-align:center"> '.$testper.'</td>';   
                            $data .='<td style="text-align:center"> '.$interviewper.'</td>';   
                            $data .='<td style="text-align:center"><a href="'.$assignuser.'" class="btn btn-primary"> Assign Course </a></td>';
                            $data .= '</tr>';
    
                    }
                }
            }

            // Below 50
                        // above 70

                        if($id == 1){
                            if($value->interview == 1){
                                if($value->testper < 50 && $value->interviewper < 50){
                                    if($value->testper > 0 ){
                                        $testper=$value->testper.' % ';
                                    }else{
                                        $testper=$value->testper;
                                    }
                        
                                    if($value->interviewper > 0 ){
                                        $interviewper=$value->interviewper.' % ';
                                    }else{
                                        $interviewper=$value->interviewper;
                                    }
                                    // if($page)
                                    //      $assignuser=$CFG->wwwroot.'/report/rslscore/assignuser.php?id='.$id.'&did='.$did;
                                    // else
                                    //     $assignuser=$CFG->wwwroot.'/report/rslscore/assignuser.php?id='.$id.'&did='.$did;
                                    $assignuser=$CFG->wwwroot.'/report/rslscore/assignuser.php?id='.$id.'&did='.$did.'&uid='.$value->userid;
                                    
                                        $data .= '<tr>';
                                        $data .='<td style="text-align:center">'.$value->username.'</td>';
                                        $data .='<td style="text-align:center">'.$value->email.'</td>';
                                        $data .='<td style="text-align:center"> '.$testper.'</td>';   
                                        $data .='<td style="text-align:center"> '.$interviewper.'</td>';   
                                        $data .= '</tr>';
                                }
                
                            }else{
                                if($value->testper  < 50 ){
                                    if($value->testper > 0 ){
                                        $testper=$value->testper.' % ';
                                    }else{
                                        $testper=$value->testper;
                                    }
                        
                                    if($value->interviewper > 0 ){
                                        $interviewper=$value->interviewper.' % ';
                                    }else{
                                        $interviewper=$value->interviewper;
                                    }
                                    // if($page)
                                    //      $assignuser=$CFG->wwwroot.'/report/rslscore/assignuser.php?id='.$id.'&did='.$did;
                                    // else
                                    //     $assignuser=$CFG->wwwroot.'/report/rslscore/assignuser.php?id='.$id.'&did='.$did;
                                    $assignuser=$CFG->wwwroot.'/report/rslscore/assignuser.php?id='.$id.'&did='.$did.'&uid='.$value->userid;
                                    
                                        $data .= '<tr>';
                                        $data .='<td style="text-align:center">'.$value->username.'</td>';
                                        $data .='<td style="text-align:center">'.$value->email.'</td>';
                                        $data .='<td style="text-align:center"> '.$testper.'</td>';   
                                        $data .='<td style="text-align:center"> '.$interviewper.'</td>';   
                                        $data .= '</tr>';
                
                                }
                            }
                        }



      
        }


        $data .='</tbody></table></div></div>';
        echo $data;


    
}else{

    redirect($CFG->wwwroot.'/my',"Acess Denied.");
}

// echo $OUTPUT->paging_bar($t_count, $page, $perpage, $baseurl);
// echo $OUTPUT->download_dataformat_selector(get_string('userbulkdownload', 'admin'), 'rslscore_report.php','dataformat', array('cid' => $cid ,'report' => $report,'orgid' => $orgid) );

echo $OUTPUT->footer();
?>