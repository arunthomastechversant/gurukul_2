<?php
// This file is part of miniOrange moodle plugin
//
// This plugin is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This library is contain overridden moodle method.
 *
 * Contains authentication method.
 *
 * @copyright   2020  miniOrange
 * @category    authentication
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later, see license.txt
 * @package     mo_saml
 */

global $CFG;
require_once('functions.php');
require_once('customer.php');
require_once($CFG->libdir.'/authlib.php');

include_once 'MetadataReader.php';

/**
 * This class contains authentication plugin method
 *
 * @package    mo_saml
 * @category   authentication
 * @copyright  2020 miniOrange
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_plugin_mo_saml extends auth_plugin_base {
    // Checking the value coming into this method is valid and empty.
    public function mo_saml_check_empty_or_null( $value ) {
        if ( ! isset( $value ) || empty( $value ) ) {
            return true;
        }
        return false;
    }
    // Constructor which has authtype, roleauth, and config variable initialized.
    public function __construct() {
        $this->authtype = 'mo_saml';
        $this->roleauth = 'auth_mo_saml';
        $this->config = get_config('auth/mo_saml');
    }
    // Checking curl installed or not. Return 1 if if present otherwise 0.
    public function mo_saml_is_curl_installed() {
        if (in_array  ('curl', get_loaded_extensions())) {
            return 1;
        } else {
            return 0;
        }
    }
    // Checking openssl installed or not. Return 1 if if present otherwise 0.
    public function mo_saml_is_openssl_installed() {
        if (in_array  ('openssl', get_loaded_extensions())) {
            return 1;
        } else {
            return 0;
        }
    }
    // Checking mcrypt installed or not. Return 1 if if present otherwise 0.
    public function mo_saml_is_mcrypt_installed() {
        if (in_array  ('mcrypt', get_loaded_extensions())) {
            return 1;
        } else {
            return 0;
        }
    }
    // User login return boolean value after checking username and password combination.
    public function user_login($username, $password) {
        global $SESSION;
        if (isset($SESSION->mo_saml_attributes)) {
            return true;
        }
        return false;
    }
    /*
    *function get_userinfo() called from index.php
    *Its purpose to rectify attributes coming froms saml with mapped attributes.
    *$samlattributes variable assigned by $SESSION->mo_saml_attributes which priviously saved in SESSION variable in index.php
    *get_attributes() method called to get all attributes variable mapped in plugin.
    *It will return $user array in which all attributes value according to mapped value.
    */
    public function get_userinfo($username = null) {
        global $SESSION;
        $samlattributes = $SESSION->mo_saml_attributes;
	//print_r($samlattributes);exit();
        // Reading saml attributes from session varible assigned before.
        $nameid = $SESSION->mo_saml_nameID; // $SESSION->mo_saml_nameID has been set to NameID returned of user
        $mapping = $this->get_attributes();
        // Plugin attributes mapped values coming from get_attributes method of this class.
        if (empty($samlattributes)) {
            $username = $nameid;
            $email = $username;
        } else {
            // If saml is not empty.
            $usernamemapping = $mapping['username'];
            $mailmapping = $mapping['email'];
            $firstnamemapping = $mapping['displayName'];
            $lastnamemapping = $mapping['lastname'];
            
            if (!empty($usernamemapping) && isset($samlattributes[$usernamemapping]) && !empty($samlattributes[$usernamemapping][0])) {
                $username = $samlattributes[$usernamemapping][0];
            }
            if (!empty($mailmapping) && isset($samlattributes[$mailmapping]) && !empty($samlattributes[$mailmapping][0])) {
                $email = $samlattributes[$mailmapping][0];
            }
            if (!empty($firstnamemapping) && isset($samlattributes[$firstnamemapping]) && !empty($samlattributes[$firstnamemapping][0])) {
                $firstname = $samlattributes[$firstnamemapping][0];
            }
            //if (!empty($lastnamemapping) && isset($samlattributes[$lastnamemapping]) && !empty($samlattributes[$lastnamemapping][0])) {
              //  $displayName = $samlattributes[$lastnamemapping][0];
            //}
            if (!empty($firstnamemapping) && isset($samlattributes[$firstnamemapping]) && !empty($samlattributes[$firstnamemapping][0])) {
                $displayName = $samlattributes[$firstnamemapping][0];
            }
        }
        $user = array();
        // This array contain and return the value of attributes which are mapped.
        if (!empty($username)) {
            $user['username'] = $username;
        }
        if (!empty($email)) {
            $user['email'] = $email;
        }
        if (!empty($displayName)) {
            $user['firstname'] = $displayName;
        }
        if (!empty($lastname)) {
            $user['lastname'] = $lastname;
        }
        //if (!empty($displayName)) {
          //  $user['displayName'] = $displayName;
        //}
        //print_r($user);exit();


	$pluginconfig = get_config('auth/mo_saml');
        $accountmatcher = $pluginconfig->accountmatcher;

        if (empty($accountmatcher)) {
            // Saml account matcher define which attribute is responsible for account creation.
            $accountmatcher = 'email';
            // Saml matcher is email if not selected.
        }
        if (($accountmatcher == 'username' && empty($user['username']) ||
            ($accountmatcher == 'email' && empty($user['email'])))) {
            $user = false;
        }

        return $user;
    }
    // Function get_attributes() called when we want mapped attributes variables in plugin.
    public function get_attributes() 
    {

        if(isset($this->config->usernamemap))
        {
            $username = $this->config->usernamemap;
        }
        else
        {
            $username = '';
        }
        if(isset($this->config->emailmap))
        {
            $email = $this->config->emailmap;
        }
        else
        {
            $email = '';
        }
	$firstname = 'displayName';
        //$lastname = 'displayName';
	//var_dump($this->config);exit;//sleep(10);
        $attributes = array (
            "username" =>$username,
            "email" => $email,
	    "displayName" => $firstname,
	    //"displayName" => $lastname,

        );
        return $attributes;
    }

    public function get_custom_attributes_mapping() {
        $custom_attribute_mapping = array();

        if(isset($this->config->mo_saml_custom_attribute_mapping_count))
        {
            $custom_attribute_count = $this->config->mo_saml_custom_attribute_mapping_count;
        }
        else
        {
            $custom_attribute_count = '';
        }

        for ($i=1;$i<=$custom_attribute_count;$i++) {
            $idp_attribute_name = "mo_saml_idp_custom_attribute_".$i;
            $custom_attribute_name = "mo_saml_custom_attribute_".$i;
            if (isset($this->config->$custom_attribute_name) && isset($this->config->$idp_attribute_name)) {
                $custom_attribute_mapping[$this->config->$custom_attribute_name] = $this->config->$idp_attribute_name;
            }
        }
        return $custom_attribute_mapping;
    }
    // Here we are assigning  role to user which is selected in role mapping.
    public function obtain_roles() {
        global $SESSION;
        $roles = 'Manager';
        if (!empty($this->config->defaultrolemap) && isset($this->config->defaultrolemap)) {
            $roles = $this->config->defaultrolemap;
        }
        return $roles;
    }


    // Sync roles assigne the role for new user if role mapping done in default role.
    public function sync_roles($user) {
        global $CFG, $DB;
        $defaultrole = $this->obtain_roles();

        if ('siteadmin' == $defaultrole) {

            $siteadmins = explode(',', $CFG->siteadmins);
            if (!in_array($user->id, $siteadmins)) {
                $siteadmins[] = $user->id;
                $newadmins = implode(',', $siteadmins);
                set_config('siteadmins', $newadmins);
            }
        }

		//consider $roles as the groups returned from IdP

		$checkrole = false;


		if($checkrole == false){
			$syscontext = context_system::instance();
			$assignedrole = $DB->get_record('role', array('shortname' => $defaultrole), '*', MUST_EXIST);
			role_assign($assignedrole->id, $user->id, $syscontext);
        }
    }
    // Returns true if this authentication plugin is internal.
    // Internal plugins use password hashes from Moodle user table for authentication.
    public function is_internal() {
        return false;
    }
    // Indicates if password hashes should be stored in local moodle database.
    // This function automatically returns the opposite boolean of what is_internal() returns.
    // Returning true means MD5 password hashes will be stored in the user table.
    // Returning false means flag 'not_cached' will be stored there instead.
    public function prevent_local_passwords() {
        return true;
    }
    // Returns true if this authentication plugin can change users' password.
    public function can_change_password() {
        return false;
    }
    // Returns true if this authentication plugin can edit the users' profile.
    public function can_edit_profile() {
        return true;
    }
    // Hook for overriding behaviour of login page.
    public function loginpage_hook() {
        global $CFG;
        $config = get_config('auth/mo_saml');
        $CFG->nolastloggedin = true;

        if(isset($config->identityname)){
            ?>
            <script src='../auth/mo_saml/includes/js/jquery.min.js'></script>
            <script>$(document).ready(function(){
                $('<a class = "btn btn-primary btn-block m-t-1" style="margin-left:auto;"  href="<?php echo $CFG->wwwroot.'/auth/mo_saml/index.php';
                ?>">Login with <?php echo($this->config->identityname); ?> </a>').insertAfter('#loginbtn')
            });</script>
            <?php
        }
    }
    // Hook for overriding behaviour of logout page.
    public function logoutpage_hook() {
        global $SESSION, $CFG;
        $logouturl = $CFG->wwwroot.'/login/index.php?saml_sso=false';
        require_logout();
        set_moodle_cookie('nobody');
        redirect($logouturl);
    }
    // Prints a form for configuring this authentication plugin.
    // It's called from admin/auth.php, and outputs a full page with a form for configuring this plugin.
    public function config_form($config, $err, $userfields) {
        include('config.html');
        // Including page for setting up the plugin data.
    }
    // Validate form data.
    public function validate_form($form, &$err) {
        // Registeration of plugin also submitting a form which is validating here.
        if (isset($_POST['option']) and $_POST[ 'option' ] == 'mo_saml_register_customer') 
        {
            $loginlink = "auth_config.php?auth=mo_saml&tab=login";
            if ( $this->mo_saml_check_empty_or_null( $_POST['email'] ) ||
                $this->mo_saml_check_empty_or_null( $_POST['password'] ) ||
                $this->mo_saml_check_empty_or_null( $_POST['confirmpassword'] ) ) {
                $err['requiredfield'] = 'Please enter the required fields.';
                redirect($loginlink, 'Please enter the required fields.', null, \core\output\notification::NOTIFY_ERROR);
            } else if ( strlen( $_POST['password'] ) < 6 || strlen( $_POST['confirmpassword'] ) < 6) {
                $err['passwordlengtherr'] = 'Choose a password with minimum length 6.';
                redirect($loginlink, 'Choose a password with minimum length 6.', null, \core\output\notification::NOTIFY_ERROR);
            }
        }
        // Service provider tab data validate here.
        if (isset($_POST['option']) and $_POST[ 'option' ] == 'save') {
            if (empty($form->samlissuer)) {
                $err['issuerurlempty'] = 'Please enter the IdP Entity ID or Issuer field.';
            }
            if (empty($form->loginurl)) {
                $err['targeturlempty'] = 'Please enter the SAML Login URL field.';
            }
        }

        // Upload Metadata or Fetch Metadata
        else if (isset($_POST['option']) and $_POST[ 'option' ] == 'saml_upload_metadata'){
            if(!preg_match("/^\w*$/", $_POST['saml_identity_metadata_provider'])) {
                return;
            }
            $this->_handle_upload_metadata();
        }

        // Attribute /Role mapping data are validate here.
    }
    // Processes and stores configuration data for this authentication plugin.
    public function process_config($config) {
        global $CFG;
        // CFG contain base url for the moodle.
        $config = get_config('auth/mo_saml');
        set_config('hostname', 'https://login.xecurify.com', 'auth/mo_saml');
        // Set host url here for rgister and login purpose of plugin.
        $actuallink = $_SERVER['HTTP_REFERER'];

        if (isset($_POST['option']) and $_POST[ 'option' ] == 'mo_saml_register_customer') {
            if (!isset($_POST['email'])) {
                $config->adminemail = '';
            }
            if (!isset($_POST['password'])) {
                $config->password = '';
            }
            if (!isset($_POST['confirmpassword'])) {
                $config->confirmpassword = '';
            }
            if (!isset($config->transactionid)) {
                $config->transactionid = '';
            }
            if (!isset($config->registrationstatus)) {
                $config->registrationstatus = '';
            }
            set_config('adminemail', $_POST['email'], 'auth/mo_saml');
            set_config('company', $CFG->wwwroot, 'auth/mo_saml');
                
            if ( strcmp( $_POST['password'], $_POST['confirmpassword']) == 0 ) {
                set_config('password', $_POST['password'], 'auth/mo_saml');
                $customer = new customer_saml();
                $content = json_decode($customer->check_customer(), true);
                if(!is_null($content))
                {
                    if ( strcasecmp( $content['status'], 'CUSTOMER_NOT_FOUND' ) == 0 ) 
                    {
                        $this->create_customer();
                    } 
                    else 
                    {
                        $licenselink = "auth_config.php?auth=mo_saml&tab=license"; 
                        $this->get_current_customer();
                        redirect($licenselink, 'Account already exists!', null, \core\output\notification::NOTIFY_WARNING);
                    }
                }
            } 
            else 
            {
                set_config('verifycustomer', '', 'auth/mo_saml');
                redirect($actuallink, 'Passwords do not match!', null, \core\output\notification::NOTIFY_ERROR);
            }
            redirect($actuallink);
            return true;
        }
        if (isset($_POST['option']) and $_POST['option'] == 'mo_saml_validate_otp') {
            // Validation and sanitization.
            $otptoken = '';
            if ( $this->mo_saml_check_empty_or_null( $_POST['otp_token'] ) ) {
                echo('registrationstatus-MO_OTP_VALIDATION_FAILURE');
                return;
            } else {
                $otptoken = $_POST['otp_token'];
            }
            $customer = new customer_saml();
            $content = json_decode($customer->validate_otp_token($config->transactionid, $otptoken ), true);
            if (strcasecmp($content['status'], 'SUCCESS') == 0) {
                $this->create_customer();
            } else {
                // Invalid one time passcode. Please enter a valid otp.
                echo('registrationstatus-MO_OTP_VALIDATION_FAILURE');
            }
            redirect($actuallink);
            return true;
        }
        if ( isset( $_POST['option'] ) and $_POST['option'] == 'verifycustomer' ) {
            if (!isset($config->adminemail)) {
                $config->adminemail = '';
            }
            if (!isset($config->password)) {
                $config->password = '';
            }
            set_config('adminemail', trim($_POST['email']), 'auth/mo_saml');
            set_config('password', trim($_POST['password']), 'auth/mo_saml');
            $config = get_config('auth/mo_saml');
            $customer = new customer_saml();
            $content = $customer->get_customer_key();
            $customerkey = json_decode( $content, true );
            if ( json_last_error() == JSON_ERROR_NONE ) {
                set_config( 'admincustomerkey', $customerkey['id'] , 'auth/mo_saml');
                set_config( 'adminapikey', $customerkey['apiKey'], 'auth/mo_saml' );
                set_config( 'customertoken', $customerkey['token'] , 'auth/mo_saml');
                
                if(isset($config->samlxcertificate))
                    $certificate = $config->samlxcertificate;
                if (empty($certificate)) {
                    set_config( 'freeversion', 1 , 'auth/mo_saml');
                }
                
                set_config('registrationstatus', 'Existing User', 'auth/mo_saml');
                set_config('verifycustomer', '', 'auth/mo_saml');
                $licenselink = "auth_config.php?auth=mo_saml&tab=license"; 
                redirect($licenselink, 'Login success!', null, \core\output\notification::NOTIFY_SUCCESS);
            } else {
                // Invalid username or password. Please try again.
                // echo('Invalid Username or Password');
                redirect($actuallink, 'Invalid Username or Password!', null, \core\output\notification::NOTIFY_ERROR);
            }
            
            return true;
        } else if ( isset( $_POST['option'] ) and $_POST['option'] == 'mo_saml_contact_us_query_option' ) {
            // Contact Us query.
            $email = $_POST['mo_saml_contact_us_email'];
            $phone = $_POST['mo_saml_contact_us_phone'];
            $query = $_POST['mo_saml_contact_us_query'];
            $customer = new customer_saml();
            if ( $this->mo_saml_check_empty_or_null( $email ) || $this->mo_saml_check_empty_or_null( $query ) ) {
                redirect($actuallink);
            } else {
                $submited = $customer->submit_contact_us( $email, $phone, $query );
                if ( $submited == false ) {
                    echo('Error During Query Submit');exit;
                } else {
                    echo('Query Submitted By You...');
                    redirect($CFG->wwwroot.'/admin/auth_config.php?auth=mo_saml&tab=config','Query submitted successfully! We will reach out to you soon.',null,\core\output\notification::NOTIFY_SUCCESS );
                    return true;
                }
            }
        } else if ( isset( $_POST['option'] ) and $_POST['option'] == 'mo_saml_resend_otp_email') {
            $email = $config->adminemail;
            $customer = new customer_saml();
            $content = json_decode($customer->send_otp_token($email, ''), true);
            if (strcasecmp($content['status'], 'SUCCESS') == 0) {
                    set_config('transactionid', $content['txId'], 'auth/mo_saml');
                    set_config('registrationstatus', 'MO_OTP_DELIVERED_SUCCESS_EMAIL', 'auth/mo_saml');
            } else {
                    set_config('registrationstatus', 'MO_OTP_DELIVERED_FAILURE_EMAIL', 'auth/mo_saml');
            }
            redirect($actuallink);
            return true;
        } else if ( isset( $_POST['option'] ) and $_POST['option'] == 'mo_saml_resend_otp_phone' ) {
            $phone = $config->phone;
            $customer = new customer_saml();
            $content = json_decode($customer->send_otp_token('', $phone, false, true), true);
            if (strcasecmp($content['status'], 'SUCCESS') == 0) {
                    set_config('transactionid', $content['txId'], 'auth/mo_saml');
                    set_config('registrationstatus', 'MO_OTP_DELIVERED_SUCCESS_PHONE', 'auth/mo_saml');
            } else {
                    set_config('registrationstatus', 'MO_OTP_DELIVERED_FAILURE_PHONE', 'auth/mo_saml');
            }
            redirect($actuallink);
            return true;
        }
        if (isset( $_POST['option'] ) and $_POST['option'] == 'mo_saml_go_registration' ) 
        {
            unset_config('verifycustomer', 'auth/mo_saml');
            $actuallink = 'auth_config.php?auth=mo_saml&tab=login';
            redirect($actuallink);
            return true;
        }
        if (isset( $_POST['option'] ) and $_POST['option'] == 'mo_saml_go_login' ) 
        {
            unset_config('adminapikey', 'auth/mo_saml');
            unset_config('admincustomerkey', 'auth/mo_saml');
            unset_config('customertoken', 'auth/mo_saml');
            unset_config('password', 'auth/mo_saml');

            set_config('verifycustomer','true', 'auth/mo_saml');
            $actuallink = 'auth_config.php?auth=mo_saml&tab=login';
            redirect($actuallink);
            return true;
        }
        if (isset( $_POST['option'] ) and $_POST['option'] == 'mo_saml_go_back' ) 
        {
            unset_config('adminapikey', 'auth/mo_saml');
            unset_config('admincustomerkey', 'auth/mo_saml');
            unset_config('company', 'auth/mo_saml');
            unset_config('customertoken', 'auth/mo_saml');
            unset_config('license_key', 'auth/mo_saml');
            unset_config('license_verified', 'auth/mo_saml');
            unset_config('newregistration', 'auth/mo_saml');
            unset_config('password', 'auth/mo_saml');
            unset_config('phone', 'auth/mo_saml');
            unset_config('regfirstname', 'auth/mo_saml');
            unset_config('registrationstatus', 'auth/mo_saml');
            unset_config('reglastname', 'auth/mo_saml');
            unset_config('vl_check_t', 'auth/mo_saml');

            set_config('verifycustomer','true', 'auth/mo_saml');
            $actuallink = 'auth_config.php?auth=mo_saml&tab=login';

            redirect($actuallink);
            return true;
        } else if ( isset( $_POST['option'] ) and $_POST['option'] == 'mo_saml_register_with_phone_option' ) {
            $phone = $_POST['phone'];
            $phone = str_replace(' ', '', $phone);
            $phone = str_replace('-', '', $phone);
            set_config('phone', $phone, 'auth/mo_saml');
            $customer = new customer_saml();
            $content = json_decode($customer->send_otp_token('', $phone, false, true), true);
            if (strcasecmp($content['status'], 'SUCCESS') == 0) {
                set_config('transactionid', $content['txId'], 'auth/mo_saml');
                set_config('registrationstatus', 'MO_OTP_DELIVERED_SUCCESS_PHONE', 'auth/mo_saml');
            } else {
                set_config('registrationstatus', 'MO_OTP_DELIVERED_FAILURE_PHONE', 'auth/mo_saml');
            }
            redirect($actuallink);
            return true;
        }
        if (isset( $_POST['option'] ) and $_POST[ 'option' ] == 'save') {

			if (!isset($config->identityname)) {
                $config->identityname = '';
            }
            if (!isset($config->loginurl)) {
                $config->loginurl = '';
            }
            if (!isset($config->samlissuer)) {
                $config->samlissuer = '';
            }
            if (!isset($config->samlxcertificate)) {
                $config->samlxcertificate = '';
            }
            $certificatex = trim($_POST['samlxcertificate']);
            $certificatex = $this->sanitize_certificate($_POST['samlxcertificate']);
            set_config('identityname', trim($_POST['identityname']), 'auth/mo_saml');
            
            set_config('loginurl', trim($_POST['loginurl']), 'auth/mo_saml');
            set_config('samlissuer', trim($_POST['samlissuer']), 'auth/mo_saml');
            set_config('samlxcertificate', trim($certificatex), 'auth/mo_saml');

            redirect($actuallink, 'Settings saved successfully!', null, \core\output\notification::NOTIFY_SUCCESS);
            return true;
        }
        if (isset($_POST['option']) and $_POST[ 'option' ] == 'mo_saml_verify_license') 
        {
            $redirect_url = "auth_config.php?auth=mo_saml&tab=config";
            redirect($redirect_url);
            return true;
        }
        if ( isset( $_POST['option'] ) and $_POST['option'] == 'general') {

            if (!isset($config->enableloginredirect)) {
                $config->enableloginredirect = '';
            }

            if(!isset($config->loginurl))
            {
                redirect($actuallink, 'Configure the plugin first! Go to the <b>Service Provider Setup</b> tab.', null, \core\output\notification::NOTIFY_ERROR);
            }

            if(array_key_exists('mo_saml_enable_login_redirect',$_POST))
                set_config('enableloginredirect', trim($_POST['mo_saml_enable_login_redirect']), 'auth/mo_saml');
            else
                unset_config('enableloginredirect', 'auth/mo_saml');
            
            redirect($actuallink);
            return true;
        }
        if (isset( $_POST['option'] ) and $_POST[ 'option' ] == 'attribute_mapping') {

            if (!isset($config->accountmatcher)) {
                $config->accountmatcher = $_POST[ 'accountmatcher' ];
            }
            if (!isset($config->usernamemap)) {
				$config->usernamemap = $_POST[ 'usernamemap' ];
            }
            if (!isset($config->emailmap)) {
			    $config->emailmap = $_POST[ 'emailmap' ];
            }
            if (!isset($config->defaultrolemap)) {
                $config->defaultrolemap = $_POST[ 'defaultrolemap' ];
            }

            set_config('accountmatcher', trim($_POST['accountmatcher']), 'auth/mo_saml');
            set_config('usernamemap',  trim($_POST['usernamemap']), 'auth/mo_saml');
            set_config('emailmap',  trim($_POST['emailmap']), 'auth/mo_saml');
            set_config('defaultrolemap', trim($_POST['defaultrolemap']), 'auth/mo_saml');


			redirect($actuallink, 'Settings saved successfully!', null, \core\output\notification::NOTIFY_SUCCESS);
            return true;
        }
        return true;
    }
    public function sanitize_certificate( $certificate ) {
        $certificate = preg_replace("/[\r\n]+/", '', $certificate);
        $certificate = str_replace( "-", '', $certificate );
        $certificate = str_replace( "BEGIN CERTIFICATE", '', $certificate );
        $certificate = str_replace( "END CERTIFICATE", '', $certificate );
        $certificate = str_replace( " ", '', $certificate );
        $certificate = chunk_split($certificate, 64, "\r\n");
        $certificate = "-----BEGIN CERTIFICATE-----\r\n" . $certificate . "-----END CERTIFICATE-----";
        return $certificate;
    }
    public function create_customer() {
        global $CFG;
        $customer = new customer_saml();
        $customerkey = json_decode( $customer->create_customer(), true );
        if ( strcasecmp( $customerkey['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS') == 0 ) {
                    $this->get_current_customer();
        } else if ( strcasecmp( $customerkey['status'], 'SUCCESS' ) == 0 ) {
            set_config( 'admincustomerkey', trim($customerkey['id']), 'auth/mo_saml' );
            set_config( 'adminapikey', $customerkey['apiKey'], 'auth/mo_saml');
            set_config( 'customertoken', $customerkey['token'], 'auth/mo_saml');
            set_config( 'freeversion', 1, 'auth/mo_saml' );
            set_config('password', '', 'auth/mo_saml');
            set_config('registrationstatus', '', 'auth/mo_saml');
            set_config('verifycustomer', '', 'auth/mo_saml');
            set_config('newregistration', '', 'auth/mo_saml');
            redirect($CFG->wwwroot.'/admin/auth_config.php?auth=mo_saml&tab=license');
        }
        set_config('password', '', 'auth/mo_saml');
    }
    // Getting customer which is already created at host for login purpose.
    public function get_current_customer() {
        global $CFG;
        $customer = new customer_saml();
        $content = $customer->get_customer_key();
        $customerkey = json_decode( $content, true );
        if ( json_last_error() == JSON_ERROR_NONE ) {
            set_config( 'admincustomerkey', trim($customerkey['id']), 'auth/mo_saml' );
            set_config( 'adminapikey', $customerkey['apiKey'] , 'auth/mo_saml');
            set_config( 'customertoken', $customerkey['token'] , 'auth/mo_saml');
            set_config('password', '', 'auth/mo_saml');

            set_config('verifycustomer', '', 'auth/mo_saml');
            set_config('newregistration', '', 'auth/mo_saml');
        //    redirect($actuallink);
        } else {
            set_config('verifycustomer', 'true', 'auth/mo_saml');
            set_config('newregistration', '', 'auth/mo_saml');
        }
    }
    // The page show in test configuration page.
    public function test_settings() {
        global $CFG;
        echo ' <iframe style="width: 690px;height: 790px;" src="'
        .$CFG->wwwroot.'/auth/mo_saml/index.php/?option=testConfig"></iframe>';
    }

    
    function _handle_upload_metadata(){

        if ( isset($_POST['metadata_file']) || isset($_POST['metadata_url'])) {
            if(!empty($_POST['metadata_file'])) {
                $file = $_POST['metadata_file'];
            } else {
                    //$file = @file_get_contents( $_POST['metadata_file']);
                $url = filter_var( htmlspecialchars($_POST['metadata_url']), FILTER_SANITIZE_URL );
                // $url=$_POST['metadata_url'];
                $response = $xml = file_get_contents($url);

                if(!is_null($response))
                    $file = $response;
                else
                    $file = null;
            }
            $this->upload_metadata($file);
        }
    }

    function upload_metadata($file)
    {
        global $CFG;
        $actuallink = $_SERVER['HTTP_REFERER'];
        $old_error_handler = set_error_handler(array($this,'handleXmlError'));
        $document = new DOMDocument();
        $document->loadXML($file);
        restore_error_handler();
        $first_child = $document->firstChild;
        
        if(!empty($first_child)) {
            $metadata = new IDPMetadataReader($document);
            $identity_providers = $metadata->getIdentityProviders();

            if(empty($identity_providers)) {
                // update_option('mo_saml_message', 'Please provide a valid metadata file.');
                // $this->mo_saml_show_error_message();
                redirect($actuallink, 'Please input a valid metadata file.', null, \core\output\notification::NOTIFY_ERROR);
                return;
            }
            foreach($identity_providers as $key => $idp){
                //$saml_identity_name = preg_match("/^[a-zA-Z0-9-\._ ]+/", $idp->getIdpName()) ? $idp->getIdpName() : "IDP";
                // $saml_identity_name = get_option('saml_identity_name');
                if(isset($_POST['saml_identity_metadata_provider']))
                    $saml_identity_name=htmlspecialchars($_POST['saml_identity_metadata_provider']);
                $saml_login_binding_type = 'HttpRedirect';
                $saml_login_url = '';
                if(array_key_exists('HTTP-Redirect', $idp->getLoginDetails()))
                    $saml_login_url = $idp->getLoginURL('HTTP-Redirect');
                else if(array_key_exists('HTTP-POST', $idp->getLoginDetails())) {
                    $saml_login_binding_type = 'HttpPost';
                    $saml_login_url = $idp->getLoginURL('HTTP-POST');
                }
                $saml_logout_binding_type = 'HttpRedirect';
                $saml_logout_url = '';

                if(array_key_exists('HTTP-Redirect', $idp->getLogoutDetails()))
                    $saml_logout_url = $idp->getLogoutURL('HTTP-Redirect');
                else if(array_key_exists('HTTP-POST', $idp->getLogoutDetails())){
                    $saml_logout_binding_type = 'HttpPost';
                    $saml_logout_url = $idp->getLogoutURL('HTTP-POST');
                }
                $saml_issuer = $idp->getEntityID();
                $saml_x509_certificate = $idp->getSigningCertificate();

                set_config('identityname', $saml_identity_name, 'auth/mo_saml');
                
                set_config('loginurl', $saml_login_url, 'auth/mo_saml');
                set_config('logouturl', $saml_logout_url, 'auth/mo_saml');
                set_config('samlissuer', $saml_issuer, 'auth/mo_saml');
                set_config('samlxcertificate', trim($saml_x509_certificate[0]), 'auth/mo_saml');

                redirect('auth_config.php?auth=mo_saml&tab=save', 'Settings saved successfully!', null, \core\output\notification::NOTIFY_SUCCESS);
                return true;
                

                // update_option('saml_identity_name', $saml_identity_name);
                // update_option('saml_login_binding_type', $saml_login_binding_type);
                // update_option('saml_login_url', $saml_login_url);
                // update_option('saml_logout_binding_type', $saml_logout_binding_type);
                // update_option('saml_logout_url', $saml_logout_url);
                // update_option('saml_issuer', $saml_issuer);
                // update_option('saml_nameid_format', "1.1:nameid-format:unspecified");
                // 	//certs already sanitized in Metadata Reader
                // update_option('saml_x509_certificate', maybe_serialize($saml_x509_certificate));
                break;
            }
            // update_option('mo_saml_message', 'Identity Provider details saved successfully.');
            // $this->mo_saml_show_success_message();
        } else {
            if(!empty($_POST['metadata_file'])){
            // 	update_option('mo_saml_message', 'Please provide a valid metadata file.');
                // $this->mo_saml_show_error_message();
                redirect($actuallink, 'Please input a valid metadata file.', null, \core\output\notification::NOTIFY_ERROR);
            } else if(!empty($_POST['metadata_url'])){
                // update_option('mo_saml_message','Please provide a valid metadata URL.');
                // $this->mo_saml_show_error_message();
                redirect($actuallink, 'Please input a valid metadata file.', null, \core\output\notification::NOTIFY_ERROR);
            } else {
                // update_option('mo_saml_message', 'Please provide a valid metadata file or a valid URL.');
                // $this->mo_saml_show_error_message();
                redirect($actuallink, 'Please input a valid metadata file.', null, \core\output\notification::NOTIFY_ERROR);
                return;
            }
        }
    }


    function handleXmlError($errno, $errstr, $errfile, $errline) {
        if ($errno==E_WARNING && (substr_count($errstr,"DOMDocument::loadXML()")>0)) {
            return;
        } else {
            return false;
        }
    }




}
