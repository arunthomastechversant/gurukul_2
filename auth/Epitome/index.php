<?php
// This file is part of Techversant Api moodle plugin

/**
 * This library is Techversant Api Login handler.
 *
 * Redirect here for saml request and response purpose
 *
 * @copyright   2022  Techversant
 * @category    authentication
 * @package     techapi
 */


require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/course/modlib.php');
require_once($CFG->dirroot.'/mod/quiz/mod_form.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot.'/group/lib.php');
require_once($CFG->dirroot.'/group/group_form.php');
require_once($CFG->dirroot. '/group/lib.php');
require_once($CFG->dirroot . '/user/selector/lib.php');
require_once($CFG->dirroot . '/course/lib.php');


global $CFG, $USER, $SESSION;
global $_POST, $_GET, $_SERVER;

$quizurl = $CFG->wwwroot.'/auth/Epitome/success.php';

if (isset($_POST['email']) && isset($_POST['courseid']) && isset($_POST['quizid']) && $_POST['email'] != "" && $_POST['courseid'] != "" && $_POST['quizid'] !="") {
    $courseid = $_POST['courseid'];
    $quizid = $_POST['quizid'];
    $quiz = $DB->get_record_sql("SELECT cm.id as cmid FROM {course_modules} cm where cm.instance = $quizid and cm.course = $courseid and cm.module = 18");
    $quizurl = $CFG->wwwroot.'/mod/quiz/view.php?id='.$quiz->cmid;
    if ($DB->record_exists_select('user', 'email = ?', array($_POST['email']))){
        if($DB->record_exists_select('user', 'email = ? and auth = ? ', array($_POST['email'],'manual'))){
            $userid = $DB->get_record('user',array('email' => $_POST['email'], 'auth' => 'manual'))->id;
            $USER->loggedin = true;
            $USER->site = $CFG->wwwroot;
            $USER = get_complete_user_data('id', $userid);
            // Everywhere we can access user by its id.
            complete_user_login($USER);
            header('Location: '.$quizurl); 
        }else{
            echo 'User Exist In This Email...!';
        }
    }else{
        $user = new stdClass();
        // This array contain and return the value of attributes which are mapped.
        if (!empty($_POST['email'])) {
            $user->email = $_POST['email'];
            $user->username = $_POST['email'];
        }
        if (!empty($_POST['firstname'])) {
            $user->firstname = $_POST['firstname'];
        }
        if (!empty($_POST['lastname'])) {
            $user->lastname = $_POST['lastname'];
        }
        $companyid = 5;
        $company = new company($companyid);
        $user->companyname = $company->get_name();
        $parentlevel = company::get_company_parentnode($company->id);
        $user->userdepartment = $parentlevel->id;
        $user->userid = 52;
        $user->newpassword = 'Epitome@1234';
        $user->companyid = $company->id;

        if (!$userid = company_user::create($user)) {
            $this->verbose("Error inserting a new user in the database!");
            if (!$this->get('ignore_errors')) {
                die();
            }
        }
        $enrol = enrol_get_plugin('manual');
        $cids=$DB->get_record_sql("select GROUP_CONCAT(DISTINCT courseid) AS courseid from {company_course} where companyid=$companyid ");
        $course=$DB->get_record_sql("select *  from {course} where id IN($cids->courseid)"); 
   
        $instance = $DB->get_record('enrol', array('enrol' => 'manual','courseid' => $course->id));
        $ue = new stdClass();
        $ue->enrolid      = $instance->id;
        $ue->status       = is_null($status) ? ENROL_USER_ACTIVE : $status;
        $ue->userid       = $userid;
        $ue->timestart    = time();
        $ue->timeend      = 0;
        $ue->modifierid   = 52;
        $ue->timecreated  = time();
        $ue->timemodified = $ue->timecreated;
        $ue->id = $DB->insert_record('user_enrolments', $ue);
        
        $roleid = 5;
        $context = context_course::instance($instance->courseid, MUST_EXIST);
        role_assign($roleid, $userid, $context->id);

        $status = new stdClass();
        $status->userid = $userid;
        $status->recruitment_id =  0;	
        $status->userstatus =  'User Created';
        $status->timestamp =  time();	
        $DB->insert_record('userstatus', $status);
        
        $USER->loggedin = true;
        $USER->site = $CFG->wwwroot;
        $USER = get_complete_user_data('id', $userid);
        // Everywhere we can access user by its id.
        complete_user_login($USER);
        header('Location: '.$quizurl); 
    }
}else{
    echo 'Missing Some Datas';
}