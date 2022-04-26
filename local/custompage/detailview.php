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
<?php
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');


require_login();
$drive_id = optional_param('drive_id', '', PARAM_RAW);
$userid = optional_param('userid', '', PARAM_RAW);
$context = context_system::instance();
$linkurl = new moodle_url('/local/custompage/detailview.php');

$enrolled_users = $DB->get_record_sql("select mqa.id as attemptid,uu.userid,mqa.uniqueid,mqa.sumgrades,qz.sumgrades as sumg,mqa.timestart,qz.name,mcm.id as quizid from mdl_urdc_user_detail uu JOIN mdl_user u ON u.id=uu.userid JOIN mdl_groups g 
ON g.id=uu.test_groupid JOIN mdl_urdc_recruitment_drive brd ON brd.id=uu.recruitment_id join mdl_quiz_attempts mqa on mqa.userid = uu.userid join {quiz} as qz on qz.id = mqa.quiz join mdl_course_modules as mcm on mcm.instance = qz.id
WHERE uu.recruitment_id =$drive_id and u.id = $userid");

//print_r($enrolled_users);exit();
// Correct the navbar .
// Set the name for the page.
$title = "Assessment Report";
$linktext = "Assessment Report (".$enrolled_users->name." )";
// Set the url.

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($title);
// Set the page heading.
$PAGE->set_heading($linktext);
$context = context_system::instance();
$companyid = iomad::get_my_companyid($context);
echo $OUTPUT->header();

$imaged =$DB->get_record_sql("SELECT * FROM {user_proctoringimages} where userid=$userid");
if($imaged)
{
	$image = $imaged->userimage;
}else{
    $image = $CFG->wwwroot.'/local/listcoursefiles/chart/userimage.jpg';
}
$datas = $DB->get_records_sql("SELECT * from {proctoringdetails} where userid = $userid and quizid = $enrolled_users->quizid limit 350");
//print_r($datas);exit();
// $datas = $DB->get_records('proctoringdetails',array('userid' => $userid));
// $datas = $DB->get_records('proctoringdetails',array('userid' => 534));
$email = $DB->get_record('user_info_data',array('fieldid' => 35,'userid' => $userid))->data;
$name = $DB->get_record('user_info_data',array('fieldid' => 34,'userid' => $userid))->data;
$totalgarde = $enrolled_users->sumgrades / $enrolled_users->sumg * 100;
$total = round($totalgarde,2);
if($total <= 34)
    $status = "Low";
else if($total >=65)
    $status = "High";
else
    $status = "Medium";
$date = date('d M, Y H:i:s A',$enrolled_users->timestart);


$uniqueid = $enrolled_users->uniqueid;


$ctry_data = $DB->get_record_sql("SELECT questions FROM {urdc_recruitment_drive} as brd join {test_questioncategory} as tq where brd.test = tq.test_id and brd.id = $drive_id ")->questions;
$ctry_ids = explode(',' , $ctry_data);
foreach($ctry_ids as $key => $value){
    $data_set = explode('-',$value);
    if($data_set[1] > 0){
        $qst_ctry = $DB->get_record_sql("SELECT id,name FROM {question_categories} where id = '$data_set[0]' ")->name;
            // array_push($qstn_ctry,$qst_ctry);
        $sql_question_attempts = "SELECT qa.id,qa.slot,q.category,qa.questionid,qa.maxmark,qas.fraction FROM {$CFG->prefix}question_attempts qa
        JOIN {$CFG->prefix}question_attempt_steps qas ON qas.questionattemptid = qa.id
        JOIN {$CFG->prefix}question q ON q.id = qa.questionid
        JOIN {$CFG->prefix}question_categories qc ON qc.id = q.category
        WHERE qa.questionusageid=$uniqueid and q.category = $data_set[0] order by qa.slot";
        $res_question_attempts = $DB->get_records_sql($sql_question_attempts);
        // $percentage = (floor($res_question_attempts->fraction)/floor($res_question_attempts->maxmark))*100;
        $count = 0;
        $percentage = 0;
        foreach($res_question_attempts as $attempt_data)
        {
            $percentage += (floor($attempt_data->fraction)/floor($attempt_data->maxmark))*100;
            $count++;
        }
        $result = $percentage/$count;
        $final_result = round($result,2);
    }
}


// print_r($email);exit();
// echo ' <button type="button" class="btn btn-secondary btn-sm" id="printdata" style="text-align:none; color: #01766e;">Print</button>';
?>
    <!-- <link href="chart/main.css" rel="stylesheet" type="text/css"/> -->
    <link href="../listcoursefiles/chart/jquery.dvstr_jqp_graph.min.css" rel="stylesheet" type="text/css"/>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="../listcoursefiles/chart/jquery.dvstr_jqp_graph.min.js" type="text/javascript"></script>
    <script src="../listcoursefiles/chart/raphael-2.1.4.min.js"></script>
    <script src="../listcoursefiles/chart/justgage.js"></script>
    <div class="card"><div id="tableContainer" class="card-body table-responsive">
        <div class="container">
            <div class="email-signature">
                <div class="image">
                    <img  src="<?php echo $image ?>"/><h4 class="text"><b><?php echo $name ?></b></h4>
                </div>    
                <div class="full">
                    <br><br><i class="fa fa-envelope fa-lg"></i><span>&nbsp&nbsp&nbsp <?php echo $email ?></span><br><br>
                    <i class="fa fa-calendar fa-lg"></i><span>&nbsp&nbsp&nbsp Appeared on <b><?php echo $date ?></b></span><br><br>
                </div>
            </div>
        </div>
            <div class="graph__header"><br><h3>Candidate's Overall Result</h3></div>
            <div class="wrapper row">
                <div class="box col-4">
                    <div id="g1" class="gauge"></div>
                </div>
                <div class="box col-8">
                    <h1><?php echo $status ?></h1></br>
                    <p>Individuals who are average on Aptitude may, with a few development areas, often be able to obtain and use information to solve problems logically and gain proficiency in skills through learning to perform a specific task, responsibility, or role.</p>
                </div>
            </div>
            <div class="graph__header"><br><br><h3>Competency Snapshot</h3></div>
            <div class="wrapper graph__block" id="graph_03"></div>
            <script>
                jQuery(document).ready(function($) {
                    var g1 = new JustGage({
                        id: 'g1',
                        value: <?php echo $total ?>,
                        min: 0,
                        max: 100,
                        symbol: '%',
                        pointer: true,
                        gaugeWidthScale: 0.6,
                        customSectors: [{
                        color: '#00ff00',
                        lo: 66,
                        hi: 100
                        }, {
                        color: '#ffff00',
                        lo: 34,
                        hi: 66
                        }, {
                        color: '#ff0000',
                        lo: 0,
                        hi: 34
                        }],
                        counter: true
                    });
                    var color_green = '#4f9100';

                    $('#graph_03').dvstr_graph({
                        // title: 'Cinebench R15.038',
                        // unit: 'Score',
                        // better: 'Higher is better',
                        separate: true,
   			grid_wmax: 100,
                        grid_part: 5,
                        // points: [
                        //     {
                        //         title: 'Single Core',
                        //         color: color_green,

                        //     }
                        // ],
                        graphs: [
                            <?php

                            $uniqueid = $enrolled_users->uniqueid;


                            $ctry_data = $DB->get_record_sql("SELECT questions FROM {urdc_recruitment_drive} as brd join {test_questioncategory} as tq where brd.test = tq.test_id and brd.id = $drive_id ")->questions;
                            $ctry_ids = explode(',' , $ctry_data);
                            foreach($ctry_ids as $key => $value){
                                $data_set = explode('-',$value);
                                if($data_set[1] > 0){
                                    $qst_ctry = $DB->get_record_sql("SELECT id,name FROM {question_categories} where id = '$data_set[0]' ")->name;
                                        // array_push($qstn_ctry,$qst_ctry);
                                    $sql_question_attempts = "SELECT qa.id,qa.slot,q.category,qa.questionid,qa.maxmark,qas.fraction FROM {$CFG->prefix}question_attempts qa
                                    JOIN {$CFG->prefix}question_attempt_steps qas ON qas.questionattemptid = qa.id
                                    JOIN {$CFG->prefix}question q ON q.id = qa.questionid
                                    JOIN {$CFG->prefix}question_categories qc ON qc.id = q.category
                                    WHERE qa.questionusageid=$uniqueid and q.category = $data_set[0]";
                                    $res_question_attempts = $DB->get_records_sql($sql_question_attempts);
                                    // $percentage = (floor($res_question_attempts->fraction)/floor($res_question_attempts->maxmark))*100;
                                    $count = 0;
                                    $percentage = 0;
                                    foreach($res_question_attempts as $attempt_data)
                                    {
                                        $percentage += (floor($attempt_data->fraction)/floor($attempt_data->maxmark))*100;
                                        $count++;
                                    }
                                    $result = $percentage/$count;
                                    $final_result = round($result,2);
                            ?>
                                
                            {
                                label: '<?php echo $qst_ctry ?>',
                                color: [
                                    color_green
                                ],
                                value: [
                                    <?php echo $final_result ?>  
                                ]
                            },
                            <?php  }
                        } 
                        ?>
                        ]
                    });
                    
                });
            </script>
        </div>
<?php
if($datas){
     echo '<section class="three-column-images">
                <div class="container" ><br/><h3>Candidate Images</h3>
                    <div class="row">';
     $count = 0;
    foreach($datas as $data){
        if($count <= 5)
            if($data->no_person == 0)
                echo '<div class="col-sm-2">
                        <img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'" class="img-thumbnail img-responsive">
                        </div>';
        $count++;
    }
    echo '</div></div></section>';
    echo '<section class="three-column-images">
            <div class="container" ><br/><h3>Malpractice Images</h3>
                <div class="row">';
    foreach($datas as $data){
        if($data->mouth_open_count == 1){
            if($data->mobile_phone_count == 1){
                echo '<div class="col-sm-2">
                <img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'" class="img-thumbnail img-responsive">
                    <i class="fa fa-openid fa-lg"></i><span>  Mouth Open</span></br>
                    <i class="fa fa-mobile fa-lg"></i><span>  Mobile Phone</span>
                </div>';
            }else if($data->head_up == 1){
                echo '<div class="col-sm-2">
                        <img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'" class="img-thumbnail img-responsive">
                            <i class="fa fa-openid fa-lg"></i><span>  Mouth Open</span></br>
                            <i class="fa fa-arrow-up fa-lg"></i><span>  Head Up</span>
                        </div>';
            }else if($data->head_down == 1){
                echo '<div class="col-sm-2">
                    <img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'" class="img-thumbnail img-responsive">
                        <i class="fa fa-openid fa-lg"></i><span>  Mouth Open</span></br>
                        <i class="fa fa-arrow-down fa-lg"></i><span>  Head Down</span>
                    </div>';
            }else if($data->head_left == 1){
                echo '<div class="col-sm-2">
                        <img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'" class="img-thumbnail img-responsive">
                            <i class="fa fa-openid fa-lg"></i><span>  Mouth Open</span></br>
                            <i class="fa fa-arrow-left fa-lg"></i><span>  Head Left</span>
                        </div>';
            }else if($data->head_right == 1){
                echo '<div class="col-sm-2">
                        <img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'" class="img-thumbnail img-responsive">
                            <i class="fa fa-openid fa-lg"></i><span>  Mouth Open</span></br>
                            <i class="fa fa-arrow-right fa-lg"></i><span>  Head Right</span>
                        </div>';
            }else{
                echo '<div class="col-sm-2">
                <img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'" class="img-thumbnail img-responsive">
                    <i class="fa fa-openid fa-lg"></i><span>  Mouth Open</span>
                </div>';
            }
        }else if($data->mobile_phone_count == 1){
            if($data->more_person == 1){
                echo '<div class="col-sm-2">
                <img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'" class="img-thumbnail img-responsive">
                    <i class="fa fa-mobile fa-lg"></i><span>  Mobile Phone</span>
                    <i class="fa fa-users fa-lg"></i><span>  More Person</span>
                </div>';
            }else{
                echo '<div class="col-sm-2">
                <img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'" class="img-thumbnail img-responsive">
                    <i class="fa fa-mobile fa-lg"></i><span>  Mobile Phone</span>
                 </div>';
            }

        }else if($data->more_person == 1){
            echo '<div class="col-sm-2">
                    <img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'" class="img-thumbnail img-responsive">
                        <i class="fa fa-users fa-lg"></i><span>  More Person</span>
                    </div>';
        }else if($data->no_person == 1){
            echo '<div class="col-sm-2">
                    <img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'" class="img-thumbnail img-responsive">
                        <i class="fa fa-user-times fa-lg"></i><span>  No Person</span>
                    </div>';
        }else if($data->head_up == 1){
            echo '<div class="col-sm-2">
                    <img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'" class="img-thumbnail img-responsive">
                        <i class="fa fa-arrow-up fa-lg"></i><span>  Head Up</span>
                    </div>';
        }else if($data->head_down == 1){
            echo '<div class="col-sm-2">
                <img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'" class="img-thumbnail img-responsive">
                    <i class="fa fa-arrow-down fa-lg"></i><span>  Head Down</span>
                </div>';
        }else if($data->head_left == 1){
            echo '<div class="col-sm-2">
                    <img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'" class="img-thumbnail img-responsive">
                        <i class="fa fa-arrow-left fa-lg"></i><span>  Head Left</span>
                    </div>';
        }else if($data->head_right == 1){
            echo '<div class="col-sm-2">
                    <img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'" class="img-thumbnail img-responsive">
                        <i class="fa fa-arrow-right fa-lg"></i><span>  Head Right</span>
                    </div>';
        }else if($data->face_recognition == 1){
            echo '<div class="col-sm-2">
                    <img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'" class="img-thumbnail img-responsive">
                        <i class="fa fa-user fa-lg"></i><span>  Face Recognition</span>
                    </div>';
        }

        // echo '<img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'"/>';

        // if($data != "")
        // {
        //     echo '<img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'"/>';
        //     // echo html_writer::empty_tag('img', array('src' => $data->user_image, 'width' =>160,'height'=>150 ));
        // }
        
    }
    echo '</div></div></section>';
}


// $imageds =$DB->get_record_sql("SELECT * FROM {user_proctoringimages} where userid=$userid");
// foreach($imageds as $imaged){

// 		if($imaged)
// 		{
// 				echo html_writer::empty_tag('img', array('src' => $imaged->userimage, 'width' =>160,'height'=>150 ));
// 		}
//     }
echo $OUTPUT->footer();
?>
<script>
$('#printdata').click(function() {
        $('.outcome').css('display', 'none');
        var printContents =  document.getElementById('region-main-box').innerHTML;  // pdf_parent
        var originalContents = document.body.innerHTML;
        $('#pdf_coverpage').css('display', 'none');

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        // location.reload();
    });
</script>

