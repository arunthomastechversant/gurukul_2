<?php
require_once(dirname(__FILE__).'/../../config.php');
global $DB,$CFG,$PAGE,$USER,$SESSION;
require_once($CFG->dirroot . '/course/lib.php');
global $DB, $CFG,$USER;
// print_r("etst");exit;
if($_POST['user']){
	
	
	$userid =$_POST['user'];
	$catsql=$DB->get_record_sql("SELECT * FROM {interview}  where userid=$userid ORDER BY id desc");
	$category = explode(',',$catsql->category);
	// echo '<pre>';print_R($category);die; 
	
	foreach($category as $catk => $catv){
		$quescat = explode('-',$catv);
		$interviewcat[$quescat[0]] = "id_category_".$quescat[0];
	}
	// echo '<pre>';print_R($interviewcat);die;
	echo json_encode($interviewcat);
}

?>
