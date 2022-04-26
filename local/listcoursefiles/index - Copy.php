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
 * List all files in a course.
 *
 * @package    local_listcoursefiles
 * @copyright  2017 Martin Gauk (@innoCampus, TU Berlin)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once('locallib.php');
global $DB, $USER, $COURSE, $PAGE, $CFG,$OUTPUT;
$courseid = required_param('courseid', PARAM_INT);
$actionid = optional_param('actionid','', PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$limit = optional_param('limit', 1, PARAM_INT);
if ($page < 0) {
    $page = 0;
}
if ($limit < 1 || $limit > LOCAL_LISTCOURSEFILES_MAX_FILES) {
    $limit = LOCAL_LISTCOURSEFILES_MAX_FILES;
}
$component = optional_param('component', 'all_wo_submissions', PARAM_ALPHAEXT);
$filetype = optional_param('filetype', 'document', PARAM_ALPHAEXT);
$action = optional_param('action', '', PARAM_ALPHAEXT);
$chosenfiles = optional_param_array('file', array(), PARAM_INT);

$context = context_course::instance($courseid);
$title = get_string('pluginname', 'local_listcoursefiles');
$url = new moodle_url('/local/listcoursefiles/index.php',
        array('courseid' => $courseid));
$datas = $DB->get_record_sql("SELECT * FROM {rsl_recruitment_drive} where id=$courseid");

$PAGE->set_title($datas->name);
$PAGE->set_heading($datas->name);

$PAGE->navbar->add('RSL Recruitment Drive List', new moodle_url('/local/listcoursefiles/drivelist.php'));
$PAGE->navbar->add($datas->name, new moodle_url('/local/listcoursefiles/index.php?courseid='.$courseid));


require_login($courseid);
require_capability('local/listcoursefiles:view', $context);
$changelicenseallowed = has_capability('local/listcoursefiles:change_license', $context);
$downloadallowed = has_capability('local/listcoursefiles:download', $context);


$files = new local_listcoursefiles\course_files($courseid, $context, $component, $filetype);

if ($action === 'change_license' && $changelicenseallowed) {
    require_sesskey();
    $license = required_param('license', PARAM_ALPHAEXT);
    try {
        $files->set_files_license($chosenfiles, $license);
    } catch (moodle_exception $e) {
        \core\notification::add($e->getMessage(), \core\output\notification::NOTIFY_ERROR);
    }
} else if ($action === 'download' && $downloadallowed) {
    require_sesskey();
    try {
        $files->download_files($chosenfiles);
    } catch (moodle_exception $e) {
        \core\notification::add($e->getMessage(), \core\output\notification::NOTIFY_ERROR);
    }
}
$r_user = $DB->get_records_sql("SELECT rud.userid,u.username,u.firstname,u.lastname ,u.email,u.deleted,u.suspended   FROM {rsl_user_detail} rud    JOIN {user} u ON u.id = rud.userid where u.deleted=0 AND rud.recruitment_id=$courseid");
	// print_r($r_user);exit;
$filelist = $files->get_file_list($page * $limit, $limit);
$licenses = local_listcoursefiles\course_files::get_available_licenses();

$tpldata = new stdClass();
$tpldata->course_selection_html = local_listcoursefiles_get_course_selection($url, $courseid);
$tpldata->component_selection_html = local_listcoursefiles_get_component_selection($url, $files->get_components(), $component);
$tpldata->file_type_selection_html = local_listcoursefiles_get_file_type_selection($url, $filetype);
//  $tpldata->paging_bar_html = $OUTPUT->paging_bar(count($r_user), $page , $limit, $url, 'page');



$tpldata->url = $url;
$tpldata->sesskey = sesskey();
$tpldata->files = array();
$tpldata->files_exist = count($r_user) > 0;
$tpldata->change_license_allowed = $changelicenseallowed;
$tpldata->download_allowed = $downloadallowed;
// $tpldata->license_select_html = html_writer::select($licenses, 'license');


    $actionarray = array(
        "" => " ---- Choose Action ---",
        "1" => "Assign To Intereview",
        "2" => "Assign To HR",
    );
    $actions=array();
    foreach ($actionarray as $key =>$val ) {
            $actions[$key] = $val;
        
    }

    // echo $OUTPUT->single_select($url, 'actionid', $actions, $currentcourseid, null, 'actionselector');exit;
    $tpldata->action_html = $OUTPUT->single_select($url, 'actionid', $actions, $courseid, null, 'actionselector');

    $bulk.='<select name="bulkaction" id="bulkaction">';
    foreach ($actionarray as $key =>$val ) {
        $bulk.=' <option value="'.$key.'">'.$val.'</option>';
    }
    $bulk.='</select>';

$tpldata->bulk = $bulk;
// echo '<pre>';print_r($r_user );exit;
foreach ($r_user as $userdata) {
    $teststatus='';
    // print_r($userdata);exit;


    // $r_user = $DB->get_records_sql("SELECT qa.*,rrd.name FROM {rsl_recruitment_drive} rrd   JOIN {quiz_attempts} qa ON qa.id = rrd.test where qa.userid=$userdata->userid ");
    $driveid=$DB->get_record_sql("SELECT * FROM {rsl_recruitment_drive}  where id=$courseid ");
    if($driveid)
    $gradedetail = $DB->get_record_sql(" select round(((10/b.sumgrades) * a.sumgrades)* 10) quizper,a.timemodified as timemodified from mdl_quiz_attempts a join mdl_quiz b on a.quiz = b.id where b.id = $driveid->test and userid = $userdata->userid order by a.timemodified desc limit 1");
    if($gradedetail){
        
        $timeoftest=date('d/m/Y H:i:s', $gradedetail->timemodified);
    }else{
        $timeoftest='Not Yet Started';
    }
    if($gradedetail->quizper){

        if($gradedetail->quizper >= 70){
            $teststatus='Pass';
        }else if($gradedetail->quizper < 70 && $gradedetail->quizper > 50){
            $teststatus='RSl Candidate';
        }else{
            $teststatus='Fail';
        }
        $grade= $gradedetail->quizper.' %';
    }else{
        $grade= "Not Yet Started";
        $teststatus='Not Yet Started';
    }
    if($userdata->suspended == 1 ){
        $userstatus='Disabled';
    }elseif($userdata->suspended == 0){
        $userstatus='Active';
    }
    // echo $userstatus;exit;
    $tplfile = new stdClass();

    // $tplfile->file_license = local_listcoursefiles\course_files::get_license_name_color($file->license);
    // $tplfile->file_id = $file->id;
    // $tplfile->file_size = display_size($file->filesize);
    // $tplfile->file_type = local_listcoursefiles\course_files::get_file_type_translation($file->mimetype);
    // $tplfile->file_uploader = fullname($file);

    // $fileurl = $files->get_file_download_url($file);
    // $tplfile->file_url = ($fileurl) ? $fileurl->out() : false;
    // $tplfile->file_name = $file->filename;

    // $componenturl = $files->get_component_url($file->contextlevel, $file->instanceid);
    // $tplfile->file_component_url = ($componenturl) ? $componenturl->out() : false;
    // $tplfile->file_component = local_listcoursefiles_get_component_translation($file->component);


    // <th class="header c0" scope="col"></th>        
    // <th class="header c1" scope="col">Username</th>
    // <th class="header c2" scope="col">Date OF Test</th>
    // <th class="header c3" scope="col">Test Status</th>
    // <th class="header c4" scope="col">User Status</th>
    // <th class="header c5" scope="col">Recruitment Status</th>
    // <th class="header c6" scope="col">Grade</th>
    // <th class="header c7" scope="col">Action</th>

    $tplfile->file_id=$userdata->userid;
    $tplfile->username=$userdata->username;
    $tplfile->timeoftest=$timeoftest;
    $tplfile->grade=$grade;
    $tplfile->teststatus=$teststatus;
    $tplfile->userstatus=$userstatus;
    

    $tplfile->file_size = $userdata->firstname;
    $tplfile->file_type=$userdata->email;


    $tpldata->files[] = $tplfile;
}
//   echo '<pre>';print_r($tpldata);exit;
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_listcoursefiles/view', $tpldata);
echo '	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
';
?>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    // $('#dashboard_report').DataTable();
    // oTable = $('#dashboard_report').dataTable();

    var table = $('#dashboard_report').DataTable();
    
} );
</script>
<?php
echo $OUTPUT->footer();
