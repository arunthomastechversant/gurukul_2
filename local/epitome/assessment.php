<?php

$domains = array('https://dev.beta.epitome.ai','https://beta.epitome.ai','http://localhost:4100','https://localhost:4200');
header('Access-Control-Allow-Origin: '.$domains);
// header('Access-Control-Allow-Origin: https://dev.beta.epitome.ai');
// header('Access-Control-Allow-Origin: https://beta.epitome.ai');
// header('Access-Control-Allow-Origin: http://localhost:4100');
// header('Access-Control-Allow-Origin: https://localhost:4200');
header('Access-Control-Allow-Methods: POST,GET,OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Content-Type, Accept');


// This file is part of Techversant Api moodle plugin

/**
 * This library is Techversant Api Login handler.
 *
 * Redirect here for saml request and response purpose
 *
 * @copyright   2022  Techversant
 * @category    Assessment Mark listing based on Quiz id
 * @package     epitome
 */

require(__DIR__.'/../../config.php');
require(__DIR__.'/../../auth/Epitome/authentication.php');
$quizJSON = file_get_contents('php://input');;
$quizData = json_decode($quizJSON ,true);

if(validateBearerToken()){
    if($quizData['quizid']){
        $quizdetails = array();
        foreach($quizData['quizid'] as $quizid){
            if($DB->record_exists_select('quiz', 'id = ?', array($quizid))){
                $quizdata = $DB->get_record('quiz', array('id' => $quizid));
                $userattempts = $DB->get_records_sql("SELECT qa.sumgrades,u.firstname,u.lastname,u.epitomeuserid,q.sumgrades as maxmark FROM {quiz_attempts} as qa join {quiz} as q on q.id = qa.quiz join {user} as u on u.id = qa.userid where qa.quiz = $quizid and u.deleted = 0 and qa.state = 'finished' ORDER BY qa.id DESC");
                $userdetails = array();
                if($userattempts){
                    foreach($userattempts as $attempt){
                        $row1['userid'] = $attempt->epitomeuserid;
                        $row1['name'] = fullname($attempt);
                        $row1['maxmark'] = $attempt->maxmark;
                        $row1['obtainedmark'] = $attempt->sumgrades;
                        array_push($userdetails,$row1);
                    }
                    $row['quizid'] = $quizdata->id;
                    $row['quizname'] = $quizdata->name;
                    $row['userdetails'] = $userdetails;
                    array_push($quizdetails,$row);
                }
            }
        }
    header('HTTP/1.1 200 OK');
    echo json_encode($quizdetails);
    }
}else{
    $response = array();
    $response['message'] = 'Unauthorized Access';
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode($response);
}