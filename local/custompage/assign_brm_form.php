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
class assign_brm_form extends moodleform {

function definition() {
        $mform = $this->_form;

        global $DB,$CFG,$PAGE,$USER;
        $brm=array();
        $brm=array(''=>'---- Choose Reporting Manager ----');

        $datas = $DB->get_record_sql("SELECT id FROM {role} where shortname = 'BRM' ");
        $data1 = $DB->get_records_sql("SELECT mu.id,mu.firstname FROM {user} as mu INNER JOIN {role_assignments} rs WHERE mu.id = rs.userid AND rs.roleid = $datas->id ");
	foreach($data1 as $key => $val){
                $brm[$val->id] = $val->firstname;
        } 	

        $size='size="20"';
      	$select = $mform->addElement('select', 'brm_id', 'Choose a Reporting Manager', $brm);
        $mform->addRule('brm_id', 'Select question category', null, 'client');
        $mform->addElement('button','assign_brm','Assign');
      
    }

    function validation($data, $files) {
        global $DB, $CFG;
        return array();
        
	}
	
}



?>


 



