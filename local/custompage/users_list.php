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
$systemcontext = context_system::instance();
$companyid = iomad::get_my_companyid($systemcontext);
$company = $DB->get_record('company',array('id' => $companyid))->shortname;
global $DB, $USER, $COURSE, $PAGE, $CFG;
$PAGE->set_title($company." Users List");
$PAGE->set_heading($company." Users List");
$PAGE->set_url($CFG->wwwroot.'/local/custompage/users_list.php');
$PAGE->navbar->add($company.' Users List', new moodle_url('/local/custompage/users_list.php'));
require_login();
echo $OUTPUT->header();
$PAGE->set_context(context_system::instance());

$baseurl = new moodle_url('/local/custompage/users_list.php');
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 10, PARAM_INT);

$t_count = count($DB->get_records_sql("SELECT * FROM {user_detail}"));

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
    $table->id = 'user_list';
        
$i = 1;
if($page != 0){
	$i = ($page * $perpage)+1;
}


$datas = $DB->get_records_sql("SELECT * FROM {user_detail} order by id DESC ",array(),$start, $perpage);
// print_r($datas);exit;
$actions = '';

if(count($datas) >=1){
        foreach($datas as $data){
                    
            // if($data->recruiment_id ==1){
            //     $drive='Drive1';
            // }else{
            //     $drive='Drive2';
            // }
            $drive='';

            if($data->recruitment_id)
            $drive = $DB->get_record_sql("SELECT * FROM {recruitment_drive} where id=$data->recruitment_id	 ");      
            
        $table->data[] = array(
                        $i,
                        $data->username,
                        $data->password,
                        $drive->name,
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
    $table->id = 'user_list';

        $table->data[] = array(
            "",
            "",
            "No Record Found",
            "",
            "");

}        
$add_organization = $CFG->wwwroot.'/local/custompage/create_users.php';

echo '<a href="'.$add_organization .'" class="btn btn-secondary" >Add '.$company.' Users</a></br></br>';
echo html_writer::table($table);

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

<script type="text/javascript">

$(document).ready(function() {
    var quotations = [];
    oTable = $('#user_list').DataTable( {
        "serverside": false,
            "lengthMenu": [
                [10, 40, 60, -1],
                [10, 40, 60, "All"]
            ],
            dom: 'lBfrtip',
            buttons: [
                'csv', 'excel'
        ],

    initComplete: function () {
        var table = $('#user_list').DataTable();
        this.api().columns().every( function () {
            var column = this;

            if (column.index() == 3) {
                $('<span style="margin-left: 10px; margin-right: 10px;"></span>   ').appendTo( '#user_list_length' );
                    var select = $('<select id="class_select" class="custom-select"></select>')
                    select.append( '<option value="" >All</option>' )
                    .appendTo( '#user_list_length' )
                    .on( 'change', function () {
                        
                        var val = $(this).val()
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );

                column.data().unique().sort().each( function ( d, j ) {
    
                        select.append( '<option value="'+d+'" >'+d+'</option>' )
                
                } );
                $('<span style="margin-left: 10px; margin-right: 10px;"></span>   ').appendTo( '#user_list_length' );
           }
        //     if (column.index() == 4) {
                
        //     $('<span style="margin-left: 10px;"></span> ').appendTo( '#user_list_length' );
        //     var select = $('<select type="text" id="class_select" class=""></select>')
        //     .appendTo( '#user_list_length' )
        //     .on( 'change', function () {

        //     var val = $(this).val()
        //     column
        //     .search( val ? '^'+val+'$' : '', true, false )
        //     .draw();
        //     } );

        //     column.data().unique().sort().each( function ( d, j ) {
        //     var reg = /<a[^>]*>([^<]+)<\/a>/g
        //     var d_text = reg.exec(d)[1];            
        //     quotations.push(d_text);
        //     } );
        //     $.each($.unique(quotations), function(i, value){
        //     //$('div').eq(1).append(value  + ' ');
        //     select.append( '<option value="'+value+'" >'+value+'</option>' )
        //     });

        // }


        } );
        
    }
} );

$('#search-datatable').keyup(function(){
      oTable.search($(this).val()).draw() ;
});
} );

</script>
