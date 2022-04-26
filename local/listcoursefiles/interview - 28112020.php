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
//require('../../config.php');
require_once($CFG->libdir.'/formslib.php');

require_login();

//~ global $DB,$CFG,$PAGE,$USER;

class interview_form extends moodleform {
	
	public function definition() {
		global $DB, $CFG,$USER;
		
		$this->interviewuser  = optional_param('userid', '', PARAM_INT);
		$interviewid  = optional_param('interviewid', '', PARAM_INT);
		$interviewerid = $USER->id;
		$mform = $this->_form;
		// $interviewerid = 35;
		$interviewdetail = $DB->get_record('interview',(['id' => $interviewid ]));

		if($interviewdetail->interviewtype == 1){
			$mform->addElement('header', 'interviewtype', 'Technical Interview');
			
		}else if($interviewdetail->interviewtype == 2){
			//  print_r("karukku");exit;
			$mform->addElement('header', 'interviewtype', 'Technical Interview 2');
			
		}else if($interviewdetail->interviewtype == 3){
			//  print_r("karukku");exit;
			$mform->addElement('header', 'interviewtype', 'Technical Interview 3');
			
		}


		$usersdet = user_get_users_by_id([$interviewerid,$this->interviewuser]);
		$interviewerdet = $usersdet[$interviewerid];
		$this->interviewuserdet = $usersdet[$this->interviewuser];
		
		
		$mform->addElement('hidden', 'userid', $this->interviewuser,'id');
	
		
		$mform->addElement('static', 'username', 'Username' , $this->interviewuserdet->username);
		
		
		$i = 1;
		
		$drivedetail = $DB->get_record('rsl_recruitment_drive',(['id' => $interviewdetail->driveid ]));
		// print_r($interviewdetail);exit;
		if(!empty($interviewdetail->testscore)){
			$mform->addElement('static', 'testscore', 'Test' , $interviewdetail->testscore);
		}
		// $where = '';
		// $where = $interviewdetail->interviewtype != 1 ? " and interviewtype < $interviewdetail->interviewtype" : '';

		if($interviewdetail->interviewtype == 1){
			$where='';
		}else{
			$where="AND interviewtype < $interviewdetail->interviewtype ";
		}
		$user_interviewstatus = $DB->get_records_sql("select * from mdl_interview where userid = $this->interviewuser $where ");
	// echo'<pre>';print_r($utv);exit;	
		foreach($user_interviewstatus as $utk => $utv) {
			//  echo'<pre>';print_r($utv);exit;	

			 if($interviewdetail->interviewtype == 2){
				//  print_r("karukku");exit;
				$mform->addElement('static', 'interview1 Score', 'Interview 1',
				$utv->interviewscore.'%' );
				
			}else if($interviewdetail->interviewtype == 3){
				if($utv->interviewtype == 1){
					$mform->addElement('static', 'interview1 Score', 'Interview 1',
					$utv->interviewscore.'%' );
				}
				if($utv->interviewtype == 2){
					$mform->addElement('static', 'interview2 Score', 'Interview 2',
					$utv->interviewscore.'%' );
				}

				
			}
			
			if($i == count($user_interviewstatus)){
				
				$interviewtype = $utv->interviewtype;
				// $mform->addElement('hidden', 'interviewtype', $interviewtype,'interviewtype');
				$category = explode(',',$utv->category);
			}
			$i++;
		}
		
		
		foreach($category as $catk => $catv){
			$quescat = explode('-',$catv);
			$interviewcat[$quescat[0]] = $quescat[1];
			$parentref[] = $quescat[0];
		}
		$interviewcat = array_filter($interviewcat);
		
		$implodecat = implode(',',array_keys($interviewcat));
		
		$sql = "select a.*,b.name as parentname from mdl_question_categories a
					join mdl_question_categories b on a.parent  = b.id 
					where a.id in ($implodecat) order by a.id desc";
		
		$catsql = $DB->get_records_sql($sql);
		
		$mform->addElement('header', 'qcategories', $catsql[$parentref[0]]->parentname);
		$size='size="10"';
		
		foreach($catsql as $ctk => $ctv){
			$mform->addElement('static', 'catinfo', '' , $ctv->info);
			$mform->addElement('text', 'category['.$ctv->id.']', $ctv->name , $size);
			$mform->addRule('category['.$ctv->id.']', 'Please enter marks', '', null, 'client');
			
			
		}
		
		$mform->addElement('hidden', 'interviewtypeid', $drivedetail->interview);
		$mform->addElement('hidden', 'interviewid', $interviewid);	
		$mform->addElement('hidden', 'interviewaverage', 'Average' , $size);
		$mform->addElement('text', 'average', 'Average' , $size);
		$mform->addElement('button', 'intro', 'Calculate');
		// $mform->addElement('header', 'result', 'Result');
		$mform->addElement('textarea', 'remark', 'Remarks', 'wrap="virtual" rows="4" cols="60"');
		

		// $mform->setDefault('remark', $remark);
		$awardingTypeRadioArr = array();
		$interviewin = $DB->get_record_sql("select * from mdl_interview where id=$interviewid ");
		// if($interviewin->interviewtype == 1){
		// 	$awardingTypeRadioArr[0] = $mform->createElement( 'radio','award','','RSL Candidate','RSL Candidate');
		// }
	
		if($drivedetail->interview == 1){
			$awardingTypeRadioArr[0] = $mform->createElement( 'radio','award','','RSL Candidate','RSL Candidate');
			
			// print_r($interviewin );exit;
			if($interviewin->interviewtype == 1){
				
				$awardingTypeRadioArr[1] = $mform->createElement( 'radio','award','','Assign To Second Interview','Assign To Second Interview');
			}
			if($interviewin->interviewtype == 2){
				$awardingTypeRadioArr[1] = $mform->createElement( 'radio','award','','Assign To Third Interview','Assign To Third Interview');
			}
			
		}
		
		// $awardingTypeRadioArr[2] = $mform->createElement( 'radio','award','','Failed','Failed');
		$awardingTypeRadioArr[3] = $mform->createElement( 'radio','award','','Moved To HR','Moved To HR');
		$mform->addGroup( $awardingTypeRadioArr,'award' );
		$mform->setDefault('award',1);
				
		$this->add_action_buttons();
	}
	
	public function validation($data, $files) {
		global $CFG, $DB;
		return array();
	}

	

}

?>


 



