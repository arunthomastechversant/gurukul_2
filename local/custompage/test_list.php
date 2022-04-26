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
$PAGE->set_title("Test List");
$PAGE->set_url($CFG->wwwroot.'/local/custompage/test_list.php');
$PAGE->navbar->add('Test List', new moodle_url('/local/custompage/test_list.php'));
require_login();
echo $OUTPUT->header();
$PAGE->set_context(context_system::instance());

$baseurl = new moodle_url('/local/custompage/test_list.php');
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 10, PARAM_INT);

$t_count = count($DB->get_records_sql("SELECT * FROM {rsl_user_detail}"));

$start = $page * $perpage;
if ($start > $t_count) {
    $page = 0;
    $start = 0;
}


   $table = new html_table();
    $table->head = array("S.No",
            "Username",
            "Password",
            "Recruitment Drive",
            "Time");
            
    $table->data = array();
    $table->class = '';
    $table->id = '';
        
$i = 1;
if($page != 0){
	$i = ($page * $perpage)+1;
}


$datas = $DB->get_records_sql("SELECT * FROM {rsl_user_detail} ",array(),$start, $perpage);
// print_r($datas);exit;
$actions = '';

if(count($datas) >=1){
        foreach($datas as $data){
                    
            if($data->recruiment_id ==1){
                $drive='Drive1';
            }else{
                $drive='Drive2';
            }

       

        $table->data[] = array(
                        $i,
                        $data->username,
                        $data->password,
                        $drive,
                        date('d/m/Y H:i:s', $data->timestamp)
                        );
                        $i=$i+1;
        }
}else{
    $table = new html_table();
    $table->head = array("S.No",
            "Username",
            "Password",
            "Recruitment Drive",
            "Time");
            
    $table->data = array();
    $table->class = '';
    $table->id = '';

        $table->data[] = array(
            "",
            "",
            "No Record Found",
            "",
            "");

}        
$add_organization = $CFG->wwwroot.'/local/custompage/create_ru_form.php';

echo '<a href="'.$add_organization .'" class="btn btn-secondary" >Add RSL Users</a>';
echo html_writer::table($table);
echo $OUTPUT->paging_bar($t_count, $page, $perpage, $baseurl);

echo $OUTPUT->footer();

