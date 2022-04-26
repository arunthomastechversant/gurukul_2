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
$PAGE->set_title("URDC Organization List");
$PAGE->set_url($CFG->wwwroot.'/local/custompage/urdc_organization_list.php');
$PAGE->navbar->add('URDC Organization List', new moodle_url('/local/custompage/urdc_organization_list.php'));
require_login();
echo $OUTPUT->header();
$PAGE->set_context(context_system::instance());

$baseurl = new moodle_url('/local/custompage/urdc_organization_list.php');
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 10, PARAM_INT);

$t_count = count($DB->get_records_sql("SELECT * FROM {urdc_organization}"));

$start = $page * $perpage;
if ($start > $t_count) {
    $page = 0;
    $start = 0;
}


   $table = new html_table();
    $table->head = array("S.No",
            "Name",
            "Address",
            "Type",
            "Actions");
            
    $table->data = array();
    $table->class = '';
    $table->id = '';
        
$i = 1;
if($page != 0){
	$i = ($page * $perpage)+1;
}


$datas = $DB->get_records_sql("SELECT * FROM {urdc_organization} ",array(),$start, $perpage);
$actions = '';

if(count($datas) >=1){
        foreach($datas as $data){
                    
                    if($data->status == 1){
                        $status      =  "Active";
                    }else{
                        $status      =  "In Active";			 
                    }
            $buttons = array();

            //edit button
        if($data->status == 1){
            $buttons[] = html_writer::link(
                new moodle_url('/local/custompage/create_urdc_organization_form.php?update=' . $data->id),
                $OUTPUT->pix_icon('t/hide', 'Change organization as In Active'),
                array('title' => get_string('edit')));
        }else{
        $buttons[] = html_writer::link(
                new moodle_url('/local/custompage/create_urdc_organization_form.php?update=' . $data->id),
                $OUTPUT->pix_icon('t/show', 'Change organization as Active'),
                array('title' => get_string('edit')));
        }
        //delete button       
        $buttons[] = html_writer::link(new moodle_url($CFG->wwwroot . '/local/custompage/update_urdc_organization_form.php?updateid='.$data->id), 
        $OUTPUT->pix_icon('t/edit', get_string('edit')),
        array('title' => get_string('edit')));
            //delete button
        $buttons[] = html_writer::link(new moodle_url($CFG->wwwroot . '/local/custompage/update_organization_form.php?updateid='.$data->id), 
                $OUTPUT->pix_icon('t/delete', get_string('delete')),
                array('title' => get_string('delete')));

            


            $actions = implode(' ', $buttons);

       

        $table->data[] = array(
                        $i,
                        $data->name,
                        $data->address,
                        $data->type,
                        $actions);
                        $i=$i+1;
        }
}else{
    $table = new html_table();
    $table->head = array("S.No",
    "Name",
    "Address",
    "Type",
    "Actions");
            
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
$add_organization = $CFG->wwwroot.'/local/custompage/create_urdc_organization_form.php';

echo '<a href="'.$add_organization .'" class="btn btn-secondary" >Add Organization</a>';
echo html_writer::table($table);
echo $OUTPUT->paging_bar($t_count, $page, $perpage, $baseurl);

echo $OUTPUT->footer();

