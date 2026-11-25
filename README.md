# Admin completion reset

This block allows an administrator to reset a student's course completion for recertification.

Maintained by Andrei Toma — TAG Digital Solutions Ltd (trading as TAG Web Design & Development), https://tandreig.com. © 2026 TAG Digital Solutions Ltd.

The reset removes course and activity completion records, Choice answers, SCORM tracking,
quiz attempts and quiz grades for the selected student in the selected course. The student
may be complete, incomplete, suspended or outside their enrolment dates. Students
cannot reset their own completion.

#### Requirements
Moodle 4.5 or later.

Supported database targets:

- MySQL 8.0 or later.
- Amazon Aurora MySQL version 3 or later, configured as MySQL 8.0 compatible.

The plugin uses Moodle's Database API, parameter placeholders and delegated transactions.
It does not use vendor-specific SQL, hard-coded table prefixes, CTEs, window functions or
MySQL-only collation behaviour.

#### Permissions
The `block/adminresetcompletion:reset` capability is not assigned to any role by default.
Site administrators receive it through Moodle's standard administrator capability override.
It may be assigned explicitly to a trusted custom role if required.

#### Installation
1. Copy the plugin into `blocks/adminresetcompletion`.
2. Log into your Moodle administrator account, go to Site administration -> Notifications,
   and follow the instructions to install or upgrade the plugin.
