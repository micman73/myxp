<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyXP Plugin - Complete Package</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f8f9fa;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 30px;
        }
        .file-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .file-header {
            background: #28a745;
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            font-weight: bold;
            margin-bottom: 15px;
            display: flex;
            justify-content: between;
            align-items: center;
        }
        .file-path {
            font-family: monospace;
            background: #f1f1f1;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
        }
        pre {
            background: #2d3748;
            color: #e2e8f0;
            padding: 20px;
            border-radius: 8px;
            overflow-x: auto;
            font-size: 12px;
            line-height: 1.4;
        }
        .install-guide {
            background: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        .xp-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .xp-table th, .xp-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .xp-table th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .download-btn {
            background: #28a745;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 10px 0;
        }
        .download-btn:hover {
            background: #218838;
        }
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .feature {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎮 MyXP Plugin - Complete Package</h1>
        <p>Smart Gamification System for Moodle with ROBUX-friendly XP Rewards</p>
        <p><strong>Version 1.0.3</strong> | Ready for Production | No Duplicates | Smart Quiz Grading</p>
    </div>

    <div class="install-guide">
        <h2>📦 Installation Instructions</h2>
        <ol>
            <li><strong>Download all files below</strong> and create the following directory structure:</li>
            <li><strong>Create folder:</strong> <code>myxp/</code></li>
            <li><strong>Upload to:</strong> <code>[moodle]/blocks/myxp/</code></li>
            <li><strong>Go to:</strong> Site Administration → Notifications</li>
            <li><strong>Install/Upgrade</strong> the plugin when prompted</li>
            <li><strong>Add block</strong> to any course to test</li>
        </ol>
    </div>

    <div class="features">
        <div class="feature">
            <h3>🎯 Smart Features</h3>
            <ul>
                <li>✅ No duplicate XP awards</li>
                <li>🏆 Performance-based quiz XP</li>
                <li>📊 Real-time progress tracking</li>
                <li>🎮 Beautiful modern UI</li>
                <li>📈 Course leaderboard</li>
                <li>💎 Level progression system</li>
            </ul>
        </div>
        <div class="feature">
            <h3>💰 ROBUX-Friendly XP Rates</h3>
            <table class="xp-table">
                <tr><th>Activity</th><th>XP</th></tr>
                <tr><td>🏆 Quiz Excellence (95%+)</td><td>10 XP</td></tr>
                <tr><td>⭐ Quiz Great (90%+)</td><td>8 XP</td></tr>
                <tr><td>✅ Quiz Good (85%+)</td><td>6 XP</td></tr>
                <tr><td>📈 Quiz Passing (80%+)</td><td>4 XP</td></tr>
                <tr><td>📚 Activity Completed</td><td>5 XP</td></tr>
                <tr><td>📝 Assignment Submit</td><td>4 XP</td></tr>
                <tr><td>💬 Forum Post (max 3/day)</td><td>2 XP</td></tr>
                <tr><td>🎯 Course Visit (1/day)</td><td>1 XP</td></tr>
            </table>
        </div>
    </div>

    <!-- File 1: Main Block File -->
    <div class="file-section">
        <div class="file-header">
            📁 Main Block File
            <span class="file-path">blocks/myxp/block_myxp.php</span>
        </div>
        <pre><?php
defined('MOODLE_INTERNAL') || die();

class block_myxp extends block_base {
    
    public function init() {
        $this->title = '🎮 My XP System';
    }
    
    public function get_content() {
        global $DB, $USER, $COURSE, $OUTPUT;
        
        if ($this->content !== null) {
            return $this->content;
        }
        
        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';
        
        if (!isloggedin() || isguestuser()) {
            $this->content->text = '<div style="text-align: center; padding: 20px; color: #666;">🎯 Please log in to see your XP progress.</div>';
            return $this->content;
        }
        
        // Get user's XP data
        $userxp = $this->get_user_xp($USER->id, $COURSE->id);
        $level_info = $this->calculate_level_info($userxp->total_xp);
        
        // Generate content
        $this->content->text = $this->generate_block_content($userxp, $level_info);
        
        return $this->content;
    }
    
    private function get_user_xp($userid, $courseid) {
        global $DB;
        
        $record = $DB->get_record('block_myxp_user', [
            'userid' => $userid,
            'courseid' => $courseid
        ]);
        
        if (!$record) {
            // Create new record
            $record = new stdClass();
            $record->userid = $userid;
            $record->courseid = $courseid;
            $record->total_xp = 0;
            $record->level = 1;
            $record->timecreated = time();
            $record->timemodified = time();
            
            $record->id = $DB->insert_record('block_myxp_user', $record);
        }
        
        return $record;
    }
    
    private function calculate_level_info($total_xp) {
        $level = 1;
        $xp_for_current = 0;
        $xp_for_next = 10; // Updated to match observer calculation
        
        while ($total_xp >= $xp_for_next) {
            $level++;
            $xp_for_current = $xp_for_next;
            $xp_for_next += (5 + ($level * 3)); // Updated to match observer
        }
        
        $progress = 0;
        if ($xp_for_next > $xp_for_current) {
            $progress = (($total_xp - $xp_for_current) / ($xp_for_next - $xp_for_current)) * 100;
        }
        
        return [
            'level' => $level,
            'current_xp' => $total_xp,
            'xp_for_next' => $xp_for_next,
            'xp_for_current' => $xp_for_current,
            'progress' => round($progress, 1),
            'xp_needed' => $xp_for_next - $total_xp
        ];
    }
    
    private function generate_block_content($userxp, $level_info) {
        global $DB, $COURSE, $USER;
        
        // Modern card-style design
        $html = '<div style="
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 20px;
            color: white;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            text-align: center;
            font-family: -apple-system, BlinkMacSystemFont, sans-serif;
        ">';
        
        // Level badge with better styling
        $level_emoji = $this->get_level_emoji($level_info['level']);
        $html .= '<div style="
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid rgba(255,255,255,0.1);
        ">';
        $html .= '<div style="font-size: 28px; margin-bottom: 5px;">' . $level_emoji . '</div>';
        $html .= '<div style="font-size: 18px; font-weight: bold;">Level ' . $level_info['level'] . '</div>';
        $html .= '</div>';
        
        // XP info with better formatting
        $html .= '<div style="margin: 15px 0; display: flex; justify-content: space-between; align-items: center;">';
        $html .= '<div style="text-align: left;">';
        $html .= '<div style="font-size: 14px; opacity: 0.9;">Total XP</div>';
        $html .= '<div style="font-size: 20px; font-weight: bold;">' . number_format($level_info['current_xp']) . '</div>';
        $html .= '</div>';
        $html .= '<div style="text-align: right;">';
        $html .= '<div style="font-size: 14px; opacity: 0.9;">Next Level</div>';
        $html .= '<div style="font-size: 16px; font-weight: bold;">' . $level_info['xp_needed'] . ' XP</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        // Enhanced progress bar
        $html .= '<div style="margin: 20px 0;">';
        $html .= '<div style="
            background: rgba(255,255,255,0.2);
            height: 25px;
            border-radius: 15px;
            overflow: hidden;
            position: relative;
            border: 1px solid rgba(255,255,255,0.1);
        ">';
        $html .= '<div style="
            background: linear-gradient(90deg, #00f5ff, #00d4aa);
            height: 100%;
            width: ' . $level_info['progress'] . '%;
            border-radius: 15px;
            transition: width 0.5s ease;
            box-shadow: 0 0 20px rgba(0,245,255,0.3);
        "></div>';
        $html .= '<div style="
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 12px;
            font-weight: bold;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        ">' . $level_info['progress'] . '%</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        // XP Earning Guide (shows new amounts)
        $html .= '<div style="
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 12px;
            margin: 15px 0;
            text-align: left;
            font-size: 11px;
        ">';
        $html .= '<div style="font-weight: bold; margin-bottom: 8px; text-align: center;">💰 Earn XP By:</div>';
        $html .= '<div style="display: grid; grid-template-columns: 1fr auto; gap: 5px; align-items: center;">';
        $html .= '<span>🏆 Quiz Excellence (95%+)</span><span style="font-weight: bold;">10 XP</span>';
        $html .= '<span>⭐ Quiz Great (90%+)</span><span style="font-weight: bold;">8 XP</span>';
        $html .= '<span>✅ Quiz Good (85%+)</span><span style="font-weight: bold;">6 XP</span>';
        $html .= '<span>📈 Quiz Passing (80%+)</span><span style="font-weight: bold;">4 XP</span>';
        $html .= '<span>📚 Activity Completed</span><span style="font-weight: bold;">5 XP</span>';
        $html .= '<span>📝 Assignment Submit</span><span style="font-weight: bold;">4 XP</span>';
        $html .= '<span>💬 Forum Post (3/day)</span><span style="font-weight: bold;">2 XP</span>';
        $html .= '<span>🎯 Course Visit (1/day)</span><span style="font-weight: bold;">1 XP</span>';
        $html .= '</div>';
        $html .= '</div>';
        
        // Recent activities (top 3)
        $recent = $this->get_recent_activities($USER->id, $COURSE->id, 3);
        if (!empty($recent)) {
            $html .= '<div style="
                background: rgba(255,255,255,0.1);
                border-radius: 10px;
                padding: 12px;
                margin: 15px 0;
                text-align: left;
            ">';
            $html .= '<div style="font-weight: bold; margin-bottom: 8px; text-align: center; font-size: 12px;">🎉 Recent Achievements</div>';
            
            foreach ($recent as $activity) {
                $time_ago = $this->time_ago($activity->timecreated);
                $html .= '<div style="
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 4px 0;
                    border-bottom: 1px solid rgba(255,255,255,0.1);
                    font-size: 10px;
                ">';
                $html .= '<span>' . htmlspecialchars($activity->activity) . '</span>';
                $html .= '<div style="text-align: right;">';
                $html .= '<div style="color: #00f5ff; font-weight: bold;">+' . $activity->xp_gained . ' XP</div>';
                $html .= '<div style="opacity: 0.7; font-size: 9px;">' . $time_ago . '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }
            $html .= '</div>';
        }
        
        // Course leaderboard position
        $position = $this->get_user_position($USER->id, $COURSE->id);
        if ($position) {
            $html .= '<div style="
                background: rgba(255,255,255,0.1);
                border-radius: 10px;
                padding: 10px;
                margin-top: 15px;
                font-size: 12px;
            ">';
            $html .= '<div>📊 Course Ranking: <strong>#' . $position['rank'] . '</strong> of ' . $position['total'] . '</div>';
            $html .= '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    private function get_level_emoji($level) {
        if ($level >= 20) return '👑';      // Crown for high levels
        if ($level >= 15) return '💎';      // Diamond
        if ($level >= 10) return '🏆';      // Trophy
        if ($level >= 7) return '⭐';       // Star
        if ($level >= 5) return '🥇';       // Gold medal
        if ($level >= 3) return '🎖️';       // Military medal
        return '🥉';                         // Bronze medal
    }
    
    private function get_recent_activities($userid, $courseid, $limit = 3) {
        global $DB;
        
        return $DB->get_records('block_myxp_log', 
            ['userid' => $userid, 'courseid' => $courseid],
            'timecreated DESC', '*', 0, $limit);
    }
    
    private function get_user_position($userid, $courseid) {
        global $DB;
        
        $sql = "SELECT 
                    COUNT(*) as total,
                    (SELECT COUNT(*) + 1 
                     FROM {block_myxp_user} b2 
                     WHERE b2.courseid = ? AND b2.total_xp > (
                         SELECT total_xp FROM {block_myxp_user} WHERE userid = ? AND courseid = ?
                     )) as rank
                FROM {block_myxp_user} 
                WHERE courseid = ?";
        
        $result = $DB->get_record_sql($sql, [$courseid, $userid, $courseid, $courseid]);
        
        if ($result && $result->total > 1) {
            return ['rank' => $result->rank, 'total' => $result->total];
        }
        
        return null;
    }
    
    private function time_ago($timestamp) {
        $diff = time() - $timestamp;
        
        if ($diff < 60) return 'now';
        if ($diff < 3600) return floor($diff/60) . 'm ago';
        if ($diff < 86400) return floor($diff/3600) . 'h ago';
        return floor($diff/86400) . 'd ago';
    }
    
    public function applicable_formats() {
        return array('course-view' => true, 'my' => true);
    }
    
    public function instance_allow_multiple() {
        return false;
    }
    
    public function has_config() {
        return true;
    }
}</pre>
    </div>

