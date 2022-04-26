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
//~ require_once(dirname(__FILE__).'/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/user/editlib.php');
require_login();

//~ global $DB,$CFG,$PAGE,$USER;

class companymanager_form extends moodleform {


	protected $title = '';
    protected $description = '';
    protected $context = null;
    protected $courseselector = null;
    protected $departmentid = 0;
    protected $companyname = '';
    protected $licenseid = 0;
    protected $licensecourses = array();

    public function __construct($actionurl, $companyid, $departmentid, $licenseid=0) {
        global $CFG, $USER;

        $this->selectedcompany = $companyid;
        $this->departmentid = $departmentid;
        $this->licenseid = $licenseid;
        $company = new company($this->selectedcompany);
        $this->companyname = $company->get_name();
        $parentlevel = company::get_company_parentnode($company->id);
        $this->companydepartment = $parentlevel->id;
        $systemcontext = \context_system::instance();

        if (\iomad::has_capability('block/iomad_company_admin:edit_all_departments', $systemcontext)) {
            $userhierarchylevel = $parentlevel->id;
        } else {
            $userlevel = $company->get_userlevel($USER);
            $userhierarchylevel = $userlevel->id;
        }

        $this->subhierarchieslist = company::get_all_subdepartments($userhierarchylevel);
        if ($this->departmentid == 0) {
            $departmentid = $userhierarchylevel;
        } else {
            $departmentid = $this->departmentid;
        }
        $this->userdepartment = $userhierarchylevel;

        $options = array('context' => $this->context,
                         'multiselect' => true,
                         'companyid' => $this->selectedcompany,
                         'departmentid' => $departmentid,
                         'subdepartments' => $this->subhierarchieslist,
                         'parentdepartmentid' => $parentlevel,
                         'showopenshared' => true,
                         'license' => false);

        $this->currentcourses = new \potential_subdepartment_course_selector('currentcourses', $options);
        $this->currentcourses->set_rows(20);
        $this->context = \context_coursecat::instance($CFG->defaultrequestcategory);
        parent::__construct($actionurl);
    }


function definition() {
	// print_r($this->userdepartment);exit;
        $mform = $this->_form;
		$strrequired = get_string('required');
global $DB,$CFG,$PAGE,$USER;

$systemcontext = context_system::instance();
$companyid = iomad::get_my_companyid($systemcontext);
	// print_r($companyid);exit;

	$necessarynames = useredit_get_required_name_fields();
	foreach ($necessarynames as $necessaryname) {
		$mform->addElement('text', $necessaryname, get_string($necessaryname), 'maxlength="100" size="30"');
		$mform->addRule($necessaryname, $strrequired, 'required', null, 'client');
		$mform->setType($necessaryname, PARAM_NOTAGS);
	}

	// Do not show email field if change confirmation is pending.
	if (!empty($CFG->emailchangeconfirmation) and !empty($user->preference_newemail)) {
		$notice = get_string('auth_emailchangepending', 'auth_email', $user);
		$notice .= '<br /><a href="edit.php?cancelemailchange=1&amp;id='.$user->id.'">'
				. get_string('auth_emailchangecancel', 'auth_email') . '</a>';
		$mform->addElement('static', 'emailpending', get_string('email'), $notice);
	} else {
		$mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="30"');
		$mform->addRule('email', $strrequired, 'required', null, 'client');
		$mform->setType('email', PARAM_EMAIL);
	}
	if (!empty($CFG->iomad_allow_username)) {
		$mform->addElement('text', 'username', get_string('username'), 'size="20"');
		$mform->addHelpButton('username', 'username', 'auth');
		$mform->setType('username', PARAM_RAW);
		$mform->disabledif('username', 'use_email_as_username', 'eq', 1);
	}
	$mform->addElement('advcheckbox', 'use_email_as_username', get_string('iomad_use_email_as_username', 'local_iomad_settings'));
	if (!empty($CFG->iomad_use_email_as_username)) {
		$mform->setDefault('use_email_as_username', 1);
	} else {
		$mform->setDefault('use_email_as_username', 0);
	}


	/* /copied from /user/editlib.php */

	$mform->addElement('static', 'blankline', '', '');
	if (!empty($CFG->passwordpolicy)) {
		$mform->addElement('static', 'passwordpolicyinfo', '', print_password_policy());
	}
	$mform->addElement('passwordunmask', 'newpassword', get_string('newpassword'), 'size="20"');
	$mform->addHelpButton('newpassword', 'newpassword');
	$mform->setType('newpassword', PARAM_RAW);
	$mform->addElement('static', 'generatepassword', '',
						get_string('leavepasswordemptytogenerate', 'block_iomad_company_admin'));

	$mform->addElement('advcheckbox', 'preference_auth_forcepasswordchange', get_string('forcepasswordchange'));
	$mform->addHelpButton('preference_auth_forcepasswordchange', 'forcepasswordchange');
	$mform->setDefault('preference_auth_forcepasswordchange', 1);

	$mform->addElement('selectyesno', 'sendnewpasswordemails',
						get_string('sendnewpasswordemails', 'block_iomad_company_admin'));
	$mform->setDefault('sendnewpasswordemails', 1);
	$mform->disabledIf('sendnewpasswordemails', 'newpassword', 'eq', '');

	$mform->addElement('date_time_selector', 'due', get_string('senddate', 'block_iomad_company_admin'));
	$mform->disabledIf('due', 'sendnewpasswordemails', 'eq', '0');
	$mform->addHelpButton('due', 'senddate', 'block_iomad_company_admin');
        
	if ($companyinfo = $DB->get_record('company', array('id' => $companyid))) {

		// Get fields from company category.
		if ($fields = $DB->get_records('user_info_field', array('categoryid' => $companyinfo->profileid))) {
			// Display the header and the fields.
			foreach ($fields as $field) {
				if($field->shortname == 'section_name'){
					require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
					$newfield = 'profile_field_'.$field->datatype;
					$formfield = new $newfield($field->id);
					$formfield->edit_field($mform);
					$mform->setDefault($formfield->inputname, $formfield->field->defaultdata);
				}

			}
		}
	}
	$mform->addElement('hidden', 'userdepartment',$this->userdepartment);   
	// $mform->addElement('hidden', 'userdepartment',$this->userdepartment); 
        $this->add_action_buttons();
      
    }

    function validation($data, $files) {
        global $DB, $CFG;
                return array();

        
	}
	
}



?>

<script src="<?php echo $CFG->wwwroot;?>/theme/iomadboost/js/jquery.js"></script>
		
<script>	
	$(document).ready(function() {
	
		$("#id_course").change(function() {
			
			var courseid = $(this).val();
			
			$.ajax({
				"url":"<?php echo $url; ?>",
				"method":"POST",
				"data":{"type":1, "courseid":courseid},
				"success":function(data) {
					$('#id_price').val(data);
				},
				"error":function(e) {
					//alert(JSON.stringify(e));
				}
			})
        });
        

        
});
</script>
 



