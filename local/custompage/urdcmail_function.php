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
    $total_qstn = array();
    $total_count = 0;
    $ctry_data = $DB->get_record_sql("SELECT questions FROM {urdc_recruitment_drive} as urd join {test_questioncategory} as tq where urd.test = tq.test_id and urd.id = $drive_id ")->questions;
    $ctry_ids = explode(',' , $ctry_data);
    foreach($ctry_ids as $key => $value){
        $data_set = explode('-',$value);
        if($data_set[1] > 0){
                $qst_ctry = $DB->get_record_sql("SELECT name FROM {question_categories} where id = '$data_set[0]' ")->name;
                array_push($qstn_ctry,$qst_ctry);
		        array_push($total_qstn,$data_set[1]);
                $total_count += $data_set[1];
            }
        }
    // print_r($qstn_ctry); 
    // $proctoring =$DB->get_record_sql("SELECT eproctoringrequired From {quizaccess_eproctoring} ep join {lead_test} lt where ep.quizid = tq.test_id and lt.courseid=$drive_id")->eproctoringrequired;
    $proctoring =$DB->get_record_sql("SELECT eproctoringrequired From {quizaccess_eproctoring} ep join {urdc_recruitment_drive} ud where ud.test = ep.quizid and ud.id=$drive_id")->eproctoringrequired;
    // print_r($proctoring);exit();
    $userheading = $DB->get_records_sql("SELECT f.id,f.name,f.shortname FROM {user_info_field} f where f.categoryid = $companyid and shortname != 'urdccollegename' ORDER BY sortorder");
    $data .= '<div class="card"><div id="tableContainer" class="card-body table-responsive">
	<table id="user_data" class="table table-striped table-inverse table-bordered table-hover no-footer" cellspacing="0" width="100%">
	<thead>
    <tr>';
            foreach($userheading as $key => $val){
                $data .= '<th style="text-align:center">' . $val->name .'</th>';
            }
            foreach($qstn_ctry as $key => $value){
                $data .= '<th style="text-align:center">' . $value .' / '.$total_qstn[$key].'</th>';
            }
            $data .='<th style="text-align:center">Total Score /'.$total_count.' </th>';
            $data .='<th style="text-align:center">Overall Percentage </th>';
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
		      <th style="text-align:center">Profile Image</th>
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
        
        // $datas = $DB->get_records_sql("SELECT u.* FROM  {urdc_user_detail} as uu join {user} as u WHERE uu.userid = u.id and uu.recruitment_id = $drive_id");
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
            $enrolled_users = $DB->get_record_sql("select mqa.id as attemptid,u.firstname,u.lastname,u.email,mcm.id as quizid,uu.userid,mqa.sumgrades,qz.sumgrades as sumg from mdl_urdc_user_detail uu JOIN mdl_user u ON u.id=uu.userid JOIN mdl_groups g 
            ON g.id=uu.test_groupid JOIN mdl_urdc_recruitment_drive brd ON brd.id=uu.recruitment_id join mdl_quiz_attempts mqa on mqa.userid = uu.userid join {quiz} as qz on qz.id = mqa.quiz join mdl_course_modules as mcm on mcm.instance = qz.id
            WHERE uu.recruitment_id =$drive_id and u.id = $value");
            // $str_status = "Not Attemted";
            // if($value->testper >= 7){
            //     $str_status = "Passed";
            // }elseif($value->testper > 0 && $value->testper < 7){
            //     $str_status = "Failed";
            // }else{
            //     $str_status = "Not Attemted";
            // }

            if($enrolled_users){
                //print_r($enrolled_users);exit();
                $data .= '<tr>';
                foreach($userheading as $key1 => $val){
                    $userdata = $DB->get_record_sql("SELECT d.data,f.datatype FROM {user_info_data} d join {user_info_field} f on f.id = d.fieldid where d.fieldid = $val->id and d.userid = $value");
                    if($val->id && $userdata){
                        if($userdata->datatype == 'datetime'){
                            $data .= '<td style="text-align:center">'.date('d/m/Y', $userdata->data).' </td>';
                        }else if($userdata->data == ""){
                            $data .= '<td style="text-align:center"> NA </td>';
                        }else{
                            if($userdata->data == 'Others'){
                                $collagename = $DB->get_record_sql("SELECT data FROM {user_info_data} where fieldid = 84 and userid = $value")->data;
                                $data .= '<td style="text-align:center">'.$collagename.'</td>';
                            }else{
                                $data .= '<td style="text-align:center">'.$userdata->data.' </td>';
                            }
                        }
                    }else{
                        $data .= '<td style="text-align:center"> NA </td>';
                    }
                    
                }
                $userid = $enrolled_users->userid;  // user id
		//print_r($userid);exit();
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
                    $percentage = $attempt_data->fraction;
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
                    //$final_percentage = $total/$count;
                    $final_result[$category_id] = round($total,2);
                    
                }
                //print_r($total);
                // $str_category = "";
                // foreach($final_result as $cat_score => $result){
                //     $category_data = $DB->get_record_sql("SELECT id,name FROM {question_categories} where id = $cat_score");
                //     $str_category .= $category_data->name .'   -   '. $result .'% </br>';
                // }
            // print_r();exit();
            // $data .='<td style="text-align:center"> '.round($enrolled_users->sumgrades,2). '/' .round($enrolled_users->sumg,2).'</td>';   
            if($enrolled_users){
                $totalgarde = 0;
                foreach($final_result as $cat_score => $result){
                    $data .='<td style="text-align:center"> '.$result.' </td>';
                    $totalgarde += $result;
                }
                $overall = $totalgarde / $total_count * 100;
                // $timetaken = $enrolled_users->timemodified - $enrolled_users->timestart;
                $diff = $enrolled_users->timemodified - $enrolled_users->timestart;
                $test = round($diff / 60,2);
                $data .='<td style="text-align:center"> '.$totalgarde.'</td>';    
                $data .='<td style="text-align:center"> '.round($overall,2).'</td>';    
                $data .='<td style="text-align:center"> '.date('d/M/Y H:i:s',$enrolled_users->timestart).'</td>';    
                $data .='<td style="text-align:center"> '.date('d/M/Y H:i:s',$enrolled_users->timemodified).'</td>';    
                $data .='<td style="text-align:center"> '.$test.'</td>';   
            }
            if($proctoring == 1){
                $voicedata = $DB->get_record_sql("SELECT * from {proctoringvoicewindow} where userid = $value and quizid = $enrolled_users->quizid ");
                //$proctoringdata = $DB->get_records_sql("SELECT * from {proctoringdetails} where userid = $value and quizid = $enrolled_users->quizid ");
                    $mouth_open_count = $DB->get_record_sql("SELECT count(mouth_open_count) as total_count from {proctoringdetails} where userid = $value and quizid =$enrolled_users->quizid and mouth_open_count = 1 ");
                    $mobile_phone_count = $DB->get_record_sql("SELECT count(mobile_phone_count) as total_count from {proctoringdetails} where userid = $value and quizid = $enrolled_users->quizid and mobile_phone_count = 1 ");
                    $more_person = $DB->get_record_sql("SELECT count(more_person) as total_count from {proctoringdetails} where userid = $value and quizid = $enrolled_users->quizid and more_person = 1 ");
                    $no_person = $DB->get_record_sql("SELECT count(no_person) as total_count from {proctoringdetails} where userid = $value and quizid = $enrolled_users->quizid and no_person = 1 ");
                    $head_up = $DB->get_record_sql("SELECT count(head_up) as total_count from {proctoringdetails} where userid = $value and quizid = $enrolled_users->quizid and head_up = 1 ");
                    $head_down = $DB->get_record_sql("SELECT count(head_down) as total_count from {proctoringdetails} where userid = $value and quizid = $enrolled_users->quizid and head_down = 1 ");
                    $head_left = $DB->get_record_sql("SELECT count(head_left) as total_count from {proctoringdetails} where userid = $value and quizid = $enrolled_users->quizid and head_left = 1 ");
                    $head_right = $DB->get_record_sql("SELECT count(head_right) as total_count from {proctoringdetails} where userid = $value and quizid = $enrolled_users->quizid and head_right = 1 ");
                    $face_recognition = $DB->get_record_sql("SELECT count(face_recognition) as total_count from {proctoringdetails} where userid = $value and quizid = $enrolled_users->quizid and face_recognition = 1 ");
                    

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
            $context = context_user::instance($value, MUST_EXIST);
            $fs = get_file_storage();
            if ($files = $fs->get_area_files($context->id, 'local_custompage', 'imagefile',false, 'sortorder', false)) 
            {
            
                foreach ($files as $file)
                { 
                    $imagepath = moodle_url::make_pluginfile_url($context->id, 'local_custompage', 'imagefile', $file->get_itemid(), $file->get_filepath(), $file->get_filename());
                }
                $imagepath = $imagepath->__toString();
            }
            $data .='<td style="text-align:center"> Attempted </td>';  
            if($imagepath == ""){
                $data .='<td style="text-align:center"></td>';  
            }else{
                $data .='<td style="text-align:center"> <a target="__blank" href=" '.$imagepath.'">View</a> </td>';  
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
    $drivedate = date('d/m/Y', $DB->get_record('urdc_recruitment_drive',array('id' => $drive_id))->startdate);
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

