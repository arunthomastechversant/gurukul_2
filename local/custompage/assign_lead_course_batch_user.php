<?php
	

require_once('../../config.php');
require_login(0, false);
global $DB, $CFG;

//  Assign  Lead Students to Selected Batch

if (isset($_POST['process_assign'])) {
	if (isset($_POST['users'])) {
            $users = $_POST['users'];
            $batchid = $_POST['batch_id'];
                  foreach ($users as $user){
                        $userObj = new stdClass();
                        $userObj->userid = $user;
                        $userObj->batchid = $batchid;
                        $userObj->created_at = time();
                        $DB->insert_record('lead_batch_user_assignments',$userObj);
                  }
                  echo "Assigned!!";
            print_r($users);exit();
        }
} 

//  Assign  Lead Trainers to Selected Course

if (isset($_POST['enrol_trainer'])) {
  if (isset($_POST['users'])) {
            $users = $_POST['users'];
            $course_id = $_POST['course_id'];
            $roleId = $DB->get_record('role', array('shortname' => "lt"))->id;
                  foreach ($users as $userId){
                        $count == 0;
                      $coursecontext = context_course::instance($course_id);
                      $instance = $DB->get_record('enrol', array('enrol' => 'manual','courseid' => $course_id));
                      $enrol = enrol_get_plugin('manual');

                      if(!($DB->record_exists('role_assignments', array('userid' => $userId, 'roleid' => $roleId)))){ 
                      // if(!is_enrolled($coursecontext, $userId)){
                            $enrol->enrol_user($instance, $userId, $roleId);
                            $count ++;
                      // print_r($enrol);exit(); 
                      }
                  }
                  if($count == 0){
                        echo " Allready Enrolled!!";
                  }else{
                        echo " Batch Enroled successfully!!";
                  }
            // print_r($users);exit();
        }
} 

//  Assign  Lead Users to Selected Batch

if (isset($_POST['assign_users'])) {
        if (isset($_POST['assign_users'])) {
            $users = $_POST['assign_users'];
            $batchid = $_POST['batchid'];
       		foreach ($users as $user){

       			$userObj = new stdClass();
       			$userObj->userid = $user;
       			$userObj->batchid = $batchid;
       			$userObj->created_at = time();
            // print_r($user);exit();
       			$DB->insert_record('lead_batch_user_assignments',$userObj);
       		}
       		echo "Assigned!!";
        }
}

// Enrol batch users to Selected Coures (batch wise)

if (isset($_POST['assign_batches'])) {
        if (isset($_POST['courseid'])) {
            $batches = $_POST['assign_batches'];
            $course_id = $_POST['courseid'];
            // $role_type = $_POST['role_type'];
            // print_r($roleId);exit();
            // if($role_type == 'trainer') {
            //       $roleId = $DB->get_record('role', array('shortname' => 'lt'))->id;
            // }
            // if ($role_type == 'student') {
            //       $roleId = $DB->get_record('role', array('shortname' => 'student'))->id;
            // }
            // print_r($course_id);exit();
            $users = array();
            foreach ($batches as $batch){
                  $user = $DB->get_records('lead_batch_user_assignments', array('batchid' => $batch));
                  array_push($users, $user);
                  
                  if(!($DB->record_exists('lead_batch_assigned_courses', array('batchid' => $batch)))){
                        $userObj = new stdClass();
                        $batchObj->batchid = $batch;
                        $batchObj->courseid = $course_id;
                        $batchObj->created_at = time();
                        // print(arg)t_r($user);exit();
                        $DB->insert_record('lead_batch_assigned_courses',$batchObj);
                  }
            }

            foreach ($users as $userdata){
                  // $ss=$DB->get_records('lead_batches');
                  foreach ($userdata as $user){
                        $count = 0;
                        $userId = $user->userid;
                        $coursecontext = context_course::instance($course_id);
                        $roleId = $DB->get_record('role', array('shortname' => "student"))->id;
                        // print_r($roleId);exit();

                        $instance = $DB->get_record('enrol', array('enrol' => 'manual','courseid' => $course_id));
                        $enrol = enrol_get_plugin('manual');
                        // print_r($instance);exit();  
                        if(!is_enrolled($coursecontext, $userId)){
                              $enrol->enrol_user($instance, $userId, $roleId);
                              $count ++;
                        }
                  }   
                  if($count == 0){
                        echo " Allready Enrolled!!";
                  }else{
                        echo " Batch Enroled successfully!!";
                  }
            }
            // Un-assign batch from mdl_lead_batch_course_assignments table
            // $DB->delete_record('lead_batch_course_assignments',$course);

      }
}

// Enrol Trainers to Selected Coures 
if (isset($_POST['values'])) {
        if (isset($_POST['course_id'])) {
                $userIds = $_POST['values'];
                $course_id = $_POST['course_id'];
                $role_type = $_POST['role_type'];
                $roleId = "";
                // print_r($roleId);exit();
                if($role_type == 'trainer') {
                        $roleId = $DB->get_record('role', array('shortname' => 'lt'))->id;
                }
                if ($role_type == 'student') {
                        $roleId = $DB->get_record('role', array('shortname' => 'student'))->id;
                }
                foreach ($userIds as $userId){
                        // $role= $DB->get_record('role', array('shortname' => 'lt'))->id;

                        $coursecontext = context_course::instance($course_id);
                        
                                
                        if(!is_enrolled($coursecontext, $userId)){
                                $enrol = enrol_get_plugin('manual');
                                $instance = $DB->get_record('enrol', array('enrol' => 'manual','courseid' => $course_id));
                                
                                $enrol->enrol_user($instance, $userId, $roleId);
                                        echo "Enrolled!!";
                        }
                }
                        
        }
        else{ print_r(error); }
}
