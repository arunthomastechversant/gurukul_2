<!-- <style>
    .fcontainer{
        display:none;
    }
    .ftoggler{
        display:none;
    }
</style> -->
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

// require_once('../../../onfig.php');

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/csvlib.class.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/local/custompage/rslbulkupload_form.php');


$context = context_system::instance();
require_login();


// Correct the navbar . urdc
// Set the name for the page.
$linktext = "Upload RSL Users";
// Set the url.
$linkurl = new moodle_url('/local/custompage/rslbulkupload.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);
// Set the page heading.
$PAGE->set_heading($linktext);

$PAGE->requires->jquery();

// Javascript for fancy select.
// Parameter is name of proper select form element.
$PAGE->requires->js_call_amd('block_iomad_company_admin/department_select', 'init', array('userdepartment', '', $userdepartment));

// get output renderer
$output = $PAGE->get_renderer('block_iomad_company_admin');
if (empty($CFG->defaulthomepage)) {
    $PAGE->navbar->add(get_string('dashboard', 'block_iomad_company_admin'), new moodle_url($CFG->wwwroot . '/my'));
}
$PAGE->navbar->add($linktext, $linkurl);

// Set the companyid
$companyid = iomad::get_my_companyid($context);

$companyshortname = '';
if ($companyid ) {
    $company = new company($companyid);
    $companyshortname = $company->get_shortname();
}
require_login(null, false); // Adds to $PAGE, creates $output.

$systemcontext = context_system::instance();

$returnurl = $CFG->wwwroot."/local/custompage/rslbulkupload.php";
$bulknurl  = $CFG->wwwroot.'/'.$CFG->admin.'/user/user_bulk.php';

$today = time();
$today = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), 0, 0, 0);

