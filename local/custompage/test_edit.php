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

class test_edit_form extends moodleform {

function definition() 
{
	global $DB,$CFG,$PAGE,$USER;
        $mform = $this->_form;

			$testid = optional_param('testid', '', PARAM_TEXT);

			$name_of_the_test="";
			$duration_of_test_in_minutes="";
			$number_of_attempts="";
			$number_of_questions_per_page="";
			$proctoring = 0;

			if($testid)
			{
				$data=$DB->get_record_sql("select * from {quiz} where id=$testid ");
				$proctoring_data=$DB->get_record_sql("SELECT * FROM {quizaccess_eproctoring} where quizid = $testid");

				$name_of_the_test				=$data->name;
				$duration_of_test_in_minutes	=$data->timelimit;
				$number_of_attempts				=$data->attempts;
				$number_of_questions_per_page   =$data->questionsperpage;
				$duration_of_test_in_minutes    = ((int)$duration_of_test_in_minutes/60);

				if($proctoring_data){
					$proctoring=1 ;
				}else{
					$proctoring=0;
				}

				// foreach($dat2 as $dat3)
				// {
					
				// 	if($data->id==$dat3->quizid)
				// 	{
				// 		$proctoring = 1;
				// 		break;
				// 	}
				// }
				//print_r($proctoring);
				//print_r("[".$name_of_the_test."][".$duration_of_test_in_minutes."][".$number_of_attempts	."][".$number_of_questions_per_page."]" );
			}



		  $mform->addElement('text', 'not', 'Name of the Test', $name_of_the_test , $size);
		  $mform->addRule('not', 'Please enter the name', 'required', null, 'client');
		  $mform->setDefault('not', $name_of_the_test);

		  $mform->addElement('text', 'duration', 'Duration Of Test In Minutes ' , $size);
		  $mform->addRule('duration', 'Enter duration in minutes', 'required', null, 'client');
		  $mform->setDefault('duration', $duration_of_test_in_minutes);

		  $mform->addElement('text', 'noa', 'Number of Attempts ' , $size);
		  $mform->addRule('noa','Enter Number of Attempts ', 'required' ,'client');
		  $mform->setDefault('noa', $number_of_attempts);

		  $mform->addElement('text', 'qpp', 'Number of Questions per Page ' , $size);
		  $mform->addRule('qpp','Enter Number of questions per page', 'required', 'client');
		  $mform->setDefault('qpp', $number_of_questions_per_page);

		  $mform->addElement('advcheckbox', 'proctoring', 'Proctoring', 'Check this to enable Proctoring for this test', array('group' => 1), array(0, 1));
		  $mform->setDefault('proctoring', $proctoring);
		  $mform->addElement('hidden', 'quizid', $testid);
        $this->add_action_buttons();
      
    }

    function validation($newtest, $files) {
		global $DB, $CFG;
		$errors = parent::validation($newtest, $files);
		$newtest = (object)$newtest;

		if(is_numeric($newtest->duration)){
		}else{
			$errors['duration'] = "Only Numbers Allowed";
		}
		if(is_numeric($newtest->noa)){	
		}else{
			$errors['noa'] = "Only Numbers Allowed";
		}
		if(is_numeric($newtest->qpp)){	
		}else{
			$errors['qpp'] = "Only Numbers Allowed";
		}

		// $request=$_REQUEST['category'];
		// foreach($request as $key => $val){
		// 	if($val){
		// 		if(is_numeric($val)){
		// 		$count = $DB->get_records_sql("select id from {question} where category=$key AND parent=0");
		// 			if(count($count) < $val){
		// 				$data = 'category['.$key.']';
		// 				$errors[$data] = "Enterd number exceed the total question";
		// 			}
		// 		}else{
		// 			$data = 'category['.$key.']';
		// 			$errors[$data] = "Only Numbers Allowed";
		// 		}
		// 	}
			
		// }

        return $errors;
	}

	
}



?>





