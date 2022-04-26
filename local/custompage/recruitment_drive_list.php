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
require_login();
$systemcontext = context_system::instance();
$companyid = iomad::get_my_companyid($systemcontext);
$company = $DB->get_record('company',array('id' => $companyid))->shortname;
$PAGE->set_title($company." recruitment drive List");
$PAGE->set_heading($company." Recruitment Drive List");
$PAGE->set_url($CFG->wwwroot.'/local/custompage/recruitment_drive_list.php');
$PAGE->navbar->add($company.' Recruitment Drive List', new moodle_url('/local/custompage/create_recruitment_drive_form.php'));
$PAGE->navbar->add('Add '.$company.' Users', new moodle_url($CFG->wwwroot.'/local/custompage/create_users.php'));

echo $OUTPUT->header();
$PAGE->set_context(context_system::instance());
$systemcontext = context_system::instance();
$companyid = iomad::get_my_companyid($systemcontext);
// print_r($companyid);exit();

$baseurl = new moodle_url('/local/custompage/recruitment_drive_list.php');
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 10, PARAM_INT);

$t_count = count($DB->get_records_sql("SELECT * FROM {recruitment_drive} where company_id = $companyid"));

$start = $page * $perpage;
if ($start > $t_count) {
    $page = 0;
    $start = 0;
}


   $table = new html_table();
    $table->head = array("S.No",
            "Name",
            "Type",
            "Test Name",

            // "Actions"
        );
            
    $table->data = array();
    $table->class = '';
    $table->id = '';
        
$i = 1;
if($page != 0){
	$i = ($page * $perpage)+1;
}


$datas = $DB->get_records_sql("SELECT * FROM {recruitment_drive} ORDER by id DESC ",array(),$start, $perpage);
$actions = '';

if(count($datas) >=1){
        foreach($datas as $data){
            $type      =  "";
            $quiz = $DB->get_record_sql("SELECT * FROM {quiz} WHERE id = $data->test");
                    if($data->interview == 1){
                        // print_r("test" );exit;

                        // print_r($inter );exit;
                        $type      =  "Test + Interview";
                    }else{
                        // print_r("kvbala");
                        $type      =  "Test";			 
                    }
            $buttons = array();

            //delete button
            $buttons[] = html_writer::link(new moodle_url($CFG->wwwroot . '/local/custompage/create_recruitment_drive_form.php?deleteid='.$data->id), 
                    $OUTPUT->pix_icon('t/delete', get_string('delete')),
                    array('title' => get_string('delete')));

            


            $actions = implode(' ', $buttons);

       

        $table->data[] = array(
                        $i,
                        $data->name,                       
                        $type,
                        $quiz->name,

                        // $actions
                    );
                        $i=$i+1;
        }
}else{
    $table = new html_table();
    $table->head = array("S.No",
    "Name",
    "Type",
    "Test Name",
    // "Actions"
    );
            
    $table->data = array();
    $table->class = '';
    $table->id = '';

        $table->data[] = array(
            "",
            "",
            "No Record Found",
            "",
            // ""
        );

}        
$add_recruitment_drive = $CFG->wwwroot.'/local/custompage/create_new_recruitment_drive.php';

echo '<a href="'.$add_recruitment_drive .'" class="btn btn-secondary" >Add recruitment drive</a>';
echo html_writer::table($table);
echo $OUTPUT->paging_bar($t_count, $page, $perpage, $baseurl);

echo $OUTPUT->footer();

