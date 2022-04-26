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

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir.'/formslib.php');
 
require_login();

class assign_batch_to_course_form extends moodleform {

function definition() {
        $mform = $this->_form;

        global $DB,$CFG,$PAGE,$USER;
        $systemcontext = context_system::instance();
		$companyid = iomad::get_my_companyid($systemcontext);

        $courses = $DB->get_records_sql("SELECT c.* FROM  {course} as c JOIN {company_course} as cc WHERE cc.courseid = c.id AND cc.companyid = $companyid");

        $course = array();
        foreach($courses as $key => $val){
                $course[$val->id] = $val->fullname;
        }  
 
        $mform->addElement('select', 'course', 'Course', $course);
		$mform->addRule('course', 'Please Select a Course', null, 'client');
        
        // $radioarray=array();
        // $radioarray[] = $mform->createElement('radio', 'roletype', '', 'Trainer', 0);
        // $radioarray[] = $mform->createElement('radio', 'roletype', '', 'Student', 1);
        // $mform->addGroup($radioarray, 'roletype_group', 'roletype');
        // $mform->setDefault('auth', 1);
        // $mform->addHelpButton('auth_group', 'roletype_group', MODULE_NAME);
        // $mform->addRule('auth_group', 'Please Select Type', 'required');

        $mform->addElement('button','enrol_batch','Assign Batch To Course');
      
    }

    function validation($data, $files) {
        global $DB, $CFG;
        return array();
        
	}
	
}
?>


 



