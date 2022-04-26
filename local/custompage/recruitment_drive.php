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
$PAGE->set_title("Recruitment Drive Details");
$PAGE->set_heading("Recruitment Drive Details");
$PAGE->set_url($CFG->wwwroot.'/local/custompage/recruitment_drive.php');
$PAGE->navbar->add('Recruitment Drive Details', new moodle_url('/local/custompage/recruitment_drive.php'));
require_login();
echo $OUTPUT->header();
$PAGE->set_context(context_system::instance());

$baseurl = new moodle_url('/local/custompage/recruitment_drive.php');
$systemcontext = context_system::instance();
$companyid = iomad::get_my_companyid($systemcontext);

    $table = new html_table();
    if($companyid == 1){ 
        $table->head = array("S.No",
            "Drive Name",
            "Test Name",
            "Start Date",
            "End Date", 
            "Interview",
            "action",
            );
    }else{
        $table->head = array("S.No",
        "Drive Name",
        "Test Name",
        "Start Date",
        "End Date", 
        "action",
        );
    }
        $table->data = array();
        $table->class = '';
        $table->id = 'drive_list';

        $i = 1;
        if($companyid == 1){
            $drive_data = $DB->get_records_sql("SELECT * FROM {rsl_recruitment_drive} ORDER BY id DESC");
        }else if($companyid == 2){
            $drive_data = $DB->get_records_sql("SELECT * FROM {urdc_recruitment_drive} ORDER BY id DESC");
        }else if($companyid == 4){
            $drive_data = $DB->get_records_sql("SELECT * FROM {bt_recruitment_drive} ORDER BY id DESC");
        }else{
            $drive_data = $DB->get_records_sql("SELECT * FROM {recruitment_drive} where company_id = $companyid ORDER BY id DESC");
        }
        
        foreach($drive_data as $data)
        {
                $edit ='<a href="'.$CFG->wwwroot.'/local/custompage/edit_recruitment_drive_form.php?driveid='.$data->id .'">Edit</a>';

                $delete ='<a href="'.$CFG->wwwroot.'/local/custompage/delete_recruitment_drive_form.php?delete='.$data->id .'">Delete</a>';
                
                $quizname = $DB->get_record_sql("SELECT * FROM {quiz} where id = $data->test")->name;

                if($data->interview==1){
                    $status="Enabled"; 
                }else{
                    $status="Disabled"; 
                }
                if($companyid == 1){
                    $table->data[] = array(
                    $i,
                    $data->name,                       
                    $quizname, 
                    date('m/d/Y H:i:s',$data->startdate), 
                    date('m/d/Y H:i:s',$data->enddate), 
                    $status,
                    $edit."&nbsp&nbsp&nbsp".$delete,   
                    );
                }else{
                    $table->data[] = array(
                        $i,
                        $data->name,                       
                        $quizname, 
                        date('m/d/Y H:i:s',$data->startdate), 
                        date('m/d/Y H:i:s',$data->enddate), 
                        $edit."&nbsp&nbsp&nbsp".$delete,   
                        );
                }
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
    var dtable = $("#drive_list").DataTable({
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
        var table = $('#drive_list').DataTable();
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


