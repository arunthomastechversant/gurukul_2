<?php
require('../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
global $DB, $CFG,$USER;
if($_POST['user']){

	$where = '';
	// $where = isset($_POST['edit']) && !empty($_POST['edit']) ? "AND id != ".$_POST['edit'] : '' ;
	// $catsql = $DB->get_record_sql("select userid,category,COALESCE(round((sum(interviewscore)+ max(testscore))/count(id)),0) as average from mdl_interview where userid = ".$_POST['user']." $where  group by userid,category order by id desc");
	
	$catsql = $DB->get_records_sql("select * from mdl_interview where userid = ".$_POST['user']);
	
	//echo '<pre>';print_R($catsql);die;
	foreach($catsql as $ck => $cv) {
		$category = explode(',',$cv->category); 
		$testscore[] = $cv->testscore;
		if(isset($_POST['edit']) && !empty($_POST['edit']) && $cv->id == ($_POST['edit'])){
			$interviewscore[] = '';
		}else{
			$interviewscore[] = $cv->interviewscore;
		}
	}
	$average = round((array_sum($interviewscore)+ max($testscore))/count($interviewscore));
	
		$i = 1;
		foreach($category as $catk => $catv){
			$quescat = explode('-',$catv);
			$interviewcat[$i] = $quescat[1] ? "id_category_".$quescat[0] : 0;
			$i++;
		}
	
	$interviewcat = array_filter($interviewcat);
	$interviewcat['average'] = $average;
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
