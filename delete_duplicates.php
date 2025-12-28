<?php
/**
 * Remove ALL duplicate voter names - keep only the first occurrence
 */

require_once 'includes/db_connect.php';

echo "<h2>🗑️ Remove Duplicate Names</h2>";
echo "<p><strong>This will delete ALL duplicate voter names, keeping only the FIRST occurrence of each name.</strong></p>";

// Find all duplicate names with vote status
$query = "
    SELECT 
        MIN(v.name) as name,
        COUNT(*) as count,
        GROUP_CONCAT(v.id ORDER BY v.id) as all_ids,
        GROUP_CONCAT(v.email ORDER BY v.id SEPARATOR ' | ') as emails,
        GROUP_CONCAT(v.phone ORDER BY v.id SEPARATOR ' | ') as phones,
        GROUP_CONCAT(CASE WHEN vt.voter_session_id IS NOT NULL THEN 'VOTED' ELSE 'NOT_VOTED' END ORDER BY v.id SEPARATOR ' | ') as vote_status
    FROM voters v
    LEFT JOIN votes vt ON vt.voter_session_id = v.id
    GROUP BY LOWER(TRIM(REPLACE(v.name, '  ', ' ')))
    HAVING count > 1
    ORDER BY name
";

$result = $conn->query($query);

if ($result->num_rows == 0) {
    echo "<div style='background:#d4edda;padding:20px;border:3px solid green;border-radius:10px;'>";
    echo "<h3 style='color:green;'>✅ No duplicate names found!</h3>";
    echo "<p>All voter names are unique.</p>";
    echo "</div>";
    echo "<br><p><a href='repair_voters.php'>Back to Repair Center</a></p>";
    $conn->close();
    exit;
}

// Store duplicates
$duplicates = [];
while ($row = $result->fetch_assoc()) {
    $duplicates[] = $row;
}

$totalDuplicates = count($duplicates);
$totalToDelete = 0;

foreach ($duplicates as $dup) {
    $ids = explode(',', $dup['all_ids']);
    $totalToDelete += (count($ids) - 1); // Keep first, delete rest
}

echo "<div style='background:#fff3cd;padding:20px;border:3px solid orange;border-radius:10px;margin:20px 0;'>";
echo "<h3 style='color:#856404;'>⚠️ Found $totalDuplicates duplicate name groups</h3>";
echo "<p><strong>Total records to be deleted: <span style='color:red;font-size:24px;'>$totalToDelete</span></strong></p>";
echo "<p>The FIRST occurrence (lowest ID) of each name will be kept.</p>";
echo "</div>";

// Show what will be kept and deleted
echo "<h3>Preview: What will be kept vs deleted</h3>";
echo "<table border='1' cellpadding='8' style='border-collapse:collapse;width:100%;font-size:12px;'>";
echo "<tr style='background:#2563eb;color:white;'>";
echo "<th>Name</th><th>Keep ID</th><th>Keep Email</th><th>Keep Phone</th><th>Voted?</th><th>Delete IDs</th><th>Count to Delete</th>";
echo "</tr>";

foreach ($duplicates as $dup) {
    $allIds = explode(',', $dup['all_ids']);
    $emails = explode(' | ', $dup['emails']);
    $phones = explode(' | ', $dup['phones']);
    $voteStatuses = explode(' | ', $dup['vote_status']);
    
    // Find which ID has voted (if any)
    $keepIndex = 0;
    $hasVoted = false;
    for ($i = 0; $i < count($allIds); $i++) {
        if ($voteStatuses[$i] === 'VOTED') {
            $keepIndex = $i;
            $hasVoted = true;
            break; // Keep the first one that voted
        }
    }
    
    $keepId = $allIds[$keepIndex];
    $keepEmail = $emails[$keepIndex];
    $keepPhone = $phones[$keepIndex];
    $keepVoted = $voteStatuses[$keepIndex];
    
    // Delete all others
    $deleteIds = [];
    for ($i = 0; $i < count($allIds); $i++) {
        if ($i !== $keepIndex) {
            $deleteIds[] = $allIds[$i];
        }
    }
    
    // Store the keep index for later use
    $dup['keep_index'] = $keepIndex;
    
    echo "<tr>";
    echo "<td><strong>" . htmlspecialchars($dup['name']) . "</strong></td>";
    echo "<td style='background:#d4edda;font-family:monospace;'>{$keepId}</td>";
    echo "<td style='background:#d4edda;font-family:monospace;font-size:11px;'>" . htmlspecialchars($keepEmail) . "</td>";
    echo "<td style='background:#d4edda;font-family:monospace;'>" . htmlspecialchars($keepPhone) . "</td>";
    echo "<td style='background:" . ($hasVoted ? "#28a745" : "#ffc107") . ";color:white;text-align:center;font-weight:bold;'>" . $keepVoted . "</td>";
    echo "<td style='background:#f8d7da;font-family:monospace;'>" . implode(', ', $deleteIds) . "</td>";
    echo "<td style='background:#f8d7da;text-align:center;color:red;font-weight:bold;'>" . count($deleteIds) . "</td>";
    echo "</tr>";
}

echo "</table>";

