<?php
// JSON data helpers for non-DB operation

function json_path($relative) {
    $base = __DIR__ . '/../data/';
    return realpath($base) ? rtrim(realpath($base), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $relative : $base . $relative;
}

function json_load($relative) {
    $path = json_path($relative);
    if (!file_exists($path)) return [];
    $raw = file_get_contents($path);
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function json_save($relative, $data) {
    $path = json_path($relative);
    $dir = dirname($path);
    if (!is_dir($dir)) {@mkdir($dir, 0777, true);}    
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return (bool)file_put_contents($path, $json);
}

function normalize_position($pos) {
    $pos = trim(preg_replace('/\s+/', ' ', (string)$pos));
    // Convert to Title Case except acronyms
    $lower = strtolower($pos);
    $title = ucwords($lower);
    // Preserve common acronyms if present
    $title = str_replace('Pro', 'PRO', $title);
    return $title;
}

function get_aspirants() {
    $aspirants = json_load('aspirants.json');
    // Normalize position strings
    foreach ($aspirants as &$a) {
        if (isset($a['position'])) {
            $a['position'] = normalize_position($a['position']);
        }
    }
    return $aspirants;
}

function get_positions_config() {
    $cfg = json_load('positions_config.json');
    $out = [];
    foreach ($cfg as $k => $v) {
        $out[normalize_position($k)] = (int)$v;
    }
    return $out;
}

function derive_positions_from_aspirants() {
    $asp = get_aspirants();
    $set = [];
    foreach ($asp as $a) {
        if (!empty($a['position'])) {
            $set[$a['position']] = true;
        }
    }
    $positions = array_keys($set);
    
    // Define custom position order
    $order = [
        'President' => 1,
        'Vice' => 2,
        'Vice President' => 2,
        'Secretary' => 3,
        'Treasurer' => 4,
        'Organizer' => 5,
        'Male Executive Member' => 6,
        'Female Executive Member' => 7,
        'Executive Members' => 8,
        'Executive Member' => 8,
        'Executive' => 8
    ];
    
    // Sort positions by defined order, then alphabetically
    usort($positions, function($a, $b) use ($order) {
        $aOrder = $order[$a] ?? 999;
        $bOrder = $order[$b] ?? 999;
        if ($aOrder != $bOrder) {
            return $aOrder - $bOrder;
        }
        return strcasecmp($a, $b);
    });
    
    return $positions;
}

function group_aspirants_by_position() {
    $asp = get_aspirants();
    $group = [];
    foreach ($asp as $a) {
        $pos = $a['position'] ?? 'Unknown';
        $group[$pos][] = $a;
    }
    // Sort candidates by name
    foreach ($group as &$list) {
        usort($list, function($x, $y){ return strcasecmp($x['name'] ?? '', $y['name'] ?? ''); });
    }
    
    // Sort the positions themselves by custom order
    $order = [
        'President' => 1,
        'Vice' => 2,
        'Vice President' => 2,
        'Secretary' => 3,
        'Treasurer' => 4,
        'Organizer' => 5,
        'Male Executive Member' => 6,
        'Female Executive Member' => 7,
        'Executive Members' => 8,
        'Executive Member' => 8,
        'Executive' => 8
    ];
    
    uksort($group, function($a, $b) use ($order) {
        $aOrder = $order[$a] ?? 999;
        $bOrder = $order[$b] ?? 999;
        if ($aOrder != $bOrder) {
            return $aOrder - $bOrder;
        }
        return strcasecmp($a, $b);
    });
    
    return $group;
}

function get_votes() {
    return json_load('votes.json');
}

function has_voted_json($session_user_id) {
    $votes = get_votes();
    foreach ($votes as $v) {
        if (($v['voter_session_id'] ?? null) === $session_user_id) {
            return true;
        }
    }
    return false;
}

function record_vote_json($session_user_id, $choices) {
    $votes = get_votes();
    $votes[] = [
        'voter_session_id' => $session_user_id,
        'timestamp' => date('c'),
        'choices' => $choices
    ];
    return json_save('votes.json', $votes);
}

function tally_results() {
    $votes = get_votes();
    $tally = [];
    foreach ($votes as $v) {
        $choices = $v['choices'] ?? [];
        foreach ($choices as $position => $ids) {
            foreach ((array)$ids as $id) {
                if (!isset($tally[$position][$id])) { $tally[$position][$id] = 0; }
                $tally[$position][$id]++;
            }
        }
    }
    return $tally;
}

function find_aspirant_by_id($id) {
    foreach (get_aspirants() as $a) {
        if ((string)$a['id'] === (string)$id) return $a;
    }
    return null;
}

?>