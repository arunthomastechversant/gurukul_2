<?php
require_once(dirname(__FILE__) . '/../../config.php');

// require_login();
// require_user();
global $DB,$CFG,$PAGE,$USER,$SESSION,$PAGE,$OUTPUT;
//$companyid = $DB->get_record('company_users',array('userid' => $USER->id))->companyid;
$datas = $DB->get_records('proctoringdetails',array('userid' => 6949));
// print_r($data);exit();
//$imageds =$DB->get_records('user_proctoringimages',array('userid' => 6949));
foreach($datas as $data){
    if($data->mouth_open_count == 1){
        echo '<img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'"/>';
    }else if($data->mobile_phone_count == 1){
        echo '<img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'"/>';
    }else if($data->more_person == 1){
        echo '<img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'"/>';
    }else if($data->no_person == 1){
        echo '<img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'"/>';
    }else if($data->head_up == 1){
        echo '<img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'"/>';
    }else if($data->head_down == 1){
        echo '<img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'"/>';
    }else if($data->head_left == 1){
        echo '<img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'"/>';
    }else if($data->head_right == 1){
        echo '<img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'"/>';
    }else if($data->face_recognition == 1){
        echo '<img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'"/>';
    }
    // if($data != "")
    // {
    //     echo '<img src="data:image/jpeg;base64,'.base64_encode( $data->user_image ).'"/>';
    //     // echo html_writer::empty_tag('img', array('src' => $data->user_image, 'width' =>160,'height'=>150 ));
    // }
    
}

//foreach($imageds as $imaged){
  //  if($imaged)
    //{
//	echo html_writer::empty_tag('img', array('src' => $imaged->userimage, 'width' =>160,'height'=>150 ));
  //  }
//}
?>
