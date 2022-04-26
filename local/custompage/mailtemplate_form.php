<?php

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir.'/formslib.php');

require_login();

//~ global $DB,$CFG,$PAGE,$USER;

class mailtemplate_form extends moodleform {

function definition() {
        $mform = $this->_form;
        global $DB,$CFG,$PAGE,$USER;
        $systemcontext = context_system::instance();
        $companyid = iomad::get_my_companyid($systemcontext);
        $size='size="40"';
        if($companyid == 1){
            $data = $DB->get_record('mail_templates', array('company_id' =>$companyid ,'type_id' => 1));
            $mform->addElement('html', '<div class="certificate_block_section">');
            $mform->addElement('header', 'For Rsl Candidate ', "For Rsl Candidate ");
            $mform->addElement('text','rslsubject','Mail Subject',$size);
            $mform->setDefault('rslsubject',$data->subject);
            $mform->addRule('rslsubject',null,'required');

            $mform->addElement('editor','rslcontent','Mail Content');
            $mform->addRule('rslcontent',null,'required');
            $mform->addElement('html', '</br>');

            $data = $DB->get_record('mail_templates', array('company_id' =>$companyid ,'type_id' => 3));
            $mform->addElement('html', '<div class="certificate_block_section">');
            $mform->addElement('header', 'For Assign Interview ', "For Assign Interview ");
            $mform->addElement('text','assignsubject','Mail Subject',$size);
            $mform->setDefault('assignsubject',$data->subject);
            $mform->addRule('assignsubject',null,'required');

            $mform->addElement('editor','assigncontent','Mail Content');
            $mform->addRule('assigncontent',null,'required');
            $mform->addElement('html', '</br>');

            $data = $DB->get_record('mail_templates', array('company_id' =>$companyid ,'type_id' => 4));
            $mform->addElement('html', '<div class="certificate_block_section">');
            $mform->addElement('header', 'For Assign HR Interview', "For Assign HR Interview ");
            $mform->addElement('text','assignhrsubject','Mail Subject',$size);
            $mform->setDefault('assignhrsubject',$data->subject);
            $mform->addRule('assignhrsubject',null,'required');

            $mform->addElement('editor','assignhrcontent','Mail Content');
            $mform->addRule('assignhrcontent',null,'required');
            $mform->addElement('html', '</br>');
            //-------------------------------------
            $datauser2 = $DB->get_record('mail_templates', array('company_id' =>$companyid ,'type_id' => 5));
            // print_r($data);exit();
            $mform->addElement('html', '<div class="certificate_block_section">');
            $mform->addElement('header', 'For User Creation in Bulk upload ', "For User Creation in Bulk upload ");
            $mform->addElement('text','rslusersubject','Mail Subject',$size);
            $mform->setDefault('rslusersubject',$datauser2->subject);
            $mform->addRule('rslusersubject',null,'required');
    
            $mform->addElement('editor','rslusercontent','Mail Content');
            $mform->setDefault('rslusercontent',$datauser2->content);
            $mform->addRule('rslusercontent',null,'required');
            $mform->addElement('html', '</br>');
            //-------------------------------------

        }
        if($companyid == 2){
            $datauser = $DB->get_record('mail_templates', array('company_id' =>$companyid ,'type_id' => 5));
            // print_r($data);exit();
            $mform->addElement('html', '<div class="certificate_block_section">');
            $mform->addElement('header', 'For User Creation in Bulk upload ', "For User Creation in Bulk upload ");
            $mform->addElement('text','usersubject','Mail Subject',$size);
            $mform->setDefault('usersubject',$datauser->subject);
            $mform->addRule('usersubject',null,'required');
    
            $mform->addElement('editor','usercontent','Mail Content');
            $mform->setDefault('usercontent',$datauser->content);
            $mform->addRule('usercontent',null,'required');
            $mform->addElement('html', '</br>');
        }
        $data1 = $DB->get_record('mail_templates', array('company_id' =>$companyid ,'type_id' => 2));
        // print_r($data);exit();
        $mform->addElement('html', '<div class="certificate_block_section">');
        $mform->addElement('header', 'For Share Report ', "For Share Report ");
        $mform->addElement('text','sharesubject','Mail Subject',$size);
        $mform->setDefault('sharesubject',$data1->subject);
        $mform->addRule('sharesubject',null,'required');

        $mform->addElement('editor','sharecontent','Mail Content');
        $mform->setDefault('sharecontent',$data1->content);
        $mform->addRule('sharecontent',null,'required');
        $mform->addElement('html', '</br>');

        $this->add_action_buttons();
    }

}

?>
