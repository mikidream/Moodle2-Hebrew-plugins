<?php
/**
 * Collapsed Topics Information
 *
 * A topic based format that solves the issue of the 'Scroll of Death' when a course has many topics. All topics
 * except zero have a toggle that displays that topic. One or more topics can be displayed at any given time.
 * Toggles are persistent on a per browser session per course basis but can be made to persist longer by a small
 * code change. Full installation instructions, code adaptions and credits are included in the 'Readme.txt' file.
 *
 * @package    course/format
 * @subpackage moonstone
 * @version    See the value of '$plugin->version' in version.php.
 * @copyright  &copy; 2009-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - gjbarnard at gmail dot com and {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
<<<<<<< HEAD
=======

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
>>>>>>> remotes/origin/CONTRIB-3378

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
 
// Portuguese Translation of Collapsed Topics Course Format
// Português Tradução de Tópicos Fechado formato do curso

// Used by the Moodle Core for identifing the format and displaying in the list of formats for a course in its settings.
// Usado pelo Core Moodle para identificando o formato e exibição na lista de formatos para um curso em suas configurações.
$string['namemoonstone']='Fechado Topicos';
$string['formatmoonstone']='Fechado Topicos';

// Used in format.php
// Usado em format.php
$string['moonstonetoggle']='Alternar';
$string['moonstonetogglewidth']='width: 34px;';

// Toggle all - Moodle Tracker CONTRIB-3190
$string['moonstoneall']='todas as alterna.';
$string['moonstoneopened']='Abra';
$string['moonstoneclosed']='Feche';

// Moodle 2.0 Enhancement - Moodle Tracker MDL-15252, MDL-21693 & MDL-22056 - http://docs.moodle.org/en/Development:Languages
// Moodle 2.0 Realce - Moodle Tracker MDL-15252, MDL-21693 & MDL-22056 - http://docs.moodle.org/en/Development:Languages
$string['sectionname'] = 'Assunto';
$string['pluginname'] = 'Fechado Topicos';
$string['section0name'] = 'Geral';

// Everything below is pending translation...
// MDL-26105
$string['page-course-view-moonstone'] = 'Any course main page in collapsed topics format';
$string['page-course-view-moonstone-x'] = 'Any course page in collapsed topics format';

// Layout enhancement - Moodle Tracker CONTRIB-3378
$string['formatsettings'] = 'Format settings'; // CONTRIB-3529
$string['setlayout'] = 'Set layout';
$string['setlayout_default'] = 'Default';
$string['setlayout_no_toggle_section_x'] = 'No toggle section x';
$string['setlayout_no_section_no'] = 'No section number';
$string['setlayout_no_toggle_section_x_section_no'] = 'No toggle section x and section number';
$string['setlayout_no_toggle_word'] = 'No toggle word';
$string['setlayout_no_toggle_word_toggle_section_x'] = 'No toggle word and toggle section x';
$string['setlayout_no_toggle_word_toggle_section_x_section_no'] = 'No toggle word, toggle section x and section number';
$string['setlayoutelements'] = 'Set elements';
$string['setlayoutstructure'] = 'Set structure';
$string['setlayoutstructuretopic']='Topic';
$string['setlayoutstructureweek']='Week';
$string['setlayoutstructurelatweekfirst']='Latest Week First';
$string['setlayoutstructurecurrenttopicfirst']='Current Topic First';
$string['resetlayout'] = 'Reset layout'; //CONTRIB-3529

// Colour enhancement - Moodle Tracker CONTRIB-3529
$string['setcolour'] = 'Set colour';
$string['colourrule'] = "Please enter a valid RGB colour, a '#' and then six hexadecimal digits.";
$string['settoggleforegroundcolour'] = 'Toggle foreground';
$string['settogglebackgroundcolour'] = 'Toggle background';
$string['settogglebackgroundhovercolour'] = 'Toggle background hover';
$string['resetcolour'] = 'Reset colour';

// Cookie consent - Moodle Tracker CONTRIB-3624
$string['cookieconsentform'] = 'Cookie consent form' ;
$string['cookieconsent'] = "Cookie consent is required to allow any course that uses the 'Collapsed Topics' format as you can see below to remember the state of the toggles.  Once you have given that consent using the icon to the right, the toggles will remember what you set them to when you refresh the page and when you return if this has been setup by your administrator.<br /><br />The cookie 'mdl_cf_moonstone' only contains the site short name, course id and a series of encoded 1's and 0's representing open or closed respectively.<br /><br />Once chosen this will be remembered for all 'Collapsed Topics' based courses and you will not be asked again unless your administrator performs a reset.";
$string['setcookieconsent'] = 'Cookie consent';
$string['cookieconsentallowed'] ='Allowed';
$string['cookieconsentdenied'] ='Denied';

// Help
$string['setlayoutelements_help']='How much information about the toggles / sections you wish to be displayed.';
$string['setlayoutstructure_help']="The layout structure of the course.  You can choose between:

'Topics' - where each section is presented as a topic in section number order.

'Weeks' - where each section is presented as a week in ascending week order.

'Latest Week First' - which is the same as weeks but the current week is shown at the top and preceding weeks in decending order are displayed below execpt in editing mode where the structure is the same as 'Weeks'.

'Current Topic First' - which is the same as 'Topics' except that the current topic is shown at the top if it has been set.";
$string['setlayout_help'] = 'Contains the settings to do with the layout of the format within the course.';
$string['resetlayout_help'] = 'Resets the layout to the default values in "/course/format/moonstone/config.php" so it will be the same as a course the first time it is in the Collapsed Topics format';
// Moodle Tracker CONTRIB-3529
$string['setcolour_help'] = 'Contains the settings to do with the colour of the format within the course.';
$string['settoggleforegroundcolour_help'] = 'Sets the colour of the text on the toggle.';
$string['settogglebackgroundcolour_help'] = 'Sets the background of the toggle.';
$string['settogglebackgroundhovercolour_help'] = 'Sets the background of the toggle when the mouse moves over it.';
$string['resetcolour_help'] = 'Resets the colours to the default values in "/course/format/moonstone/config.php" so it will be the same as a course the first time it is in the Collapsed Topics format';
// Moodle Tracker CONTRIB-3624
$string['setcookieconsent_help'] = "If you choose 'Allowed' you agree that the next time you click on a toggle in any 'Collapsed Topics' based course then the 'mdl_cf_moonstone' cookie will be placed on your computer for the duration of the browser session or longer if you administrator has allowed - they can determine for how long.  It will remember the state of the toggles when you click on them.  If you choose 'Denied' the cookie will not be placed on your computer.  Once chosen this will be remembered for all 'Collapsed Topics' based courses and you will not be asked again unless your administrator performs a reset - please refer to 'Cookie Consent Information' in the 'Readme.txt' file of the format.";
?>