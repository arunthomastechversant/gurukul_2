<?php

require_once(dirname(__FILE__).'/../../config.php');
require_once($CFG->libdir.'/dataformatlib.php');
global $DB,$CFG,$PAGE,$USER,$SESSION;

// echo $searchquery ;exit;
$dataformat = optional_param('dataformat', '', PARAM_ALPHA);
$cid = optional_param('cid', '', PARAM_RAW);
$report = optional_param('report', '', PARAM_RAW);
$orgid = optional_param('orgid', '', PARAM_RAW);

// echo $searchquery ;exit;


    $columns = array(
        'sno' => 'S.No',
        'username' => 'Username',
        'firstname' => 'First Name',
        'lastname' => 'Last Name',
        'email' => 'Email',
        'status' => 'Status'
    );


    $columns = array(
        'sno' => 'S.No',
        'username' => 'Username',
        'firstname' => 'First Name',
        'lastname' => 'Last Name',
        'email' => 'Email',
        'status' => 'Status'
    ); 
if($report == 1){
    $enrolled_users = $DB->get_recordset_sql("SELECT @a:=@a+1 AS sno ,u.username AS username,u.firstname AS firstname,u.lastname AS lastname,u.email AS email,'Enrolled' AS status FROM mdl_user u JOIN (SELECT DISTINCT mu.id FROM mdl_user mu JOIN mdl_user_enrolments ue ON ue.userid = mu.id JOIN mdl_enrol me ON (me.id = ue.enrolid AND me.courseid = '".$cid."' ) INNER JOIN mdl_company_users cu  WHERE 1 = 1 AND mu.deleted = 0 AND cu.companyid='".$orgid."' ) je ON je.id = u.id INNER JOIN mdl_company comp INNER JOIN (SELECT @a:= 0) as sno WHERE u.deleted = 0   AND comp.id='".$orgid."'" );
} 
if($report == 2){
    $compleated = $DB->get_record_sql("select GROUP_CONCAT(cc.userid) AS userid from mdl_course_completions cc INNER JOIN mdl_company_users cu ON cc.userid = cu.userid where cc.timestarted != 0 AND  cu.companyid = '" . $orgid . "' AND cc.timecompleted != ''  AND cc.course = '" . $cid . "'");

    $enrolled_users =$DB->get_recordset_sql("SELECT @a:=@a+1 AS sno ,u.username AS username,u.firstname AS firstname,u.lastname AS lastname,u.email AS email,'Compleated' AS status  from mdl_user u INNER JOIN (SELECT @a:= 0) as sno INNER JOIN mdl_company comp where u.id IN ($compleated->userid) AND comp.id='".$orgid."'");
}
if($report == 3){
    $compleated = $DB->get_record_sql("select GROUP_CONCAT(cc.userid) AS userid from mdl_course_completions cc INNER JOIN mdl_company_users cu ON cc.userid = cu.userid where cc.timestarted != 0 AND  cu.companyid = '" . $orgid . "' AND cc.timecompleted IS NULL  AND cc.course = '" . $cid . "'");
        
    $enrolled_users =$DB->get_recordset_sql("SELECT @a:=@a+1 AS sno ,u.username AS username,u.firstname AS firstname,u.lastname AS lastname,u.email AS email,'In Progress' AS status  from mdl_user u INNER JOIN (SELECT @a:= 0) as sno INNER JOIN mdl_company comp where u.id IN ($compleated->userid) AND comp.id='".$orgid."'");
}
if($report == 4){
    $compleated = $DB->get_record_sql("select GROUP_CONCAT(cc.userid) AS userid from mdl_course_completions cc INNER JOIN mdl_company_users cu ON cc.userid = cu.userid where cc.timestarted = 0 AND  cu.companyid = '" . $orgid . "' AND cc.course = '" . $cid . "'");
        
    $enrolled_users =$DB->get_recordset_sql("SELECT @a:=@a+1 AS sno ,u.username AS username,u.firstname AS firstname,u.lastname AS lastname,u.email AS email,'Yet to start' AS status  from mdl_user u INNER JOIN (SELECT @a:= 0) as sno INNER JOIN mdl_company comp where u.id IN ($compleated->userid) AND comp.id='".$orgid."'");
}

$filename = "rslscore_report_".date("d-m-Y");

download_as_dataformat($filename, $dataformat, $columns, $enrolled_users);
$enrolled_users->close();


?>