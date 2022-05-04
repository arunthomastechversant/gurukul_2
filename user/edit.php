<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Allows you to edit a users profile
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

require_once('../config.php');
require_once($CFG->libdir.'/gdlib.php');
require_once($CFG->dirroot.'/user/edit_form.php');
require_once($CFG->dirroot.'/user/editlib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/user/lib.php');

$userid = optional_param('id', $USER->id, PARAM_INT);    // User id.
$course = optional_param('course', SITEID, PARAM_INT);   // Course id (defaults to Site).
$returnto = optional_param('returnto', null, PARAM_ALPHA);  // Code determining where to return to after save.
$cancelemailchange = optional_param('cancelemailchange', 0, PARAM_INT);   // Course id (defaults to Site).

$PAGE->set_url('/user/edit.php', array('course' => $course, 'id' => $userid));

if (!$course = $DB->get_record('course', array('id' => $course))) {
    print_error('invalidcourseid');
}
// BK
$userstatus = $DB->get_record('rsl_user_detail', array( 'userid' => $userid));
$urdcUserStatus = $DB->get_record('urdc_user_detail', array( 'userid' => $userid));
$bt_UserStatus = $DB->get_record('bt_user_detail', array( 'userid' => $userid));
if(isset($userstatus) || isset($urdcUserStatus) || isset($bt_UserStatus)){
    
$count_rsl = $DB->count_records('rsl_user_detail', array( 'userid' => $userid));
$count_urdc = $DB->count_records('urdc_user_detail', array( 'userid' => $userid));
$count_bt = $DB->count_records('bt_user_detail', array( 'userid' => $userid));


    if($count_rsl > 0){
        if (!$userstatus1 = $DB->get_record('userstatus', array('userstatus' => 'User Loggedin ', 'userid' => $userid))) {

            $drive=$DB->get_record('rsl_user_detail', array('userid' => $userid));
            $record2 = new stdClass();
            $record2->userid = $userid;
            $record2->recruitment_id =  $drive->recruitment_id;	
            $record2->userstatus =  'User Loggedin ';	
            $record2->timestamp =  time();	
            $DB->insert_record('userstatus', $record2);	
        }
    }
    if($count_urdc > 0){
        if (!$urdcUserStatus = $DB->get_record('userstatus', array('userstatus' => 'User Loggedin ', 'userid' => $userid))) {

            $drive=$DB->get_record('urdc_user_detail', array('userid' => $userid));
            $record2 = new stdClass();
            $record2->userid = $userid;
            $record2->recruitment_id =  $drive->recruitment_id; 
            $record2->userstatus =  'User Loggedin ';   
            $record2->timestamp =  time();  
            $DB->insert_record('userstatus', $record2); 
        }
    }
    if($count_bt > 0){
        if(!$bt_UserStatus = $DB->get_record('userstatus', array('userstatus' => 'User Loggedin ', 'userid' => $userid))) {

            $drive=$DB->get_record('bt_user_detail', array('userid' => $userid));
            $record2 = new stdClass();
            $record2->userid = $userid;
            $record2->recruitment_id =  $drive->recruitment_id; 
            $record2->userstatus =  'User Loggedin ';   
            $record2->timestamp =  time();  
            $DB->insert_record('userstatus', $record2); 
        }
    }

   


}



// BK
require_login();
require_user();
if ($course->id != SITEID) {
    require_login($course);
} else if (!isloggedin()) {
    if (empty($SESSION->wantsurl)) {
        $SESSION->wantsurl = $CFG->wwwroot.'/user/edit.php';
    }
    redirect(get_login_url());
} else {
    $PAGE->set_context(context_system::instance());
}

// Guest can not edit.
if (isguestuser()) {
    print_error('guestnoeditprofile');
}

// The user profile we are editing.
if (!$user = $DB->get_record('user', array('id' => $userid))) {
    print_error('invaliduserid');
}

// Guest can not be edited.
if (isguestuser($user)) {
    print_error('guestnoeditprofile');
}

// User interests separated by commas.
$user->interests = core_tag_tag::get_item_tags_array('core', 'user', $user->id);

