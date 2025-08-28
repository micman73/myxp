other['completionstate'] == COMPLETION_COMPLETE) {
            
            // Check if user already got XP for this specific module completion
            if (!self::check_unique_xp_award($event->userid, $event->courseid, 'module_completion', $event->contextinstanceid)) {
                self::award_xp(
                    $event->userid,
                    $event->courseid,
                    'Activity Completed',
                    5, // Reduced from 50 to 5 (÷10)
                    $event->contextinstanceid,
                    'module_completion'
                );
            }
        }
    }
    
    /**
     * Award XP for quiz submission (ONCE per quiz per user, ONLY if passing grade achieved)
     */
    public static function quiz_submitted(\mod_quiz\event\attempt_submitted $event) {
        global $DB;
        
        // Get quiz attempt details
        $attempt = $DB->get_record('quiz_attempts', ['id' => $event->objectid]);
        if (!$attempt) {
            return;
        }
        
        // Get quiz details including passing grade
        $quiz = $DB->get_record('quiz', ['id' => $attempt->quiz]);
        if (!$quiz || $quiz->sumgrades <= 0) {
            return;
        }
        
        // Calculate percentage score
        $percentage = ($attempt->sumgrades / $quiz->sumgrades) * 100;
        
        // Get the passing grade (grade to pass) - default 80% if not set
        $passing_grade = 80; // Default passing grade
        if ($quiz->grade > 0) {
            // Check if there's a specific grade_to_pass set in gradebook
            $grade_item = $DB->get_record('grade_items', [
                'courseid' => $event->courseid,
                'itemtype' => 'mod',
                'itemmodule' => 'quiz',
                'iteminstance' => $quiz->id
            ]);
            
            if ($grade_item && $grade_item->gradepass > 0) {
                $passing_grade = ($grade_item->gradepass / $grade_item->grademax) * 100;
            }
        }
        
        // ONLY AWARD XP IF QUIZ IS ACTUALLY PASSED
        if ($percentage < $passing_grade) {
            error_log("XP DENIED: User {$event->userid} scored {$percentage}% but needs {$passing_grade}% to pass quiz {$quiz->id}");
            return; // No XP for failed attempts
        }
        
        // Check if user already got XP for THIS SPECIFIC QUIZ (not attempt)
        if (!self::check_unique_xp_award($event->userid, $event->courseid, 'quiz_completion', $attempt->quiz)) {
            
            // SMART XP CALCULATION based on performance tiers
            $xp_amount = self::calculate_quiz_xp($percentage);
            
            // Determine performance level for activity description
            $performance_level = self::get_performance_level($percentage);
            
            self::award_xp(
                $event->userid,
                $event->courseid,
                "Quiz Completed - {$performance_level}",
                $xp_amount,
                $attempt->quiz, // Use quiz ID, not attempt ID
                'quiz_completion'
            );
        }
    }
    
    /**
     * NEW: Calculate XP for quiz based on performance tiers
     */
    private static function calculate_quiz_xp($percentage) {
        if ($percentage >= 95) {
            return 10; // Excellence (was 100, now /10)
        } elseif ($percentage >= 90) {
            return 8;  // Great (was 80, now /10)
        } elseif ($percentage >= 85) {
            return 6;  // Good (was 60, now /10)
        } elseif ($percentage >= 80) {
            return 4;  // Passing (was 40, now /10)
        }
        
        return 0; // Below passing grade gets no XP
    }
    
    /**
     * NEW: Get performance level description
     */
    private static function get_performance_level($percentage) {
        if ($percentage >= 95) {
            return '🏆 Excellence';
        } elseif ($percentage >= 90) {
            return '⭐ Great';
        } elseif ($percentage >= 85) {
            return '✅ Good';
        } else {
            return '📈 Passing';
        }
    }
    
    /**
     * Award XP for assignment submission (ONCE per assignment per user)
     */
    public static function assignment_submitted(\mod_assign\event\assessable_submitted $event) {
        global $DB;
        
        // Get assignment ID from the submission
        $submission = $DB->get_record('assign_submission', ['id' => $event->objectid]);
        if (!$submission) {
            return;
        }
        
        // Check if user already got XP for THIS SPECIFIC ASSIGNMENT
        if (!self::check_unique_xp_award($event->userid, $event->courseid, 'assignment_completion', $submission->assignment)) {
            self::award_xp(
                $event->userid,
                $event->courseid,
                'Assignment Submitted',
                4, // Reduced from 40 to 4 (÷10)
                $submission->assignment, // Use assignment ID, not submission ID
                'assignment_completion'
            );
        }
    }
    
    /**
     * Award XP for forum participation (LIMITED per day)
     */
    public static function forum_post_created(\mod_forum\event\post_created $event) {
        global $DB;
        
        // Get forum ID from the post
        $discussion = $DB->get_record('forum_discussions', ['id' => $event->other['discussionid']]);
        if (!$discussion) {
            return;
        }
        
        // Only award 3 times per day per forum (existing logic)
        if (!self::check_daily_limit($event->userid, $event->courseid, 'forum_post', 3, $discussion->forum)) {
            self::award_xp(
                $event->userid,
                $event->courseid,
                'Forum Post',
                2, // Reduced from 15 to 2 (≈÷10, rounded up)
                $discussion->forum,
                'forum_post'
            );
        }
    }
    
    /**
     * Award XP for course access (LIMITED per day)
     */
    public static function course_viewed(\core\event\course_viewed $event) {
        // Only award once per day per course (existing logic)
        if (!self::check_daily_limit($event->userid, $event->courseid, 'course_visit', 1, $event->courseid)) {
            self::award_xp(
                $event->userid,
                $event->courseid,
                'Course Visit',
                1, // Reduced from 5 to 1 (÷5, minimal reward)
                $event->courseid,
                'course_visit'
            );
        }
    }
    
    /**
     * NEW: Check if user already received XP for specific unique action
     * Prevents duplicate XP for same quiz, assignment, activity completion
     */
    private static function check_unique_xp_award($userid, $courseid, $objecttype, $objectid) {
        global $DB;
        
        $exists = $DB->get_record('block_myxp_log', [
            'userid' => $userid,
            'courseid' => $courseid,
            'objecttype' => $objecttype,
            'objectid' => $objectid
        ]);
        
        return ($exists !== false); // Returns true if already awarded (so skip)
    }
    
    /**
     * IMPROVED: Check daily limits (now supports specific object tracking)
     */
    private static function check_daily_limit($userid, $courseid, $activity_type, $limit, $objectid = null) {
        global $DB;
        
        $today_start = strtotime('today');
        $today_end = $today_start + 86400;
        
        $sql = "SELECT COUNT(*) 
                FROM {block_myxp_log} 
                WHERE userid = ? AND courseid = ? AND objecttype = ? 
                AND timecreated >= ? AND timecreated < ?";
        
        $params = [$userid, $courseid, $activity_type, $today_start, $today_end];
        
        // If specific object is provided, add it to the query
        if ($objectid !== null) {
            $sql .= " AND objectid = ?";
            $params[] = $objectid;
        }
        
        $count = $DB->count_records_sql($sql, $params);
        
        return $count >= $limit;
    }
    
    /**
     * Core XP awarding function (unchanged)
     */
    private static function award_xp($userid, $courseid, $activity, $xp_amount, $objectid = null, $objecttype = null) {
        global $DB;
        
        // Check if user is enrolled in course
        if (!is_enrolled(context_course::instance($courseid), $userid)) {
            return;
        }
        
        // Get or create user XP record
        $user_xp = $DB->get_record('block_myxp_user', [
            'userid' => $userid,
            'courseid' => $courseid
        ]);
        
        if (!$user_xp) {
            $user_xp = new stdClass();
            $user_xp->userid = $userid;
            $user_xp->courseid = $courseid;
            $user_xp->total_xp = 0;
            $user_xp->level = 1;
            $user_xp->timecreated = time();
            $user_xp->timemodified = time();
            $user_xp->id = $DB->insert_record('block_myxp_user', $user_xp);
        }
        
        // Update XP
        $old_level = $user_xp->level;
        $user_xp->total_xp += $xp_amount;
        $user_xp->level = self::calculate_level($user_xp->total_xp);
        $user_xp->timemodified = time();
        $DB->update_record('block_myxp_user', $user_xp);
        
        // Log the activity
        $log = new stdClass();
        $log->userid = $userid;
        $log->courseid = $courseid;
        $log->activity = $activity;
        $log->xp_gained = $xp_amount;
        $log->objectid = $objectid;
        $log->objecttype = $objecttype;
        $log->timecreated = time();
        $DB->insert_record('block_myxp_log', $log);
        
        // Send notification if user leveled up
        if ($user_xp->level > $old_level) {
            self::send_level_up_notification($userid, $user_xp->level, $courseid);
        }
        
        // ENHANCED DEBUG LOG (remove in production)
        error_log("🎯 XP AWARDED: User {$userid} got {$xp_amount} XP for '{$activity}' (object: {$objectid}, type: {$objecttype}) in course {$courseid}. Total XP now: {$user_xp->total_xp}");
    }
    
    /**
     * Calculate level based on XP (ADJUSTED for new lower XP amounts)
     */
    private static function calculate_level($total_xp) {
        $level = 1;
        $xp_for_next = 10; // Reduced from 100 to 10 (÷10)
        
        while ($total_xp >= $xp_for_next) {
            $level++;
            $xp_for_next += (5 + ($level * 3)); // Reduced progression (was 50 + level*25)
        }
        
        return $level;
    }
    
    /**
     * Send level up notification (unchanged)
     */
    private static function send_level_up_notification($userid, $new_level, $courseid) {
        global $DB;
        
        $user = $DB->get_record('user', ['id' => $userid]);
        $course = $DB->get_record('course', ['id' => $courseid]);
        
        if ($user && $course) {
            error_log("🎉 LEVEL UP: User {$user->firstname} {$user->lastname} reached level {$new_level} in course {$course->shortname}");
        }
    }
    /**
 * Award XP when a quiz is graded (manual or regrading).
 */
    public static function quiz_graded(\mod_quiz\event\attempt_graded $event) {
                global $DB;
                $data = $event->get_data();
                $userid   = (int)$data['relateduserid'] ?: (int)$data['userid'];
                $courseid = (int)$data['courseid'];
                $attemptid = (int)$data['objectid'];
            
                if (!$userid || !$courseid || !$attemptid) {
                    return;
                }
            
                // Ένα μικρό bonus για graded, όχι μόνο για passed.
                if (!self::check_unique_xp_award($userid, $courseid, 'quiz_graded', $attemptid)) {
                    self::award_xp(
                        $userid,
                        $courseid,
                        'Quiz Graded',
                        3,
                        $attemptid,
                        'quiz_graded'
                    );
                }
}
                
}
