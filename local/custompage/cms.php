<?php
require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/csvlib.class.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/local/custompage/cms_form.php');
global $USER;

$context = context_system::instance();
require_login();


// Correct the navbar .
// Set the name for the page.
$linktext = "Site Content Management";
// Set the url.
$linkurl = new moodle_url('/local/custompage/cms.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);
// Set the page heading.
$PAGE->set_heading($linktext);
$PAGE->navbar->add('Site Content Management', new moodle_url($CFG->wwwroot.'/local/custompage/cms.php'));
$systemcontext = context_system::instance();
$companyid = iomad::get_my_companyid($systemcontext);
echo $OUTPUT->header();
$context = context_user::instance($companyid, MUST_EXIST);
$contextslider = context_user::instance($USER->id, MUST_EXIST);
$mform = new cms_form();

$thankyoudata = new stdClass;
$thankyoudata->id = 1;
$draftitemid = file_get_submitted_draft_itemid('thankyoupage');
file_prepare_draft_area($draftitemid, $context->id, 'local_custompage', 'thankyoupage', $companyid,
                        array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 1));
$thankyoudata->thankyoupage = $draftitemid;
$mform->set_data($thankyoudata);

$welcomedata = new stdClass;
$welcomedata->id = 1;
$draftitemid = file_get_submitted_draft_itemid('welcomepage');
file_prepare_draft_area($draftitemid, $context->id, 'local_custompage', 'welcomepage', $companyid,
                        array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 1));
$welcomedata->welcomepage = $draftitemid;
$mform->set_data($welcomedata);

$assessmentdata = new stdClass;
$assessmentdata->id = 1;
$draftitemid = file_get_submitted_draft_itemid('assessmentpage');
file_prepare_draft_area($draftitemid, $context->id, 'local_custompage', 'assessmentpage', $companyid,
                        array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 1));
$assessmentdata->assessmentpage = $draftitemid;
$mform->set_data($assessmentdata);

if($mform->is_cancelled()){
    $return = $CFG->wwwroot.'/my';
    redirect($return);
}else if($formdata = $mform->get_data()){
    if($formdata->thankyoupage){
        file_save_draft_area_files($formdata->thankyoupage, $context->id , 'local_custompage', 'thankyoupage',$companyid, array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 1));
    }
    if($formdata->welcomepage){
        file_save_draft_area_files($formdata->welcomepage, $context->id , 'local_custompage', 'welcomepage',$companyid, array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 1));
    }
    if($formdata->assessmentpage){
        file_save_draft_area_files($formdata->assessmentpage, $context->id , 'local_custompage', 'assessmentpage',$companyid, array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => 1));
    }
    $cmsid = $DB->get_record('cms',array('company_id' => $companyid,'type' => 'thankyou'))->id;
    if($cmsid){
        $data = new stdclass();
        $data->id = $cmsid;
        $data->heading = $formdata->thankyouheading;
        $data->content = $formdata->thankyoucontent;
        $DB->update_record('cms',$data);
    }else{
        $data = new stdclass();
        $data->company_id = $companyid;
        $data->type = 'thankyou';
        $data->heading = $formdata->thankyouheading;
        $data->content = $formdata->thankyoucontent;
        $DB->insert_record('cms',$data);
    }

    $cmsid = $DB->get_record('cms',array('company_id' => $companyid,'type' => 'welcome'))->id;
    if($cmsid){
        $data = new stdclass();
        $data->id = $cmsid;
        $data->heading = $formdata->welcomeheading;
        $data->content = $formdata->welcomecontent;
        $DB->update_record('cms',$data);
    }else{
        $data = new stdclass();
        $data->company_id = $companyid;
        $data->type = 'welcome';
        $data->heading = $formdata->welcomeheading;
        $data->content = $formdata->welcomecontent;
        $DB->insert_record('cms',$data);
    }
    $urlto = $CFG->wwwroot.'/local/custompage/cms.php';
    redirect($urlto, 'Data Updated Successfully ', 8); 

}

$mform->display();

echo $OUTPUT->footer();
?>