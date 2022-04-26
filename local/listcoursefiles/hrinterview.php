
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
// print_r("teetet");exit;
//~ global $DB,$CFG,$PAGE,$USER;

class hrinterview_form extends moodleform {

function definition() {

        $mform = $this->_form;
		global $DB,$CFG,$PAGE,$USER;
		$pro='';
		$userid = optional_param('userid','', PARAM_INT);
		$interviewdata = $DB->get_record_sql("SELECT * FROM {hrinterview} WHERE userid = $userid");	
		if($interviewdata->category_score)
		$pro=explode(',',$interviewdata->category_score);
		if($pro){
			$categoryarray=array();
			foreach($pro as $val) {
				$implode_data=explode('-',$val);
				$cat[$implode_data[0]] = $implode_data[1];

			}
		}
		// print_r($cat);exit;	
		$driveid=$DB->get_record_sql("SELECT * FROM {rsl_recruitment_drive}  where id=$interviewdata->driveid ");

		$gradedetail = $DB->get_record_sql(" select round(((10/b.sumgrades) * a.sumgrades)* 10) quizper,a.timemodified as timemodified from mdl_quiz_attempts a join mdl_quiz b on a.quiz = b.id where b.id = $driveid->test and userid = $userid order by a.timemodified desc limit 1");
		$rr=array();
		$categories=array(''=>'-- Choose status --','Selected'=>'Selected','Rejected'=>'Rejected');
		foreach($categories as $key => $val){
				$rr[$key] = $val;
		} 	
		
		$interdata = $DB->get_records_sql("SELECT * FROM {interview} WHERE userid = $userid");	
		//  echo'<pre>';print_r($interdata);exit;	

		// $mform->addElement('static', 'test_mark', 'Test Mark',
		// $gradedetail->quizper.'%' );


		if($interdata){
			foreach ($interdata as $userdata =>$val) {
				// echo'<pre>';print_r($val->isrsl);exit;
				// if($val->isrsl == 1){
				// 	$mform->addElement('static', 'RSL Interview Score', 'RSL Interview ',
				// 	$val->interviewscore.'%' );
				// }else if($val->interviewtype == 1){
				// 	$mform->addElement('static', 'interview1 Score', 'Interview 1',
				// 	$val->interviewscore.'%' );
				// }else if($val->interviewtype == 2){
				// 	$mform->addElement('static', 'interview2 Score', 'Interview 2',
				// 	$val->interviewscore.'%' );
				// }else if($val->interviewtype == 3){
				// 	$mform->addElement('static', 'interview3 Score', 'Interview 3',
				// 	$val->interviewscore.'%' );
				// }
			}
		}

//------------------------------
		$imaged =$DB->get_record_sql("SELECT * FROM {user_proctoringimages} where userid=$userid");
		if($imaged)
		{
				echo html_writer::empty_tag('img', array('src' => $imaged->userimage, 'width' =>160,'height'=>150 ));
		}
//----------------------------------

		  $questions = $DB->get_records_sql("SELECT * FROM {hrquestions} ");
		  $size='size="50"';
		  foreach ($questions as $userdata =>$val) {
			  $name='name['.$val->id.']';
		  $mform->addElement('text', $name, $val->questions , $size);

			if (array_key_exists($val->id, $cat)) 
			{ 
			$mform->setDefault($name, $cat[$val->id]);
			}


		  }
		   $remark=$interviewdata->remark;
		  $mform->addElement('textarea', 'remark', 'Remarks', 'wrap="virtual" rows="5" cols="60"');
		  $mform->setDefault('remark', $remark);
		  $select = $mform->addElement('select', 'status', 'Select Status', $rr);
		  $mform->addRule('status', '', '', null, 'client');
		  $mform->setDefault('status', $interviewdata->status);

		  $mform->addElement('hidden', 'userid',$userid);  
        
        $this->add_action_buttons();
      
    }

    function validation($data, $files) {
        global $DB, $CFG;
                return array();

        
	}
	
}



?>


 