// Remote users cannot be edited. Note we have to perform the strict user_not_fully_set_up() check.
// Otherwise the remote user could end up in endless loop between user/view.php and here.
// Required custom fields are not supported in MNet environment anyway.
if (is_mnet_remote_user($user)) {
    if (user_not_fully_set_up($user, true)) {
        $hostwwwroot = $DB->get_field('mnet_host', 'wwwroot', array('id' => $user->mnethostid));
        print_error('usernotfullysetup', 'mnet', '', $hostwwwroot);
    }
    redirect($CFG->wwwroot . "/user/view.php?course={$course->id}");
}

// Load the appropriate auth plugin.
$userauth = get_auth_plugin($user->auth);

if (!$userauth->can_edit_profile()) {
    print_error('noprofileedit', 'auth');
}

if ($editurl = $userauth->edit_profile_url()) {
    // This internal script not used.
    redirect($editurl);
}

if ($course->id == SITEID) {
    $coursecontext = context_system::instance();   // SYSTEM context.
} else {
    $coursecontext = context_course::instance($course->id);   // Course context.
}
$systemcontext   = context_system::instance();
$personalcontext = context_user::instance($user->id);

// Check access control.
if ($user->id == $USER->id) {
    // Editing own profile - require_login() MUST NOT be used here, it would result in infinite loop!
    if (!has_capability('moodle/user:editownprofile', $systemcontext)) {
        print_error('cannotedityourprofile');
    }

} else {
    // Teachers, parents, etc.
    require_capability('moodle/user:editprofile', $personalcontext);
    // No editing of guest user account.
    if (isguestuser($user->id)) {
        print_error('guestnoeditprofileother');
    }
    // No editing of primary admin!
    if (is_siteadmin($user) and !is_siteadmin($USER)) {  // Only admins may edit other admins.
        print_error('useradmineditadmin');
    }
}

if ($user->deleted) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('userdeleted'));
    echo $OUTPUT->footer();
    die;
}

$PAGE->set_pagelayout('admin');
$PAGE->set_context($personalcontext);
if ($USER->id != $user->id) {
    $PAGE->navigation->extend_for_user($user);
} else {
    if ($node = $PAGE->navigation->find('myprofile', navigation_node::TYPE_ROOTNODE)) {
        $node->force_open();
    }
}

// Process email change cancellation.
if ($cancelemailchange) {
    cancel_email_update($user->id);
}

// Load user preferences.
useredit_load_preferences($user);

// Load custom profile fields data.
profile_load_data($user);


// Prepare the editor and create form.
$editoroptions = array(
    'maxfiles'   => EDITOR_UNLIMITED_FILES,
    'maxbytes'   => $CFG->maxbytes,
    'trusttext'  => false,
    'forcehttps' => false,
    'context'    => $personalcontext
);

$user = file_prepare_standard_editor($user, 'description', $editoroptions, $personalcontext, 'user', 'profile', 0);
// Prepare filemanager draft area.
$draftitemid = 0;
$filemanagercontext = $editoroptions['context'];
$filemanageroptions = array('maxbytes'       => $CFG->maxbytes,
                             'subdirs'        => 0,
                             'maxfiles'       => 1,
                             'accepted_types' => 'optimised_image');
file_prepare_draft_area($draftitemid, $filemanagercontext->id, 'user', 'newicon', 0, $filemanageroptions);
$user->imagefile = $draftitemid;
// Create form.
$userform = new user_edit_form(new moodle_url($PAGE->url, array('returnto' => $returnto)), array(
    'editoroptions' => $editoroptions,
    'filemanageroptions' => $filemanageroptions,
    'user' => $user));

$emailchanged = false;

//----------------------------------resumeback uploaded file-------------------------------------
$resumedata = new stdClass;
$resumedata->id = $USER->id;
$draftitemid = file_get_submitted_draft_itemid('resume');
file_prepare_draft_area($draftitemid, $personalcontext->id, 'local_custompage', 'resume', $USER->id,
                            array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 1));
$resumedata->resume = $draftitemid;
$userform->set_data($resumedata);
//----------------------------------

