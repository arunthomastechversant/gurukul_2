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
require_once("$CFG->libdir/resourcelib.php");
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/course/modlib.php');
require_once($CFG->dirroot.'/mod/quiz/mod_form.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/mod/quiz/lib.php');
require_once($CFG->dirroot . '/course/lib.php');



require_login();

class edit_recruitment_drive_form extends moodleform {

function definition() 
{
	global $DB,$CFG,$PAGE,$USER;
        $mform = $this->_form;

            $driveid = optional_param('driveid', '', PARAM_TEXT);
            $name_of_the_recruitment    = "";
            $select_test                = "";
            $interview                  = 0;
            $startdate                  = "";
            $enddate                    = "";

            if($driveid)
			      {
              $systemcontext = context_system::instance();
              $companyid = iomad::get_my_companyid($systemcontext);
              $courseid=$DB->get_record_sql("select GROUP_CONCAT(DISTINCT courseid) AS courseid from {company_course} where companyid=$companyid ");
              $course=$DB->get_record_sql("select *  from {course} where id IN($courseid->courseid) ");  		
              $uorg = $DB->get_records_sql("SELECT * FROM {quiz} WHERE course = $course->id");
              if($companyid == 1){
                $drive_data = $DB->get_record_sql("SELECT * FROM {rsl_recruitment_drive} where id=$driveid");
              }else if($companyid == 2){
                $drive_data = $DB->get_record_sql("SELECT * FROM {urdc_recruitment_drive} where id=$driveid");
              }else if($companyid == 4){
                $drive_data = $DB->get_record_sql("SELECT * FROM {bt_recruitment_drive} where id=$driveid");
              }else{
                $drive_data = $DB->get_record_sql("SELECT * FROM {recruitment_drive} where id=$driveid");
              }
              $data2 = $DB->get_records_sql("SELECT * FROM {quiz} ");

              if($drive_data->interview==1)
              {
                $interview=1; 
              }        

                    $name_of_the_recruitment = $drive_data->name;
                    $startdate               = $drive_data->startdate;        
                    $enddate                 = $drive_data->enddate;        

            }

            foreach($uorg as $val)
            {
              $org[$val->id] = $val->name;
            } 

           // print_r($driveid);
            $mform->addElement('hidden', 'driveid', $driveid);
          //  $mform->setDefault('driveid', $driveid);

            $mform->addElement('text', 'name', 'Name Of The Recruitment' , $size);
            $mform->addRule('name', get_string('required'), 'required', null, 'client');
            $mform->setDefault('name', $name_of_the_recruitment);

     
            $mform->addElement('date_time_selector', 'startdate', "Recruitment Start Date", array('optional' => true));
          //   $mform->addRule('startdate', get_string('required'), 'required', null, 'client');
            $mform->addHelpButton('startdate', $startdate);
  
            $mform->addElement('date_time_selector', 'enddate', "Recruitment End Date", array('optional' => true));
         //   $mform->addHelpButton('enddate', $enddate);
             $mform->setDefault('startdate', $startdate);
             $mform->setDefault('enddate', $enddate);
                      

            $select = $mform->addElement('select', 'test', 'Select Test', $org);
            // $mform->setDefault('test', $select_test);
             $mform->addRule('test', '', '', null, 'client');
             $mform->setDefault('test',$drive_data->test);
             if($companyid == 1){

              $mform->addElement('advcheckbox', 'interview', 'Interview', 'Check this to add interview for this recruitment drive', array('group' => 1), array(0, 1));
              $mform->setDefault('interview', $interview);
             }
            


          $this->add_action_buttons();
      
    }

    function validation($newtest, $files) {
    }

	
}



?>





