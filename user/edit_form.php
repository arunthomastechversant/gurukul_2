
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
 * Form to edit a users profile
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    //  It must be included from a Moodle page.
}

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * Class user_edit_form.
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_edit_form extends moodleform {

    /**
     * Define the form.
     */
    public function definition () {
        global $CFG, $COURSE, $USER,$DB;

        $mform = $this->_form;
        $editoroptions = null;
        $filemanageroptions = null;
        $usernotfullysetup = user_not_fully_set_up($USER);

        if (!is_array($this->_customdata)) {
            throw new coding_exception('invalid custom data for user_edit_form');
        }
        $editoroptions = $this->_customdata['editoroptions'];
        $filemanageroptions = $this->_customdata['filemanageroptions'];
        $user = $this->_customdata['user'];
        $userid = $user->id;

        if (empty($user->country)) {
            // We must unset the value here so $CFG->country can be used as default one.
            unset($user->country);
        }
        
        // Accessibility: "Required" is bad legend text.
        $strgeneral  = get_string('general');
        $strrequired = get_string('required');

        // Add some extra hidden fields.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'course', $COURSE->id);
        $mform->setType('course', PARAM_INT);

        // Print the required moodle fields first.
        $mform->addElement('header', 'moodle', $strgeneral);

        // Shared fields.
        useredit_shared_definition($mform, $editoroptions, $filemanageroptions, $user);
        
        // Extra settigs.
//----------------------unhide to disable image upload options------
       /*
	if (!empty($CFG->disableuserimages) || $usernotfullysetup) 
        {
          $mform->removeElement('deletepicture');
            $mform->removeElement('imagefile');
           $mform->removeElement('imagealt');
        }
	*/
//--------------------------------------------------------
        // If the user isn't fully set up, let them know that they will be able to change
        // their profile picture once their profile is complete.
        if ($usernotfullysetup) {
            $userpicturewarning = $mform->createElement('warning', 'userpicturewarning', 'notifymessage', get_string('newpictureusernotsetup'));
            $enabledusernamefields = useredit_get_enabled_name_fields();
            if ($mform->elementExists('moodle_additional_names')) {
                $mform->insertElementBefore($userpicturewarning, 'moodle_additional_names');
            } else if ($mform->elementExists('moodle_interests')) {
                $mform->insertElementBefore($userpicturewarning, 'moodle_interests');
            } else {
                $mform->insertElementBefore($userpicturewarning, 'moodle_optional');
            }

            // This is expected to exist when the form is submitted.
            $imagefile = $mform->createElement('hidden', 'imagefile');
            $mform->insertElementBefore($imagefile, 'userpicturewarning');
        }

        // Next the customisable profile fields.
        // print_r($USER);exit;
        // BK
        $drive=$DB->get_record_sql("select *  from {role_assignments} where userid =$userid AND roleid=5");
        if($drive){
            profile_definition($mform, $userid);
        }
        $state = array();
        $disrtict = array();
        $state=array(''=>'---- Choose a State ----');
        $disrtict=array(''=>'---- Choose a District ----');
        $datas = $DB->get_records_sql("SELECT * FROM {states}");
        foreach($datas as $value){
		    $state[$value->state_name] = $value->state_name;
        }
        $company = $DB->get_record('company_users',array('userid' => $userid))->companyid;
        //print_r($company);exit();
        if($company == 1){
            $select = $mform->addElement('select', 'profile_field_rslstate', 'State', $state);
            $mform->addRule('profile_field_rslstate', get_string('required'), 'required', null, 'client');

            $select = $mform->addElement('select', 'profile_field_rsldistrict', 'District', $disrtict);
            $mform->addRule('profile_field_rsldistrict', get_string('required'), 'required', null, 'client');
//----------------------------------------------------------------------------
//            $mform->addElement('filemanager', 'resume', 'Upload Your Resume', null,array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 1,
  //          'accepted_types' => 'pdf,doc,docx'));

 	      $mform->addElement('filemanager', 'resume', 'Upload Your Resume', null,array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 1,
              'accepted_types' => array('application/pdf','application/msword')));

//------------------------------------------------------------------------------------
        }
        if($company == 2){
            $select = $mform->addElement('select', 'profile_field_urdcstate', 'State', $state);
            $mform->addRule('profile_field_urdcstate', get_string('required'), 'required', null, 'client');

            $select = $mform->addElement('select', 'profile_field_urdcdistrict', 'District', $disrtict);
            $mform->addRule('profile_field_urdcdistrict', get_string('required'), 'required', null, 'client');
        }
        if($company == 4){
            $select = $mform->addElement('select', 'profile_field_btstate', 'State', $state);
            $mform->addRule('profile_field_btstate', get_string('required'), 'required', null, 'client');

            $select = $mform->addElement('select', 'profile_field_btdistrict', 'District', $disrtict);
            $mform->addRule('profile_field_btdistrict', get_string('required'), 'required', null, 'client');
        }
        
        // BK
        // profile_definition($mform, $userid);

        $this->add_action_buttons(true, "Save Profile");

        $this->set_data($user);
    }

    /**
     * Extend the form definition after the data has been parsed.
     */
    public function definition_after_data() {
        global $CFG, $DB, $OUTPUT;

        $mform = $this->_form;
        $userid = $mform->getElementValue('id');

        // Trim required name fields.
        foreach (useredit_get_required_name_fields() as $field) {
            $mform->applyFilter($field, 'trim');
        }

        if ($user = $DB->get_record('user', array('id' => $userid))) {

            // Remove description.
            if (empty($user->description) && !empty($CFG->profilesforenrolledusersonly) && !$DB->record_exists('role_assignments', array('userid' => $userid))) {
                $mform->removeElement('description_editor');
            }

            // Print picture.
            $context = context_user::instance($user->id, MUST_EXIST);
            $fs = get_file_storage();
            $hasuploadedpicture = ($fs->file_exists($context->id, 'user', 'icon', 0, '/', 'f2.png') || $fs->file_exists($context->id, 'user', 'icon', 0, '/', 'f2.jpg'));
            if (!empty($user->picture) && $hasuploadedpicture) {
                $imagevalue = $OUTPUT->user_picture($user, array('courseid' => SITEID, 'size' => 64));
            } else {
                $imagevalue = get_string('none');
            }
            //$imageelement = $mform->getElement('currentpicture');
            //$imageelement->setValue($imagevalue);

            if ($mform->elementExists('deletepicture') && !$hasuploadedpicture) {
                $mform->removeElement('deletepicture');
            }

            // Disable fields that are locked by auth plugins.
            $fields = get_user_fieldnames();
            $authplugin = get_auth_plugin($user->auth);
            $customfields = $authplugin->get_custom_user_profile_fields();
            $customfieldsdata = profile_user_record($userid, false);
            $fields = array_merge($fields, $customfields);
            foreach ($fields as $field) {
                if ($field === 'description') {
                    // Hard coded hack for description field. See MDL-37704 for details.
                    $formfield = 'description_editor';
                } else {
                    $formfield = $field;
                }
                if (!$mform->elementExists($formfield)) {
                    continue;
                }

                // Get the original value for the field.
                if (in_array($field, $customfields)) {
                    $key = str_replace('profile_field_', '', $field);
                    $value = isset($customfieldsdata->{$key}) ? $customfieldsdata->{$key} : '';
                } else {
                    $value = $user->{$field};
                }

                $configvariable = 'field_lock_' . $field;
                if (isset($authplugin->config->{$configvariable})) {
                    if ($authplugin->config->{$configvariable} === 'locked') {
                        $mform->hardFreeze($formfield);
                        $mform->setConstant($formfield, $value);
                    } else if ($authplugin->config->{$configvariable} === 'unlockedifempty' and $value != '') {
                        $mform->hardFreeze($formfield);
                        $mform->setConstant($formfield, $value);
                    }
                }
            }

            // Next the customisable profile fields.
            profile_definition_after_data($mform, $user->id);

        } else {
            profile_definition_after_data($mform, 0);
        }
    }

    /**
     * Validate incoming form data.
     * @param array $usernew
     * @param array $files
     * @return array
     */
    public function validation($usernew, $files) {
        global $CFG, $DB ,$USER;
        $errors = parent::validation($usernew, $files);
        $usernew = (object)$usernew;
        $roles = $DB->get_records_sql("SELECT roleid from {role_assignments} where userid = $USER->id");
        $roleid = 5;
        foreach($roles as $role){
            if($role->roleid == 10){
                $roleid = 10;
            }
        } 
        // print_r($roles);exit();
        // if($roleid == 5){
        //     $user    = $DB->get_record('user', array('id' => $usernew->id));
        //     $company = $DB->get_record('company_users',array('userid' => $USER->id))->companyid;
        //     // print_r($company);exit();
        //     $fields = $DB->get_records('user_info_field',array('categoryid' => $company,'param2' => 10));
        //     if($fields){
        //         // validate contact number
        //         foreach($fields as $key => $field){
        //             $field_name = 'profile_field_'.$field->shortname;
        //             // $field_name .= $field->shortname;
        //             //print_r($field_name);exit();
		//     $count = strlen($usernew->$field_name);
        //             if(is_numeric($usernew->$field_name)){
        //                 $number =$usernew->$field_name;
        //                 $check = 0;
        //                 for($i = 0; $i<10; $i++){
        //                     if($number[$i] == 0 && $check == $i){
        //                         $check++ ;
        //                     }
        //                 }
        //                 $count = $count - $check;
        //                 if($count < 10){
        //                     $errors[$field_name] = "Enter a valid Mobile Number";
        //                 }
        //             }else{
        //                 $errors[$field_name] = "Only Numbers Allowed";

        //             }
        //         }
        //     }


        //     // 12th or diploma validation and guardian age valiation
        //     if($company == 2){
        //         //if($usernew->profile_field_urdc12Th == "" && $usernew->profile_field_urdcDP == ""){
        //             // print_r($company);exit();
        //             //$errors['profile_field_urdc12Th'] = "12th or Diploma Percentage is Mandatory";
        //             //$errors['profile_field_urdcDP'] = "12th or Diploma Percentage is Mandatory";
        //         //}
        //         if(!is_numeric($usernew->profile_field_urdcNOAB)){
        //             $errors['profile_field_urdcNOAB'] = "Only Numbers Allowed";
        //         }
        //         $age = $DB->get_record('user_info_field',array('categoryid' => $company,'param2' => 3))->shortname;
        //         $age_field = 'profile_field_'.$age;
        //         // $age_field .= $age;
        //         if($usernew->$age_field != ""){
        //             if(is_numeric($usernew->$age_field)){
        //             }else{
        //                 $errors[$age_field] = "Only Numbers Allowed";
        //             }
        //         }
		// if($usernew->profile_field_urdccollege == "Others" && $usernew->profile_field_urdccollegename == ""){

        //             $errors['profile_field_urdccollegename'] = "College Name is Mandatory";
        //         }
                
        //     // marks validation 
        //     $mark_fields = $DB->get_records('user_info_field',array('categoryid' => $company,'param2' => 5));
        //     if($mark_fields){
        //         foreach($mark_fields as $key => $mark_field){
        //             $field_name = 'profile_field_'.$mark_field->shortname;
        //             // $field_name .= $mark_field->shortname;
        //             //print_r($field_name);exit();
        //             if($usernew->$field_name != ""){
        //                 if(is_numeric($usernew->$field_name)){
        //                     if($usernew->$field_name > 100){
        //                         $errors[$field_name] = "Enter Percentage Below 100";
        //                     }
        //                 }else{
        //                     $errors[$field_name] = "Only Numbers Allowed";
    
        //                 }
        //             }
        //         }
        //     }
        // }

        //     // Validate email.
        //     if (!isset($usernew->email)) {
        //         // Mail not confirmed yet.
        //     } else if (!validate_email($usernew->email)) {
        //         $errors['email'] = get_string('invalidemail');
        //     } else if (($usernew->email !== $user->email) && empty($CFG->allowaccountssameemail)) {
        //         // Make a case-insensitive query for the given email address.
        //         $select = $DB->sql_equal('email', ':email', false) . ' AND mnethostid = :mnethostid AND id <> :userid';
        //         $params = array(
        //             'email' => $usernew->email,
        //             'mnethostid' => $CFG->mnet_localhost_id,
        //             'userid' => $usernew->id
        //         );
        //         // If there are other user(s) that already have the same email, show an error.
        //         if ($DB->record_exists_select('user', $select, $params)) {
        //            $errors['email'] = get_string('emailexists');
        //         }
        //     }

        //     if (isset($usernew->email) and $usernew->email === $user->email and over_bounce_threshold($user)) {
        //         $errors['email'] = get_string('toomanybounces');
        //     }

            
        //     if (isset($usernew->email) and !empty($CFG->verifychangedemail) and !isset($errors['email']) and !has_capability('moodle/user:update', context_system::instance())) 
        //     {
        //         $errorstr = email_is_not_allowed($usernew->email);
        //         if ($errorstr !== false) 
        //         {
        //             $errors['email'] = $errorstr;
        //         }
        //     }

          
            
        
        //  }
//-------------------------------------------------------------------------
	 // valid email format validation
	// if($company == 1){

    //     if(filter_var($usernew->profile_field_rslrslemail, FILTER_VALIDATE_EMAIL)) 
    //     {
    //         //print_r("email ok");exit();
    //     }
    //     else
    //     {
    //         $errors['profile_field_rslrslemail'] = "This is an invalid email format!";
          
    //     }

	//  if(ctype_alpha(str_replace(" ","",$usernew->profile_field_rslrslfullname)))
    //     {
    //     }
    //     else
    //     {
    //         $errors['profile_field_rslrslfullname'] = "special character and numbers are not allowed in this field";
    //     }

    //     if(ctype_alpha(str_replace(" ","",$usernew->profile_field_highest_qualification)))
    //     {
    //     }
    //     else
    //     {
    //         $errors['profile_field_highest_qualification'] = "special character and numbers are not allowed in this field";
    //     }

    //     if(ctype_alpha(str_replace(" ","",$usernew->profile_field_primary_skill)))
    //     {
    //     }
    //     else
    //     {
    //         $errors['profile_field_primary_skill'] = "special character and numbers are not allowed in this field";
    //     }

    //     if(ctype_alpha(str_replace(" ","",$usernew->profile_field_secondary_skill)))
    //     {
    //     }
    //     else
    //     {
    //         $errors['profile_field_secondary_skill'] = "special character and numbers are not allowed in this field";
    //     }

    //     if(strlen($usernew->profile_field_address)>50)
    //     {
    //         $errors['profile_field_address']="maximum size of address is 50 charecter";
    //     }
	// }

//-----------------------------------------------------------------------------------------


        // Next the customisable profile fields.
        //$errors += profile_validation($usernew, $files);
       //print_r($errors);exit();
        return $errors;
    }
}







