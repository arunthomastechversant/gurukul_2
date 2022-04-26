<?php
require('../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
global $DB, $CFG,$USER;



$catsql = implode($_POST['user2']);

if($catsql)
{

$test = $DB->get_records_sql("select name from mdl_question_categories where id = ".$catsql);
    
echo (json_encode($test));

die;

}

?>