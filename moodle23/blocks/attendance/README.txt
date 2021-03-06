********************************************************************************************* 
****** WARNING: THIS MODULE AND BLOCK ARE IN DEVELOPMENT. USE WITH CAUTION ****** 
********************************************************************************************* 

--------------
RELEASE NOTES
--------------
1.0.1 - 09/16/2005 Lady800cc [moodle.org member]
- Fixed problems with permissions for teachers
- Changed date output to include day of week

1.0.2 - 9 nov 2005 Dmitry Pupinin [moodle.org member]
+ added Report tab
+ taking attendances, view reports by groups
* timestamp used in database (database will automaticaly update)
* source code generaly modified with moodle's standarts
* percents calculation modified (excused means present)

1.0.3 - 9 jan 2006 Dmitry Pupinin [moodle.org member]
+ grading attendances
+ grade weight can be different for each course (may be a negative)
+ grade added to GRADEBOOK (by means of special module "Attendance for Block")
+ different intervals for Report (weeks, months, all)
+ creating multiply sessions in any time
+ description for each session
+ support Postgres (not tested!)

1.0.4 - 17 jan 2006 Dmitry Pupinin [moodle.org member]
* bug fixed: if pages was open from outside moodle error displayed instead login page
* bug fixed: Postgres syntax error (thanks to Michael Deane)

1.0.5 - 23 jan 2006 Dmitry Pupinin [moodle.org member]
+ group names added to Excel and Text reports (if groups exist)
+ attendance displayed on user's quick and full report pages
+ students sort possibility in report
+ user's names is links now
* log messages updated
* bug fixed: guest can't see anything now
* bug fixed: isteacher() was called without parameters

1.0.6 - 2 feb 2006 Dmitry Pupinin [moodle.org member]
+ dates in head of report table is links to update attendance
+ percent calculating is updated (see Q&A in bottom of this page)
+ each acronym in table head on "Update attendance" page now works as "Select All" for radiobuttons in 
  his column (thanks to Michael Deane for idea)
+ date formats define in langs
* bug fixed: not all users were in Excel and Text exports

1.0.7 - 25 feb 2006 - Dmitry Pupinin [moodle.org member]
+ user's pictures added to Report and Update pages
+ icons (edit, delete) taking from themes
+ possibility to add sessions for every week or using period
* Postgres support improved
* supporting servers with php directive "short_open_tag=Off" (was used only <?php tag)
* bug fixed: Division by zero
* bug fixed: Notice displayed if selected group is empty
! not a bug: Adding multiple sessions cause warning and don't work (see description on http://moodle.org/mod/forum/discuss.php?d=38987#186029)

1.0.8 - 13 jul 2006 - Dmitry Pupinin [moodle.org member]
* bug fixed: acronyms and descriptions took from lang rather then course settings
* bug fixed: default acronyms displayed in report and exports
* bug fixed: warning on Admin page (v1.6) in Debug mode
* defaults of variables moved in context help
+ indexes added to database
+ student's view improved
+ user's name and surname in tables now is link to "student view" for instructor. User's picture is link to student details.
+ "version for printing" added to "student view"
+ utf migrating

1.0.9 - 20 feb 2007 - Dmitry Pupinin [moodle.org member]
+ attendances are showing from course's start date (use it when new semester starts)

1.1.0 - 10 may 2007 - Dmitry Pupinin [moodle.org member]
* bug fixed: remark for user can move to other user
* bug fixed: when group selected list of users not sorting
! IMPORTANT: This version is last for Moodle 1.6 (only critical bugfixes can be)

2.0   - 10 may 2007 - Dmitry Pupinin [moodle.org member]
* code rewrited. Now this is Module with block.
* this version only compatible with latest Moodle 1.8 (without grouping!)

2.0.1 - 02 jul 2007 - Dmitry Pupinin [moodle.org member]
* bug fixed: course id in block. Now using $COURSE

2.4.0 - 16 jul 2011 - Artem Andreev [moodle.org member]
* porting to Moodle 2.0

--------
ABOUT
--------
This is version 2.4.x of the attendance block. It is still IN DEVELOPMENT and should not be considered a stable release unless otherwise noted. It has been tested on Moodle 1.8+, MySQL and PHP 4.4+.

The attendance block was start development by the Human Logic Development Team, Dubai, UAE.
Visit them online at www.human-logic.com

Development of 1.0 branch was continue by Dmitry Pupinin (dlnsk at ngs dot ru). This branch isn't compatibly with version 1.2 from Human Logic!

Change the major number to 2.0 is way to show greater differences of new version from version 1.0.x and 1.2 from Human Logic! In this version main part of code moved to module attforblock! Now block is just additional feature for module.

More information about attforblock module read in corresponding directory.

This module and block may be distributed under the terms of the General Public License
(see http://www.gnu.org/licenses/gpl.txt for details)

-----------
PURPOSE
-----------
The attendance module and block are designed to allow instructors of a course keep an attendance log of the students in their courses. The instructor will setup the frequency of his classes (# of days per week & length of course) and the attendance block is ready for use. To take attendance, the instructor clicks on the "Update Attendance" button and is presented with a list of all the students in that course, along with 4 options: Present, Absent, Late & Excused, with a Remarks textbox. Instructors can download the attendance for their course in Excel format or text format.
Only the instructor can update the attendance data. However, a student gets to see his attendance record.

----------------
INSTALLATION
----------------
The attendance follows standard installation procedures. Place the "attendance" directory in your blocks directory, "attforblock" directory in your mod directory. Please delete old language files from your moodledata/lang/en directory if you are upgrading the module. Then visit the Admin page in Moodle to activate it.

--------------
QUESTIONS ?
--------------
If you have questions, concerns or comments about this branch of block, you can post to either the Moodle forums or contact me directly at dlnsk at ngs dot ru

Q: Why attendance percent on pages of block don't equal to percent in Gradebook?
A: Gradebook display how much points student had in this course in comparison with how much he could have. Pages of block display percent of attendance. For example, if student for presence had 3 points and for absence had 1 point then he can't have 0% in Gradebook even though he was absent always, but on block pages in this situation he will have exactly 0%.



Thanks!