// Confirmation form
if (!isset($_POST['confirm_delete'])) {
    echo "<div style='background:#f8d7da;padding:30px;margin:30px 0;border:3px solid red;border-radius:10px;'>";
    echo "<h3 style='color:red;'>⚠️ WARNING: This action cannot be undone!</h3>";
    echo "<p><strong>$totalToDelete voter records will be permanently deleted.</strong></p>";
    echo "<p>Review the table above carefully before proceeding.</p>";
    
    echo "<form method='post'>";
    echo "<label style='display:block;margin:20px 0;'>";
    echo "<input type='checkbox' name='confirm_checkbox' required> ";
    echo "I understand this will delete $totalToDelete records and cannot be undone";
    echo "</label>";
    echo "<input type='hidden' name='confirm_delete' value='1'>";
    echo "<button type='submit' style='background:#dc3545;color:white;padding:15px 40px;border:none;cursor:pointer;font-size:18px;font-weight:bold;border-radius:8px;'>DELETE $totalToDelete DUPLICATE RECORDS</button>";
    echo " ";
    echo "<a href='check_duplicates.php' style='padding:15px 40px;background:#6c757d;color:white;text-decoration:none;border-radius:8px;display:inline-block;'>Cancel</a>";
    echo "</form>";
    echo "</div>";
} else {
    // Perform deletion
    echo "<h3>Deleting duplicate records...</h3>";
    
    $deleted = 0;
    $errors = 0;
    
    echo "<table border='1' cellpadding='8' style='border-collapse:collapse;width:100%;'>";
    echo "<tr style='background:#2563eb;color:white;'><th>Name</th><th>Kept ID</th><th>Deleted IDs</th><th>Status</th></tr>";
    
    foreach ($duplicates as $dup) {
        $allIds = explode(',', $dup['all_ids']);
        $voteStatuses = explode(' | ', $dup['vote_status']);
        
        // Find which ID has voted (if any)
        $keepIndex = 0;
        for ($i = 0; $i < count($allIds); $i++) {
            if ($voteStatuses[$i] === 'VOTED') {
                $keepIndex = $i;
                break; // Keep the first one that voted
            }
        }
        
        $keepId = $allIds[$keepIndex];
        
        // Delete all others
        $deleteIds = [];
        for ($i = 0; $i < count($allIds); $i++) {
            if ($i !== $keepIndex) {
                $deleteIds[] = $allIds[$i];
            }
        }
        
        // Delete all except the one to keep
        if (!empty($deleteIds)) {
            $deleteIdsList = implode(',', $deleteIds);
            $stmt = $conn->prepare("DELETE FROM voters WHERE id IN ($deleteIdsList)");
            
            if ($stmt->execute()) {
                $deletedCount = $stmt->affected_rows;
                $deleted += $deletedCount;
                
                echo "<tr style='background:#d4edda;'>";
                echo "<td>" . htmlspecialchars($dup['name']) . "</td>";
                echo "<td style='font-family:monospace;'>{$keepId} ({$voteStatuses[$keepIndex]})</td>";
                echo "<td style='font-family:monospace;'>{$deleteIdsList}</td>";
                echo "<td style='color:green;'>✓ Deleted {$deletedCount} records</td>";
                echo "</tr>";
            } else {
                $errors++;
                echo "<tr style='background:#f8d7da;'>";
                echo "<td>" . htmlspecialchars($dup['name']) . "</td>";
                echo "<td colspan='3' style='color:red;'>✗ Error: " . $conn->error . "</td>";
                echo "</tr>";
            }
            $stmt->close();
        }
    }
    
    echo "</table>";
    
    echo "<div style='background:#d4edda;padding:30px;margin:30px 0;border:3px solid green;border-radius:10px;'>";
    echo "<h3 style='color:green;'>✅ Deletion Complete!</h3>";
    echo "<ul style='font-size:18px;'>";
    echo "<li><strong>Records deleted:</strong> <span style='color:red;font-size:24px;'>$deleted</span></li>";
    echo "<li><strong>Errors:</strong> $errors</li>";
    echo "<li><strong>Unique names kept:</strong> " . count($duplicates) . "</li>";
    echo "</ul>";
    echo "</div>";
    
    // Also delete their votes if any
    echo "<h3>Cleaning up votes from deleted records...</h3>";
    
    $allDeletedIds = [];
    foreach ($duplicates as $dup) {
        $allIds = explode(',', $dup['all_ids']);
        $deleteIds = array_slice($allIds, 1);
        $allDeletedIds = array_merge($allDeletedIds, $deleteIds);
    }
    
    if (!empty($allDeletedIds)) {
        foreach ($allDeletedIds as $deletedId) {
            $stmt = $conn->prepare("DELETE FROM votes WHERE voter_session_id = ?");
            $idStr = (string)$deletedId;
            $stmt->bind_param("s", $idStr);
            $stmt->execute();
            $stmt->close();
        }
        echo "<p style='color:green;'>✓ Removed votes from deleted voter records</p>";
    }
    
    echo "<br><p><a href='check_duplicates.php' style='padding:15px 30px;background:#28a745;color:white;text-decoration:none;border-radius:8px;'>Verify No Duplicates Remain</a></p>";
}

$conn->close();

echo "<br><p><a href='repair_voters.php'>Back to Repair Center</a> | <a href='admin/voters.php'>View Voters List</a></p>";
?>
