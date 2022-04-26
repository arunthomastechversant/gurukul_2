<?php
require_once('../config.php');
// require_login(0, false);

global $DB, $CFG;

// states displaying
// if($_POST['type'] == 1){
//     $select_data = '<option value="" selected disabled>---- Choose a State ----</option>';
//     $states = $DB->get_records_sql("SELECT * from {states}");
//     foreach($states as $keys => $state){
//        $select_data .='<option value =' .$state->state_name.'>' .$state->state_name.'</option>';
//     }
//     echo $select_data;
// }
if(isset($_POST['state_name'])){
    $state_name = $_POST['state_name'];
    $state_id = $DB->get_record('states',array('state_name' => $state_name))->id;
    $select_data = '<option value="" selected disabled>---- Choose a District ----</option>';
    $cities = $DB->get_records_sql("SELECT * from {cities} where state_id = $state_id");
    foreach($cities as $keys => $city){
       $select_data .='<option value =' .$city->city_name.'>' .$city->city_name.'</option>';
    }
    echo $select_data;
}

?>
