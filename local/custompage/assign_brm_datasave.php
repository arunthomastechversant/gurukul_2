<?php
require('../../config.php');
$data = new stdClass();
$systemcontext = context_system::instance();
$companyid = iomad::get_my_companyid($systemcontext);
$data->brmid = $_POST['brm_id'];
$data->companyid = $companyid;
$values = $_POST['values'];
// print_r($values);
foreach($values as $key => $val){
    $data->userid = $val;
    $data->timestamp = time();
    $DB->insert_record('brm_assignements', $data);
    // print_r($data);
} 	
echo 'Successfully assigend users';

?>