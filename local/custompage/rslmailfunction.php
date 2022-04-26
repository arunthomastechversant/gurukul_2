<?php
require(__DIR__.'/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
global $DB,$USER,$COURSE,$CFG,$PAGE,$OUTPUT;
if($_POST['idlist']){
    $datas = $_POST['idlist'];
    foreach($datas as $data){
        $course=$DB->get_record_sql("select *  from {course} where  shortname ='rsl-test' ");
        $quizzz = $DB->get_record_sql("SELECT * FROM {modules} WHERE name = 'quiz'");
        $instance = $DB->get_record_sql("SELECT rud.recruitment_id,rrd.test FROM {rsl_user_detail} rud Join {rsl_recruitment_drive} rrd ON rrd.id= rud.recruitment_id where rud.userid=$data");
        $cmiddata = $DB->get_record_sql("SELECT * FROM {course_modules} WHERE course = $course->id AND module = $quizzz->id AND instance = $instance->test");
        $attemptdata = $DB->get_record_sql("SELECT * FROM {quiz_attempts} WHERE userid = $data AND quiz = $instance->test");
        
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
        foreach($byGroup_category as $kkey => $subcats)
        {
            $category_id  = $kkey;
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


        // $rslquest = $DB->get_record_sql("SELECT rrd.test,tq.questions  FROM {rsl_recruitment_drive} rrd  JOIN {test_questioncategory} tq ON tq.test_id = rrd.test where rrd.id=$courseid");
        
		// $pro=explode(',',$rslquest->questions);  
        $courses = "";
		if($final_result){
            $enrol = enrol_get_plugin('manual'); 
            // $categoryarray=array();

			foreach($final_result as $fkey => $fval) {
                
                if($fval <= 50){    
                    if($courses !="")
                        $courses .=', ';                          
                    $qcourse=$DB->get_record_sql("SELECT * FROM {question_category_mapping}  where question_categoryid=$fkey")->courseid;
                    $courses .= $DB->get_record('course',array('id' => $qcourse))->fullname; ; 
                }
            }
        }     

        $userdetails = $DB->get_record('rsl_user_detail',array('userid' => $data));
        $firstname = $DB->get_record('user_info_data',array('fieldid' => 81,'userid' => $data))->data; 
        $courses   = $courses;
        $username  = $userdetails->username;
        $password  = $userdetails->password;
        $search    = array("{{firstname}}", "{{courses}}", "{{username}}", "{{password}}");
        $replace   = array($firstname, $courses , $username, $password );

        $maildata = $DB->get_record('mail_templates', array('company_id' =>1 ,'type_id' => 1));
        $mailcontent = str_replace($search,$replace,$maildata->content ); 
        $curl=curl_init();
	curl_setopt($curl,CURLOPT_URL,'https://qbpm.quest-global.com:8081/common-api/mail/send');
	curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,2);
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
	//curl_setopt($curl, CURLOPT_HEADER, true);
	$mailid = $DB->get_record('user_info_data',array('fieldid' => 80,'userid' => $data))->data;
	$subject = $maildata->subject;
	//print_r($mailid);exit();
	$headers = array("Authorization" => "Basic U2NoZWR1bGVyOjFRdUVTVGhkYiFAIzFRdUVTVA==", "Content-Type" => "application/json");
	$data = array("from" => "no-replay@quest-global.com", "to"=> $mailid, "subject" => $subject, "isHtml" => "true", "text" => $mailcontent, "isProd" => "true");
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic U2NoZWR1bGVyOjFRdUVTVGhkYiFAIzFRdUVTVA==','Content-Type: application/json'));
	//curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
	$buffer = json_decode(curl_exec($curl), true);
	$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);

	if ($httpcode == 200){
   		print "your email has been sent sccessfully";
	}else if ($httpcode == 401){
   		print "Unauthorized";
	}else if ($httpcode == 404){
   		print "Not Found";
	}else if ($httpcode == 500){
   		print "Something went wrong!";
	}
    }
    //echo 'Mail Sent Successfully';
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
