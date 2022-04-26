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

//~ global $DB,$CFG,$PAGE,$USER;

class create_urdc_test_form extends moodleform {

function definition() {
	global $DB,$CFG,$PAGE,$USER;
        $mform = $this->_form;
        $qid  = optional_param('qid', '', PARAM_TEXT);
		$parentcat = $DB->get_record('question_categories', array('id'=>$qid));
	if($qid)
	$qcat = $DB->get_records('question_categories', array('parent'=>$qid));
    $systemcontext = context_system::instance();
    $companyid = iomad::get_my_companyid($systemcontext);
	$courseid=$DB->get_record_sql("select GROUP_CONCAT(DISTINCT courseid) AS courseid from {company_course} where companyid=$companyid ");
    $course=$DB->get_record_sql("select *  from {course} where id IN($courseid->courseid) ");
    // print_r($course);
    if($companyid){
        $cid=$DB->get_record_sql("select * from {context} where contextlevel=50 and instanceid=$course->id");
        $contexts=$cid->id;
    }

	$categories = $this->get_categories_for_contexts( $contexts, 'parent, sortorder, name ASC', $top);
	$rr=array();
	$rr=array(''=>'---- Question Categories ----');
	foreach($categories as $key => $val){
			$rr[$val->id] = $val->name;
	} 	
    // print_r($rr);
		//  echo '<pre>';print_r($categories);exit;

		  $size='size="10"';
		  $mform->addElement('header', 'createtest', 'Create Test');
		  $select = $mform->addElement('select', 'question_category', 'Choose the Category', $rr);
		  $mform->addRule('question_category', 'Select question category', 'required', null, 'client');
		  $mform->setDefault('question_category',$qid);


		  $mform->addElement('text', 'not', 'Name of the Test' , $size);
		  $mform->addRule('not', 'Please enter the name', 'required', null, 'client');
   
		//   $mform->addElement('text', 'questions', ' Number of Questions' , $size);
		//   $mform->addRule('questions', 'Please enter the number of questions', 'required', null, 'client');


		//   $select->setMultiple(true);

		  $mform->addElement('text', 'duration', 'Duration Of Test In Minutes ' , $size);
		  $mform->addRule('duration', 'Duration in minutes', 'required', null, 'client');

		  $mform->addElement('text', 'noa', 'Number of Attempts ' , $size);
		  $mform->addRule('noa','Number of Attempts ', 'required' ,'client');
		  $mform->setDefault('noa', 1);

		  $mform->addElement('text', 'qpp', 'Number of Questions per Page ' , $size);
		  $mform->addRule('qpp','Number of questions per page', 'required', 'client');
		  $mform->setDefault('qpp', 10);

		  $mform->addElement('advcheckbox', 'proctoring', 'Proctoring', 'Check this to enable Proctoring for this test', array('group' => 1), array(0, 1));

		//   if($qcat ){
		// 	$mform->addElement('header', 'qcategories', $parentcat->name);
		// 	foreach ($qcat as $key => $value) {

		// 		$ccatid=$DB->get_records_sql("select * from {question} where category=$value->id AND parent=0");

		// 		$mform->addElement('text', 'category['.$value->id.']', $value->name.'( Total Questions - '.count($ccatid).' )' , $size);
		// 		$mform->addRule('category['.$value->id.']', 'Please enter number of questions', '', null, 'client');
		// 	}
		// }

		  $mform->addElement('hidden', 'courseid', $course->id);
        
        $this->add_action_buttons();
      
    }

    function validation($newtest, $files) {
		global $DB, $CFG;
		$errors = parent::validation($newtest, $files);
		$newtest = (object)$newtest;
		// validation for duration 
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
		   
		// validation for question count
		$request=$_REQUEST['category'];
		foreach($request as $key => $val){
			if($val){
				if(is_numeric($val)){
				$count = $DB->get_records_sql("select id from {question} where category=$key AND parent=0");
					if(count($count) < $val){
						$data = 'category['.$key.']';
						$errors[$data] = "Enterd number exceed the total question";
					}
				}else{
					$data = 'category['.$key.']';
					$errors[$data] = "Only Numbers Allowed";
				}
			}
			
		}
		// print_r($errors);exit();
        return $errors;
    
    }
    

	function get_categories_for_contexts($contexts, $sortorder = 'parent, sortorder, name ASC', $top = false) {
		
		global $DB;
		$courseid  = optional_param('courseid', '', PARAM_TEXT);
		$topwhere = $top ? '' : 'AND c.parent <> 0';
	   
		$parent=$DB->get_record_sql("select * from {question_categories} where contextid=$contexts and name='top'");
		$child=$DB->get_record_sql("select * from {question_categories} where parent=$parent->id");
		return $DB->get_records_sql("
				SELECT c.*, (SELECT count(1) FROM {question} q
							WHERE c.id = q.category AND q.hidden='0' AND q.parent='0') AS questioncount
				  FROM {question_categories} c
				 WHERE c.contextid IN ($contexts) AND c.name!='top' AND c.parent= $child->id $topwhere
              ORDER BY $sortorder");
	}
	
}



?>





