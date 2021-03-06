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
 * A class for representing question categories.
 *
 * @package    moodlecore
 * @subpackage questionbank
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

// number of categories to display on page
define('QUESTION_PAGE_LENGTH', 25);

require_once($CFG->libdir . '/listlib.php');
require_once($CFG->dirroot . '/question/category_form.php');
require_once($CFG->dirroot . '/question/move_form.php');


/**
 * Class representing a list of question categories
 *
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_category_list extends moodle_list {
    public $table = "question_categories";
    public $listitemclassname = 'question_category_list_item';
    /**
     * @var reference to list displayed below this one.
     */
    public $nextlist = null;
    /**
     * @var reference to list displayed above this one.
     */
    public $lastlist = null;

    public $context = null;
    public $sortby = 'parent, sortorder, name';

    public function __construct($type='ul', $attributes='', $editable = false, $pageurl=null, $page = 0, $pageparamname = 'page', $itemsperpage = 20, $context = null){
        parent::__construct('ul', '', $editable, $pageurl, $page, 'cpage', $itemsperpage);
        $this->context = $context;
    }

    public function get_records() {
        $this->records = get_categories_for_contexts($this->context->id, $this->sortby);
    }

    /**
     * Returns the highest category id that the $item can have as its parent.
     * Note: question categories cannot go higher than the TOP category.
     *
     * @param list_item $item The item which its top level parent is going to be returned.
     * @return int
     */
    public function get_top_level_parent_id($item) {
        // Put the item at the highest level it can go.
        $topcategory = question_get_top_category($item->item->contextid, true);
        return $topcategory->id;
    }

    /**
     * process any actions.
     *
     * @param integer $left id of item to move left
     * @param integer $right id of item to move right
     * @param integer $moveup id of item to move up
     * @param integer $movedown id of item to move down
     * @return void
     * @throws coding_exception
     */
    public function process_actions($left, $right, $moveup, $movedown) {
        $category = new stdClass();
        if (!empty($left)) {
            // Moved Left (In to another category).
            $category->id = $left;
            $category->contextid = $this->context->id;
            $event = \core\event\question_category_moved::create_from_question_category_instance($category);
            $event->trigger();
        } else if (!empty($right)) {
            // Moved Right (Out of the current category).
            $category->id = $right;
            $category->contextid = $this->context->id;
            $event = \core\event\question_category_moved::create_from_question_category_instance($category);
            $event->trigger();
        }
        parent::process_actions($left, $right, $moveup, $movedown);
    }
}

