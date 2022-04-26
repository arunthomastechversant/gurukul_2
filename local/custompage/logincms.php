<?php
require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/csvlib.class.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/local/custompage/logincms_form.php');
global $USER;

$context = context_system::instance();
require_login();


// Correct the navbar .
// Set the name for the page.
$linktext = "Login Content Management";
// Set the url.
$linkurl = new moodle_url('/local/custompage/logincms.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);
// Set the page heading.
$PAGE->set_heading($linktext);
$PAGE->navbar->add('Login Content Management', new moodle_url($CFG->wwwroot.'/local/custompage/logincms.php'));
$context = context_system::instance();
$companyid = iomad::get_my_companyid($context);
echo $OUTPUT->header();
$mform = new logincms_form();
if (is_siteadmin())
{
    $sliderdata = new stdClass;
    $sliderdata->id = 1;
    $draftitemid = file_get_submitted_draft_itemid('loginslider');
    file_prepare_draft_area($draftitemid, $context->id, 'local_custompage', 'loginslider', $USER->id,
                            array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 3));
    $sliderdata->loginslider = $draftitemid;
    $mform->set_data($sliderdata);
}

$gurukullogo = new stdClass;
$gurukullogo->id = 1;
$draftitemid = file_get_submitted_draft_itemid('gurukullogo');
file_prepare_draft_area($draftitemid, $context->id, 'local_custompage', 'gurukullogo', $USER->id,
                        array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 1));
$gurukullogo->gurukullogo = $draftitemid;
$mform->set_data($gurukullogo);

$questlogo = new stdClass;
$questlogo->id = 1;
$draftitemid = file_get_submitted_draft_itemid('questlogo');
file_prepare_draft_area($draftitemid, $context->id, 'local_custompage', 'questlogo', $USER->id,
                        array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 1));
$questlogo->questlogo = $draftitemid;
$mform->set_data($questlogo);

$loginbgimg = new stdClass;
$loginbgimg->id = 1;
$draftitemid = file_get_submitted_draft_itemid('loginbgimg');
file_prepare_draft_area($draftitemid, $context->id, 'local_custompage', 'loginbgimg', $USER->id,
                        array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 1));
$loginbgimg->loginbgimg = $draftitemid;
$mform->set_data($loginbgimg);

if($mform->is_cancelled()){

}else if($formdata = $mform->get_data()){
    if($formdata->loginslider){
        file_save_draft_area_files($formdata->loginslider, $context->id , 'local_custompage', 'loginslider',$USER->id, array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 3));
    }
    if($formdata->gurukullogo){
        file_save_draft_area_files($formdata->gurukullogo, $context->id , 'local_custompage', 'gurukullogo',$USER->id, array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 1));
    }
    if($formdata->questlogo){
        file_save_draft_area_files($formdata->questlogo, $context->id , 'local_custompage', 'questlogo',$USER->id, array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 1));
    }
    if($formdata->loginbgimg){
        file_save_draft_area_files($formdata->loginbgimg, $context->id , 'local_custompage', 'loginbgimg',$USER->id, array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 1));
    }
    if (is_siteadmin())
    {
       $data =  new stdclass();
       $data->id = 5;
       $data->heading = $formdata->loginheading;
       $data->content = $formdata->logincontent;
       $DB->update_record('cms',$data);
    }
    $urlto = $CFG->wwwroot.'/local/custompage/logincms.php';
    redirect($urlto, 'Data Updated Successfully ', 8); 

}

$mform->display();

echo $OUTPUT->footer();
?>