// Array of all valid fields for validation.

    $mform = new admin_uploaduser_form1();

    if ($formdata = $mform->get_data()) {
        $iid = csv_import_reader::get_new_iid('uploaduser');
        $cir = new csv_import_reader($iid, 'uploaduser');

        // print_r($cir);exit();
        $content = $mform->get_file_content('userfile');
        $optype = $formdata->uutype;
        $readcount = $cir->load_csv_content($content,
                                            $formdata->encoding,
                                            $formdata->delimiter_name
                                            );
        // print_r($cir->get_columns());exit();
        if (!$columns = $cir->get_columns()) {
           print_error('cannotreadtmpfile', 'error', $returnurl);
        }

        unset($content);

        // Keep track of new users.
        $cir->init();
        $usercount = 0;
        $repeatemail = 0;
        while ($line = $cir->next()) {
            $user = new stdClass();

            // Add fields to user object.
            foreach ($line as $key => $value) {
                if ($value !== '') {
                    $key = $columns[$key];
                    $user->$key = $value;
                } else {
                    $user->{$columns[$key]} = '';
                }
            }
            $userid = $DB->get_record('user', array('email' => $user->email))->id;
            if(!$userid){
                $drive=$DB->get_record_sql("select *  from {rsl_recruitment_drive} where name = '$user->drive'");
                if($drive){
                    // print_r($drive);exit();
                    $data='';
                    $userdata = new stdClass();
                    $userdata->firstname = 'RSL';
                    $userdata->lastname = time();
                    $userdata->username = 'rsl'.rand(10,100).password_generate(3);
    		        $userdata->userdepartment=$departmentid; 
		            $userdata->email=$user->email; 
                
                    $userdata->newpassword='Rsl@1'.password_generate(4);
                    $systemcontext = context_system::instance();
                    $companyid = iomad::get_my_companyid($systemcontext);
                    $userdata->userid = $USER->id;
                    if ($companyid > 0) {
                        $userdata->companyid = $companyid;
                    }
                
                    if (!$userid = company_user::create($userdata)) {
                        $this->verbose("Error inserting a new user in the database!");
                        if (!$this->get('ignore_errors')) {
                            die();
                        }
                    }
                    // print_r($userid);exit;
                    $userdetail=$DB->get_record_sql("select * from {user} where id =$userid");
                    $user = $DB->get_record('user', array('id' => $userid, 'deleted' => 0), '*', MUST_EXIST);
                    $enrol = enrol_get_plugin('manual');
                
                
                
                        $cids=$DB->get_record_sql("select GROUP_CONCAT(DISTINCT courseid) AS courseid from {company_course} where companyid=$companyid ");
                        // print_r($cids);exit;
                        $course_id=$DB->get_records_sql("select *  from {course} where id IN($cids->courseid)"); 
                    
                        foreach ($course_id as $key => $course){
                            $enrolmethod='manual';   
                            $course = $DB->get_record('course', array('id' => $course->id), '*', MUST_EXIST);      
                            $instance = $DB->get_record('enrol', array('enrol' => 'manual','courseid' => $course->id));
                            // $end_date = strtotime("+$nodays day", $drive->enddate);
                            $enrol->enrol_user($instance, $user->id, 5,$drive->startdate,$drive->enddate);
                        }
                        
                        
                        if (!groups_add_member($drive->test_groupid, $user->id)) {
                            print_error('erroraddremoveuser', 'group', $returnurl);
                        }
                        $record1 = new stdClass();
                        $record1->userid = $userid;
                        $record1->username =  $userdata->username;		
                        $record1->password = $userdata->newpassword;	
                        $record1->companyid = $companyid;
                        $record1->recruitment_id = $drive->id; 
                        $record1->timestamp =  time();
                        $record1->test_groupid =  $drive->test_groupid;
                        $record1->rsl_due=  '';
                        $lastinsertid = $DB->insert_record('rsl_user_detail', $record1);
                
                
                        $record2 = new stdClass();
                        $record2->userid = $userid;
                        $record2->recruitment_id =  $drive->id;	
                        $record2->userstatus =  'User Created';	
                        $record2->timestamp =  time();	
                        $DB->insert_record('userstatus', $record2);
                        if($lastinsertid > 0 )
                            $usercount++;

                        // print_r($user);exit();
                        $search    = array("{{username}}", "{{password}}", "{{date}}", "{{time}}", "{{url}}");
                        $replace   = array($user->username, $userdata->newpassword, date('d-m-Y', $drive->startdate), date('H:i:s A', $drive->startdate), $CFG->wwwroot);
                        
                        $data = $DB->get_record('mail_templates', array('company_id' =>$companyid ,'type_id' => 5));
                        
                        $message = str_replace($search,$replace,$data->content); 
                        $noreplyuser = core_user::get_noreply_user();
                        
                        email_to_user($user, $noreplyuser, $data->subject, $message);

                }
            }else{
                $repeatemail++;
            }
            
        }

        if ($readcount === false) {
            // TODO: need more detailed error info.
            print_error('csvloaderror', '', $returnurl);
        } else if ($readcount == 0) {
            print_error('csvemptyfile', 'error', $returnurl);
        }

        if($usercount == 0 && $repeatemail != 0){
            $usercount = 'Users are Already Created...!';
        }else if($usercount == 0){
            $usercount = 'Recruitment Drive Missing...!';
        }else{
            $usercount .= ' Users Created Succesfully';
        }
        redirect($returnurl, $usercount, 8);
        // exit();
    } else {
        echo $output->header();

        echo $output->heading_with_help(get_string('uploadusers', 'tool_uploaduser'), 'uploadusers', 'tool_uploaduser');

        $mform->display();
        echo $output->footer();
        die;
    }
// Print the header.
echo $output->header();

// Print the form.

echo $output->heading(get_string('uploaduserspreview', 'tool_uploaduser'));

function password_generate($chars) 
{
  $data = '1234567890abcefghijklmnopqrstuvwxyz';
  return substr(str_shuffle($data), 0, $chars);
}
