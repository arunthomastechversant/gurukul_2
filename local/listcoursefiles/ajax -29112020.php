<?php
require('../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
global $DB, $CFG,$USER;
if($_POST['user']){
	
	$catsql = $DB->get_record_sql("select userid,category,COALESCE(round((sum(interviewscore)+ max(testscore))/count(id)),0) as average from mdl_interview where userid = ".$_POST['user']." group by userid,category order by id desc");
	
	//echo '<pre>';print_R($catsql);die;
	
	$category = explode(',',$catsql->category); 
	$i = 1;
	foreach($category as $catk => $catv){
		$quescat = explode('-',$catv);
		$interviewcat[$i] = $quescat[1] ? "id_category_".$quescat[0] : 0;
		$i++;
	}
	$interviewcat = array_filter($interviewcat);
	$interviewcat['average'] = $catsql->average;
	// print_r($interviewcat);exit;
	echo (json_encode($interviewcat)); die;
	//print_R($interviewcat);die;
}
elseif($_POST['average']){
	
	//~ $average = $_POST['average']);
	
	//~ if($average >= 70){
		
	//~ }
	
}
?>
