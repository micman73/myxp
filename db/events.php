<?php
defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname'   => '\core\event\course_module_completion_updated',
        'callback'    => '\block_myxp\observer::activity_completed',
        'includefile' => '/blocks/myxp/classes/observer.php',
    ],
    [
        'eventname'   => '\mod_quiz\event\attempt_submitted',
        'callback'    => '\block_myxp\observer::quiz_submitted',
        'includefile' => '/blocks/myxp/classes/observer.php',
    ],
    [
        'eventname'   => '\mod_quiz\event\attempt_graded',
        'callback'    => '\block_myxp\observer::quiz_graded',
        'includefile' => '/blocks/myxp/classes/observer.php',
    ],
    [
        'eventname'   => '\mod_assign\event\assessable_submitted',
        'callback'    => '\block_myxp\observer::assignment_submitted',
        'includefile' => '/blocks/myxp/classes/observer.php',
    ],
    [
        'eventname'   => '\mod_forum\event\post_created',
        'callback'    => '\block_myxp\observer::forum_post_created',
        'includefile' => '/blocks/myxp/classes/observer.php',
    ],
    [
        'eventname'   => '\core\event\course_viewed',
        'callback'    => '\block_myxp\observer::course_viewed',
        'includefile' => '/blocks/myxp/classes/observer.php',
    ],
];
