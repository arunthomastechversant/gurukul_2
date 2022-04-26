
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
require_once('assign_lead_trainer_form.php');
?>
<script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
<?php
global $DB, $USER, $COURSE, $PAGE, $CFG;
$PAGE->set_title("Assign Lead Trainers");
$PAGE->set_heading("Assign Lead Trainers");
$PAGE->set_url($CFG->wwwroot.'/local/custompage/assign_lead_trainer.php');
$PAGE->navbar->add('Assign Lead User', new moodle_url('/local/custompage/assign_lead_trainer.php'));
require_login();
echo $OUTPUT->header();
$PAGE->set_context(context_system::instance());

$baseurl = new moodle_url('/local/custompage/assign_lead_trainer_form.php');
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 10, PARAM_INT);
$systemcontext = context_system::instance();
$companyid = iomad::get_my_companyid($systemcontext);
$t_count = count($DB->get_records_sql("SELECT * FROM {company_users} where companyid = $companyid"));

$courses = $DB->get_records_sql("SELECT c.* FROM  {course} as c JOIN {company_course} as cc WHERE cc.courseid = c.id AND cc.companyid = $companyid");
echo '<label>Select a course : </label>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
echo '<select type="" name="id_course_select" id="id_course_select" class="custom-select"><option value="" selected disabled>---- Choose a Courses ----</option>';
foreach($courses as $key => $course){
    echo '<option value =' .$course->id.'>' .$course->fullname.'</option>';
}  
echo '</select>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
echo '<button class="btn btn-primary" id="id_assign_lu">Enrol</button></br></br></br>';
$start = $page * $perpage;
if ($start > $t_count) {
    $page = 0;
    $start = 0;
}


   $table = new html_table();
    $table->head = array("S.No",
            "<input type='checkbox' id='main_assign' value='1' unchecked>  Assign",
            "Username",
            "City",
            "Time",
            "Status");
            
    $table->data = array();
    $table->class = '';
    $table->id = 'lead_trainer_list';
        
$i = 1;
if($page != 0){
	$i = ($page * $perpage)+1;
}

$users = $DB->get_records_sql("SELECT u.* FROM  {user} as u 
    JOIN {lead_user_detail} as lu 
    WHERE lu.userid = u.id AND lu.role = 'trainer'");
if(count($users) >=1){
        foreach($users as $user){
// print_r($user);
            $roleId = $DB->get_record('role_assignments', array('userid' => $user->id))->roleid;
            if(!($DB->record_exists('role_assignments', array('userid' => $user->id, 'roleid' => $roleId)))){

            $actions = "<input type='checkbox' name='assin' id=' $user->id '>";
            $status = "<span class='label label-danger'>Not Enroled</span>";
            }else{
                $actions = "<span class='label label-success'>Enroled</span>";
                $status = "<span class='label label-success'>Enroled</span>";
            }
            
            $table->data[] = array(
                        $i,
                        $actions,
                        $user->username,
                        $user->city,
                        date('d/m/Y H:i:s', $user->timecreated),
                        $status,
                        );
                        $i=$i+1;
        }
        }else{
            $table = new html_table();
            $table->head = array("S.No",
            "Assign",
            "Username",
            "Time",
            "Status");
            
        $table->data = array();
        $table->class = '';
        $table->id = 'lead_trainer_list';

        $table->data[] = array(
            "",
            "",
            "",
            "No Record Found",
            "",
            "");

}        

 

// echo "<select type='text' name='brm_id'><option>----Choose One----</option>
//         <option></option>
// </select>";

// $mform = new assign_lead_trainer_form();
// $mform->display();
echo html_writer::table($table);
// echo $OUTPUT->paging_bar($t_count, $page, $perpage, $baseurl);

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
        oTable = $('#lead_trainer_list').DataTable( {

        initComplete: function () {
            var table = $('#lead_trainer_list').DataTable();
            this.api().columns().every( function () {
                var column = this;

                if (column.index() == 7) {
                    $('<span style="margin-left: 10px;"></span>   ').appendTo( '#lead_trainer_list_length' );
                        var select = $('<select id="class_select" class="custom-select"></select>')
                        .appendTo( '#lead_trainer_list_length' )
                        .on( 'change', function () {
                            
                            var val = $(this).val()
                            column
                                .search( val ? '^'+val+'$' : '', true, false )
                                .draw();
                        } );

                    column.data().unique().sort().each( function ( d, j ) {
        
                            select.append( '<option value="'+d+'" >'+d+'</option>' )
                    
                    } );
               }
            //     if (column.index() == 6) {
                    
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

$('#main_assign').click(function(e){
    var table= $(e.target).closest('table');
    $('td input:checkbox',table).prop('checked',this.checked);
});

$('#id_assign_lu').click(function(){

    var course_id = document.getElementById("id_course_select").value;  
    // var batch_id = document.getElementById("id_batch").value;   
    var values = new Array();
        var enrol_type = '';
    $("#lead_trainer_list input[name=assin]:checked").each(function () {
        values.push(this.id);
    });

    if(values == ""){
        alert('Choose at least one User');
    }else if(course_id == ""){
        alert('Select a Course');
    }else{
        $.ajax({
            type: "POST",
            // url: "assign_lu_as_trainer.php",
            url: "assign_lead_course_batch_user.php",
            data: {
                'users': values,
                'course_id': course_id,
                'role_type': "trainer",
                // 'process_assign': "assign-user-to-batch"
                'enrol_trainer': 1
            },
            success: function(data) {
                alert("Enroled Successfully");
                location.reload();
            }
        });

    }
})

</script>