$rimgprofiledata3 = new stdClass;
$rimgprofiledata3->id = $USER->id;
$draftitemid3 = file_get_submitted_draft_itemid('imagefile');
file_prepare_draft_area($draftitemid3, $personalcontext->id, 'local_custompage', 'imagefile', $USER->id,
                            array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 1));
$rimgprofiledata3->imagefile = $draftitemid3;
$userform->set_data($rimgprofiledata3);

//-----------------------------------------------------------------------------------------------


// Deciding where to send the user back in most cases.
// $role = $DB->get_record_sql("SELECT roleid from {role_assignments} where userid = $USER->id")->roleid;
$role = $DB->get_record_sql("SELECT roleid from {role_assignments} where userid = $USER->id ORDER BY id DESC LIMIT 1")->roleid;
$companyid = $DB->get_record('company_users',array('userid' => $userid))->companyid;
// print_r($companyid);exit();
$course = $DB->get_record('company_course_mapping',array('companyid' => $companyid))->courseid;
//print_r($role);exit();
if($role == 10 || $role == 1){
    $returnurl = $CFG->wwwroot.'/my';
    redirect($returnurl);
}else if($role == 17){
   $returnUrl = $CFG->wwwroot.'/local/listcoursefiles/interviewlist.php';
   redirect($returnUrl);
}
else if($role == 16){
   $returnUrl = $CFG->wwwroot.'/local/listcoursefiles/hrinterviewlist.php';
   redirect($returnUrl);
}

else if ($returnto === 'profile') {
    if ($course->id != SITEID) {
        $returnurl = new moodle_url('/user/view.php', array('id' => $user->id, 'course' => $course->id));
    } else {
        $returnurl = new moodle_url('/user/profile.php', array('id' => $user->id));
    }
} else {
    $chkrecru = $DB->get_record_sql("SELECT * FROM mdl_role_assignments where contextid = 1  and userid =$user->id");
    $checkUserole = $DB->get_record_sql("SELECT * FROM mdl_role_assignments where userid =$user->id");
    $userstatus = $DB->get_record('rsl_user_detail', array( 'userid' => $userid));
    // print_r($userstatus);exit();
    $rolename = $DB->get_field('role','shortname',['id' => $chkrecru->roleid]);
    $user_rolename = $DB->get_field('role','shortname',['id' => $checkUserole->roleid]);
    if($rolename == 'rr'){
        $reurl = $CFG->wwwroot.'/local/listcoursefiles/hrinterviewlist.php';
        redirect($reurl);
    }elseif($rolename == 'editingteacher'){
        $reurl = $CFG->wwwroot.'/local/listcoursefiles/interviewlist.php';
        redirect($reurl);
       
    }elseif($userstatus->itracqid != 0){
        $count_rsl = $DB->count_records('rsl_user_detail', array( 'userid' => $userid));
        if($count_rsl > 0 && isset($userstatus)){
            $rectdetail = $DB->get_record('rsl_recruitment_drive', array( 'id' => $userstatus->recruitment_id));
            $quiz = $DB->get_record_sql("SELECT rud.userid,rud.recruitment_id,cm.id as cmid   FROM {rsl_user_detail} rud   JOIN {rsl_recruitment_drive} rrd  ON rrd.id = rud.recruitment_id join {course_modules} cm ON cm.instance=rrd.test where cm.course=$course AND cm.module=18 AND cm.instance =$rectdetail->test  AND rud.userid=$userid");
            $returnurl = $CFG->wwwroot.'/mod/quiz/view.php?id='.$quiz->cmid;
            redirect($returnurl);
        }
    }
    
    $returnurl = $CFG->wwwroot.'/my';
    //print_r($returnurl);exit();
    // $returnurl = new moodle_url('/user/preferences.php', array('userid' => $user->id));
    // print_r($returnurl);exit();
}

