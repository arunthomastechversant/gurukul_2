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


class create_urdc_recruitment_drive_form extends moodleform {

function definition() {
        $mform = $this->_form;

global $DB,$CFG,$PAGE,$USER;

		$systemcontext = context_system::instance();
		$companyid = iomad::get_my_companyid($systemcontext);
		$courseid=$DB->get_record_sql("select GROUP_CONCAT(DISTINCT courseid) AS courseid from {company_course} where companyid=$companyid ");
		$course=$DB->get_record_sql("select *  from {course} where id IN($courseid->courseid) ");  		
		$uorg = $DB->get_records_sql("SELECT * FROM {quiz} WHERE course = $course->id");
		// print_r($uorg);exit;
		
		foreach($uorg as $val){
					  $org[$val->id] = $val->name;
			} 

		  $size='size="50"';
		  $mform->addElement('text', 'name', 'Name Of The Recruitment' , $size);
		  $mform->addRule('name', get_string('required'), 'required', null, 'client');
   
		  $mform->addElement('date_time_selector', 'startdate', "Recruitment Start Date", array('optional' => true));
		//   $mform->addRule('startdate', get_string('required'), 'required', null, 'client');
		  $mform->addHelpButton('startdate', 'startdate');
		  
		  $mform->addElement('date_time_selector', 'enddate', "Recruitment End Date", array('optional' => true));
		  $mform->addHelpButton('enddate', 'enddate');
		  		  
		  $select = $mform->addElement('select', 'test', 'Select Test', $org);
		  $mform->addRule('test', '', '', null, 'client');
		  $mform->addElement('hidden', 'courseid',$course->id);  
        
        $this->add_action_buttons();
      
    }

    function validation($data, $files) {
        global $DB, $CFG;
                return array();

        
	}
	
}



?>


 



