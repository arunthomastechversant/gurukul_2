<script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/autofill/2.3.5/css/autoFill.dataTables.min.css">

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">
<style>
    .loader {
        position: absolute;
        left: 50%;
        top: 50%;
        z-index: 1;
        width: 120px;
        height: 120px;
        margin: -76px 0 0 -76px;
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid #3498db;
        -webkit-animation: spin 2s linear infinite;
        animation: spin 2s linear infinite;
        border-top: 16px solid blue;
        border-bottom: 16px solid blue;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    #loader {
        display: none;
    }
</style>
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
$systemcontext = context_system::instance();
$companyid = iomad::get_my_companyid($systemcontext);
$title = 'Skill Soft Users';
$datas = $DB->get_records_sql("SELECT * FROM {rsl_user_detail} order by id DESC ");
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_url($CFG->wwwroot.'/local/custompage/skilsoftusers.php');
$PAGE->navbar->add($title, new moodle_url('/local/custompage/skilsoftusers.php'));
require_login();
echo $OUTPUT->header();
$PAGE->set_context(context_system::instance());

$baseurl = new moodle_url('/local/custompage/skilsoftusers.php');
$table = new html_table();
$table->head = array(
        '<input type="checkbox" id="mainselect" value="1"> All',
        "Username",
        "Password",
        "Drive Name",
        "_sys_first_name",
        "_sys_last_name",
        "_sys_emailaddress",
        "_sys_display_first_name",
        "_sys_display_last_name",
        "_sys_locaton",
        "Force Change Password",
        "Status",
        "Role",
        "Group Membership",
        "Group Operation",
	"custom_privileges");  
$table->data = array();
$table->class = 'table-striped';
$table->id = 'user_list';
if($datas !="")
    foreach($datas as $data){
        // $grade = $DB->get_record_sql(" select round(((10/b.sumgrades) * a.sumgrades)* 10) quizper from mdl_quiz_attempts a join mdl_quiz b on a.quiz = b.id where a.userid = $data->userid");
        $userstatus =$DB->get_record_sql("SELECT * FROM {userstatus}  where userid=$data->userid ORDER BY id DESC")->userstatus;
        if(($userstatus == "RSL Candidate")){
            $details = $DB->get_record('user',array('id' => $data->userid));
            $role = $DB->get_record_sql(" select shortname from {role} r join {role_assignments} ra on ra.roleid = r.id where ra.userid =  $data->userid")->shortname;  
            $userdata = $DB->get_record('user_info_data',array('userid' => $data->userid , 'fieldid' => 81))->data;
            $mailid = $DB->get_record('user_info_data',array('fieldid' => 80,'userid' => $data->userid))->data;
            $drive = $DB->get_record("rsl_recruitment_drive",array('id' => $data->recruitment_id))->name;
            if($details->suspended == 1 ){
                $userstatus='Disabled';
            }elseif($details->suspended == 0){
                $userstatus='Enabled';
            }
            // print_r($userdata);exit();
            $table->data[] = array(
                '<input type="checkbox" class="userid" id= ' .$data->userid. '>',
                $data->username,
                $data->password,
                $drive,
                $userdata,
                "",
                $mailid,
                $userdata,
                "",
                $details->city,
                "No",
                "",
                $role,
                "",
                "",
		"",
            );
        }
    }
    echo '<div id="loader" class="loader"></div>';
echo '<button id="send_mail" class="btn btn-primary">Send Mail</button></br></br>';
echo '<div class="card"><div id="tableContainer" class="card-body table-responsive">';
echo html_writer::table($table);
echo '</div></div>';
echo '<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
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
    var dtable = $("#user_list").DataTable({
        "serverside": false,
            "lengthMenu": [
                [10, 40, 60, -1],
                [10, 40, 60, "All"]
            ],
            dom: 'lBfrtip',
                        buttons: [{
                    extend: 'csv',
                    exportOptions: {
                        columns: [1, 2, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15]
                    }
                },
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: [1, 2, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15]
                    }
                },
            ],
            
            // dom : 'lBfrtip',    
            // buttons: [
            //     {
            //         extend: 'csv',
            //         exportOptions: {
            //             orthogonal: 'sort'
            //         }
            //     },
            //     {
            //         extend: 'excel',
            //         exportOptions: {
            //             orthogonal: 'sort'
            //         }
            //     }        
            // ],
            // columnDefs: [{
            // targets:[0],
            // render: function(data, type, row, meta){
            //     if(type === 'sort'){
            //         var $input = $(data).find('input[type="checkbox"]').addBack();
            //         data = ($input.prop('checked')) ? "1" : "0";
            //     }
            //     return data;    
            // }
            // }],

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

    });
    $('#search-datatable').keyup(function(){
      table.search($(this).val()).draw() ;
    });
    $('#mainselect').on('click', function() {
        var rows = dtable.rows({
            'search': 'applied'
        }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
    });

    $('#send_mail').on('click',function(){
        idlist = [];
        dtable.$('.userid').each(function() {
            if (this.checked) {
                idlist.push(this.id);
            }
        });
        if(idlist == ""){
            alert('Choose at least one..');
        }
        else{
            $('#loader').show();
            $.ajax({
                type: "post",
                url: "rslmailfunction.php",
                data: {
                    'idlist': idlist,
                },
                success: function(data) {
                    $('#loader').hide();
                    alert('Mail Sent Successfully');
                    //location.reload();
                }
            });
        }

    })
});
</script>
