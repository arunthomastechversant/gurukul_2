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
 * Adds new instance of enrol_payu to specified course
 * or edits current instance.
 *
 * @package    enrol_payu
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once('interview.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/course/lib.php');

require_login();
?>

<?php
$interviewuser  = optional_param('userid', '', PARAM_INT);

$PAGE->set_pagelayout('admin');
$PAGE->set_title("Interview Form");
$PAGE->set_url($CFG->wwwroot.'/local/listcoursefiles/interview_form.php?userid='.$interviewuser);
$coursenode = $PAGE->navbar->add('Interviewlist', new moodle_url($CFG->wwwroot.'/listcoursefiles/interviewlist.php'));
$PAGE->set_context(context_system::instance());
echo $OUTPUT->header();
$mform = new interview_form();
//echo '<pre>';print_r($mform);die;
if ($mform->is_cancelled()) {
    redirect($return);

} else if ($data = $mform->get_data()) {
	echo '<pre>';print_R($data);die;
}
$mform->display();
?>
<script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
<script>
	
	$(document).ready(function(){	
		$( "#id_intro" ).click(function() {
			var user =  $('#id').val();
			alert(user);
			$.ajax({
			  url: 'ajax.php',
			  type: 'POST',
			  data:{user:user},
			  dataType: 'json',
			  success: function(data) {
				alert(data);
			  }
			});
		});
	
	});
</script>
<?php
echo $OUTPUT->footer();






