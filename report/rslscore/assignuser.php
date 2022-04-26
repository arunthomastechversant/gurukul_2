<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Adds new instance of enrol_payu to specified course
 * or edits current instance.
 *
 * @package    enrol_payu
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/course/modlib.php');
require_once($CFG->dirroot.'/mod/quiz/mod_form.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot.'/group/lib.php');
require_once($CFG->dirroot.'/group/group_form.php');
require_once($CFG->dirroot. '/group/lib.php');
require_once($CFG->dirroot . '/user/selector/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

require_login();
$id = optional_param('id', '', PARAM_RAW);
$did  = optional_param('did', '', PARAM_RAW);
$uid  = optional_param('uid', '', PARAM_RAW);


$PAGE->set_pagelayout('admin');
$PAGE->set_title("Assign RSL User");
$PAGE->set_heading("Assign RSL User");
$PAGE->set_url($CFG->wwwroot.'/report/rslscore/assignuser.php');

if($uid){
    $assignuser=$CFG->wwwroot.'/report/rslscore/assignuser.php?id='.$id.'&did='.$did.'&uid='.$uid;
}else{
    $assignuser=$CFG->wwwroot."/report/rslscore/assignuser.php?id=".$id."&did=".$did;
}
$coursenode = $PAGE->navbar->add('Assign RSL User', $assignuser);
$PAGE->set_context(context_system::instance());
//~ $url = $CFG->wwwroot.'/local/custompage/get_data.php';
$return = $CFG->wwwroot."/report/rslscore/users.php?id=".$id."&did=".$did;
echo $OUTPUT->header();

if($uid){

    $user = $DB->get_record('user', array('id' => $uid, 'deleted' => 0), '*', MUST_EXIST);
    $enrol = enrol_get_plugin('manual');
    $nodays=15;
    $course_id=$DB->get_records_sql("select *  from {course} where shortname ='rsl-learning'");
    
    foreach ($course_id as $key => $course){
                $enrolmethod='manual';   
                $course = $DB->get_record('course', array('id' => $course->id), '*', MUST_EXIST);      
                $instance = $DB->get_record('enrol', array('enrol' => 'manual','courseid' => $course->id));
    
    
                $end_date = strtotime("+$nodays day", time());
                $enrol->enrol_user($instance, $user->id, 5,time(),$end_date);
    
    }
    redirect($return);
}else{
    $enrolled_users =$DB->get_records_sql("select ru.*,g.name AS groupname,u.email,rrd.interview,ru.userid AS userid,rrd.test As testid ,(select round(grade * 10) from mdl_quiz_grades where quiz = rrd.test and userid = ru.userid) as testper, case when rrd.interview = 1 then (select round(grade * 10) from mdl_quiz_grades where quiz = ru.interview_id and userid = ru.userid) else 'No Interview' end interviewper from mdl_rsl_user_detail ru INNER JOIN mdl_user u ON u.id=ru.userid JOIN mdl_groups g ON g.id=ru.test_groupid JOIN mdl_rsl_recruitment_drive rrd ON rrd.id=ru.recruitment_id WHERE ru.recruitment_id =$did");


    foreach($enrolled_users as $key => $value){

        // above 70

        if($id == 3){
            if($value->interview == 1){

                $user = $DB->get_record('user', array('id' => $value->userid, 'deleted' => 0), '*', MUST_EXIST);
                $enrol = enrol_get_plugin('manual');
                $nodays=15;
                $course_id=$DB->get_records_sql("select *  from {course} where shortname ='rsl-learning'");
                
                foreach ($course_id as $key => $course){
                            $enrolmethod='manual';   
                            $course = $DB->get_record('course', array('id' => $course->id), '*', MUST_EXIST);      
                            $instance = $DB->get_record('enrol', array('enrol' => 'manual','courseid' => $course->id));
                
                
                            $end_date = strtotime("+$nodays day", time());
                            $enrol->enrol_user($instance, $user->id, 5,time(),$end_date);
                
                }


            }else{
                if($value->testper > 70 ){

                    $user = $DB->get_record('user', array('id' => $value->userid, 'deleted' => 0), '*', MUST_EXIST);
                    $enrol = enrol_get_plugin('manual');
                    $nodays=15;
                    $course_id=$DB->get_records_sql("select *  from {course} where shortname ='rsl-learning'");
                    
                    foreach ($course_id as $key => $course){
                                $enrolmethod='manual';   
                                $course = $DB->get_record('course', array('id' => $course->id), '*', MUST_EXIST);      
                                $instance = $DB->get_record('enrol', array('enrol' => 'manual','courseid' => $course->id));
                    
                    
                                $end_date = strtotime("+$nodays day", time());
                                $enrol->enrol_user($instance, $user->id, 5,time(),$end_date);
                    
                    }

                }
            }
        }
        
        // 50 to 70 per

        if($id == 2){
            if($value->interview == 1){
                if($value->testper <= 70 && $value->interviewper <= 70  && $value->testper >= 50 && $value->interviewper >= 50){

                    
                $user = $DB->get_record('user', array('id' => $value->userid, 'deleted' => 0), '*', MUST_EXIST);
                $enrol = enrol_get_plugin('manual');
                $nodays=15;
                $course_id=$DB->get_records_sql("select *  from {course} where shortname ='rsl-learning'");
                
                foreach ($course_id as $key => $course){
                            $enrolmethod='manual';   
                            $course = $DB->get_record('course', array('id' => $course->id), '*', MUST_EXIST);      
                            $instance = $DB->get_record('enrol', array('enrol' => 'manual','courseid' => $course->id));
                
                
                            $end_date = strtotime("+$nodays day", time());
                            $enrol->enrol_user($instance, $user->id, 5,time(),$end_date);
                
                }
                }

            }else{
                if($value->testper <= 70 && $value->testper >= 50 ){
                    
                $user = $DB->get_record('user', array('id' => $value->userid, 'deleted' => 0), '*', MUST_EXIST);
                $enrol = enrol_get_plugin('manual');
                $nodays=15;
                $course_id=$DB->get_records_sql("select *  from {course} where shortname ='rsl-learning'");
                
                foreach ($course_id as $key => $course){
                            $enrolmethod='manual';   
                            $course = $DB->get_record('course', array('id' => $course->id), '*', MUST_EXIST);      
                            $instance = $DB->get_record('enrol', array('enrol' => 'manual','courseid' => $course->id));
                
                
                            $end_date = strtotime("+$nodays day", time());
                            $enrol->enrol_user($instance, $user->id, 5,time(),$end_date);
                
                }


                }
            }
        }

        // Below 50
                    // above 70

        if($id == 3){
            if($value->interview == 1){
                if($value->testper < 50 && $value->interviewper < 50){
                    
                $user = $DB->get_record('user', array('id' => $value->userid, 'deleted' => 0), '*', MUST_EXIST);
                $enrol = enrol_get_plugin('manual');
                $nodays=15;
                $course_id=$DB->get_records_sql("select *  from {course} where shortname ='rsl-learning'");
                
                foreach ($course_id as $key => $course){
                            $enrolmethod='manual';   
                            $course = $DB->get_record('course', array('id' => $course->id), '*', MUST_EXIST);      
                            $instance = $DB->get_record('enrol', array('enrol' => 'manual','courseid' => $course->id));
                
                
                            $end_date = strtotime("+$nodays day", time());
                            $enrol->enrol_user($instance, $user->id, 5,time(),$end_date);
                
                }



                }

            }else{
                if($value->testper  < 50 ){
                    
                $user = $DB->get_record('user', array('id' => $value->userid, 'deleted' => 0), '*', MUST_EXIST);
                $enrol = enrol_get_plugin('manual');
                $nodays=15;
                $course_id=$DB->get_records_sql("select *  from {course} where shortname ='rsl-learning'");
                
                foreach ($course_id as $key => $course){
                            $enrolmethod='manual';   
                            $course = $DB->get_record('course', array('id' => $course->id), '*', MUST_EXIST);      
                            $instance = $DB->get_record('enrol', array('enrol' => 'manual','courseid' => $course->id));
                
                
                            $end_date = strtotime("+$nodays day", time());
                            $enrol->enrol_user($instance, $user->id, 5,time(),$end_date);
                
                }




                }
            }
        }



  
    }

    redirect($return);

}


echo $OUTPUT->footer();






