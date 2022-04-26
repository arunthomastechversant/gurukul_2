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
require_once('assign_brm_form.php');
?>
<script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
<?php
global $DB, $USER, $COURSE, $PAGE, $CFG;
$PAGE->set_title("BT Users List");
$PAGE->set_url($CFG->wwwroot.'/local/custompage/assign_brm.php');
$PAGE->navbar->add('BT Users List', new moodle_url('/local/custompage/assign_brm.php'));
require_login();
echo $OUTPUT->header();
$PAGE->set_context(context_system::instance());

$baseurl = new moodle_url('/local/custompage/assign_brm.php');
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 10, PARAM_INT);

$t_count = count($DB->get_records_sql("SELECT * FROM {bt_user_detail}"));

$start = $page * $perpage;
if ($start > $t_count) {
    $page = 0;
    $start = 0;
}


   $table = new html_table();
    $table->head = array("S.No",
            "<input type='checkbox' id='main_assign' value='1' unchecked>  Assign",
            "Username",
            "Password",
            "Recruitment Drive",
            "Time");
            
    $table->data = array();
    $table->class = '';
    $table->id = 'assign_brm';
        
$i = 1;
if($page != 0){
	$i = ($page * $perpage)+1;
}
// brm_assignements userid = bt_user_detail id

$datas = $DB->get_records_sql("SELECT * FROM  {bt_user_detail} as bu WHERE bu.id NOT IN (SELECT userid FROM {brm_assignements} )");
// print_r($data1);
// $datas = $DB->get_records_sql("SELECT * FROM {bt_user_detail} order by id DESC ");
// print_r($datas);exit;

// print_r($datas);

if(count($datas) >=1){
        foreach($datas as $data){
            $drive='';
            if($data->recruitment_id)
            $drive = $DB->get_record_sql("SELECT * FROM {bt_recruitment_drive} where id=$data->recruitment_id	 ");      
            $actions = "<input type='checkbox' name='assin' id=' $data->id '>";
            $table->data[] = array(
                        $i,
                        $actions,
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
            "Assign",
            "Username",
            "Password",
            "Recruitment Drive",
            "Time");
            
        $table->data = array();
        $table->class = '';
        $table->id = 'assign_brm';

        $table->data[] = array(
            "",
            "",
            "",
            "No Record Found",
            "",
            "");

}        
$add_organization = $CFG->wwwroot.'/local/custompage/assign_brm.php';

// echo "<select type='text' name='brm_id'><option>----Choose One----</option></select>";
$mform = new assign_brm_form();
$mform->display();
echo html_writer::table($table);
// echo $OUTPUT->paging_bar($t_count, $page, $perpage, $baseurl);

echo $OUTPUT->footer();

?>
<script>

$('#main_assign').click(function(e){
    var table= $(e.target).closest('table');
    $('td input:checkbox',table).prop('checked',this.checked);
});

$('#id_assign_brm').click(function(){
    var brm_id = document.getElementById("id_brm_id").value;
    var values = new Array();
    $("#assign_brm input[name=assin]:checked").each(function () {
        values.push(this.id);
    });
    if(values == ""){
        alert('Choose at least one..');
    }else if(brm_id == ""){
        alert('Choose a reporting manager');
    }else{
        $.ajax({
            type: "post",
            url: "assign_brm_datasave.php",
            data: {
                'values': values,
                'brm_id': brm_id,
            },
            success: function(data) {
                alert(data);
                location.reload();
            }
        });
        // alert("val---" + values.join(", "));

    }
})

</script>

