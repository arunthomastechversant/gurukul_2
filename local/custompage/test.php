<script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/autofill/2.3.5/css/autoFill.dataTables.min.css">

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">
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
$PAGE->set_title("Test Details");
$PAGE->set_heading("Test Details");
$PAGE->set_url($CFG->wwwroot.'/local/custompage/test.php');
$PAGE->navbar->add('Test Details', new moodle_url('/local/custompage/test.php'));
require_login();
echo $OUTPUT->header();
$PAGE->set_context(context_system::instance());

$baseurl = new moodle_url('/local/custompage/test.php');

    $table = new html_table();
    $table->head = array("S.No",
            "Quiz Name",
            "Number of attempt",
            "Questions per Page",
            "Time Limit",    
            "Proctoring",
            "Action",
        );
            
    $table->data = array();
    $table->class = '';
    $table->id = 'test_list';
        
$i = 1;
$systemcontext = context_system::instance();
$companyid = iomad::get_my_companyid($systemcontext);
// print_r($companyid);exit();
$datas = $DB->get_records_sql("SELECT q.id,name,attempts,questionsperpage,timelimit FROM {quiz} as q join {course} as c on c.id = q.course join {company_course} as cc on cc.courseid = c.id where cc.companyid = $companyid ORDER BY q.id DESC");
// print_r($datas);exit();
    foreach($datas as $data)
    {
        $proctoring = $DB->get_record_sql("SELECT * FROM {quizaccess_eproctoring} where quizid=$data->id");
        $deletequiz =  $DB->get_record_sql("select * from {course_modules} where instance = $data->id")->id;
        if($proctoring){
            $status="enabled" ;
        }else{
            $status="disabled";
        }
        
        $edit ='<a href="'.$CFG->wwwroot.'/local/custompage/test_edit_form.php?testid='.$data->id .'">Edit</a>';

        $delete ='<a href="'.$CFG->wwwroot.'/local/custompage/test_delete_form.php?delete='. $deletequiz .'">Delete</a>';


        $table->data[] = array(
            $i, 
            $data->name,  
            $data->attempts,
            $data->questionsperpage,  
            ((int)$data->timelimit)/60,
            $status,   
            $edit."&nbsp&nbsp&nbsp".$delete,              
            );
            $i=$i+1;
    }
       

echo html_writer::table($table);
// echo $OUTPUT->paging_bar($t_count, $page, $perpage, $baseurl);
echo '<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/autofill/2.3.5/js/dataTables.autoFill.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.flash.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>';
echo $OUTPUT->footer();
?>





<script>
$(document).ready( function () {
    var dtable = $("#test_list").DataTable({
        "serverside": false,
            "lengthMenu": [
                [10, 40, 60, -1],
                [10, 40, 60, "All"]
            ],
           // dom: 'lBfrtip',
            buttons: [
                'csv', 'excel'
            ],
            

        initComplete: function () {
        var table = $('#test_list').DataTable();
        this.api().columns().every( function () {
            var column = this;

            if (column.index() == 1) {
                $('<span style="margin-left: 10px; margin-right: 10px;"></span>   ').appendTo( '#drive_list_length' );
                    var select = $('<select id="class_select" class="custom-select"></select>')
                    select.append( '<option value="" >All</option>' )
                    .appendTo( '#drive_list_length' )
                    .on( 'change', function () {
                        
                        var val = $(this).val()
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );

                column.data().unique().sort().each( function ( d, j ) {
    
                        select.append( '<option value="'+d+'" >'+d+'</option>' )
                
                } );
                $('<span style="margin-left: 10px; margin-right: 10px;"></span>   ').appendTo( '#drive_list_length' );
           }

        } );
        
    }

    });
    $('#search-datatable').keyup(function(){
      dtable.search($(this).val()).draw() ;
    });
})

</script>


