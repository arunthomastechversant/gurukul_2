<?php
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');


require_login();
$drive_id = optional_param('drive_id', '', PARAM_RAW);
$userid = optional_param('userid', '', PARAM_RAW);
$context = context_system::instance();
$linkurl = new moodle_url('/local/custompage/detailview.php');


// $enrolled_users = $DB->get_record_sql("select mqa.id as attemptid,uu.userid,mqa.uniqueid,mqa.sumgrades,qz.sumgrades as sumg,mqa.timestart,qz.name,mcm.id as quizid from mdl_urdc_user_detail uu JOIN mdl_user u ON u.id=uu.userid JOIN mdl_groups g 
// ON g.id=uu.test_groupid JOIN mdl_urdc_recruitment_drive brd ON brd.id=uu.recruitment_id join mdl_quiz_attempts mqa on mqa.userid = uu.userid join {quiz} as qz on qz.id = mqa.quiz join mdl_course_modules as mcm on mcm.instance = qz.id
// WHERE uu.recruitment_id =$drive_id and u.id = $userid");
// $userid = 123;
$title = "Candidate Images";
// $linktext = "Assessment Report (".$enrolled_users->name." )";
// Set the url.

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($title);
// Set the page heading.
$PAGE->set_heading($title);
$context = context_system::instance();
$companyid = iomad::get_my_companyid($context);
echo $OUTPUT->header();
$datas = $DB->get_records_sql("SELECT user_image,face_match from {proctoringdetails} where userid = $userid limit 350");
// print_r($userid);exit();

if($datas){
    echo '<section class="three-column-images">
               <div class="container" ><br/><h3></h3>
                   <div class="row">';
   foreach($datas as $data){
        echo '<div class="col-sm-2">
            <img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'" class="img-thumbnail img-responsive">
            <i class=""></i><span>'.$data->face_match.'</span></br>
            </div>';
   }
   echo '</div></div></section>';
}

echo $OUTPUT->footer();

?>
<style>
    .three-column-images
    {
        /* background:skyblue; */
    }
    .three-column-images .row
    {
        text-align:center;
    }
    .three-column-images img
    {
        margin-bottom:10px;
        margin-top:50px;
    }

    .three-column-images i
    {
        font-weight:bold;
    }

    .container .email-signature .image img{
        height:30px;
        width:30px;
        object-fit:cover;
        border-radius:50%;
        border:2px solid white;
        margin-top:5px;
        float:left;
        align:left;
        }
         .container .email-signature .image h4{
         font-size:17px;
         float:left;
         padding-left:7px;
         margin-top:12px;
         /* letter-spacing:20px; */
         }
         .container .email-signature  i{
         height:25px;
         width:25px;
         line-height:35px;
         text-align:center;
         margin-top:0px;
         font-size:21px;
         }

    .gauge {
      width: 320px;
      height: 240px;
    }
    .circular--square {
        border-radius: 50%;
        width : 50px;
    }
    .for-label{
        font-size : 25px;
        margin-top: 10px;
    }
    
</style>

