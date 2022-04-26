<?php
use core_completion\progress;
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->libdir.'/accesslib.php');
//require_once($CFG->libdir. '/coursecatlib.php');
require_once($CFG->dirroot."/local/coursecompletion/lib/dedication_lib.php");

function coursestatus(){
    global $CFG,$DB;
    $dedicationtime;
    $get_course = $DB->get_records_sql("SELECT courseid from {question_category_mapping} ORDER BY id desc ");

    foreach ($get_course as $key => $value )
    {
        
        $get_users = $DB->get_records_sql("SELECT u.id,
                                            u.username,
                                            u.email,
                                            ue.timestart AS timestart,
                                            ue.timeend AS timeend,
                                            ud.recruitment_id
                                            FROM   {user} u 
                                            JOIN {user_enrolments} ue   ON ue.userid = u.id
                                            LEFT JOIN {enrol} e  ON ue.enrolid = e.id  
                                            LEFT JOIN {rsl_user_detail} ud  ON ud.userid = u.id             
                                            WHERE  e.courseid=$value->courseid AND e.enrol = 'manual'  ");
                                            // print_r($get_course );exit;
                                            
        foreach ($get_users as $ukey => $uvalue )
        {
         
            $get_userrole = $DB->get_records_sql("SELECT * from {role_assignments} where userid= $uvalue->id AND roleid =5");
            $userstatus='RSL Completed';
            // $rectdetail =$DB->get_record('userstatus', array('userid' => $uvalue->id,'recruitment_id' => $uvalue->recruitment_id));
        
        //  print_r("SELECT * from mdl_userstatus where userid= $uvalue->id AND recruitment_id =$rectid AND userstatus='RSL Completed'");exit;
        // $rectdetail = $DB->get_records_sql("SELECT * from mdl_userstatus where userid= $uvalue->id  AND userstatus='RSL Completed'");
        //     $rectdetail = $DB->get_records_sql("SELECT * from mdl_userstatus where userid= $uvalue->id  ");
            // print_r("SELECT * from mdl_userstatus where userid= $uvalue->id AND recruitment_id =$uvalue->recruitment_id AND userstatus='RSL Completed'");exit;
	    // print_r( date('Ymd', time()));exit;
	   // print_r($get_userrole);print_r(date('ymd',$uvalue->timeend));echo '<br>';print_r(date('ymd',time()));exit;  
            if($get_userrole){
                if(date('Ymd', time()) == date('Ymd', $uvalue->timeend)  ){   
                   //print_r("equal");exit;
                    // print_r($uvalue);
                    $record2 = new stdClass();
                    $record2->userid = $uvalue->id;
                    $record2->recruitment_id =  $uvalue->recruitment_id;	
                    $record2->userstatus =  'RSL Completed';	
                    $record2->timestamp =  time();
                   
                    $rectdetail = $DB->get_record_sql("SELECT * from mdl_userstatus where userid= $uvalue->id ORDER BY id desc LIMIT 1 ");
                    if($rectdetail->userstatus == 'Assigned For RSL' && $rectdetail->userstatus != 'RSL Completed')
                    $DB->insert_record('userstatus', $record2);
                     
                }
                
            }
        }
        

    }
    

  


} 
?>

