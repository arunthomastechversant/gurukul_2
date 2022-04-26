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

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    //  It must be included from a Moodle page.
}

require_once($CFG->libdir.'/formslib.php');

require_once($CFG->dirroot .'/blocks/iomad_company_admin/lib.php');

class admin_uploaduser_form1 extends company_moodleform {
    public function definition () {
        global $CFG, $USER,$DB;
        $mform =& $this->_form;

        $mform->addElement('header', 'settingsheader', get_string('upload'));

        $mform->addElement('filepicker', 'userfile', get_string('file'), null, array('accepted_types' => array('.csv')));
        $mform->addRule('userfile', null, 'required');

        $choices = csv_import_reader::get_delimiter_list();
        $mform->addElement('select', 'delimiter_name', get_string('csvdelimiter', 'tool_uploaduser'), $choices);
        if (array_key_exists('cfg', $choices)) {
            $mform->setDefault('delimiter_name', 'cfg');
        } else if (get_string('listsep', 'langconfig') == ';') {
            $mform->setDefault('delimiter_name', 'semicolon');
        } else {
            $mform->setDefault('delimiter_name', 'comma');
        }

        $choices = core_text::get_encodings();
        $mform->addElement('select', 'encoding', get_string('encoding', 'tool_uploaduser'), $choices);
        $mform->setDefault('encoding', 'UTF-8');

        $choices = array('10' => 10, '20' => 20, '100' => 100, '1000' => 1000, '100000' => 100000);
        $mform->addElement('select', 'previewrows', get_string('rowpreviewnum', 'tool_uploaduser'), $choices);
        $mform->setType('previewrows', PARAM_INT);

        $choices = array(UU_ADDNEW    => get_string('uuoptype_addnew', 'tool_uploaduser'),
                         UU_ADDINC    => get_string('uuoptype_addinc', 'tool_uploaduser'),
                         UU_ADD_UPDATE => get_string('uuoptype_addupdate', 'tool_uploaduser'),
                         UU_UPDATE     => get_string('uuoptype_update', 'tool_uploaduser'));
        $mform->addElement('select', 'uutype', get_string('uuoptype', 'tool_uploaduser'), $choices);

        $this->add_action_buttons(false, 'Update Users');
    }
}


class admin_uploaduser_form3 extends moodleform {
    public function definition () {
        global $CFG, $USER;
        $mform =& $this->_form;
        $this->add_action_buttons(false, get_string('uploadnewfile'));
    }
}