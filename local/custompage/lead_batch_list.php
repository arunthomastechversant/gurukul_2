<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/autofill/2.3.5/css/autoFill.dataTables.min.css">
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
$PAGE->set_title("Lead Batch List");
$PAGE->set_url($CFG->wwwroot.'/local/custompage/lead_batch_list.php');
$PAGE->navbar->add('Lead Batch List', new moodle_url('/local/custompage/lead_batch_list.php'));
require_login();
echo $OUTPUT->header();
$PAGE->set_context(context_system::instance());

$baseurl = new moodle_url('/local/custompage/lead_batch_list.php');
$page = optional_param('page', 0, PARAM_INT);
   $table = new html_table();
    $table->head = array("S.No",
            "Name",
            "Time");
            
    $table->data = array();
    $table->class = '';
    $table->id = '';
        
$i = 1;
if($page != 0){
	$i = ($page * $perpage)+1;
}


$batches = $DB->get_records('lead_batches');


if(count($batches) >=1){
        foreach($batches as $batch){    
            
        $table->data[] = array(
                        $i,
                        $batch->name,
                        date('d/m/Y H:i:s', $batch->created_at)
                        );
                        $i=$i+1;
        }
}else{
    $table = new html_table();
    $table->head = array("S.No",
            "Name",
            "Time");
            
    $table->data = array();
    $table->class = '';
    $table->id = 'batch_list';

        $table->data[] = array(
            "",
            "",
            "No Record Found",
            "",
            "");

}        
$add_batch = $CFG->wwwroot.'/local/custompage/create_lead_batch_form.php';

echo '<a href="'.$add_batch .'" class="btn btn-secondary" >Add New Batch</a>';
echo html_writer::table($table);
echo $OUTPUT->paging_bar($t_count, $page, $perpage, $baseurl);

echo '
        <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/autofill/2.3.5/js/dataTables.autoFill.min.js"></script>
        ';

echo $OUTPUT->footer();

?>

<script type="text/javascript">
    $(document).ready(function() {
        var quotations = [];
        oTable = $('#batch_list').DataTable();
    }
</script>
