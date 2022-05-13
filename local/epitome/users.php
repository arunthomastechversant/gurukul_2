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
$userJSON = file_get_contents('php://input');;
$userData = json_decode($userJSON ,true);

if($userData){
    $userids = explode(',',$userData['userid']);
    $userdetails = array();
    foreach($userids as $userid){
        $userdata = $DB->get_record('user', array('epitomeuserid' => $userid));
        $userattempts = $DB->get_records_sql("SELECT q.name,q.id,qa.sumgrades FROM {quiz_attempts} as qa join {quiz} as q on q.id = qa.quiz join {user} as u on u.id = qa.userid where u.epitomeuserid = $userid ORDER BY qa.id DESC");
        $quizetails = array();
        if($userattempts){
            foreach($userattempts as $attempt){
                $row1['quizid'] = $attempt->id;
                $row1['quizname'] = $attempt->name;
                $row1['mark'] = $attempt->sumgrades;
                array_push($quizetails,$row1);
            }
            $row['userid'] = $userdata->epitomeuserid;
            $row['name'] = fullname($userdata);
            $row['quizetails'] = $quizetails;
            array_push($userdetails,$row);
        }
    }
echo json_encode($userdetails);
}