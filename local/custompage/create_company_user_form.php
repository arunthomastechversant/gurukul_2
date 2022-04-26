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
 * Adds new instance of enrol_payu to specified course
 * or edits current instance.
 *
 * @package    enrol_payu
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

require_once('create_company_user.php');
$companyid = optional_param('companyid', company_user::companyid(), PARAM_INTEGER);
$departmentid = optional_param('departmentid', 0, PARAM_INTEGER);
$licenseid = optional_param('licenseid', 0, PARAM_INTEGER);
require_login();
$PAGE->set_pagelayout('admin');
$PAGE->set_title("Create Company User");
$PAGE->set_heading("Create Company User");
$PAGE->set_url($CFG->wwwroot.'/local/custompage/create_company_user_form.php');
$coursenode = $PAGE->navbar->add('Create Company User
', new moodle_url($CFG->wwwroot.'/local/custompage/create_company_user.php'));
$PAGE->set_context(context_system::instance());
$linkurl = new moodle_url('/local/custompage/create_company_user_form.php');
$return = $CFG->wwwroot.'/my';
echo $OUTPUT->header();
$systemcontext = context_system::instance();
$companyid = iomad::get_my_companyid($systemcontext);
$mform = new companyuser_form($PAGE->url, $companyid, $departmentid, $licenseid);
if ($mform->is_cancelled()) {
    redirect($return);

} else if ($data = $mform->get_data()) {
	// Trim first and lastnames
	// $data->managertype=0;
    $data->firstname = trim($data->firstname);
    $data->lastname = trim($data->lastname);
	$systemcontext = context_system::instance();
	$companyid = iomad::get_my_companyid($systemcontext);
    $data->userid = $USER->id;
    if ($companyid > 0) {
        $data->companyid = $companyid;
    }

    if (!$userid = company_user::create($data)) {
        $this->verbose("Error inserting a new user in the database!");
        if (!$this->get('ignore_errors')) {
            die();
        }
    }
    $user = new stdclass();
    $user->id = $userid;
    $data->id = $userid;
    $roledata=$DB->get_record_sql("select * from {role} WHERE id=$data->managertype ");

    // Save custom profile fields data.
    profile_save_data($data);

    $systemcontext = context_system::instance();

    // Check if we are assigning a different role to the user.
    if (!empty($data->managertype || !empty($data->educator))) {
        if($roledata->shotname ='rr'){
        
            $context = context_system::instance();
            role_assign($data->managertype, $userid, $context->id);
        }else{
            company::upsert_company_user($userid, $companyid, $data->userdepartment, $data->managertype, $data->educator);
        }
        
    }

    // Assign the user to the default company department.
    $parentnode = company::get_company_parentnode($companyid);
    // if (iomad::has_capability('block/iomad_company_admin:edit_all_departments', $systemcontext)) {
    //     $userhierarchylevel = $parentnode->id;
    // } else {
    //     $userlevel = $company->get_userlevel($USER);
    //     $userhierarchylevel = $userlevel->id;
    // }
    $userhierarchylevel = $parentnode->id;
    company::assign_user_to_department($data->userdepartment, $userid);

    // Enrol the user on the courses.
    if (!empty($createcourses)) {
        $userdata = $DB->get_record('user', array('id' => $userid));
        company_user::enrol($userdata, $createcourses, $companyid);
    }
    // Assign and licenses.
    if (!empty($licenseid)) {
        $licenserecord = (array) $DB->get_record('companylicense', array('id' => $licenseid));
        if (!empty($licenserecord['program'])) {
            // If so the courses are not passed automatically.
            $data->licensecourses =  $DB->get_records_sql_menu("SELECT c.id, clc.courseid FROM {companylicense_courses} clc
                                                                   JOIN {course} c ON (clc.courseid = c.id
                                                                   AND clc.licenseid = :licenseid)",
                                                                   array('licenseid' => $licenserecord['id']));
        }

        if (!empty($data->licensecourses)) {
            $userdata = $DB->get_record('user', array('id' => $userid));
            $count = $licenserecord['used'];
            $numberoflicenses = $licenserecord['allocation'];
            foreach ($data->licensecourses as $licensecourse) {
                if ($count >= $numberoflicenses) {
                    // Set the used amount.
                    $licenserecord['used'] = $count;
                    $DB->update_record('companylicense', $licenserecord);
                    redirect(new moodle_url("/blocks/iomad_company_admin/company_license_users_form.php",
                                             array('licenseid' => $licenseid, 'error' => 1)));
                }

                $issuedate = time();
                $DB->insert_record('companylicense_users',
                                    array('userid' => $userdata->id,
                                          'licenseid' => $licenseid,
                                          'issuedate' => $issuedate,
                                          'licensecourseid' => $licensecourse));

                // Create an event.
                $eventother = array('licenseid' => $licenseid,
                                    'issuedate' => $issuedate,
                                    'duedate' => $data->due);
                $event = \block_iomad_company_admin\event\user_license_assigned::create(array('context' => context_course::instance($licensecourse),
                                                                                              'objectid' => $licenseid,
                                                                                              'courseid' => $licensecourse,
                                                                                              'userid' => $userdata->id,
                                                                                              'other' => $eventother));
                $event->trigger();
                $count++;
            }
        }
    }

    if (isset($data->submitandback)) {
        redirect($dashboardurl, get_string('usercreated', 'block_iomad_company_admin'), null, \core\output\notification::NOTIFY_SUCCESS);
    } else {
        redirect($linkurl, get_string('usercreated', 'block_iomad_company_admin'), null, \core\output\notification::NOTIFY_SUCCESS);
    }

}

   
$mform->display();
echo $OUTPUT->footer();