if ($userform->is_cancelled()) {
    $returnurl = $CFG->wwwroot.'/login/logout.php?sesskey='.sesskey();
    redirect($returnurl);
} else if ($usernew = $userform->get_data()) {
    $emailchangedhtml = '';
    if($companyid == 1)
        $usernew->profile_field_rsldistrict = $_REQUEST['profile_field_rsldistrict'];
    elseif($companyid == 2)
        $usernew->profile_field_urdcdistrict = $_REQUEST['profile_field_urdcdistrict'];
    elseif($companyid == 4)
        $usernew->profile_field_btdistrict = $_REQUEST['profile_field_btdistrict'];

    // print_r($usernew);exit();
    if ($CFG->emailchangeconfirmation) {
        // Users with 'moodle/user:update' can change their email address immediately.
        // Other users require a confirmation email.
        if (isset($usernew->email) and $user->email != $usernew->email && !has_capability('moodle/user:update', $systemcontext)) {
            $a = new stdClass();
            $emailchangedkey = random_string(20);
            set_user_preference('newemail', $usernew->email, $user->id);
            set_user_preference('newemailkey', $emailchangedkey, $user->id);
            set_user_preference('newemailattemptsleft', 3, $user->id);

            $a->newemail = $emailchanged = $usernew->email;
            $a->oldemail = $usernew->email = $user->email;

            $emailchangedhtml = $OUTPUT->box(get_string('auth_changingemailaddress', 'auth', $a), 'generalbox', 'notice');
            $emailchangedhtml .= $OUTPUT->continue_button($returnurl);
        }
    }

    $authplugin = get_auth_plugin($user->auth);

    $usernew->timemodified = time();

    // Description editor element may not exist!
    if (isset($usernew->description_editor) && isset($usernew->description_editor['format'])) {
        $usernew = file_postupdate_standard_editor($usernew, 'description', $editoroptions, $personalcontext, 'user', 'profile', 0);
    }

    // Pass a true old $user here.
    if (!$authplugin->user_update($user, $usernew)) {
        // Auth update failed.
        print_error('cannotupdateprofile');
    }

    // Update user with new profile data.
    user_update_user($usernew, false, false);

    // Update preferences.
    useredit_update_user_preference($usernew);

    // Update interests.
    if (isset($usernew->interests)) {
        useredit_update_interests($usernew, $usernew->interests);
    }

    // Update user picture.
    if (empty($CFG->disableuserimages)) {
        core_user::update_picture($usernew, $filemanageroptions);
    }

    // Update mail bounces.
    useredit_update_bounces($user, $usernew);

    // Update forum track preference.
    useredit_update_trackforums($user, $usernew);

    // Save custom profile fields data.
    // print_r($usernew);exit();
    profile_save_data($usernew);

    $context = context_user::instance($userid, MUST_EXIST);
    if($usernew->resume){
        file_save_draft_area_files($usernew->resume, $context->id , 'local_custompage', 'resume',$userid, array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 1));
    }
//------------------------------------------saving file to draft---------------------------------------

    if($usernew->imagefile)
    {
        file_save_draft_area_files($usernew->imagefile, $context->id , 'local_custompage', 'imagefile',$userid, array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 1));
    }