/**
 * An item in a list of question categories.
 *
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_category_list_item extends list_item {
    public function set_icon_html($first, $last, $lastitem){
        global $CFG;
        $category = $this->item;
        $url = new moodle_url('/question/category.php', ($this->parentlist->pageurl->params() + array('edit'=>$category->id)));
        $this->icons['edit']= $this->image_icon(get_string('editthiscategory', 'question'), $url, 'edit');
        parent::set_icon_html($first, $last, $lastitem);
        $toplevel = ($this->parentlist->parentitem === null);//this is a top level item
        if (($this->parentlist->nextlist !== null) && $last && $toplevel && (count($this->parentlist->items)>1)){
            $url = new moodle_url($this->parentlist->pageurl, array('movedowncontext'=>$this->id, 'tocontext'=>$this->parentlist->nextlist->context->id, 'sesskey'=>sesskey()));
            $this->icons['down'] = $this->image_icon(
                get_string('shareincontext', 'question', $this->parentlist->nextlist->context->get_context_name()), $url, 'down');
        }
        if (($this->parentlist->lastlist !== null) && $first && $toplevel && (count($this->parentlist->items)>1)){
            $url = new moodle_url($this->parentlist->pageurl, array('moveupcontext'=>$this->id, 'tocontext'=>$this->parentlist->lastlist->context->id, 'sesskey'=>sesskey()));
            $this->icons['up'] = $this->image_icon(
                get_string('shareincontext', 'question', $this->parentlist->lastlist->context->get_context_name()), $url, 'up');
        }
    }

    public function item_html($extraargs = array()){
        global $CFG, $OUTPUT;
        $str = $extraargs['str'];
        $category = $this->item;

        $editqestions = get_string('editquestions', 'question');

        // Each section adds html to be displayed as part of this list item.
        $questionbankurl = new moodle_url('/question/edit.php', $this->parentlist->pageurl->params());
        $questionbankurl->param('cat', $category->id . ',' . $category->contextid);
        $item = '';
        $text = format_string($category->name, true, ['context' => $this->parentlist->context]);
        if ($category->idnumber !== null && $category->idnumber !== '') {
            $text .= ' ' . html_writer::span(
                    html_writer::span(get_string('idnumber', 'question'), 'accesshide') .
                    ' ' . $category->idnumber, 'badge badge-primary');
        }
        $text .= ' (' . $category->questioncount . ')';
        $item .= html_writer::tag('b', html_writer::link($questionbankurl, $text,
                        ['title' => $editqestions]) . ' ');
        $item .= format_text($category->info, $category->infoformat,
                array('context' => $this->parentlist->context, 'noclean' => true));

        // Don't allow delete if this is the top category, or the last editable category in this context.
        if ($category->parent && !question_is_only_child_of_top_category_in_context($category->id)) {
            $deleteurl = new moodle_url($this->parentlist->pageurl, array('delete' => $this->id, 'sesskey' => sesskey()));
            $item .= html_writer::link($deleteurl,
                    $OUTPUT->pix_icon('t/delete', $str->delete),
                    array('title' => $str->delete));
        }

        return $item;
    }
}


/**
 * Class for performing operations on question categories.
 *
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_category_object {

    /**
     * @var array common language strings.
     */
    public $str;

    /**
     * @var array nested lists to display categories.
     */
    public $editlists = array();
    public $tab;
    public $tabsize = 3;

    /**
     * @var moodle_url Object representing url for this page
     */
    public $pageurl;

    /**
     * @var question_category_edit_form Object representing form for adding / editing categories.
     */
    public $catform;

    /**
     * Constructor.
     *
     * @param int $page page number
     * @param moodle_url $pageurl base URL of the display categories page. Used for redirects.
     * @param context[] $contexts contexts where the current user can edit categories.
     * @param int $currentcat id of the category to be edited. 0 if none.
     * @param int|null $defaultcategory id of the current category. null if none.
     * @param int $todelete id of the category to delete. 0 if none.
     * @param context[] $addcontexts contexts where the current user can add questions.
     */
    public function __construct($page, $pageurl, $contexts, $currentcat, $defaultcategory, $todelete, $addcontexts) {

        $this->tab = str_repeat('&nbsp;', $this->tabsize);

        $this->str = new stdClass();
        $this->str->course         = get_string('course');
        $this->str->category       = get_string('category', 'question');
        $this->str->categoryinfo   = get_string('categoryinfo', 'question');
        $this->str->questions      = get_string('questions', 'question');
        $this->str->add            = get_string('add');
        $this->str->delete         = get_string('delete');
        $this->str->moveup         = get_string('moveup');
        $this->str->movedown       = get_string('movedown');
        $this->str->edit           = get_string('editthiscategory', 'question');
        $this->str->hide           = get_string('hide');
        $this->str->order          = get_string('order');
        $this->str->parent         = get_string('parent', 'question');
        $this->str->add            = get_string('add');
        $this->str->action         = get_string('action');
        $this->str->top            = get_string('top');
        $this->str->addcategory    = get_string('addcategory', 'question');
        $this->str->editcategory   = get_string('editcategory', 'question');
        $this->str->cancel         = get_string('cancel');
        $this->str->editcategories = get_string('editcategories', 'question');
        $this->str->page           = get_string('page');

        $this->pageurl = $pageurl;

        $this->initialize($page, $contexts, $currentcat, $defaultcategory, $todelete, $addcontexts);
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function question_category_object($page, $pageurl, $contexts, $currentcat, $defaultcategory, $todelete, $addcontexts) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($page, $pageurl, $contexts, $currentcat, $defaultcategory, $todelete, $addcontexts);
    }

    /**
     * Initializes this classes general category-related variables
     */
    public function initialize($page, $contexts, $currentcat, $defaultcategory, $todelete, $addcontexts) {
        global $CFG,$DB;
        $courseid  = optional_param('courseid', '', PARAM_TEXT);
  
        $systemcontext = context_system::instance();
        $companyid = iomad::get_my_companyid($systemcontext);
        if($companyid){
            $cid=$DB->get_record_sql("select * from {context} where contextlevel=50 and instanceid=$courseid");
            $context_id=$cid->id;
        }
        $lastlist = null;
        foreach ($contexts as $context){
            // bk
            if($context_id == $context->id){
                $this->editlists[$context->id] = new question_category_list('ul', '', true, $this->pageurl, $page, 'cpage', QUESTION_PAGE_LENGTH, $context);
                $this->editlists[$context->id]->lastlist =& $lastlist;
                if ($lastlist!== null){
                    $lastlist->nextlist =& $this->editlists[$context->id];
                }
                $lastlist =& $this->editlists[$context->id];
            }

        }

        $count = 1;
        $paged = false;
        foreach ($this->editlists as $key => $list){
            list($paged, $count) = $this->editlists[$key]->list_from_records($paged, $count);
        }
        $this->catform = new question_category_edit_form($this->pageurl, compact('contexts', 'currentcat'));
        if (!$currentcat){
            $this->catform->set_data(array('parent'=>$defaultcategory));
        }
    }

    /**
     * Displays the user interface
     *
     */
    public function display_user_interface() {

        /// Interface for editing existing categories
        $this->output_edit_lists();


        echo '<br />';
        /// Interface for adding a new category:
        $this->output_new_table();
        echo '<br />';

    }

    /**
     * Outputs a table to allow entry of a new category
     */
    public function output_new_table() {
        $this->catform->display();
    }

    /**
     * Outputs a list to allow editing/rearranging of existing categories
     *
     * $this->initialize() must have already been called
     *
     */
    public function output_edit_lists() {
        global $OUTPUT;

        echo $OUTPUT->heading_with_help(get_string('editcategories', 'question'), 'editcategories', 'question');
        // echo '<pre>';print_r($this->editlists);exit;

        foreach ($this->editlists as $context => $list){
            $listhtml = $list->to_html(0, array('str'=>$this->str));
            if ($listhtml){
                echo $OUTPUT->box_start('boxwidthwide boxaligncenter generalbox questioncategories contextlevel' . $list->context->contextlevel);
                $fullcontext = context::instance_by_id($context);
                echo $OUTPUT->heading(get_string('questioncatsfor', 'question', $fullcontext->get_context_name()), 3);
                echo $listhtml;
                echo $OUTPUT->box_end();
            }
        }
        echo $list->display_page_numbers();
     }

    /**
     * gets all the courseids for the given categories
     *
     * @param array categories contains category objects in  a tree representation
     * @return array courseids flat array in form categoryid=>courseid
     */
    public function get_course_ids($categories) {
        $courseids = array();
        foreach ($categories as $key=>$cat) {
            $courseids[$key] = $cat->course;
            if (!empty($cat->children)) {
                $courseids = array_merge($courseids, $this->get_course_ids($cat->children));
            }
        }
        return $courseids;
    }

    public function edit_single_category($categoryid) {
    /// Interface for adding a new category
        global $DB;
        /// Interface for editing existing categories
        $category = $DB->get_record("question_categories", array("id" => $categoryid));
        if (empty($category)) {
            print_error('invalidcategory', '', '', $categoryid);
        } else if ($category->parent == 0) {
            print_error('cannotedittopcat', 'question', '', $categoryid);
        } else {
            $category->parent = "{$category->parent},{$category->contextid}";
            $category->submitbutton = get_string('savechanges');
            $category->categoryheader = $this->str->edit;
            $this->catform->set_data($category);
            $this->catform->display();
        }
    }

    /**
     * Sets the viable parents
     *
     *  Viable parents are any except for the category itself, or any of it's descendants
     *  The parentstrings parameter is passed by reference and changed by this function.
     *
     * @param    array parentstrings a list of parentstrings
     * @param   object category
     */
    public function set_viable_parents(&$parentstrings, $category) {

        unset($parentstrings[$category->id]);
        if (isset($category->children)) {
            foreach ($category->children as $child) {
                $this->set_viable_parents($parentstrings, $child);
            }
        }
    }

    /**
     * Gets question categories
     *
     * @param    int parent - if given, restrict records to those with this parent id.
     * @param    string sort - [[sortfield [,sortfield]] {ASC|DESC}]
     * @return   array categories
     */
    public function get_question_categories($parent=null, $sort="sortorder ASC") {
        global $COURSE, $DB;
        if (is_null($parent)) {
            $categories = $DB->get_records('question_categories', array('course' => $COURSE->id), $sort);
        } else {
            $select = "parent = ? AND course = ?";
            $categories = $DB->get_records_select('question_categories', $select, array($parent, $COURSE->id), $sort);
        }
        return $categories;
    }

    /**
     * Deletes an existing question category
     *
     * @param int deletecat id of category to delete
     */
    public function delete_category($categoryid) {
        global $CFG, $DB;
        question_can_delete_cat($categoryid);
        if (!$category = $DB->get_record("question_categories", array("id" => $categoryid))) {  // security
            print_error('unknowcategory');
        }
        /// Send the children categories to live with their grandparent
        $DB->set_field("question_categories", "parent", $category->parent, array("parent" => $category->id));

        /// Finally delete the category itself
        $DB->delete_records("question_categories", array("id" => $category->id));

        // Log the deletion of this category.
        $event = \core\event\question_category_deleted::create_from_question_category_instance($category);
        $event->add_record_snapshot('question_categories', $category);
        $event->trigger();

    }

    public function move_questions_and_delete_category($oldcat, $newcat){
        question_can_delete_cat($oldcat);
        $this->move_questions($oldcat, $newcat);
        $this->delete_category($oldcat);
    }

    public function display_move_form($questionsincategory, $category){
        global $OUTPUT;
        $vars = new stdClass();
        $vars->name = $category->name;
        $vars->count = $questionsincategory;
        echo $OUTPUT->box(get_string('categorymove', 'question', $vars), 'generalbox boxaligncenter');
        $this->moveform->display();
    }

    public function move_questions($oldcat, $newcat){
        global $DB;
        $questionids = $DB->get_records_select_menu('question',
                'category = ? AND (parent = 0 OR parent = id)', array($oldcat), '', 'id,1');
        question_move_questions_to_category(array_keys($questionids), $newcat);
    }

    /**
     * Create a new category.
     *
     * Data is expected to come from question_category_edit_form.
     *
     * By default redirects on success, unless $return is true.
     *
     * @param string $newparent 'categoryid,contextid' of the parent category.
     * @param string $newcategory the name.
     * @param string $newinfo the description.
     * @param bool $return if true, return rather than redirecting.
     * @param int|string $newinfoformat description format. One of the FORMAT_ constants.
     * @param null $idnumber the idnumber. '' is converted to null.
     * @return bool|int New category id if successful, else false.
     */
    public function add_category($newparent, $newcategory, $newinfo, $return = false, $newinfoformat = FORMAT_HTML,
            $idnumber = null) {
        global $DB;
        if (empty($newcategory)) {
            print_error('categorynamecantbeblank', 'question');
        }
        list($parentid, $contextid) = explode(',', $newparent);
        //moodle_form makes sure select element output is legal no need for further cleaning
        require_capability('moodle/question:managecategory', context::instance_by_id($contextid));

        if ($parentid) {
            if(!($DB->get_field('question_categories', 'contextid', array('id' => $parentid)) == $contextid)) {
                print_error('cannotinsertquestioncatecontext', 'question', '', array('cat'=>$newcategory, 'ctx'=>$contextid));
            }
        }

        if ((string) $idnumber === '') {
            $idnumber = null;
        } else if (!empty($contextid)) {
            // While this check already exists in the form validation, this is a backstop preventing unnecessary errors.
            if ($DB->record_exists('question_categories',
                    ['idnumber' => $idnumber, 'contextid' => $contextid])) {
                $idnumber = null;
            }
        }

        $cat = new stdClass();
        $cat->parent = $parentid;
        $cat->contextid = $contextid;
        $cat->name = $newcategory;
        $cat->info = $newinfo;
        $cat->infoformat = $newinfoformat;
        $cat->sortorder = 999;
        $cat->stamp = make_unique_id_code();
        $cat->idnumber = $idnumber;
        print_r($cat);exit;
        $categoryid = $DB->insert_record("question_categories", $cat);

        // Log the creation of this category.
        $category = new stdClass();
        $category->id = $categoryid;
        $category->contextid = $contextid;
        $event = \core\event\question_category_created::create_from_question_category_instance($category);
        $event->trigger();

        if ($return) {
            return $categoryid;
        } else {
            redirect($this->pageurl);//always redirect after successful action
        }
    }

    /**
     * Updates an existing category with given params.
     *
     * Warning! parameter order and meaning confusingly different from add_category in some ways!
     *
     * @param int $updateid id of the category to update.
     * @param int $newparent 'categoryid,contextid' of the parent category to set.
     * @param string $newname category name.
     * @param string $newinfo category description.
     * @param int|string $newinfoformat description format. One of the FORMAT_ constants.
     * @param int $idnumber the idnumber. '' is converted to null.
     * @param bool $redirect if true, will redirect once the DB is updated (default).
     */
    public function update_category($updateid, $newparent, $newname, $newinfo, $newinfoformat = FORMAT_HTML,
            $idnumber = null, $redirect = true) {
        global $CFG, $DB;
        if (empty($newname)) {
            print_error('categorynamecantbeblank', 'question');
        }

        // Get the record we are updating.
        $oldcat = $DB->get_record('question_categories', array('id' => $updateid));
        $lastcategoryinthiscontext = question_is_only_child_of_top_category_in_context($updateid);

        if (!empty($newparent) && !$lastcategoryinthiscontext) {
            list($parentid, $tocontextid) = explode(',', $newparent);
        } else {
            $parentid = $oldcat->parent;
            $tocontextid = $oldcat->contextid;
        }

        // Check permissions.
        $fromcontext = context::instance_by_id($oldcat->contextid);
        require_capability('moodle/question:managecategory', $fromcontext);

        // If moving to another context, check permissions some more, and confirm contextid,stamp uniqueness.
        $newstamprequired = false;
        if ($oldcat->contextid != $tocontextid) {
            $tocontext = context::instance_by_id($tocontextid);
            require_capability('moodle/question:managecategory', $tocontext);

            // Confirm stamp uniqueness in the new context. If the stamp already exists, generate a new one.
            if ($DB->record_exists('question_categories', array('contextid' => $tocontextid, 'stamp' => $oldcat->stamp))) {
                $newstamprequired = true;
            }
        }

        if ((string) $idnumber === '') {
            $idnumber = null;
        } else if (!empty($tocontextid)) {
            // While this check already exists in the form validation, this is a backstop preventing unnecessary errors.
            if ($DB->record_exists_select('question_categories',
                    'idnumber = ? AND contextid = ? AND id <> ?',
                    [$idnumber, $tocontextid, $updateid])) {
                $idnumber = null;
            }
        }

        // Update the category record.
        $cat = new stdClass();
        $cat->id = $updateid;
        $cat->name = $newname;
        $cat->info = $newinfo;
        $cat->infoformat = $newinfoformat;
        $cat->parent = $parentid;
        $cat->contextid = $tocontextid;
        $cat->idnumber = $idnumber;
        if ($newstamprequired) {
            $cat->stamp = make_unique_id_code();
        }
        $DB->update_record('question_categories', $cat);

        // Log the update of this category.
        $event = \core\event\question_category_updated::create_from_question_category_instance($cat);
        $event->trigger();

        // If the category name has changed, rename any random questions in that category.
        if ($oldcat->name != $cat->name) {
            $where = "qtype = 'random' AND category = ? AND " . $DB->sql_compare_text('questiontext') . " = ?";

            $randomqtype = question_bank::get_qtype('random');
            $randomqname = $randomqtype->question_name($cat, false);
            $DB->set_field_select('question', 'name', $randomqname, $where, array($cat->id, '0'));

            $randomqname = $randomqtype->question_name($cat, true);
            $DB->set_field_select('question', 'name', $randomqname, $where, array($cat->id, '1'));
        }

        if ($oldcat->contextid != $tocontextid) {
            // Moving to a new context. Must move files belonging to questions.
            question_move_category_to_context($cat->id, $oldcat->contextid, $tocontextid);
        }

        // Cat param depends on the context id, so update it.
        $this->pageurl->param('cat', $updateid . ',' . $tocontextid);
        if ($redirect) {
            redirect($this->pageurl); // Always redirect after successful action.
        }
    }
}
