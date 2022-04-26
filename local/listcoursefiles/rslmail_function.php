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
//$datas = $DB->get_record_sql("SELECT * FROM {rsl_recruitment_drive} where id=$drive_id");
    //$r_user = $DB->get_records_sql("SELECT rud.userid,u.username,u.firstname,u.lastname ,u.email,u.deleted,u.suspended,rud.rsl_due FROM {rsl_user_detail} rud JOIN {user} u ON u.id = rud.userid where u.deleted=0 AND rud.recruitment_id=$drive_id");
    $qstn_ctry = array();
    $ctry_data = $DB->get_record_sql("SELECT questions FROM {rsl_recruitment_drive} as brd join {test_questioncategory} as tq where brd.test = tq.test_id and brd.id = $drive_id ")->questions;
    $ctry_ids = explode(',' , $ctry_data);
    foreach($ctry_ids as $key => $value){
        $data_set = explode('-',$value);
        if($data_set[1] > 0){
                $qst_ctry = $DB->get_record_sql("SELECT id,name FROM {question_categories} where id = '$data_set[0]' ")->name;
                array_push($qstn_ctry,$qst_ctry);
            }
        }

    $driveid=$DB->get_record_sql("SELECT * FROM {rsl_recruitment_drive}  where id=$drive_id ");
    $proctoring =$DB->get_record_sql("SELECT eproctoringrequired From {quizaccess_eproctoring} ep join {rsl_recruitment_drive} ud where ud.test = ep.quizid and ud.id=$drive_id")->eproctoringrequired;
    // $proctoring =$DB->get_record_sql("SELECT proctoring From {test_questioncategory} tq join {rsl_recruitment_drive} rd where rd.test = tq.test_id and rd.id=$drive_id")->proctoring;
    // print_r($proctoring);exit();
    $itracqs = $DB->get_records_sql("SELECT itracqid FROM {rsl_user_detail} where recruitment_id = $drive_id");
    $itracqcount == 0;
    foreach($itracqs as $itracq)
        if($itracq->itracqid != 0)
            $itracqcount++;
    // print_r($itracqcount);exit();
    $userheading = $DB->get_records_sql("SELECT f.id,f.name,f.shortname FROM {user_info_field} f where f.categoryid = $companyid ORDER BY sortorder");
    // print_r($userheading);exit();
    $data .= '<div class="card"><div id="tableContainer" class="card-body table-responsive">
	<table id="user_data" class="table table-striped table-inverse table-bordered table-hover no-footer" cellspacing="0" width="100%">
	<thead>
        <tr>
            <th class="header c2" scope="col">Date OF Test</th>
            <th class="header c3" scope="col">Test Status</th>
            <th class="header c4" scope="col">User Status</th>
            <th class="header c5" scope="col">Recruitment Status</th>

            <th class="header c5" scope="col">Remarks </th>
            <th class="header c5" scope="col">RSL Due Date</th>';
            // <th class="header c5" scope="col">Interview Marks </th>
            if($itracqcount > 0){
                $data .= '<th class="header c6" scope="col">iTracQ id</th>';
            }
            foreach($userheading as $key => $val){
                $data .= '<th style="text-align:center">' . $val->name .'</th>';
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
            $data .='<th style="text-align:center">Grade </th>';
            foreach($qstn_ctry as $key => $value){
                $data .= '<th style="text-align:center">' . $value .'</th>';
            }
            $data .= '<th style="text-align:center">Status</th>
		</tr>
	</thead>';
    foreach ($idlist as $userdata) {
        $teststatus='';$grade='';
        // print_r($userdata);exit;

        // $r_user = $DB->get_records_sql("SELECT qa.*,rrd.name FROM {rsl_recruitment_drive} rrd   JOIN {quiz_attempts} qa ON qa.id = rrd.test where qa.userid=$userdata ");
        // $driveid=$DB->get_record_sql("SELECT * FROM {rsl_recruitment_drive}  where id=$drive_id ");
        if($driveid)
        $gradedetail = $DB->get_record_sql(" select round(((10/b.sumgrades) * a.sumgrades)* 10) quizper,a.timemodified as timemodified from mdl_quiz_attempts a join mdl_quiz b on a.quiz = b.id where b.id = $driveid->test and userid = $userdata order by a.timemodified desc limit 1");
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
        $drivedata=$DB->get_record_sql("SELECT * FROM {userstatus}  where userid=$userdata ORDER BY id DESC ");
        $drivestatus = $drivedata->userstatus;
        if($timeoftest != 'Not Yet Started' &&  empty($teststatus)){
            $teststatus='Fail';
        }
        if($userdata->rsl_due){
            $rsl_due=date('d/m/Y H:i:s', $userdata->rsl_due);
        }else{
            $rsl_due='-';
        }

        $remarkvar='';
        // $remarksdata = $DB->get_records_sql("SELECT id,remark,interviewtype,isrsl FROM {interview} WHERE userid = $userdata");
        $remarksdata = $DB->get_records_sql("SELECT id,remark,interviewtype,categoryscores FROM {interview} WHERE userid = $userdata");
        // echo'<pre>';print_r($remarksdata);exit();
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

            // $scorearray=array();
            // $score = "";
            // foreach ($remarksdata as $rkey => $rscore) {
            //     $intcat=explode(',', $rscore->categoryscores);
            //     if($rscore->isrsl == 1){
            //         $score='RSL Interview : ';
            //         $intcat=explode(',', $rscore->categoryscores);
            //         foreach($intcat as $key => $cat){
            //             $data_set = explode('-',$cat);
            //             if($data_set[1] > 0){
            //                 $ctry_name = $DB->get_record_sql("SELECT name FROM {question_categories} where id = '$data_set[0]' ")->name;
            //                 $score .= $ctry_name.'-' .$data_set[1].', ';
            //             }
            //         }
            //     }else if($rscore->interviewtype == 1){
            //         $score='Interview 1 : ';
            //         $intcat=explode(',', $rscore->categoryscores);
            //         foreach($intcat as $key => $cat){
            //             $data_set = explode('-',$cat);
            //             if($data_set[1] > 0){
            //                 $ctry_name = $DB->get_record_sql("SELECT name FROM {question_categories} where id = '$data_set[0]' ")->name;
            //                 $score .= $ctry_name.'-' .$data_set[1].', ';
            //             }
            //         }
            //     }else if($rscore->interviewtype == 2){
            //         $score='Interview 2 : ';
            //         $intcat=explode(',', $rscore->categoryscores);
            //         foreach($intcat as $key => $cat){
            //             $data_set = explode('-',$cat);
            //             if($data_set[1] > 0){
            //                 $ctry_name = $DB->get_record_sql("SELECT name FROM {question_categories} where id = '$data_set[0]' ")->name;
            //                 $score .= $ctry_name.'-' .$data_set[1].', ';
            //             }
            //         }
            //     }else if($rscore->interviewtype == 3){
            //         $score='Interview 3 : ';
            //         $intcat=explode(',', $rscore->categoryscores);
            //         foreach($intcat as $key => $cat){
            //             $data_set = explode('-',$cat);
            //             if($data_set[1] > 0){
            //                 $ctry_name = $DB->get_record_sql("SELECT name FROM {question_categories} where id = '$data_set[0]' ")->name;
            //                 $score .= $ctry_name.'-' .$data_set[1].', ';
            //             }
            //         }
            //     }
        
            //     array_push($scorearray,$score);
            // }
            // $finalscore=implode(' ', $scorearray);
        }

        $enrolled_users = $DB->get_record_sql("select mqa.id as attemptid,bu.userid,mcm.id as quizid,u.firstname,u.lastname,
        mqa.sumgrades,qz.sumgrades as sumg,bu.itracqid from mdl_rsl_user_detail bu JOIN mdl_user u ON u.id=bu.userid JOIN mdl_groups g
        ON g.id=bu.test_groupid JOIN mdl_rsl_recruitment_drive brd ON brd.id=bu.recruitment_id join mdl_quiz_attempts mqa on
        mqa.userid = bu.userid join {quiz} as qz on qz.id = mqa.quiz join mdl_course_modules as mcm on mcm.instance = qz.id
        WHERE bu.recruitment_id =$drive_id and u.id = $userdata");
        // $str_status = "Not Attemted;
        // if($value->testper >= 7){
        //     $str_status = "Passed";
        // }elseif($value->testper > 0 && $value->testper < 7){
        //     $str_status = "Failed";
        // }else{
        //     $str_status = "Not Attemted";
        // }
        if($enrolled_users){
            $data .= '<tr>';
                $data .= '<td style="text-align:center">'.$timeoftest.' </td>';
                $data .= '<td style="text-align:center">'.$teststatus.' </td>';
                $data .= '<td style="text-align:center">'.$userstatus.' </td>';
                $data .= '<td style="text-align:center">'.$drivestatus.' </td>';
                // $data .= '<td style="text-align:center">'.$finalscore.' </td>';
                $data .= '<td style="text-align:center">'.$remarkvar.' </td>';
                $data .= '<td style="text-align:center">'.$rsl_due.' </td>';
                if($itracqcount > 0){
                    $data .= '<td style="text-align:center">'.$enrolled_users->itracqid.' </td>';
                }
                // $data .= '<td style="text-align:center">'.$enrolled_users->firstname.' </td>';
                // $data .= '<td style="text-align:center">'.$enrolled_users->lastname.' </td>';
                foreach($userheading as $key1 => $val){
                    $userdata1 = $DB->get_record_sql("SELECT d.data FROM {user_info_data} d  where d.fieldid = $val->id and d.userid = $userdata")->data;
                    // print($userdata);
                    if($val->id && $userdata1){
                        $data .= '<td style="text-align:center">'.$userdata1.' </td>';
                    }else{
                        $data .= '<td style="text-align:center"> NA </td>';
                    }
                }
                if($proctoring == 1){
                    $voicedata = $DB->get_record_sql("SELECT * from {proctoringvoicewindow} where userid = $userdata and quizid = $enrolled_users->quizid ");
		            $mouth_open_count = $DB->get_record_sql("SELECT count(mouth_open_count) as total_count from {proctoringdetails} where userid = $userdata and quizid = $enrolled_users->quizid and mouth_open_count = 1 ");
                    $mobile_phone_count = $DB->get_record_sql("SELECT count(mobile_phone_count) as total_count from {proctoringdetails} where userid = $userdata and quizid = $enrolled_users->quizid and mobile_phone_count = 1 ");
                    $more_person = $DB->get_record_sql("SELECT count(more_person) as total_count from {proctoringdetails} where userid = $userdata and quizid = $enrolled_users->quizid and more_person = 1 ");
                    $no_person = $DB->get_record_sql("SELECT count(no_person) as total_count from {proctoringdetails} where userid = $userdata and quizid = $enrolled_users->quizid and no_person = 1 ");
                    $head_up = $DB->get_record_sql("SELECT count(head_up) as total_count from {proctoringdetails} where userid = $userdata and quizid = $enrolled_users->quizid and head_up = 1 ");
                    $head_down = $DB->get_record_sql("SELECT count(head_down) as total_count from {proctoringdetails} where userid = $userdata and quizid = $enrolled_users->quizid and head_down = 1 ");
                    $head_left = $DB->get_record_sql("SELECT count(head_left) as total_count from {proctoringdetails} where userid = $userdata and quizid = $enrolled_users->quizid and head_left = 1 ");
                    $head_right = $DB->get_record_sql("SELECT count(head_right) as total_count from {proctoringdetails} where userid = $userdata and quizid = $enrolled_users->quizid and head_right = 1 ");
                    $face_recognition = $DB->get_record_sql("SELECT count(face_recognition) as total_count from {proctoringdetails} where userid = $userdata and quizid = $enrolled_users->quizid and face_recognition = 1 ");
                    
		    //print_r($mouth_open_count->total_count);exit();
                    $windowcount = $voicedata->window_change - $voicedata->tab_change;
                    if($windowcount < 0)
                        $windowcount = 0;
                    $data .='<td class="header c2" scope="col">'. $voicedata->background_noise .'</td>';
                    $data .='<td class="header c2" scope="col">'. $voicedata->tab_change .'</td>';
                    $data .='<td class="header c2" scope="col">'. $windowcount .'</td>';
                    $data .='<td class="header c2" scope="col">'. $mouth_open_count->total_count .'</td>';
                    $data .='<td class="header c2" scope="col">'. $mobile_phone_count->total_count .'</td>';
                    $data .='<td class="header c2" scope="col">'. $more_person->total_count .'</td>';
                    $data .='<td class="header c2" scope="col">'. $no_person->total_count .'</td>';
                    $data .='<td class="header c2" scope="col">'. $head_up->total_count .'</td>';
                    $data .='<td class="header c2" scope="col">'. $head_down->total_count .'</td>';
                    $data .='<td class="header c2" scope="col">'. $head_left->total_count .'</td>';
                    $data .='<td class="header c2" scope="col">'. $head_right->total_count .'</td>';
                    $data .='<td class="header c2" scope="col">'. $face_recognition->total_count .'</td>';
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
            $data .='<td style="text-align:center"> Attempted </td>';   
        }
        $data .= '</tr>';
    }
  
    }

    $data .='</tbody></table></div></div></div>';
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
    $drivedate = date('d/m/Y', $DB->get_record('rsl_recruitment_drive',array('id' => $drive_id))->startdate);
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
