<?php
require(__DIR__.'/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
global $DB,$USER,$COURSE,$CFG,$PAGE,$OUTPUT;

$context = context_system::instance();
$companyid = iomad::get_my_companyid($context);
$mail_id = array();

$mail_id = $_POST['mail_id'];
$idlist = $_POST['idlist'];
$drive_id = $_POST['driveid'];
$qstn_ctry = array();
    $ctry_data = $DB->get_record_sql("SELECT questions FROM {recruitment_drive} as brd join {test_questioncategory} as tq where brd.test = tq.test_id and brd.id = $drive_id ")->questions;
    $ctry_ids = explode(',' , $ctry_data);
    foreach($ctry_ids as $key => $value){
        $data_set = explode('-',$value);
        if($data_set[1] > 0){
                $qst_ctry = $DB->get_record_sql("SELECT name FROM {question_categories} where id = '$data_set[0]' ")->name;
                array_push($qstn_ctry,$qst_ctry);
            }
        }
    $proctoring =$DB->get_record_sql("SELECT eproctoringrequired From {quizaccess_eproctoring} ep join {recruitment_drive} ud where ud.test = ep.quizid and ud.id=$drive_id")->eproctoringrequired;
    // $proctoring =$DB->get_record_sql("SELECT proctoring From {test_questioncategory} tq join {recruitment_drive} bd where bd.test = tq.test_id and bd.id=$drive_id")->proctoring;
    $userheading = $DB->get_records_sql("SELECT f.id,f.name,f.shortname FROM {user_info_field} f where f.categoryid = $companyid");
    // print_r($userheading);exit();
    $data .= '<div class="card"><div id="tableContainer" class="card-body table-responsive">
	<table id="user_data" class="table table-striped table-inverse table-bordered table-hover no-footer" cellspacing="0" width="100%">
	<thead>
        <tr>
            <th style="text-align:center"><input type="checkbox" id="mainselect" value="1"> All</th>
            <th class="header c3" scope="col">First Name</th>
            <th class="header c3" scope="col">Last Name</th>';
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
    
 
        // $enrolled_users =$DB->get_records_sql("select ru.*,g.name AS groupname,u.email,rrd.interview,ru.userid AS userid,rrd.test As testid ,(select round(((10/b.sumgrades) * a.sumgrades)* 10) quizper from mdl_quiz_attempts a join mdl_quiz b on a.quiz = b.id where b.id = rrd.test and userid = ru.userid order by a.timemodified desc limit 1) as testper, case when rrd.interview = 1 then (select round(((10/b.sumgrades) * a.sumgrades)* 10) quizper from mdl_quiz_attempts a join mdl_quiz b on a.quiz = b.id where b.id = ru.interview_id order by a.timemodified desc limit 1) else 'No Interview' end interviewper from mdl_rsl_user_detail ru INNER JOIN mdl_user u ON u.id=ru.userid JOIN mdl_groups g ON g.id=ru.test_groupid JOIN mdl_rsl_recruitment_drive rrd ON rrd.id=ru.recruitment_id WHERE ru.recruitment_id =$drive_id");

        // $enrolled_users = $DB->get_records_sql("select mqa.id as attemptid,u.firstname,u.lastname,u.email,bu.userid,mqa.sumgrades,qz.sumgrades as sumg,
        // (select round((10/b.sumgrades) * a.sumgrades) quizper from mdl_quiz_attempts a join mdl_quiz b 
        // on a.quiz = b.id where b.id = brd.test and userid = bu.userid order by a.timemodified desc limit 1)
        // as testper from mdl_user_detail bu INNER JOIN mdl_user u ON u.id=bu.userid JOIN mdl_groups g 
        // ON g.id=bu.test_groupid JOIN mdl_recruitment_drive brd ON brd.id=bu.recruitment_id join mdl_quiz_attempts mqa on mqa.userid = bu.userid join {quiz} as qz on qz.id = mqa.quiz
        // WHERE bu.recruitment_id =$drive_id");
        
        // $datas = $DB->get_records_sql("SELECT u.* FROM  {user_detail} as bu join {user} as u WHERE bu.userid = u.id and bu.recruitment_id = $drive_id and bu.company_id = $companyid");
        // print_r($datas);

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
        
    //  print_r($enrolled_users);exit; 
        foreach($idlist as $key => $value){
            // print_r($value);
            $enrolled_users = $DB->get_record_sql("select mqa.id as attemptid,bu.userid,qz.id as quizid,mcm.id as cmid,u.firstname,u.lastname,
            mqa.sumgrades,qz.sumgrades as sumg from mdl_user_detail bu JOIN mdl_user u ON u.id=bu.userid JOIN mdl_groups g
            ON g.id=bu.test_groupid JOIN mdl_recruitment_drive brd ON brd.id=bu.recruitment_id join mdl_quiz_attempts mqa on
            mqa.userid = bu.userid join {quiz} as qz on qz.id = mqa.quiz join mdl_course_modules as mcm on mcm.instance = qz.id
            WHERE bu.recruitment_id =$drive_id and u.id = $value");
            // print_r($enrolled_users);exit();
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
                $data .= '<td style="text-align:center"><input type="checkbox" class="userid" id= ' .$value. '></td>';
                $data .= '<td style="text-align:center">'.$enrolled_users->firstname.' </td>';
                $data .= '<td style="text-align:center">'.$enrolled_users->lastname.' </td>';
                    foreach($userheading as $key1 => $val){
                        $userdata = $DB->get_record_sql("SELECT d.data FROM {user_info_data} d  where d.fieldid = $val->id and d.userid = $value")->data;
                        // print($userdata);
                        if($val->id && $userdata){
                                $data .= '<td style="text-align:center">'.$userdata.' </td>';
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
                    $voicedata = $DB->get_record_sql("SELECT * from {proctoringvoicewindow} where userid = $value and quizid = $enrolled_users->cmid ");
                    $proctoringdata = $DB->get_records_sql("SELECT * from {proctoringdetails} where userid = $value and quizid = $enrolled_users->cmid ");

                    $mouth_open_count =0;
                    $mobile_phone_count = 0;
                    $more_person = 0;
                    $no_person = 0;
                    $head_up = 0;
                    $head_down = 0;
                    $head_left =0;
                    $head_right = 0;
                    $face_recognition =0;
                    foreach($proctoringdata as $pdata)
                    {
                        $mouth_open_count += $pdata->mouth_open_count;
                        $mobile_phone_count += $pdata->mobile_phone_count;
                        $more_person += $pdata->more_person;
                        $no_person += $pdata->no_person;
                        $head_up += $pdata->head_up;
                        $head_down += $pdata->head_down;
                        $head_left += $pdata->head_left;
                        $head_right += $pdata->head_right;
                        $face_recognition += $pdata->face_recognition;  
                    }
                    $data .='<td class="header c2" scope="col">'. $voicedata->background_noise .'</td>';
                    $data .='<td class="header c2" scope="col">'. $voicedata->tab_change .'</td>';
                    $data .='<td class="header c2" scope="col">'. $voicedata->window_change .'</td>';
                    $data .='<td class="header c2" scope="col">'. $mouth_open_count .'</td>';
                    $data .='<td class="header c2" scope="col">'. $mobile_phone_count .'</td>';
                    $data .='<td class="header c2" scope="col">'. $more_person .'</td>';
                    $data .='<td class="header c2" scope="col">'. $no_person .'</td>';
                    $data .='<td class="header c2" scope="col">'. $head_up .'</td>';
                    $data .='<td class="header c2" scope="col">'. $head_down .'</td>';
                    $data .='<td class="header c2" scope="col">'. $head_left .'</td>';
                    $data .='<td class="header c2" scope="col">'. $head_right .'</td>';
                    $data .='<td class="header c2" scope="col">'. $face_recognition .'</td>'; 
                }
                $data .='<td style="text-align:center"> Attempted </td>'; 
            }

            $data .= '</tr>';
            }
      
        }

        $data .='</tbody></table></div></div>';
        // echo $data;
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

    $mail_ids = explode(',',$mail_id);
    // print_r($mail_ids);exit();

foreach($mail_ids as $mailid){
    $maildata = $DB->get_record('mail_templates', array('company_id' =>$companyid ,'type_id' => 2));
    $drivedate = date('d/m/Y', $DB->get_record('recruitment_drive',array('id' => $drive_id))->startdate);
    $search    = array("{{drivedate}}");
    $replace   = array($drivedate);
    $mailcontent = str_replace($search,$replace,$maildata->content );
    $mailcontent .= "</br>";
    $mailcontent .= $data;
    $user = $DB->get_record('user',array('id' => $USER->id));
    $user->email = $mailid;

    $subject = $maildata->subject;
    
    $noreplyuser = core_user::get_noreply_user();

    email_to_user($user, $noreplyuser, $subject, $mailcontent);
}

?>
