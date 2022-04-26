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
require_once('create_urm.php');
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

require_login();

$PAGE->set_pagelayout('admin');
$PAGE->set_title("Add URDC Reporting Manager");
$PAGE->set_heading("Add URDC Reporting Manager");
$PAGE->set_url($CFG->wwwroot.'/local/custompage/create_urm_form.php');
$coursenode = $PAGE->navbar->add('Add URDC Reporting Manager', new moodle_url($CFG->wwwroot.'/local/custompage/create_urm_form.php'));
$PAGE->set_context(context_system::instance());
$return = $CFG->wwwroot.'/local/custompage/create_urm_form.php';
echo $OUTPUT->header();

$mform = new create_brm_form();
if ($mform->is_cancelled()) {
    redirect($return);

} else if ($record = $mform->get_data()) {
    
    $systemcontext = context_system::instance();
    $companyid = iomad::get_my_companyid($systemcontext);
    $company = new company($companyid);
    $companyname = $company->get_name();
    $systemcontext = \context_system::instance();
    
    if (\iomad::has_capability('block/iomad_company_admin:edit_all_departments', $systemcontext)) {
        $userhierarchylevel = $parentlevel->id;
    } else {
        $userlevel = $company->get_userlevel($USER);
        $userhierarchylevel = $userlevel->id;
    }

   // print_r($record);exit();

    $user = new stdClass();
    $user->firstname = $record->name;
    $user->lastname = '';
    $user->email = $record->email;
    $user->username = $record->email;
    $user->password = 'Urm@1'.password_generate(4);  
    $user->confirmed = 1;
    $user->mnethostid = $DB->get_field('mnet_application','id',['name'=>'moodle']);
    $user->timecreated = time();
    $user->address = $record->location;
    $user->city = $record->location;
    // $user->maildisplay = 0;
    print_r($user->password);

    // Create user record and return id.
    $id = user_create_user($user);
    $user->id = $id;


    // $roleid = $record->roleid;
    $enrolmethod = "manual";
    $enrol = enrol_get_plugin($enrolmethod);
    $course_id = 16;
    $context = context_course::instance($course_id);
    // $instances = enrol_get_instances($course_id, true);
    // $manualinstance = null;

    // foreach ($instances as $instance) {
    //         if ($instance->name == $enrolmethod) {
    //                 $manualinstance = $instance;
    //                 break;
    //         }
    // }
    // if ($manualinstance !== null) {
    //         $instanceid = $enrol->add_default_instance($course);
    //         if ($instanceid === null) {
    //                 $instanceid = $enrol->add_instance($course);
    //         }
    //         $instance = $DB->get_record('enrol', array('id' => $instanceid));
    // }

    // $instance = $DB->get_record('enrol', array('enrol' => 'manual','courseid' => $course_id));


    $value = new stdClass();
    $value->roleid = $record->roleid;
    $value->contextid = $context->id;
    $value->userid = $id;
    $value->timemodified = time();
    $value->modifierid = $USER->id;
    $value->component = "";
    $value->itemid = 0;
    $value->sortorder = 0;
    // $instance = ;
    // print_r($enrol);
    // $enrol->enrol_user($instance, $user->id, $roleid);
    $data = $DB->insert_record('role_assignments',$value);


    // $urlto = $CFG->wwwroot.'/local/custompage/.php';
    redirect($return, 'URM Created Successfully ', 8);

}

   
$mform->display();



function password_generate($chars) 
{
  $data = '1234567890abcefghijklmnopqrstuvwxyz';
  return substr(str_shuffle($data), 0, $chars);
}

echo $OUTPUT->footer();
