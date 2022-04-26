<?php
require('../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
global $DB, $CFG,$USER,$SESSION;
$quizid  = optional_param('id', '', PARAM_INT);

if($_POST['dataurl']  && $_POST['userid'] && $_POST['quizid'] ){

	$context = context_user::instance($_POST['userid'], MUST_EXIST);
	$fs = get_file_storage();
	if ($files = $fs->get_area_files($context->id, 'local_custompage', 'imagefile',false, 'sortorder', false)) 
    {
        foreach ($files as $file)
        { 
            $imagepath = moodle_url::make_pluginfile_url($context->id, 'local_custompage', 'imagefile', $file->get_itemid(), $file->get_filepath(), $file->get_filename());
        }
        $imagepath = $imagepath->__toString();
    }
	if ($prodetail = $DB->get_record('user_proctoringimages', array('userid' => $_POST['userid'],'quizid' => $_POST['quizid']))) {
	// print_r($prodetail );exit;
		$record2 = new stdClass();
		$record2->id = $prodetail->id;	
		$record2->userimage =  $_POST['dataurl'];	
		$record2->profileimagepath =  $imagepath;	
		// print_r($record2);exit;
		$DB->update_record('user_proctoringimages', $record2);
		$interviewcat = 'Success';
		$_SESSION['validated']=1;
	}else{
		$record2 = new stdClass();
		$record2->name = $USER->username;
		$record2->userid =  $_POST['userid'];	
		$record2->quizid =  $_POST['quizid'];	
		$record2->userimage =  $_POST['dataurl'];
		$record2->profileimagepath =  $imagepath;	
		$insert=$DB->insert_record('user_proctoringimages', $record2);
		if($insert){
			$interviewcat = 'Success';
		}
		$_SESSION['validated']=1;
	}

	$return_arr = array("response" => $interviewcat);

// Encoding array in JSON format
echo json_encode($return_arr);

}
// Tab Changed
if($_POST['tab']  && $_POST['userid'] && $_POST['quizid'] ){


	if ($prodetail = $DB->get_record('proctoringvoicewindow', array('userid' => $_POST['userid'],'quizid' => $_POST['quizid']))) {
	// print_r($prodetail );exit;
		$record2 = new stdClass();
		$record2->id = $prodetail->id;	
		$record2->tab_change =  $prodetail->tab_change+1;	
		// print_r($record2);exit;
		$DB->update_record('proctoringvoicewindow', $record2);
		$interviewcat = 'Success';

	}else{
		$record2 = new stdClass();
		$record2->userid =  $_POST['userid'];	
		$record2->quizid =  $_POST['quizid'];	
		$record2->background_noise =  0;
		$record2->window_change =  0;
		$record2->tab_change =  1;
		$insert=$DB->insert_record('proctoringvoicewindow', $record2);
		if($insert){
			$interviewcat = 'Success';
		}

	}
	$return_arr = array("response" => $interviewcat);


// Encoding array in JSON format
echo json_encode($return_arr);

}


// Window Changed
if($_POST['window']  && $_POST['userid'] && $_POST['quizid'] ){

	if ($prodetail = $DB->get_record('proctoringvoicewindow', array('userid' => $_POST['userid'],'quizid' => $_POST['quizid']))) {
	// print_r($prodetail );exit;
		$record2 = new stdClass();
		$record2->id = $prodetail->id;	
		$record2->window_change =  $prodetail->window_change+1;	
		// print_r($record2);exit;
		$DB->update_record('proctoringvoicewindow', $record2);
		$interviewcat = 'Success';

	}else{
		$record2 = new stdClass();
		$record2->name = $USER->username;
		$record2->userid =  $_POST['userid'];	
		$record2->quizid =  $_POST['quizid'];	
		$record2->background_noise =  0;
		$record2->window_change =  1;
		$record2->tab_change =  0;
		$insert=$DB->insert_record('proctoringvoicewindow', $record2);
		if($insert){
			$interviewcat = 'Success';
		}

	}
	$return_arr = array("response" => $interviewcat);


// Encoding array in JSON format
echo json_encode($return_arr);

}
// volume Changed
if($_POST['volume']  && $_POST['userid'] && $_POST['quizid'] ){

	if ($prodetail = $DB->get_record('proctoringvoicewindow', array('userid' => $_POST['userid'],'quizid' => $_POST['quizid']))) {
	// print_r($prodetail );exit;
		$record2 = new stdClass();
		$record2->id = $prodetail->id;	
		$record2->background_noise =  $prodetail->background_noise+1;	
		// print_r($record2);exit;
		$DB->update_record('proctoringvoicewindow', $record2);
		$interviewcat = 'Success';

	}else{
		$record2 = new stdClass();
		$record2->name = $USER->username;
		$record2->userid =  $_POST['userid'];	
		$record2->quizid =  $_POST['quizid'];	
		$record2->background_noise =  1;
		$record2->window_change =  0;
		$record2->tab_change =  0;
		$insert=$DB->insert_record('proctoringvoicewindow', $record2);
		if($insert){
			$interviewcat = 'Success';
		}

	}
	$return_arr = array("response" => $interviewcat);


// Encoding array in JSON format
echo json_encode($return_arr);

}
?>
