<?php

/**
  This file contains general functions for the course format Collapsed Topics
  Thanks to Sam Hemelryk who modified the Moodle core code for 2.0, and
  I have copied and modified under the terms of the following license:
  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see http://www.gnu.org/licenses/.
 */
require_once($CFG->dirroot . '/course/format/moonstone/config.php'); // For Collaped Topics defaults.

/**
 * Indicates this format uses sections.
 *
 * @return bool Returns true
 */
function callback_moonstone_uses_sections() {
    return true;
}

/**
 * Used to display the course structure for a course where format=Collapsed Topics
 *
 * This is called automatically by {@link load_course()} if the current course
 * format = Collapsed Topics.
 *
 * @param navigation_node $navigation The course node
 * @param array $path An array of keys to the course node
 * @param stdClass $course The course we are loading the section for
 */
function callback_moonstone_load_content(&$navigation, $course, $coursenode) {
    return $navigation->load_generic_course_sections($course, $coursenode, 'moonstone');
}

/**
 * The string that is used to describe a section of the course
 *
 * @return string
 */
function callback_moonstone_definition() {
    return get_string('sectionname', 'format_moonstone');
}

/**
 * The GET argument variable that is used to identify the section being
 * viewed by the user (if there is one)
 *
 * @return string
 */
function callback_moonstone_request_key() {
    return 'moonstone';
}

/**
 * Gets the name for the provided section.
 *
 * @param stdClass $course
 * @param stdClass $section
 * @return string
 */
function callback_moonstone_get_section_name($course, $section) {

    // We can't add a node without any text
    if ((string) $section->name !== '') {
        return format_string($section->name, true, array('context' => context_course::instance($course->id)));
    } else if ($section->section == 0) {
        return get_string('section0name', 'format_moonstone');
    } else {
        global $tcsetting;
        if (empty($tcsetting) == true) {
            $tcsetting = get_moonstone_setting($course->id); // CONTRIB-3378
        }
        //$renderer = $PAGE->get_renderer('format_moonstone');
        //$setting = $renderer->get_tc_setting();
        //print_object($tcsetting);
        if (($tcsetting->layoutstructure == 1) || ($tcsetting->layoutstructure == 4)) {
            return get_string('sectionname', 'format_moonstone') . ' ' . $section->section;
        } else {
            $dateformat = ' ' . get_string('strftimedateshort');
            if ($tcsetting->layoutstructure == 5) {
                $day = format_moonstone_get_section_day($section, $course);

                $weekday = userdate($day, $dateformat);
                return $weekday;
            } else {
                $dates = format_moonstone_get_section_dates($section, $course);

                // We subtract 24 hours for display purposes.
                $dates->end = ($dates->end - 86400);

                $weekday = userdate($dates->start, $dateformat);
                $endweekday = userdate($dates->end, $dateformat);
                return $weekday . ' - ' . $endweekday;
            }
        }
    }
}

/**
 * Declares support for course AJAX features
 *
 * @see course_format_ajax_support()
 * @return stdClass
 */
function callback_moonstone_ajax_support() {
    $ajaxsupport = new stdClass();
    $ajaxsupport->capable = true;
    $ajaxsupport->testedbrowsers = array('MSIE' => 6.0, 'Gecko' => 20061111, 'Opera' => 9.0, 'Safari' => 531, 'Chrome' => 6.0);
    return $ajaxsupport;
}

/**
 * Returns a URL to arrive directly at a section
 *
 * @param int $courseid The id of the course to get the link for
 * @param int $sectionnum The section number to jump to
 * @return moodle_url
 */
function callback_moonstone_get_section_url($courseid, $sectionnum) {
    return new moodle_url('/course/view.php', array('id' => $courseid, 'ctopic' => $sectionnum));
}

/**
 * Callback function to do some action after section move
 *
 * @param stdClass $course The course entry from DB
 * @return array This will be passed in ajax respose.
 */
function callback_moonstone_ajax_section_move($course) {
    global $COURSE, $PAGE;

    $titles = array();
    rebuild_course_cache($course->id);
    $modinfo = get_fast_modinfo($COURSE);
    $renderer = $PAGE->get_renderer('format_moonstone');
    if ($renderer && ($sections = $modinfo->get_section_info_all())) {
        foreach ($sections as $number => $section) {
            $titles[$number] = $renderer->section_title($section, $course);
        }
    }
    return array('sectiontitles' => $titles, 'action' => 'move');
}

/**
 * Gets the format setting for the course or if it does not exist, create it.
 * CONTRIB-3378
 * @param int $courseid The course identifier.
 * @return int The format setting.
 */
