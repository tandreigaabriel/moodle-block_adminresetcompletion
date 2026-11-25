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

namespace block_adminresetcompletion\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Form for selecting a completed user.
 *
 * @package    block_adminresetcompletion
 * @copyright  2026 TAG Digital Solutions Ltd <https://tandreig.com>
 * @author     Andrei Toma
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class select_user_form extends \moodleform {
    /**
     * Defines the form.
     */
    protected function definition(): void {
        $mform = $this->_form;
        $users = $this->_customdata['users'];

        if (!$users) {
            $mform->addElement('static', 'nousers', '', get_string('nocompletedstudents', 'block_adminresetcompletion'));
            return;
        }

        $mform->addElement('autocomplete', 'user', get_string('student', 'block_adminresetcompletion'), $users);
        $mform->addRule('user', get_string('required'), 'required', null, 'client');
        $mform->setType('user', PARAM_INT);
        $mform->addElement('hidden', 'course', $this->_customdata['courseid']);
        $mform->setType('course', PARAM_INT);
        $this->add_action_buttons(false, get_string('continue'));
    }
}
