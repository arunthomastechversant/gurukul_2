<?php

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir.'/formslib.php');

require_login();

//~ global $DB,$CFG,$PAGE,$USER;

class cms_form extends moodleform {

function definition() {
        $mform = $this->_form;
        global $DB,$CFG,$PAGE,$USER;
        $systemcontext = context_system::instance();
        $companyid = iomad::get_my_companyid($systemcontext);

        $data = $DB->get_record('cms', array('company_id'=>$companyid , 'type' => 'welcome'));
        $mform->addElement('header', 'Welcome Page', " Welcome Page");
        $mform->addElement('text','welcomeheading','Heading');
        $mform->setDefault('welcomeheading',$data->heading);
        $mform->addRule('welcomeheading',null,'required');
        $mform->addElement('textarea','welcomecontent','Content');
        $mform->setDefault('welcomecontent',$data->content);
        $mform->addRule('welcomecontent',null,'required');
        $mform->addElement('filemanager', 'welcomepage', 'Upload Background Image', null,array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 1,
        'accepted_types' => 'png,jpeg,jpg'));
        $mform->addElement('filemanager', 'assessmentpage', 'Upload Assessment Image', null,array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 1,
        'accepted_types' => 'png,jpeg,jpg,svg'));
        $mform->addElement('html', '</br>');

        $data = $DB->get_record('cms', array('company_id'=>$companyid , 'type' => 'thankyou'));
        $mform->addElement('header', 'Thank You Page', " Thank You Page");
        $mform->addElement('text','thankyouheading','Heading');
        $mform->setDefault('thankyouheading',$data->heading);
        $mform->addRule('thankyouheading',null,'required');
        $mform->addElement('textarea','thankyoucontent','Content');
        $mform->setDefault('thankyoucontent',$data->content);
        $mform->addRule('thankyoucontent',null,'required');
        $mform->addElement('filemanager', 'thankyoupage', 'Upload Image', null,array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 1,
        'accepted_types' => 'png,jpeg,jpg'));
        $mform->addElement('html', '</br>');

        $this->add_action_buttons();

    }

}

?>