<!-- <style>
    .fcontainer{
        display:none;
    }
    .ftoggler{
        display:none;
    }
</style> -->
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
 * Allows you to edit a users profile
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

// require_once('../../../onfig.php');

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/csvlib.class.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/local/custompage/itracquserupdate_form.php');


$context = context_system::instance();
require_login();


// Correct the navbar .
// Set the name for the page.
$linktext = "Update iTracQ User";
// Set the url.
$linkurl = new moodle_url('/local/custompage/itracquserupdate.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);
// Set the page heading.
$PAGE->set_heading($linktext);

$PAGE->requires->jquery();

// Javascript for fancy select.
// Parameter is name of proper select form element.
$PAGE->requires->js_call_amd('block_iomad_company_admin/department_select', 'init', array('userdepartment', '', $userdepartment));

// get output renderer
$output = $PAGE->get_renderer('block_iomad_company_admin');
if (empty($CFG->defaulthomepage)) {
    $PAGE->navbar->add(get_string('dashboard', 'block_iomad_company_admin'), new moodle_url($CFG->wwwroot . '/my'));
}
$PAGE->navbar->add($linktext, $linkurl);

// Set the companyid
$companyid = iomad::get_my_companyid($context);

$companyshortname = '';
if ($companyid ) {
    $company = new company($companyid);
    $companyshortname = $company->get_shortname();
}
require_login(null, false); // Adds to $PAGE, creates $output.

$systemcontext = context_system::instance();

$returnurl = $CFG->wwwroot."/local/custompage/itracquserupdate.php";
$bulknurl  = $CFG->wwwroot.'/'.$CFG->admin.'/user/user_bulk.php';

$today = time();
$today = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), 0, 0, 0);

// Array of all valid fields for validation.

    $mform = new admin_uploaduser_form1();

    if ($formdata = $mform->get_data()) {
        $iid = csv_import_reader::get_new_iid('uploaduser');
        $cir = new csv_import_reader($iid, 'uploaduser');

        // print_r($cir);exit();
        $content = $mform->get_file_content('userfile');
        $optype = $formdata->uutype;
        $readcount = $cir->load_csv_content($content,
                                            $formdata->encoding,
                                            $formdata->delimiter_name
                                            );
        // print_r($cir->get_columns());exit();
        if (!$columns = $cir->get_columns()) {
           print_error('cannotreadtmpfile', 'error', $returnurl);
        }

        unset($content);

        // Keep track of new users.
        $usercount = 0;
        $cir->init();
        while ($line = $cir->next()) {
            $user = new stdClass();

            // Add fields to user object.
            foreach ($line as $key => $value) {
                if ($value !== '') {
                    $key = $columns[$key];
                    $user->$key = $value;
                } else {
                    $user->{$columns[$key]} = '';
                }
            }
            $userid = $DB->get_record('user', array('email' => $user->email))->id;
            if($userid !=""){            
                $userdriveid = $DB->get_record('rsl_user_detail', array('userid' => $userid))->id;
                $userdata = new stdclass();
                $userdata->id = $userdriveid;
                $userdata->userid = $userid;
                $userdata->itracqid = $user->itracqid;
                $DB->update_record('rsl_user_detail',$userdata);
                $usercount ++;
            }
            
        }
        // print_r($userdata);
        if ($readcount === false) {
            // TODO: need more detailed error info.
            print_error('csvloaderror', '', $returnurl);
        } else if ($readcount == 0) {
            print_error('csvemptyfile', 'error', $returnurl);
        }
        $usercount .= ' Users Data Updated Succesfully';
        redirect($returnurl, $usercount, 8);
        // exit();
    } else {
        echo $output->header();

        echo $output->heading_with_help(get_string('uploadusers', 'tool_uploaduser'), 'uploadusers', 'tool_uploaduser');

        $mform->display();
        echo $output->footer();
        die;
    }
// Print the header.
echo $output->header();

// Print the form.

echo $output->heading(get_string('uploaduserspreview', 'tool_uploaduser'));