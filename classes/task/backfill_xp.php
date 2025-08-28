<?php
namespace block_myxp\task;
defined('MOODLE_INTERNAL') || die();

class backfill_xp extends \core\task\scheduled_task {
    public function get_name(): string {
        return get_string('task_backfill', 'block_myxp');
    }

    public function execute() {
        global $DB;
        // Activity completions
        $sql = "SELECT cmc.userid, cm.course AS courseid, cmc.coursemoduleid AS cmid
                  FROM {course_modules_completion} cmc
                  JOIN {course_modules} cm ON cm.id = cmc.coursemoduleid
                 WHERE cmc.completionstate = 1";
        $rs = $DB->get_recordset_sql($sql);
        foreach ($rs as $r) {
            \block_myxp\observer::activity_completed($r);
        }
        $rs->close();

        // Quiz submissions
        $sql = "SELECT qa.userid, q.course AS courseid, qa.id AS attemptid
                  FROM {quiz_attempts} qa
                  JOIN {quiz} q ON q.id = qa.quiz
                 WHERE qa.state = 'finished'";
        $rs = $DB->get_recordset_sql($sql);
        foreach ($rs as $r) {
            $fakeevent = new \stdClass();
            $fakeevent->userid = $r->userid;
            $fakeevent->courseid = $r->courseid;
            $fakeevent->objectid = $r->attemptid;
            \block_myxp\observer::quiz_submitted($fakeevent);
        }
        $rs->close();

        return true;
    }
}