//-----------------------------------------------------------------------------------------------------
    $fs = get_file_storage();
    if ($files = $fs->get_area_files($context->id, 'local_custompage', 'resume',false, 'sortorder', false)) 
    {
        foreach ($files as $file) 
        { 
            $filepath = moodle_url::make_pluginfile_url($context->id, 'local_custompage', 'resume', $file->get_itemid(), $file->get_filepath(), $file->get_filename());
        }
        $filepath = $filepath->__toString();
    }

    if($companyid == 1){
        $data = new stdclass();
        $data->id = $DB->get_record('rsl_user_detail',array('userid' => $userid))->id;
        if($filepath == ""){
            $data->resume_status = 'Pending';
        }else{
            $data->resume_status = 'Uploaded';
        }
        $DB->update_record('rsl_user_detail',$data);
    }

    
    // print_r($data);exit();    
 
    // Trigger event.
    \core\event\user_updated::create_from_userid($user->id)->trigger();

    // If email was changed and confirmation is required, send confirmation email now to the new address.
    if ($emailchanged !== false && $CFG->emailchangeconfirmation) {
        $tempuser = $DB->get_record('user', array('id' => $user->id), '*', MUST_EXIST);
        $tempuser->email = $emailchanged;

        $supportuser = core_user::get_support_user();

        $a = new stdClass();
        $a->url = $CFG->wwwroot . '/user/emailupdate.php?key=' . $emailchangedkey . '&id=' . $user->id;
        $a->site = format_string($SITE->fullname, true, array('context' => context_course::instance(SITEID)));
        $a->fullname = fullname($tempuser, true);
        $a->supportemail = $supportuser->email;

        $emailupdatemessage = get_string('emailupdatemessage', 'auth', $a);
        $emailupdatetitle = get_string('emailupdatetitle', 'auth', $a);

        // Email confirmation directly rather than using messaging so they will definitely get an email.
        $noreplyuser = core_user::get_noreply_user();
        // BK
        // if (!$mailresults = email_to_user($tempuser, $noreplyuser, $emailupdatetitle, $emailupdatemessage)) {
        //     die("could not send email!");
        // }
    }

    // Reload from db, we need new full name on this page if we do not redirect.
    $user = $DB->get_record('user', array('id' => $user->id), '*', MUST_EXIST);

    if ($USER->id == $user->id) {
        // Override old $USER session variable if needed.
        foreach ((array)$user as $variable => $value) {
            if ($variable === 'description' or $variable === 'password') {
                // These are not set for security nad perf reasons.
                continue;
            }
            $USER->$variable = $value;
        }
        // Preload custom fields.
        profile_load_custom_fields($USER);
    }

    if (is_siteadmin() and empty($SITE->shortname)) {
        // Fresh cli install - we need to finish site settings.
        redirect(new moodle_url('/admin/index.php'));
    }

    // if (!$emailchanged || !$CFG->emailchangeconfirmation) {
    //     $userstatus = $DB->get_record('rsl_user_detail', array( 'userid' => $userid));
    //     if($userstatus){
    //         $rectdetail = $DB->get_record('rsl_recruitment_drive', array( 'id' => $userstatus->recruitment_id));
    //         $quiz = $DB->get_record_sql("SELECT rud.userid,rud.recruitment_id,cm.id as cmid   FROM {rsl_user_detail} rud   JOIN {rsl_recruitment_drive} rrd  ON rrd.id = rud.recruitment_id join {course_modules} cm ON cm.instance=rrd.test where cm.course=$course AND cm.module=18 AND cm.instance =$rectdetail->test  AND rud.userid=$userid");
    //         $reurl = $CFG->wwwroot.'/mod/quiz/view.php?id='.$quiz->cmid;
    //         redirect($reurl);
    //     }else{
    //         $chkrecru = $DB->get_record_sql("SELECT * FROM mdl_role_assignments where contextid = 1  and userid = $USER->id");
    //         $rolename = $DB->get_field('role','shortname',['id' => $chkrecru->roleid]);
    //         // print_r($rolename);exit;
    //         if($rolename == 'rr'){
    //             $reurl = $CFG->wwwroot.'/local/listcoursefiles/hrinterviewlist.php';
    //             redirect($reurl);
    //         }elseif($rolename == 'editingteacher'){
    //             $reurl = $CFG->wwwroot.'/local/listcoursefiles/interviewlist.php';
    //             redirect($reurl);
               
    //         }
           
          
    //     }
    //     redirect($returnurl);
        
    // }

    // redirection to quiz

    if (!$emailchanged || !$CFG->emailchangeconfirmation) {

        $userstatus = $DB->get_record('rsl_user_detail', array( 'userid' => $userid));
        $urdcUserStatus = $DB->get_record('urdc_user_detail', array( 'userid' => $userid));
        $btUserStatus = $DB->get_record('bt_user_detail', array( 'userid' => $userid));
        $commonUserStatus = $DB->get_record('user_detail', array( 'userid' => $userid));

        $count_rsl = $DB->count_records('rsl_user_detail', array( 'userid' => $userid));
        $count_urdc = $DB->count_records('urdc_user_detail', array( 'userid' => $userid));
        $count_bt = $DB->count_records('bt_user_detail', array( 'userid' => $userid));
        $common_count = $DB->count_records('user_detail', array( 'userid' => $userid));

        if($count_rsl > 0 && isset($userstatus)){
            $rectdetail = $DB->get_record('rsl_recruitment_drive', array( 'id' => $userstatus->recruitment_id));
            $quiz = $DB->get_record_sql("SELECT rud.userid,rud.recruitment_id,cm.id as cmid   FROM {rsl_user_detail} rud   JOIN {rsl_recruitment_drive} rrd  ON rrd.id = rud.recruitment_id join {course_modules} cm ON cm.instance=rrd.test where cm.course=$course AND cm.module=18 AND cm.instance =$rectdetail->test  AND rud.userid=$userid");
            $reurl = $CFG->wwwroot.'/mod/quiz/view.php?id='.$quiz->cmid;
            redirect($reurl);
        }
        elseif ($count_urdc > 0 && isset($urdcUserStatus)) {
            $rectdetail = $DB->get_record('urdc_recruitment_drive', array( 'id' => $urdcUserStatus->recruitment_id));
            $quiz = $DB->get_record_sql("SELECT rud.userid,rud.recruitment_id,cm.id as cmid   FROM {urdc_user_detail} rud   JOIN {urdc_recruitment_drive} rrd  ON rrd.id = rud.recruitment_id join {course_modules} cm ON cm.instance=rrd.test where cm.course=$course AND cm.module=18 AND cm.instance =$rectdetail->test  AND rud.userid=$userid");
            $reurl = $CFG->wwwroot.'/mod/quiz/view.php?id='.$quiz->cmid;
            redirect($reurl);
        }
        elseif ($count_bt > 0 && isset($btUserStatus)) {
            $rectdetail = $DB->get_record('bt_recruitment_drive', array( 'id' => $btUserStatus->recruitment_id));
            $quiz = $DB->get_record_sql("SELECT rud.userid,rud.recruitment_id,cm.id as cmid   FROM {bt_user_detail} rud  JOIN {bt_recruitment_drive} rrd  ON rrd.id = rud.recruitment_id join {course_modules} cm ON cm.instance=rrd.test where cm.course=$course AND cm.module=18 AND cm.instance =$rectdetail->test  AND rud.userid=$userid");
            // echo "SELECT rud.userid,rud.recruitment_id,cm.id as cmid   FROM {bt_user_detail} rud  JOIN {bt_recruitment_drive} rrd  ON rrd.id = rud.recruitment_id join {course_modules} cm ON cm.instance=rrd.test where cm.course=16 AND cm.module=18 AND cm.instance =$rectdetail->test  AND rud.userid=$userid";
            // print_r($quiz);
            $reurl = $CFG->wwwroot.'/mod/quiz/view.php?id='.$quiz->cmid;
            // echo $reurl;exit();
            redirect($reurl);
        }elseif ($common_count > 0 && isset($commonUserStatus)){
            // print_r($commonUserStatus);exit();
            $rectdetail = $DB->get_record('recruitment_drive', array( 'id' => $commonUserStatus->recruitment_id));
            $quiz = $DB->get_record_sql("SELECT rud.userid,rud.recruitment_id,cm.id as cmid FROM {user_detail} rud JOIN {recruitment_drive} rrd  ON rrd.id = rud.recruitment_id join {course_modules} cm ON cm.instance=rrd.test where cm.course=$course AND cm.module=18 AND cm.instance =$rectdetail->test  AND rud.userid=$userid");
            // print_r($course);exit();
            $reurl = $CFG->wwwroot.'/mod/quiz/view.php?id='.$quiz->cmid;
            redirect($reurl);
        }

        else {

            $chkrecru = $DB->get_record_sql("SELECT * FROM mdl_role_assignments where contextid = 1  and userid = $USER->id");
            $checkUseRole = $DB->get_record_sql("SELECT * FROM mdl_role_assignments where userid = $USER->id");
            $rolename = $DB->get_field('role','shortname',['id' => $chkrecru->roleid]);
            $user_rolename = $DB->get_field('role','shortname',['id' => $checkUseRole->roleid]);
            if($rolename == 'rr'){
                $reurl = $CFG->wwwroot.'/local/listcoursefiles/hrinterviewlist.php';
                redirect($reurl);
            }elseif($rolename == 'editingteacher'){
                $reurl = $CFG->wwwroot.'/local/listcoursefiles/interviewlist.php';
                redirect($reurl);
            }elseif($user_rolename == 'URM'){
                $reurl = $CFG->wwwroot.'/local/custompage/urm_user_list.php';
                redirect($reurl);
               
            }elseif($user_rolename == 'BRM'){
                $reurl = $CFG->wwwroot.'/local/custompage/brm_user_list.php';
                redirect($reurl);
               
            }
        redirect($returnurl);
        
        }
    }
}


