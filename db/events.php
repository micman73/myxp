
 '\core\event\course_module_completion_updated',
        'callback' => 'block_myxp_observer::course_module_completed',
        'includefile' => '/blocks/myxp/classes/observer.php',
    ),
    
    // Quiz attempt submitted
    array(
        'eventname' => '\mod_quiz\event\attempt_submitted',
        'callback' => 'block_myxp_observer::quiz_submitted',
        'includefile' => '/blocks/myxp/classes/observer.php',
    ),
    
    // Assignment submitted
    array(
        'eventname' => '\mod_assign\event\assessable_submitted',
        'callback' => 'block_myxp_observer::assignment_submitted',
        'includefile' => '/blocks/myxp/classes/observer.php',
    ),
    
    // Forum post created
    array(
        'eventname' => '\mod_forum\event\post_created',
        'callback' => 'block_myxp_observer::forum_post_created',
        'includefile' => '/blocks/myxp/classes/observer.php',
    ),
    
    // Course viewed
    array(
        'eventname' => '\core\event\course_viewed',
        'callback' => 'block_myxp_observer::course_viewed',
        'includefile' => '/blocks/myxp/classes/observer.php',
    ),
);
