# Change log

## 1.0.4 - 2026-11-22

- First Moodle Marketplace release; supports Moodle 4.5 and 5.0.
- Coding standard (moodle-extra), PHPDoc and Moodle Plugin CI checks pass; GitHub Actions CI and Marketplace release workflow added.
- No functional change.

## 1.0.3 - 2026-07-30

- Show all enrolled completion-tracked students, including suspended and expired enrolments.
- Allow administrators to reset incomplete or partially reset completion data.

## 1.0.2 - 2026-07-30

- Skip Choice, SCORM and Quiz cleanup when an optional activity plugin or its tables are not installed.

## 1.0.1 - 2026-07-30

- Removed N+1 completion and enrolment checks from the student selector.
- Load actively enrolled, tracked and completed students with one database query.
- Documented MySQL 8.0 and Aurora MySQL 3 compatibility.

## 1.0.0 - 2026-07-30

- Introduced the Admin completion reset block.
- Set Moodle 4.5 as the minimum supported release.
- Restricted reset access to a dedicated capability with no default role assignment.
- Added an administrator workflow for selecting a completed student.
- Added login, course-context, capability, enrolment, CSRF and confirmation checks.
- Replaced hard-coded table prefixes and unsafe SQL.
- Added transactional resets and Moodle-supported quiz attempt deletion.
- Declared Privacy API compliance.