// Display page header.


echo $OUTPUT->header();
//echo $OUTPUT->heading($userfullname);

if ($emailchanged) {
    echo $emailchangedhtml;
} else {
    // Finally display THE form.
    $userform->display();
}

// And proper footer.
echo '<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>';
echo $OUTPUT->footer();
?>
<script>
$(document).ready( function () {


    $('#id_profile_field_rsldistrict').append('<option value="" selected disabled>---- Choose a District ----</option>');
    $('#id_profile_field_btdistrict').html('<option value="" selected disabled>---- Choose a District ----</option>');
    $('#id_profile_field_urdcdistrict').html('<option value="" selected disabled>---- Choose a District ----</option>');

        if($('#id_profile_field_urdccollege').val()=='Others'){
            $('#fitem_id_profile_field_urdccollegename').show();
        }else{
            $('#fitem_id_profile_field_urdccollegename').hide();
        }

    $("#id_profile_field_urdccollege" ).change(function() {
        // var value = $('#id_profile_field_urdccollage').val();
  
        if($('#id_profile_field_urdccollege').val()=='Others'){
            $('#fitem_id_profile_field_urdccollegename').show();
        }else{
            $('#fitem_id_profile_field_urdccollegename').hide();
        }
        
    });


if($('#id_profile_field_urdcstate').val()!=null){
    $.ajax({
        type: "POST",
        url: "statecity.php",
        data: {
            'type' : 2,
            'state_name' : $('#id_profile_field_urdcstate').val(),
        },
        success: function(data) {
            // alert(data);
           // $("#id_test_select").empty();
       	 $("#id_profile_field_urdcdistrict").empty();

            $("#id_profile_field_urdcdistrict").append(data);
        }
});
}


if($('#id_profile_field_btstate').val()!=null){
    $.ajax({
        type: "POST",
        url: "statecity.php",
        data: {
            'type' : 2,
            'state_name' : $('#id_profile_field_btstate').val(),
        },
        success: function(data) {
            // alert(data);
           // $("#id_test_select").empty();
       	 $("#id_profile_field_btdistrict").empty();

            $("#id_profile_field_btdistrict").append(data);
        }
 	   });
}



if($('#id_profile_field_rslstate').val()!=null){
    $.ajax({
        type: "POST",
        url: "statecity.php",
        data: {
            'type' : 2,
            'state_name' : $('#id_profile_field_rslstate').val(),
        },
        success: function(data) {
            // alert(data);
           // $("#id_test_select").empty();
       	 $("#id_profile_field_rsldistrict").empty();

            $("#id_profile_field_rsldistrict").append(data);
        }
 	   });
}
})

$(document).on('change','#id_profile_field_rslstate',function(){
    $.ajax({
        type: "POST",
        url: "statecity.php",
        data: {
            'type' : 2,
            'state_name' : this.value,
        },
        success: function(data) {
        
           $("#id_profile_field_rsldistrict").empty();
           $("#id_profile_field_rsldistrict").append(data);
 //  $("#id_profile_field_rsldistrict").html(data);
        }
    });
})

$(document).on('change','#id_profile_field_btstate',function(){
    $.ajax({
        type: "POST",
        url: "statecity.php",
        data: {
            'type' : 2,
            'state_name' : this.value,
        },
        success: function(data) {
            $("#id_profile_field_btdistrict").html(data);
        }
    });
})

$(document).on('change','#id_profile_field_urdcstate',function(){
    $.ajax({
        type: "POST",
        url: "statecity.php",
        data: {
            'type' : 2,
            'state_name' : this.value,
        },
        success: function(data) {
            $("#id_profile_field_urdcdistrict").html(data);
        }
    });
})
</script>   
<style>
    #id_moodle_picture{
        display : block;
    }
    #page-navbar,#message-user-button{
        display : none;
    }
    .ftoggler,.collapsible-actions .collapseexpand{
        display : none;
    }
</style>