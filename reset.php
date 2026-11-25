<?php
// This file is part of Moodle - http://moodle.org/.
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
 * Administrator completion-reset workflow.
 *
 * @package    block_adminresetcompletion
 * @copyright  2026 TAG Digital Solutions Ltd <https://tandreig.com>
 * @author     Andrei Toma
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

$courseid = required_param('course', PARAM_INT);
$userid = optional_param('user', 0, PARAM_INT);
$confirm = optional_param('confirm', false, PARAM_BOOL);

$course = get_course($courseid);
require_login($course);

$context = context_course::instance($courseid);
require_capability('block/adminresetcompletion:reset', $context);

$baseurl = new moodle_url('/blocks/adminresetcompletion/reset.php', ['course' => $courseid]);
$PAGE->set_url($baseurl);
$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_title(get_string('resetconfirm', 'block_adminresetcompletion'));
$PAGE->set_heading($course->fullname);

$service = new \block_adminresetcompletion\local\reset_service($course);
if (!$service->is_completion_enabled()) {
    throw new moodle_exception('completionnotenabled', 'completion');
}

if (!$userid) {
    $form = new \block_adminresetcompletion\form\select_user_form($baseurl, [
        'users' => $service->get_resettable_users(),
        'courseid' => $courseid,
    ]);

    if ($data = $form->get_data()) {
        redirect(new moodle_url($baseurl, ['user' => $data->user]));
    }

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('selectstudent', 'block_adminresetcompletion'));
    $form->display();
    echo $OUTPUT->footer();
    exit;
}

$user = $service->require_resettable_user($userid);

if ($confirm && data_submitted()) {
    require_sesskey();
    $service->reset($user);

    redirect(
        new moodle_url('/course/view.php', ['id' => $courseid]),
        get_string('resetsuccess', 'block_adminresetcompletion', fullname($user)),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

$confirmurl = new moodle_url($baseurl, ['user' => $userid, 'confirm' => 1]);
echo $OUTPUT->header();
echo $OUTPUT->confirm(
    get_string('resetdescriptionuser', 'block_adminresetcompletion', fullname($user)),
    new single_button($confirmurl, get_string('resetcompletionaction', 'block_adminresetcompletion'), 'post'),
    new single_button($baseurl, get_string('cancel'), 'get')
);
echo $OUTPUT->footer();
