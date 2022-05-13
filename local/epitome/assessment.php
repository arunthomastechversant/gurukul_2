<?php

header('Access-Control-Allow-Origin: https://dev.beta.epitome.ai');
header('Access-Control-Allow-Origin: https://beta.epitome.ai');
header('Access-Control-Allow-Origin: http://localhost:4100');
header('Access-Control-Allow-Origin: https://localhost:4200');
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
$quizJSON = file_get_contents('php://input');;
$quizData = json_decode($quizJSON ,true);

if($quizData){
    $quizids = explode(',',$quizData['quizid']);
    $quizdetails = array();
    foreach($quizids as $quizid){
        $quizdata = $DB->get_record('quiz', array('id' => $quizid));
        $userattempts = $DB->get_records_sql("SELECT qa.sumgrades,u.firstname,u.lastname,u.epitomeuserid FROM {quiz_attempts} as qa join {user} as u on u.id = qa.userid where qa.quiz = $quizid and u.deleted = 0 ORDER BY qa.id DESC");
        $userdetails = array();
        if($userattempts){
            foreach($userattempts as $attempt){
                $row1['userid'] = $attempt->epitomeuserid;
                $row1['name'] = fullname($attempt);
                $row1['mark'] = $attempt->sumgrades;
                array_push($userdetails,$row1);
            }
            $row['quizid'] = $quizdata->id;
            $row['quizname'] = $quizdata->name;
            $row['userdetails'] = $userdetails;
            array_push($quizdetails,$row);
        }
    }
echo json_encode($quizdetails);
}