<?php
require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/csvlib.class.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/local/custompage/mailtemplate_form.php');
global $USER;

$context = context_system::instance();
require_login();


// Correct the navbar .
// Set the name for the page.
$linktext = "Mail Content Management";
// Set the url.
$linkurl = new moodle_url('/local/custompage/mailtemplate.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);
// Set the page heading.
$PAGE->set_heading($linktext);
$PAGE->navbar->add('Mail Content Management', new moodle_url($CFG->wwwroot.'/local/custompage/mailtemplate.php'));
$context = context_system::instance();
$companyid = iomad::get_my_companyid($context);
echo $OUTPUT->header();
$mform = new mailtemplate_form();
if($companyid == 1){
    $data = $DB->get_record('mail_templates', array('company_id' =>$companyid ,'type_id' => 1));
    $rslcontent = new stdClass;
    $rslcontent->format = 1;
    $rslcontent->rslcontent['text'] = $data->content;
    $mform->set_data($rslcontent);

    $data = $DB->get_record('mail_templates', array('company_id' =>$companyid ,'type_id' => 3));
    $assigncontent = new stdClass;
    $assigncontent->format = 1;
    $assigncontent->assigncontent['text'] = $data->content;
    $mform->set_data($assigncontent);

    $data = $DB->get_record('mail_templates', array('company_id' =>$companyid ,'type_id' => 4));
    $assignhrcontent = new stdClass;
    $assignhrcontent->format = 1;
    $assignhrcontent->assignhrcontent['text'] = $data->content;
    $mform->set_data($assignhrcontent);
    //-------------------------------------
    $datauser2 = $DB->get_record('mail_templates', array('company_id' =>$companyid ,'type_id' => 5));
    $rslusercontent = new stdClass;
    $rslusercontent->format = 1;
    $rslusercontent->rslusercontent['text'] = $datauser2->content;
    $mform->set_data($rslusercontent);
    //-------------------------------------



}
if($companyid ==2){
    $datauser = $DB->get_record('mail_templates', array('company_id' =>$companyid ,'type_id' => 5));
    $usercontent = new stdClass;
    $usercontent->format = 1;
    $usercontent->usercontent['text'] = $datauser->content;
    $mform->set_data($usercontent);
}
$data1 = $DB->get_record('mail_templates', array('company_id' =>$companyid ,'type_id' => 2));
$sharecontent = new stdClass;
$sharecontent->format = 1;
$sharecontent->sharecontent['text'] = $data1->content;
$mform->set_data($sharecontent);

if($mform->is_cancelled()){
    $cancelurl = $CFG->wwwroot.'/my';
    redirect($cancelurl);
}else if($formdata = $mform->get_data()){

    if($companyid == 1){
        $rslmailid = $DB->get_record('mail_templates',array('company_id' => $companyid ,'type_id' => 1))->id;
        if($rslmailid){
            $rsldata =  new stdclass();
            $rsldata->id = $rslmailid;
            $rsldata->subject = $formdata->rslsubject;
            $rsldata->content = $formdata->rslcontent['text'];
            $DB->update_record('mail_templates',$rsldata);
        }else{
            $rsldata =  new stdclass();
            $rsldata->company_id = $companyid;
            $rsldata->type_id = 1;
            $rsldata->subject = $formdata->rslsubject;
            $rsldata->content = $formdata->rslcontent['text'];
            $DB->insert_record('mail_templates',$rsldata);
        }

        $assignmailid = $DB->get_record('mail_templates',array('company_id' => $companyid ,'type_id' => 3))->id;
        if($assignmailid){
            $assigndata =  new stdclass();
            $assigndata->id = $assignmailid;
            $assigndata->subject = $formdata->assignsubject;
            $assigndata->content = $formdata->assigncontent['text'];
            $DB->update_record('mail_templates',$assigndata);
        }else{
            $assigndata =  new stdclass();
            $assigndata->company_id = $companyid;
            $assigndata->type_id = 3;
            $assigndata->subject = $formdata->assignsubject;
            $assigndata->content = $formdata->assigncontent['text'];
            $DB->insert_record('mail_templates',$assigndata);
        }
        $assignhrmailid = $DB->get_record('mail_templates',array('company_id' => $companyid ,'type_id' => 4))->id;
        if($assignhrmailid)
        {
            $assignhrdata =  new stdclass();
            $assignhrdata->id = $assignhrmailid;
            $assignhrdata->subject = $formdata->assignhrsubject;
            $assignhrdata->content = $formdata->assignhrcontent['text'];
            $DB->update_record('mail_templates',$assignhrdata);
        }
        else
        {
            $assignhrdata =  new stdclass();
            $assignhrdata->company_id = $companyid;
            $assignhrdata->type_id = 4;
            $assignhrdata->subject = $formdata->assignhrsubject;
            $assignhrdata->content = $formdata->assignhrcontent['text'];
            $DB->insert_record('mail_templates',$assignhrdata);
        }

        $usercreatemplate = $DB->get_record('mail_templates',array('company_id' => $companyid ,'type_id' => 5))->id;
        if($usercreatemplate){
            $userdata =  new stdclass();
            $userdata->id = $usercreatemplate;
            $userdata->subject = $formdata->rslusersubject;
            $userdata->content = $formdata->rslusercontent['text'];
            $DB->update_record('mail_templates',$userdata);
        }else{
            $userdata =  new stdclass();
            $userdata->company_id = $companyid;
            $userdata->type_id = 5;
            $userdata->subject = $formdata->rslusersubject;
            $userdata->content = $formdata->rslusercontent['text'];
            $DB->insert_record('mail_templates',$userdata);
        }


    }
    if($companyid == 2){
        $usercreatemplateid = $DB->get_record('mail_templates',array('company_id' => $companyid ,'type_id' => 5))->id;
        if($usercreatemplateid){
            $userdata =  new stdclass();
            $userdata->id = $usercreatemplateid;
            $userdata->subject = $formdata->usersubject;
            $userdata->content = $formdata->usercontent['text'];
            $DB->update_record('mail_templates',$userdata);
        }else{
            $userdata =  new stdclass();
            $userdata->company_id = $companyid;
            $userdata->type_id = 5;
            $userdata->subject = $formdata->usersubject;
            $userdata->content = $formdata->usercontent['text'];
            $DB->insert_record('mail_templates',$userdata);
        }
    }

    $maitemplateid = $DB->get_record('mail_templates',array('company_id' => $companyid ,'type_id' => 2))->id;
    if($maitemplateid){
        $sharedata =  new stdclass();
        $sharedata->id = $maitemplateid;
        $sharedata->subject = $formdata->sharesubject;
        $sharedata->content = $formdata->sharecontent['text'];
        $DB->update_record('mail_templates',$sharedata);
    }else{
        $sharedata =  new stdclass();
        $sharedata->company_id = $companyid;
        $sharedata->type_id = 2;
        $sharedata->subject = $formdata->sharesubject;
        $sharedata->content = $formdata->sharecontent['text'];
        $DB->insert_record('mail_templates',$sharedata);
    }


    $urlto = $CFG->wwwroot.'/local/custompage/mailtemplate.php';
    redirect($urlto, 'Data Updated Successfully ', 8); 

}

$mform->display();

echo $OUTPUT->footer();
?>
