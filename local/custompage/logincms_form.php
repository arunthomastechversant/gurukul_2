<?php

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir.'/formslib.php');

require_login();

//~ global $DB,$CFG,$PAGE,$USER;

class logincms_form extends moodleform {

function definition() {
        $mform = $this->_form;
        global $DB,$CFG,$PAGE,$USER;
        $systemcontext = context_system::instance();
        $companyid = iomad::get_my_companyid($systemcontext);
        $data = $DB->get_record('cms', array('company_id'=> 0, 'type' => 'login'));
        // print_r($data);exit();
        $mform->addElement('html', '<div class="certificate_block_section">');
        $mform->addElement('header', 'Login Page ', "Login Page ");
        $mform->addElement('text','loginheading','Main Heading');
        $mform->setDefault('loginheading',$data->heading);
        $mform->addRule('loginheading',null,'required');

        $mform->addElement('textarea','logincontent','Login Content');
        $mform->setDefault('logincontent',$data->content);
        $mform->addRule('logincontent',null,'required');
        $mform->addElement('html', '</br>');
        $mform->addRule('loginpage',null,'required');
        $mform->addElement('filemanager', 'loginbgimg', 'Login page background', null,array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 1,
        'accepted_types' => 'png,jpeg,jpg'));
        $mform->addElement('filemanager', 'loginslider', 'Login Slider Images', null,array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 3,
            'accepted_types' => 'png,jpeg,jpg'));
        $mform->addElement('filemanager', 'gurukullogo', 'Gurukul Logo', null,array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 1,
            'accepted_types' => 'png,jpeg,jpg'));
        $mform->addElement('filemanager', 'questlogo', 'Quest Logo', null,array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 1,
            'accepted_types' => 'png,jpeg,jpg'));
        $mform->addElement('html', '</br>');
        $this->add_action_buttons();
    }

}

?>