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
 * For a given question type, list the number of
 *
 * @package    report
 * @subpackage elearning
 * @copyright  2008 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require(__DIR__.'/../../config.php');
// print_r("test");exit;
global $DB, $USER, $COURSE, $PAGE, $CFG;
$PAGE->set_title("BT recruitment_drive List");
$PAGE->set_heading('BT Score List'); 
$PAGE->set_url($CFG->wwwroot.'/local/custompage/bu_drive_list.php');
$PAGE->navbar->add('BT Score List', new moodle_url('/local/custompage/bu_drive_list.php'));
require_login();
echo $OUTPUT->header();
$PAGE->set_context(context_system::instance());

$baseurl = new moodle_url('/local/custompage/bu_drive_list.php');
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 10, PARAM_INT);

$t_count = count($DB->get_records_sql("SELECT rd.id,rd.name as drive_name,q.name as test,q.timelimit FROM {bt_recruitment_drive} as rd INNER JOIN {bt_user} as bu INNER JOIN {quiz} as q where rd.id = bu.drive_id and rd.test = q.id"));

$start = $page * $perpage;
if ($start > $t_count) {
    $page = 0;
    $start = 0;
}


   $table = new html_table();
    $table->head = array("S.No",
            "Drive Name",
            "Test",
            "Time(In seconds)",
            "Actions",
            // "Actions"
        );
            
    $table->data = array();
    $table->class = '';
    $table->id = '';
        
$i = 1;
if($page != 0){
	$i = ($page * $perpage)+1;
}

// $datas = $datas = $DB->get_records_sql("SELECT rd.id,rd.name as drive_name,q.name as test,q.timelimit FROM {bt_recruitment_drive} as rd INNER JOIN {bt_user} as bu INNER JOIN {quiz} as q where rd.id = bu.drive_id and rd.test = q.id",array(),$start, $perpage);
$datas = $datas = $DB->get_records_sql("SELECT rd.id,rd.name as drive_name,q.name as test,q.timelimit FROM {bt_recruitment_drive} as rd INNER JOIN {quiz} as q where rd.test = q.id");
if(count($datas) >=1){
        foreach($datas as $data){

            $users_url=$CFG->wwwroot."/local/custompage/bu_drive_list_detail.php?drive_id=".$data->id;
            $users_list = '<a href="' . $users_url .
            '">View Users</a>';

        $table->data[] = array(
                        $i,
                        $data->drive_name,                       
                        $data->test,
                        $data->timelimit,
                        $users_list 

                        // $actions
                    );
                        $i=$i+1;
        }
}else{
    $table = new html_table();
    $table->head = array("S.No",
    "Name",
    "Number Of Users",
    "Time(In seconds)",
    "Action",
    );
            
    $table->data = array();
    $table->class = '';
    $table->id = '';

        $table->data[] = array(
            "",
            "",
            "No Record Found",
            "",
             ""
        );

}        

echo html_writer::table($table);

echo $OUTPUT->footer();

