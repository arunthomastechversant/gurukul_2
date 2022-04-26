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
class urdcuserreport_form extends moodleform {

function definition() {
        $mform = $this->_form;

        global $DB,$CFG,$PAGE,$USER;
        $drive_id = optional_param('drive_id', '', PARAM_RAW);

        $drives = $DB->get_records_sql("SELECT id,name FROM  {urdc_recruitment_drive} ");

        $shortname = array();
        $shortname=array(''=>'---- Recrutment Drives ----');
        foreach($drives as $key => $drive){
            $shortname[$drive->id] = $drive->name;
        }  

        $mform->addElement('select', 'drive_select', 'Select a drive :', $shortname);
        $mform->addRule('drive_select', 'Please select a drive', null, 'client');
        $mform->setDefault('drive_select',$drive_id);
      
    }

    function validation($data, $files) {
        global $DB, $CFG;
        return array();
        
	}
	
}



?>


 



