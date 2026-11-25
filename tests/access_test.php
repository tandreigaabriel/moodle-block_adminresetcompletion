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

namespace block_adminresetcompletion;

/**
 * Capability tests for Admin completion reset.
 *
 * @package    block_adminresetcompletion
 * @copyright  2026 TAG Digital Solutions Ltd <https://tandreig.com>
 * @author     Andrei Toma
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversNothing
 */
final class access_test extends \advanced_testcase {
    /**
     * Students cannot reset completion, while site administrators can.
     */
    public function test_reset_capability_is_admin_only_by_default(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
        $context = \context_course::instance($course->id);

        $this->setUser($student);
        $this->assertFalse(has_capability('block/adminresetcompletion:reset', $context));

        $this->setAdminUser();
        $this->assertTrue(has_capability('block/adminresetcompletion:reset', $context));
    }
}
