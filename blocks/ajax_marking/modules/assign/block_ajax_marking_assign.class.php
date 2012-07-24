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
 * Class file for the Assign module grading functions
 *
 * @package    block
 * @subpackage ajax_marking
 * @copyright  2012 Matt Gibson
 * @author     Matt Gibson {@link http://moodle.org/user/view.php?id=81450}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/blocks/ajax_marking/classes/query_base.class.php');
require_once($CFG->dirroot.'/blocks/ajax_marking/classes/module_base.class.php');
require_once($CFG->dirroot.'/blocks/ajax_marking/classes/filters.class.php');
require_once($CFG->dirroot.'/mod/assign/locallib.php');

/**
 * Extension to the block_ajax_marking_module_base class which adds the parts that deal
 * with the assign module.
 */
class block_ajax_marking_assign extends block_ajax_marking_module_base {

    /**
     * Constructor. Needs to be duplicated in all modules, so best put in parent. PHP4 issue though.
     *
     * The aim is to pass in the main ajax_marking_functions object by reference, so that its
     * properties are accessible
     *
     * @internal param object $reference the parent object to be referred to
     * @return \block_ajax_marking_assign
     */
    public function __construct() {

        // Call parent constructor with the same arguments (keep for 2.1 - PHP 5.3 needed.
        parent::__construct();

        $this->modulename = 'assign'; // DB modulename.
        $this->capability = 'mod/assign:grade';
        $this->icon = 'mod/assign/icon.gif';
    }

    /**
     * Makes the grading interface for the pop up. Robbed from /mod/assign/locallib.php
     * line 1583ish - view_single_grade_page().
     *
     * @param array $params From $_GET
     * @param object $coursemodule The coursemodule object that the user has been authenticated
     * against
     * @param bool $data
     * @throws coding_exception
     * @global $PAGE
     * @global stdClass $CFG
     * @global moodle_database $DB
     * @global $OUTPUT
     * @global stdClass $USER
     * @return string
     */
    public function grading_popup($params, $coursemodule, $data = false) {

        global $PAGE, $CFG, $DB;

        $modulecontext = context_module::instance($coursemodule->id);
        $course = $DB->get_record('course', array('id' => $coursemodule->course));
        $coursecontext = context_course::instance($course->id);

        $assign = new assign($modulecontext, $coursemodule, $course);

        /* @var mod_assign_renderer $renderer */
        $renderer = $PAGE->get_renderer('mod_assign');

        $output = '';

        // Include grade form.
        require_once($CFG->dirroot.'/mod/assign/gradeform.php');

        // Need submit permission to submit an assignment.
        require_capability('mod/assign:grade', $modulecontext);

        /* Pinched from private method assign::get_grading_userid_list() */
        $filter = get_user_preferences('assign_filter', '');
        $table = new assign_grading_table($assign, 0, $filter, 0, false);
        $useridlist = $table->get_column_data('userid');

        $userid = $params['userid'];

        $rownum = 0;
        foreach ($useridlist as $key => $useridinlist) {
            if ($useridinlist == $userid) {
                $rownum = $key;
                reset ($useridlist); // Just in case.
                break;
            }
        }

        $last = false;
        if ($rownum == count($useridlist) - 1) {
            $last = true;
        }

        $user = $DB->get_record('user', array('id' => $userid));
        if ($user) {
            $output .= $renderer->render(new assign_user_summary($user, $course->id,
                                                                     has_capability('moodle/site:viewfullnames',
                                                                                    $coursecontext)));
        }
        $submission = $DB->get_record('assign_submission',
                                      array('assignment' => $assign->get_instance()->id,
                                            'userid' => $userid));

        // Get the current grade. Pinched from assign::get_user_grade().
        $grade = $DB->get_record('assign_grades',
                                 array('assignment' => $assign->get_instance()->id,
                                       'userid' => $userid));
        // Pinched from assign::is_graded().
        $isgraded = (!empty($grade) && $grade->grade !== null && $grade->grade >= 0);

        if ($assign->can_view_submission($userid)) {
            $gradelocked = ($grade && $grade->locked) || $assign->grading_disabled($userid);
            $widget =
                new assign_submission_status($assign->get_instance()->allowsubmissionsfromdate,
                                             $assign->get_instance()->alwaysshowdescription,
                                             $submission,
                                             $assign->is_any_submission_plugin_enabled(),
                                             $gradelocked,
                                             $isgraded,
                                             $assign->get_instance()->duedate,
                                             $assign->get_submission_plugins(),
                                             $assign->get_return_action(),
                                             $assign->get_return_params(),
                                             $assign->get_course_module()->id,
                                             assign_submission_status::GRADER_VIEW,
                                             false,
                                             false);
            $output .= $renderer->render($widget);
        }
        if ($grade) {
            $data = new stdClass();
            if ($grade->grade !== null && $grade->grade >= 0) {
                $data->grade = format_float($grade->grade, 2);
            }
        } else {
            $data = new stdClass();
            $data->grade = '';
        }

        // Now show the grading form.
        $customdata = array(
            'useridlist' => $useridlist,
            'rownum' => $rownum,
            'last' => $last
        );
        $mform = new mod_assign_grade_form(block_ajax_marking_form_url($params),
                                           array($assign,
                                                 $data,
                                                 $customdata),
                                           'post',
                                           '',
                                           array('class' => 'gradeform'));
        $output .= $renderer->render(new assign_form('gradingform', $mform));

        $assign->add_to_log('view grading form',
                          get_string('viewgradingformforstudent', 'assign',
                                     array('id' => $user->id,
                                           'fullname' => fullname($user))));

        return $output;
    }

    /**
     * Process and save the data from the feedback form. Pinched from
     * assign::process_and_save_grade().
     *
     * @param object $data from the feedback form
     * @param $params
     * @return string
     */
    public function process_data($data, $params) {

        global $DB;

        $coursemodule = $DB->get_record('course_modules', array('id' => $params['coursemoduleid']));
        $modulecontext = context_module::instance($coursemodule->id);
        $course = $DB->get_record('course', array('id' => $coursemodule->course));
        $assign = new assign($modulecontext, $coursemodule, $course);

        // This is horrible, but seems to be the least ugly approach. The alternative is to
        // duplicate all the functionality o0f this method outside of the class.
        $assignrelector = new ReflectionClass('assign');
        $method = $assignrelector->getMethod("process_save_grade");
        $method->setAccessible(true);
        $method->invoke($assign, null);

        return '';
    }

    /**
     * Returns a query object with the basics all set up to get assignment stuff
     *
     * @global moodle_database $DB
     * @return block_ajax_marking_query_base
     */
    public function query_factory() {

        global $DB, $USER;

        $query = new block_ajax_marking_query_base($this);

        $query->add_from(array(
                              'table' => 'assign',
                              'alias' => 'moduletable',
                         ));

        $query->add_from(array(
                              'join' => 'INNER JOIN',
                              'table' => 'assign_submission',
                              'alias' => 'sub',
                              'on' => 'sub.assignment = moduletable.id'
                         ));
        // LEFT JOIN, rather than NOT EXISTS because we may have an empty form saved, which
        // will create a grade record, but with a null grade. These should still count as ungraded.
        $query->add_from(array(
                              'join' => 'LEFT JOIN',
                              'table' => 'assign_grades',
                              'on' => 'assign_grades.assignment = moduletable.id AND
                                       assign_grades.userid = sub.userid AND
                                       assign_grades.grader = :assigngraderid'
                         ));
        $query->add_param('assigngraderid', $USER->id);

        // Standard user id for joins.
        $query->add_select(array('table' => 'sub',
                                 'column' => 'userid'));
        $query->add_select(array('table' => 'sub',
                                 'column' => 'timemodified',
                                 'alias' => 'timestamp'));

        $statustext = $DB->sql_compare_text('sub.status');
        $query->add_where(array(
                               'type' => 'AND',
                               'condition' => $statustext." = '".ASSIGN_SUBMISSION_STATUS_SUBMITTED."'"));
        $query->add_where(array(
                               'type' => 'AND',
                               'condition' => 'assign_grades.grade IS NULL'
                          ));

        // First bit: not graded
        // Second bit of first bit: has been resubmitted
        // Third bit: if it's advanced upload, only care about the first bit if 'send for marking'
        // was clicked.

        return $query;
    }
}

/**
 * Holds any custom filters for userid nodes that this module offers
 */
class block_ajax_marking_assign_userid extends block_ajax_marking_filter_base {

    /**
     * Not sure we'll ever need this, but just in case...
     *
     * @static
     * @param block_ajax_marking_query_base $query
     * @param $userid
     */
    public static function where_filter($query, $userid) {
        $countwrapper = self::get_countwrapper_subquery($query);
        $clause = array(
            'type' => 'AND',
            'condition' => 'sub.userid = :assignuseridfilteruserid');
        $countwrapper->add_where($clause);
        $query->add_param('assignuseridfilteruserid', $userid);
    }

    /**
     * Makes user nodes for the assign modules by grouping them and then adding in the right
     * text to describe them.
     *
     * @static
     * @param block_ajax_marking_query_base $query
     */
    public static function nextnodetype_filter($query) {

        $countwrapper = self::get_countwrapper_subquery($query);

        // Make the count be grouped by userid.
        $conditions = array(
            'table' => 'moduleunion',
            'column' => 'userid',
            'alias' => 'id');
        $countwrapper->add_select($conditions, true);
        $conditions = array(
            'table' => 'countwrapperquery',
            'column' => 'timestamp',
            'alias' => 'tooltip');
        $query->add_select($conditions);

        $conditions = array(
            'table' => 'usertable',
            'column' => 'firstname');
        $query->add_select($conditions);
        $conditions = array(
            'table' => 'usertable',
            'column' => 'lastname');
        $query->add_select($conditions);

        $table = array(
            'table' => 'user',
            'alias' => 'usertable',
            'on' => 'usertable.id = countwrapperquery.id');
        $query->add_from($table);
    }

}
