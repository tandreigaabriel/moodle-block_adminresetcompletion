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

namespace block_adminresetcompletion\local;

/**
 * Resets completion and supported activity data for one course user.
 *
 * @package    block_adminresetcompletion
 * @copyright  2026 Andrei Toma
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reset_service {
    /** @var \completion_info */
    private \completion_info $completion;

    /** @var \context_course */
    private \context_course $context;

    /**
     * Creates the service.
     *
     * @param \stdClass $course
     */
    public function __construct(/** @var \stdClass Course to reset. */ private \stdClass $course) {
        $this->completion = new \completion_info($course);
        $this->context = \context_course::instance($course->id);
    }

    /**
     * Reports whether course completion is enabled.
     *
     * @return bool
     */
    public function is_completion_enabled(): bool {
        return $this->completion->is_enabled();
    }

    /**
     * Returns enrolled users whose role is tracked for completion.
     *
     * @return array user id => display name
     */
    public function get_resettable_users(): array {
        global $DB;

        [$enrolledsql, $params] = get_enrolled_sql(
            $this->context,
            'moodle/course:isincompletionreports',
            0,
            false
        );

        $sql = "SELECT u.id, u.firstname, u.lastname, u.email
                  FROM {user} u
                  JOIN ($enrolledsql) eu ON eu.id = u.id
              ORDER BY u.lastname, u.firstname, u.id";
        $users = $DB->get_records_sql($sql, $params);

        $options = [];
        foreach ($users as $user) {
            $options[$user->id] = fullname($user) . ' (' . $user->email . ')';
        }
        return $options;
    }

    /**
     * Loads and validates the selected user.
     *
     * @param int $userid
     * @return \stdClass
     */
    public function require_resettable_user(int $userid): \stdClass {
        $user = \core_user::get_user($userid, '*', MUST_EXIST);
        if (!is_enrolled($this->context, $user, 'moodle/course:isincompletionreports', false)) {
            throw new \moodle_exception('usernotenrolled', 'block_adminresetcompletion');
        }
        return $user;
    }

    /**
     * Resets the selected user's data.
     *
     * @param \stdClass $user
     */
    public function reset(\stdClass $user): void {
        global $DB;

        $transaction = $DB->start_delegated_transaction();
        $params = ['courseid' => $this->course->id, 'userid' => $user->id];

        $DB->delete_records_select(
            'course_modules_completion',
            'coursemoduleid IN (SELECT id FROM {course_modules} WHERE course = :courseid) AND userid = :userid',
            $params
        );
        $DB->delete_records('course_completion_crit_compl', [
            'course' => $this->course->id,
            'userid' => $user->id,
        ]);
        $DB->delete_records('course_completions', ['course' => $this->course->id, 'userid' => $user->id]);

        $this->delete_choice_data($user->id);
        $this->delete_scorm_data($user->id);
        $this->delete_quiz_data($user->id);

        $transaction->allow_commit();
        \cache::make('core', 'completion')->purge();

        $event = \block_adminresetcompletion\event\completion_reset::create([
            'context' => $this->context,
            'relateduserid' => $user->id,
        ]);
        $event->trigger();
    }

    /**
     * Deletes Choice answers.
     *
     * @param int $userid
     */
    private function delete_choice_data(int $userid): void {
        global $DB;

        $dbman = $DB->get_manager();
        if (
            !$dbman->table_exists(new \xmldb_table('choice')) ||
                !$dbman->table_exists(new \xmldb_table('choice_answers'))
        ) {
            return;
        }

        $DB->delete_records_select(
            'choice_answers',
            'choiceid IN (SELECT id FROM {choice} WHERE course = :courseid) AND userid = :userid',
            ['courseid' => $this->course->id, 'userid' => $userid]
        );
    }

    /**
     * Deletes SCORM tracking.
     *
     * @param int $userid
     */
    private function delete_scorm_data(int $userid): void {
        global $DB;

        $dbman = $DB->get_manager();
        if (
            !$dbman->table_exists(new \xmldb_table('scorm')) ||
                !$dbman->table_exists(new \xmldb_table('scorm_scoes_track'))
        ) {
            return;
        }

        $DB->delete_records_select(
            'scorm_scoes_track',
            'scormid IN (SELECT id FROM {scorm} WHERE course = :courseid) AND userid = :userid',
            ['courseid' => $this->course->id, 'userid' => $userid]
        );
    }

    /**
     * Deletes quiz attempts using the quiz API so grades are recalculated.
     *
     * @param int $userid
     */
    private function delete_quiz_data(int $userid): void {
        global $CFG, $DB;

        $dbman = $DB->get_manager();
        $requiredtables = ['quiz', 'quiz_attempts', 'quiz_grades'];
        foreach ($requiredtables as $tablename) {
            if (!$dbman->table_exists(new \xmldb_table($tablename))) {
                return;
            }
        }

        $quizlib = $CFG->dirroot . '/mod/quiz/locallib.php';
        if (!is_readable($quizlib)) {
            return;
        }
        require_once($quizlib);

        $quizzes = $DB->get_records('quiz', ['course' => $this->course->id]);
        foreach ($quizzes as $quiz) {
            $attempts = $DB->get_records('quiz_attempts', ['quiz' => $quiz->id, 'userid' => $userid]);
            foreach ($attempts as $attempt) {
                quiz_delete_attempt($attempt, $quiz);
            }
        }
    }
}
