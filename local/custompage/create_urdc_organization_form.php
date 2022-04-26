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
require_once('create_urdc_organization.php');
require_login();

$PAGE->set_pagelayout('admin');
$PAGE->set_title("Add URDC Organization");
$PAGE->set_heading("Add URDC Organization");
$PAGE->set_url($CFG->wwwroot.'/local/custompage/create_urdc_organization_form.php');
$coursenode = $PAGE->navbar->add('Add URDC Organization', new moodle_url($CFG->wwwroot.'/local/custompage/create_urdc_organization_form.php'));
$PAGE->set_context(context_system::instance());
//~ $url = $CFG->wwwroot.'/local/custompage/get_data.php';
$return = $CFG->wwwroot.'/local/custompage/urdc_organization_list.php';
echo $OUTPUT->header();

$mform = new create_urdc_organization_form;
if ($mform->is_cancelled()) {
    redirect($return);

} else if ($data = $mform->get_data()) {

$record = new stdClass();
$record->name           =  $data->name;
$record->address         =  $data->address;
$record->type    =  $data->type;
$record->status        =  1;

$insert_record = $DB->insert_record('urdc_organization', $record);
;
$urlto = $CFG->wwwroot.'/local/custompage/urdc_organization_list.php';
redirect($urlto, 'Organization Created Successfully ', 8);

}

   
$mform->display();
echo $OUTPUT->footer();
