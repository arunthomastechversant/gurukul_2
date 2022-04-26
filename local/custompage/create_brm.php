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

class create_brm_form extends moodleform {

function definition() {
        $mform = $this->_form;

global $DB,$CFG,$PAGE,$USER;
$rr=array();
$rd=array();
// $role = $DB->get_record_sql("SELECT * FROM {role} WHERE shortname = 'rr'");

// print_r($role);

$datas = $DB->get_record_sql("SELECT id FROM {role} where shortname = 'BRM' ");

		  $size='size="20"';
		  $mform->addElement('text', 'name', 'Name Of reporting manager' , $size);
		  $mform->addRule('name', get_string('required'), 'required', null, 'client');
   
		  $mform->addElement('text', 'location', 'Location' , $size);
		  $mform->addRule('days', get_string('required'), 'required', null, 'client');

		  
		  $select = $mform->addElement('text', 'email', 'Reporting manager email', $size);
          $mform->addRule('email', get_string('required'), 'required', null, 'client');
          
          $mform->addElement('hidden','roleid',$datas->id);
        
        $this->add_action_buttons();
      
    }

    function validation($data, $files) {
        global $DB, $CFG;
        if (!$user or (isset($usernew->email) && $user->email !== $usernew->email)) {
            if (!validate_email($usernew->email)) {
                $err['email'] = get_string('invalidemail');
            } else if (empty($CFG->allowaccountssameemail)
                    and $DB->record_exists('user', array('email' => $usernew->email, 'mnethostid' => $CFG->mnet_localhost_id))) {
                $err['email'] = get_string('emailexists');
            }
        }
        return array();
        
	}
	
}



?>
