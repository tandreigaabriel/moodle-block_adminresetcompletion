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

namespace block_adminresetcompletion\event;

/**
 * Event fired after an administrator resets a student's completion.
 *
 * @package    block_adminresetcompletion
 * @copyright  2026 TAG Digital Solutions Ltd <https://tandreig.com>
 * @author     Andrei Toma
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class completion_reset extends \core\event\base {
    /**
     * Initialises event data.
     */
    protected function init(): void {
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    /**
     * Returns the event name.
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('eventcompletionreset', 'block_adminresetcompletion');
    }

    /**
     * Returns a human-readable event description.
     *
     * @return string
     */
    public function get_description(): string {
        return "The user with id '{$this->userid}' reset completion for the user with id " .
            "'{$this->relateduserid}' in the course with id '{$this->courseid}'.";
    }

    /**
     * Returns the course URL.
     *
     * @return \moodle_url
     */
    public function get_url(): \moodle_url {
        return new \moodle_url('/course/view.php', ['id' => $this->courseid]);
    }
}
