<?php
ini_set('display_errors', '1');
define('CLI_SCRIPT', true);
@set_time_limit(0);
echo 'Query Execution Starts at '.date('d/m/y h:i:s').'<br><br>';

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->dirroot.'/local/coursecompletion/lib.php');
//print_r($CFG->dirroot.'/local/coursecompletion/lib.php');

require_once($CFG->dirroot.'/local/coursecompletion/lib.php');

//echo "rara";exit;
$cronlogstarttime           = 'Query Execution Starts at '.date('d/m/y h:i:s');
$timestarted                = time();
//Course completion Function 

$course_status              = coursestatus();

$timemodified               = time();
$cronlogstoptime            = 'Query Execution Ends at '.date('d/m/y h:i:s');
$cronobj                    =  new stdClass; 
$cronobj->course_status  	= $course_status;
$cronobj->cronlogstarttime	= $cronlogstarttime;
$cronobj->cronlogstoptime	= $cronlogstoptime;
$cronobj->timestarted	    = $timestarted;
$cronobj->timemodified   	= $timemodified;


echo 'Query Execution Ends at '.date('d/m/y h:i:s').'<br><br>'."\n\n";
?>
