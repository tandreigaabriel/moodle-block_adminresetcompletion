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
 * Admin completion reset block.
 *
 * @package    block_adminresetcompletion
 * @copyright  2026 Andrei Toma
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Provides administrators with a link to the completion reset workflow.
 */
class block_adminresetcompletion extends block_base {
    /**
     * Sets the block title.
     */
    public function init(): void {
        $this->title = get_string('pluginname', 'block_adminresetcompletion');
    }

    /**
     * Builds block content.
     *
     * @return stdClass
     */
    public function get_content(): stdClass {
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = (object) ['text' => '', 'footer' => ''];
        $course = $this->page->course;
        if (!$course || $course->id == SITEID) {
            return $this->content;
        }

        $context = context_course::instance($course->id);
        if (!has_capability('block/adminresetcompletion:reset', $context)) {
            return $this->content;
        }

        $completion = new completion_info($course);
        if (!$completion->is_enabled()) {
            $this->content->text = get_string('completiondisabled', 'block_adminresetcompletion');
            return $this->content;
        }

        $url = new moodle_url('/blocks/adminresetcompletion/reset.php', ['course' => $course->id]);
        $this->content->text = get_string('adminintro', 'block_adminresetcompletion');
        $this->content->footer = html_writer::link($url, get_string('selectstudent', 'block_adminresetcompletion'));

        return $this->content;
    }

    /**
     * Restricts the block to course pages.
     *
     * @return array
     */
    public function applicable_formats(): array {
        return ['all' => false, 'course-view' => true];
    }
}
