<?php
// Helper Functions File
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/json_utils.php';

// Ensure session is started for auth and routing
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

// Check if user is voter
function is_voter() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'voter';
}

// Redirect to login if not authenticated
function require_login($role = null) {
    if (!is_logged_in()) {
        header('Location: ' . SITE_URL . '/login.php');
        exit;
    }
    
    if ($role && $_SESSION['role'] != $role) {
        header('Location: ' . SITE_URL . '/unauthorized.php');
        exit;
    }
}

// Hash password
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Verify password
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

// Sanitize input
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Generate unique voter ID
function generate_voter_id() {
    return 'VOTER_' . strtoupper(bin2hex(random_bytes(4)));
}

// Get voter information
function get_voter_info($voter_id) {
    $voters = json_load('voters.json');
    foreach ($voters as $v) {
        if ((string)($v['id'] ?? '') === (string)$voter_id) {
            return $v;
        }
    }
    return null;
}

// Check if voter has voted
function has_voted($voter_id) {
    $votes = json_load('votes.json');
    foreach ($votes as $v) {
        if (($v['voter_session_id'] ?? null) === (string)$voter_id) {
            return true;
        }
    }
    return false;
}

// Get all positions
function get_all_positions() {
    $positions = derive_positions_from_aspirants();
    $cfg = get_positions_config();
    $out = [];
    foreach ($positions as $name) {
        $out[] = [
            'position_name' => $name,
            'seats' => (int)($cfg[$name] ?? 1)
        ];
    }
    return $out;
}

// Get candidates for position
// Deprecated: position_id-based candidates. Use get_position_candidates_by_name.
function get_position_candidates($position_id) {
    return [];
}

function get_position_candidates_by_name($position_name) {
    $groups = group_aspirants_by_position();
    return $groups[$position_name] ?? [];
}

// Get election results
function get_election_results() {
    $tally = tally_results();
    $results = [];
    foreach ($tally as $position => $byId) {
        foreach ($byId as $id => $count) {
            $asp = find_aspirant_by_id($id);
            $results[] = [
                'position_name' => $position,
                'candidate_id' => $id,
                'candidate_name' => $asp['name'] ?? ('#' . $id),
                'vote_count' => $count
            ];
        }
    }
    usort($results, function($a, $b){
        $cmp = strcasecmp($a['position_name'], $b['position_name']);
        if ($cmp !== 0) return $cmp;
        return $b['vote_count'] <=> $a['vote_count'];
    });
    return $results;
}

// Cast vote
function cast_vote($voter_id, $votes_array) {
    // JSON-backed voting; expects $votes_array as [position_name => [candidate_id, ...]]
    return record_vote_json((string)$voter_id, $votes_array);
}

// Format date
function format_date($date) {
    return date('M d, Y H:i A', strtotime($date));
}

// Get total votes cast
function get_total_votes_cast() {
    if (!function_exists('json_load')) {
        require_once __DIR__ . '/json_utils.php';
    }
    $votes = json_load('votes.json');
    $unique = [];
    foreach ($votes as $v) {
        $uid = $v['voter_session_id'] ?? null;
        if ($uid) { $unique[$uid] = true; }
    }
    return count($unique);
}

// Get total registered voters
function get_total_registered_voters() {
    if (!function_exists('json_load')) {
        require_once __DIR__ . '/json_utils.php';
    }
    $voters = json_load('voters.json');
    return is_array($voters) ? count($voters) : 0;
}

// Send response as JSON
function send_json_response($success, $message, $data = null) {
    header('Content-Type: application/json');
    $response = [
        'success' => $success,
        'message' => $message,
        'data' => $data
    ];
    echo json_encode($response);
    exit;
}
?>
