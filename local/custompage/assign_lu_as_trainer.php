<?php

require_once('../../config.php');
require_login(0, false);
global $DB, $CFG;


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
                        // if(!is_enrolled($coursecontext, $userId)){
                          
                                // $enrolmethod = "manual";
                                // $user = $DB->get_record('user', array('id' => $userId, 'deleted' => 0), '*', MUST_EXIST);
                                // $course = $DB->get_record('course', array('id' => $course_id), '*', MUST_EXIST);
                                // $context = context_course::instance($course_id);
                                // $enrol = enrol_get_plugin($enrolmethod);
                                // $instances = enrol_get_instances($course_id, true);
                                // $manualinstance = null;
                                // foreach ($instances as $instance) {
                                //         if ($instance->name == $enrolmethod) {
                                //                 $manualinstance = $instance;
                                //                 break;
                                //         }
                                // }
                                // if ($manualinstance !== null) {
                                //         $instanceid = $enrol->add_default_instance($course);
                                //         if ($instanceid === null) {
                                //                 $instanceid = $enrol->add_instance($course);
                                //         }
                                //         $instance = $DB->get_record('enrol', array('id' => $instanceid));
                                // }
                                // $enrol->enrol_user($instance, $userId, 20);
                                
                // print_r($roleId);exit();
                                
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

