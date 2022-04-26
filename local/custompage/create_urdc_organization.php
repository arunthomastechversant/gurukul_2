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

//~ global $DB,$CFG,$PAGE,$USER;

class create_urdc_organization_form extends moodleform {

function definition() {
        $mform = $this->_form;

global $DB,$CFG,$PAGE,$USER;

		$systemcontext = context_system::instance();
		$companyid = iomad::get_my_companyid($systemcontext);
		$updateid = optional_param('updateid', 0, PARAM_INT);   
		$uo = $DB->get_record_sql("SELECT * FROM {urdc_organization} WHERE id = $updateid");

		  $size='size="50"';
		  $mform->addElement('text', 'name', 'Name' , $size);
		  $mform->addRule('name', get_string('required'), 'required', null, 'client');
		  $mform->setDefault('name', $uo->name);
   
		  $mform->addElement('textarea', 'address', "Address",'wrap="virtual" rows="10" cols="50"');
		  $mform->addRule('address', get_string('required'), 'required', null, 'client');
		  $mform->setDefault('address', $uo->address);
		  

		  $attributes = array("1"=>"Tier1","2"=>"Tier2","3"=>"Tier3");
		  $mform->addElement('select', 'type', 'Type',$attributes);
		  $mform->addRule('type', get_string('required'), 'required', null, 'client');
		  $mform->setDefault('type', $uo->type);

		  $mform->addElement('hidden', 'updateid',$updateid);  
        
        $this->add_action_buttons();
      
    }

    function validation($data, $files) {
        global $DB, $CFG;
                return array();

        
	}
	
}



?>


 