function get_moonstone_setting($courseid) {
    global $DB;
    global $TCCFG;

    if (!$setting = $DB->get_record('format_moonstone_settings', array('courseid' => $courseid))) {
        // Default values...
        $setting = new stdClass();
        $setting->courseid = $courseid;
        $setting->layoutelement = $TCCFG->defaultlayoutelement;
        $setting->layoutstructure = $TCCFG->defaultlayoutstructure;
        $setting->layoutcolumns = $TCCFG->defaultlayoutcolumns;
        $setting->tgfgcolour = $TCCFG->defaulttgfgcolour;
        $setting->tgbgcolour = $TCCFG->defaulttgbgcolour;
        $setting->tgbghvrcolour = $TCCFG->defaulttgbghvrcolour;

        if (!$setting->id = $DB->insert_record('format_moonstone_settings', $setting)) {
            error('Could not set format setting. Collapsed Topics format database is not ready.  An admin must visit notifications.');
        }
    }

    return $setting;
}

/**
 * Sets the format setting for the course or if it does not exist, create it.
 * CONTRIB-3378
 * @param int $courseid The course identifier.
 * @param int $layoutelement The layout element value to set.
 * @param int $layoutstructure The layout structure value to set.
 * @param int $layoutcolumns The layout columns value to set.
 * @param string $tgfgcolour The toggle foreground colour to set.
 * @param string $tgbgcolour The toggle background colour to set.
 * @param string $tgbghvrcolour The toggle background hover colour to set.
 */
function put_moonstone_setting($courseid, $layoutelement, $layoutstructure, $layoutcolumns, $tgfgcolour, $tgbgcolour, $tgbghvrcolour) {
    global $DB;
    if ($setting = $DB->get_record('format_moonstone_settings', array('courseid' => $courseid))) {
        $setting->layoutelement = $layoutelement;
        $setting->layoutstructure = $layoutstructure;
        $setting->layoutcolumns = $layoutcolumns;
        $setting->tgfgcolour = $tgfgcolour;
        $setting->tgbgcolour = $tgbgcolour;
        $setting->tgbghvrcolour = $tgbghvrcolour;
        $DB->update_record('format_moonstone_settings', $setting);
    } else {
        $setting = new stdClass();
        $setting->courseid = $courseid;
        $setting->layoutelement = $layoutelement;
        $setting->layoutstructure = $layoutstructure;
        $setting->layoutcolumns = $layoutcolumns;
        $setting->tgfgcolour = $tgfgcolour;
        $setting->tgbgcolour = $tgbgcolour;
        $setting->tgbghvrcolour = $tgbghvrcolour;
        $DB->insert_record('format_moonstone_settings', $setting);
    }
}

function reset_moonstone_setting($layout, $colour) {
    global $DB;
    global $TCCFG;
	
	$records = $DB->get_records('format_moonstone_settings');
	//print_object($records);
	foreach($records as $record) {
	    if ($layout) {
	       $record->layoutelement = $TCCFG->defaultlayoutelement;
		   $record->layoutstructure = $TCCFG->defaultlayoutstructure;
		   $record->layoutcolumns = $TCCFG->defaultlayoutcolumns;
	    }
	    if ($colour) {
            $record->tgfgcolour = $TCCFG->defaulttgfgcolour;
            $record->tgbgcolour = $TCCFG->defaulttgbgcolour;
            $record->tgbghvrcolour = $TCCFG->defaulttgbghvrcolour;
	    }
	    $DB->update_record('format_moonstone_settings', $record);
	}
	//$records = $DB->get_records('format_moonstone_settings');
	//print_object($records);
}

/**
 * Deletes the layout entry for the given course.
 * CONTRIB-3520
 */
function format_moonstone_delete_course($courseid) {
    global $DB;

    $DB->delete_records("format_moonstone_settings", array("courseid" => $courseid));
    $DB->delete_records("user_preferences", array("name" => 'moonstone_toggle_' . $courseid));
}

/**
 * Return the start and end date of the passed section
 *
 * @param stdClass $section The course_section entry from the DB
 * @param stdClass $course The course entry from DB
 * @return stdClass property start for startdate, property end for enddate
 */
function format_moonstone_get_section_dates($section, $course) {
    $oneweekseconds = 604800;
    // Hack alert. We add 2 hours to avoid possible DST problems. (e.g. we go into daylight
    // savings and the date changes.
    $startdate = $course->startdate + 7200;

    $dates = new stdClass();
    $dates->start = $startdate + ($oneweekseconds * ($section->section - 1));
    $dates->end = $dates->start + $oneweekseconds;

    return $dates;
}

/**
 * Return the start and end date of the passed section
 *
 * @param stdClass $section The course_section entry from the DB
 * @param stdClass $course The course entry from DB
 * @return stdClass property start for startdate, property end for enddate
 */
function format_moonstone_get_section_day($section, $course) {
    $onedayseconds = 86400;
    // Hack alert. We add 2 hours to avoid possible DST problems. (e.g. we go into daylight
    // savings and the date changes.
    $startdate = $course->startdate + 7200;

    $day = $startdate + ($onedayseconds * ($section->section - 1));

    return $day;
}