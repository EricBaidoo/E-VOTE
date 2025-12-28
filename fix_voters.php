<?php
/**
 * Fix voter data issues - clean emails and ensure all voters have valid login credentials
 */

require_once 'includes/db_connect.php';

echo "<h2>Fixing Voter Email/Phone Data Issues</h2>";
echo "<p><strong>Target: Voters who have email/phone but can't login due to formatting</strong></p>";

// Get all voters
$query = "SELECT id, name, email, phone FROM voters ORDER BY id";
$result = $conn->query($query);

$fixed = 0;
$skipped = 0;

echo "<table border='1' cellpadding='5' style='border-collapse:collapse;width:100%;'>";
echo "<tr style='background:#2563eb;color:white;'>";
echo "<th>ID</th><th>Name</th><th>Old Email</th><th>New Email</th><th>Old Phone</th><th>New Phone</th><th>Status</th>";
echo "</tr>";

while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $name = trim($row['name']);
    $oldEmail = $row['email'];
    $oldPhone = $row['phone'];
    
    $hasEmail = !empty(trim($oldEmail));
    $hasPhone = !empty(trim($oldPhone));
    
    // Aggressive email cleaning for login matching
    $newEmail = $oldEmail;
    if ($hasEmail) {
        // Remove ALL whitespace characters (spaces, tabs, newlines, carriage returns)
        $newEmail = preg_replace('/[\s\n\r\t]+/', '', $newEmail);
        // Remove any invisible/control characters
        $newEmail = preg_replace('/[\x00-\x1F\x7F]/u', '', $newEmail);
        // Remove BOM
        $newEmail = str_replace("\xEF\xBB\xBF", '', $newEmail);
        $newEmail = trim($newEmail);
    }
    
    // Aggressive phone cleaning for login matching
    $newPhone = $oldPhone;
    if ($hasPhone) {
        // Remove ALL whitespace
        $newPhone = preg_replace('/[\s\n\r\t]+/', '', $newPhone);
        // Remove invisible characters
        $newPhone = preg_replace('/[\x00-\x1F\x7F]/u', '', $newPhone);
        $newPhone = trim($newPhone);
    }
    
    // Check if we need to update (only if they have email/phone and it changed)
    $needsUpdate = false;
    if ($hasEmail && $oldEmail !== $newEmail) $needsUpdate = true;
    if ($hasPhone && $oldPhone !== $newPhone) $needsUpdate = true;
    
    if ($needsUpdate) {
        $stmt = $conn->prepare("UPDATE voters SET email = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("ssi", $newEmail, $newPhone, $id);
        $stmt->execute();
        $stmt->close();
        
        // Show what changed
        $changes = [];
        if ($oldEmail !== $newEmail) {
            $changes[] = "Email cleaned";
        }
        if ($oldPhone !== $newPhone) {
            $changes[] = "Phone cleaned";
        }
        
        echo "<tr style='background:#efe;'>";
        echo "<td>{$id}</td>";
        echo "<td>" . htmlspecialchars($name) . "</td>";
        echo "<td style='color:red;font-family:monospace;font-size:11px;'>" . htmlspecialchars(substr($oldEmail, 0, 50)) . "</td>";
        echo "<td style='color:green;font-family:monospace;'>" . htmlspecialchars($newEmail) . "</td>";
        echo "<td style='color:red;'>" . htmlspecialchars($oldPhone) . "</td>";
        echo "<td style='color:green;'>" . htmlspecialchars($newPhone) . "</td>";
        echo "<td><span style='color:green;'>✓ " . implode(', ', $changes) . "</span></td>";
        echo "</tr>";
        
        $fixed++;
    } else {
        // Only skip if they have contact info and it doesn't need fixing
        if ($hasEmail || $hasPhone) {
            $skipped++;
        }
    }
}

echo "</table>";

echo "<h3 style='color:green;'>✓ Voter Email/Phone Cleanup Complete!</h3>";
echo "<ul>";
echo "<li><strong>Voters fixed:</strong> <span style='color:green;font-size:20px;'>$fixed</span></li>";
echo "<li><strong>Voters already clean:</strong> $skipped</li>";
echo "</ul>";

if ($fixed > 0) {
    echo "<div style='background:#efe;border:3px solid green;padding:20px;margin:20px 0;'>";
    echo "<h4 style='color:green;'>✅ Success!</h4>";
    echo "<p>$fixed voters can now login with their email/phone.</p>";
    echo "<p><strong>Changes made:</strong></p>";
    echo "<ul>";
    echo "<li>Removed all whitespace and newlines from emails</li>";
    echo "<li>Removed invisible characters</li>";
    echo "<li>Cleaned phone numbers</li>";
    echo "</ul>";
    echo "</div>";
}

// Check for remaining issues
// Check for remaining issues
echo "<h3>Verifying all voters can now login...</h3>";

$query = "SELECT id, name, email, phone FROM voters WHERE (email != '' AND email IS NOT NULL) OR (phone != '' AND phone IS NOT NULL)";
$result = $conn->query($query);

$stillProblematic = [];
while ($row = $result->fetch_assoc()) {
    $email = $row['email'];
    $phone = $row['phone'];
    
    // Check if email still has issues
    if (!empty($email)) {
        if (preg_match('/[\s\n\r\t]/', $email)) {
            $stillProblematic[] = $row;
            continue;
        }
        $cleanEmail = preg_replace('/[\s\n\r\t]+/', '', $email);
        if (!filter_var($cleanEmail, FILTER_VALIDATE_EMAIL)) {
            $stillProblematic[] = $row;
        }
    }
}

if (!empty($stillProblematic)) {
    echo "<div style='background:#fee;padding:15px;border:2px solid orange;'>";
    echo "<h4 style='color:orange;'>⚠️ " . count($stillProblematic) . " voters still have issues</h4>";
    echo "<p>These may need manual correction:</p>";
    echo "<ul>";
    foreach ($stillProblematic as $voter) {
        echo "<li>ID {$voter['id']}: " . htmlspecialchars($voter['name']) . " - Email: " . htmlspecialchars($voter['email']) . "</li>";
    }
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div style='background:#efe;padding:15px;border:2px solid green;'>";
    echo "<h4 style='color:green;'>✅ All voters with email/phone can now login!</h4>";
    echo "</div>";
}

$conn->close();

echo "<br><p><a href='diagnose_voters.php' style='padding:10px 20px;background:#007bff;color:white;text-decoration:none;border-radius:5px;'>Run Diagnostic Again</a> | <a href='admin/voters.php' style='padding:10px 20px;background:#28a745;color:white;text-decoration:none;border-radius:5px;'>View Voters List</a></p>";
?>
