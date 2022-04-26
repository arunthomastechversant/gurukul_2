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

$userid = 6;
$context = context_user::instance($userid, MUST_EXIST);
$fs = get_file_storage();
if ($files = $fs->get_area_files($context->id, 'local_custompage', 'imagefile',false, 'sortorder', false)) 
{
   
    foreach ($files as $file) 
    { 
        $imagepath = moodle_url::make_pluginfile_url($context->id, 'local_custompage', 'imagefile', $file->get_itemid(), $file->get_filepath(), $file->get_filename());
    }
    $imagepath = $imagepath->__toString();
}


print_r($imagepath);

