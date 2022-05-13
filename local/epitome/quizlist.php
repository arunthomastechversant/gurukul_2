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
 * @category    Quiz listing
 * @package     epitome
 */

require(__DIR__.'/../../config.php');
$companyid = 5;
$quizlist = $DB->get_records_sql("SELECT q.id,name,tq.questions FROM {quiz} as q join {course} as c on c.id = q.course join {company_course} as cc on cc.courseid = c.id join {test_questioncategory} as tq on tq.test_id = q.id where cc.companyid = $companyid ORDER BY q.id DESC");
$response = array();
$response['message'] = 'Success';
$quizdetails = array();
foreach($quizlist as $quiz){
    $questions = explode(',', $quiz->questions);
    $questioncategory = array();
    foreach($questions as $question){
        $category = explode('-', $question);
        if($category[1] > 0){
            $row1['category'] = $category[0];
            $row1['categoryname'] = $DB->get_record('question_categories', array('id'=>$category[0]))->name;
            array_push($questioncategory,$row1);
        }
    }
    $row['quizid'] = $quiz->id;
    $row['quizname'] = $quiz->name;
    $row['questioncategories'] = $questioncategory;
    array_push($quizdetails,$row);

}

echo json_encode($quizdetails);