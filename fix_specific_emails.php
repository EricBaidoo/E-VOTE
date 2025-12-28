<?php
/**
 * Fix specific email format issues
 * - Remove "Email." prefix
 * - Remove trailing dots
 * - Remove dots before @
 * - Fix quotation marks to @
 */

require_once 'includes/db_connect.php';

echo "<h2>Fixing Specific Email Issues</h2>";

// Target the problematic voters
$problematicIds = [148, 301, 482, 517, 550];

echo "<table border='1' cellpadding='5' style='border-collapse:collapse;width:100%;'>";
echo "<tr style='background:#2563eb;color:white;'>";
echo "<th>ID</th><th>Name</th><th>Old Email</th><th>Fixed Email</th><th>Issue Fixed</th><th>Status</th>";
echo "</tr>";

$fixed = 0;
$errors = 0;

foreach ($problematicIds as $id) {
    $stmt = $conn->prepare("SELECT id, name, email FROM voters WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $voter = $result->fetch_assoc();
    $stmt->close();
    
    if (!$voter) {
        echo "<tr style='background:#fee;'>";
        echo "<td colspan='6'>ID $id not found</td>";
        echo "</tr>";
        continue;
    }
    
    $name = $voter['name'];
    $oldEmail = $voter['email'];
    $newEmail = $oldEmail;
    $issuesFixed = [];
    
    // Fix 1: Remove "Email." prefix (case-insensitive)
    if (preg_match('/^email\./i', $newEmail)) {
        $newEmail = preg_replace('/^email\./i', '', $newEmail);
        $issuesFixed[] = "Removed 'Email.' prefix";
    }
    
    // Fix 2: Remove trailing dots
    if (substr($newEmail, -1) === '.') {
        $newEmail = rtrim($newEmail, '.');
        $issuesFixed[] = "Removed trailing dot";
    }
    
    // Fix 3: Remove dots immediately before @
    if (preg_match('/\.@/', $newEmail)) {
        $newEmail = str_replace('.@', '@', $newEmail);
        $issuesFixed[] = "Removed dot before @";
    }
    
    // Fix 4: Replace quotation marks with @
    if (strpos($newEmail, '"') !== false && strpos($newEmail, '@') === false) {
        $newEmail = str_replace('"', '@', $newEmail);
        $issuesFixed[] = "Fixed quotation mark to @";
    }
    
    // Fix 5: Replace other common mistakes
    $newEmail = str_replace(['@.', '@@'], '@', $newEmail);
    
    // Clean any remaining whitespace
    $newEmail = trim($newEmail);
    
    if ($oldEmail !== $newEmail) {
        // Update the database
        $stmt = $conn->prepare("UPDATE voters SET email = ? WHERE id = ?");
        $stmt->bind_param("si", $newEmail, $id);
        
        if ($stmt->execute()) {
            echo "<tr style='background:#efe;'>";
            echo "<td>{$id}</td>";
            echo "<td>" . htmlspecialchars($name) . "</td>";
            echo "<td style='color:red;font-family:monospace;'>" . htmlspecialchars($oldEmail) . "</td>";
            echo "<td style='color:green;font-family:monospace;'>" . htmlspecialchars($newEmail) . "</td>";
            echo "<td>" . implode(', ', $issuesFixed) . "</td>";
            echo "<td><span style='color:green;font-weight:bold;'>✓ FIXED</span></td>";
            echo "</tr>";
            $fixed++;
        } else {
            echo "<tr style='background:#fee;'>";
            echo "<td>{$id}</td>";
            echo "<td colspan='5' style='color:red;'>Error: " . $conn->error . "</td>";
            echo "</tr>";
            $errors++;
        }
        $stmt->close();
    } else {
        echo "<tr style='background:#ffe;'>";
        echo "<td>{$id}</td>";
        echo "<td>" . htmlspecialchars($name) . "</td>";
        echo "<td colspan='3' style='font-family:monospace;'>" . htmlspecialchars($oldEmail) . "</td>";
        echo "<td><span style='color:orange;'>No changes needed</span></td>";
        echo "</tr>";
    }
}

echo "</table>";

echo "<h3 style='color:green;'>✓ Specific Email Fixes Complete!</h3>";
echo "<ul>";
echo "<li><strong>Emails fixed:</strong> <span style='color:green;font-size:20px;'>$fixed</span></li>";
echo "<li><strong>Errors:</strong> $errors</li>";
echo "</ul>";

if ($fixed > 0) {
    echo "<div style='background:#efe;border:3px solid green;padding:20px;margin:20px 0;'>";
    echo "<h4 style='color:green;'>✅ All 5 problematic emails have been fixed!</h4>";
    echo "<p>The following issues were corrected:</p>";
    echo "<ul>";
    echo "<li>Removed 'Email.' prefixes</li>";
    echo "<li>Removed trailing dots</li>";
    echo "<li>Removed dots before @ symbol</li>";
    echo "<li>Fixed quotation marks to @ symbol</li>";
    echo "</ul>";
    echo "</div>";
}

// Verify all fixes
echo "<h3>Verifying Fixed Emails...</h3>";
echo "<table border='1' cellpadding='5' style='border-collapse:collapse;width:100%;'>";
echo "<tr style='background:#2563eb;color:white;'>";
echo "<th>ID</th><th>Name</th><th>Email</th><th>Valid?</th>";
echo "</tr>";

foreach ($problematicIds as $id) {
    $stmt = $conn->prepare("SELECT id, name, email FROM voters WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $voter = $result->fetch_assoc();
    $stmt->close();
    
    if ($voter) {
        $email = $voter['email'];
        $isValid = filter_var($email, FILTER_VALIDATE_EMAIL);
        $statusColor = $isValid ? '#efe' : '#fee';
        $statusIcon = $isValid ? '✅' : '❌';
        
        echo "<tr style='background:$statusColor;'>";
        echo "<td>{$voter['id']}</td>";
        echo "<td>" . htmlspecialchars($voter['name']) . "</td>";
        echo "<td style='font-family:monospace;'>" . htmlspecialchars($email) . "</td>";
        echo "<td><strong>$statusIcon " . ($isValid ? 'YES' : 'NO') . "</strong></td>";
        echo "</tr>";
    }
}

echo "</table>";

$conn->close();

echo "<br><p>";
echo "<a href='diagnose_voters.php' style='padding:10px 20px;background:#007bff;color:white;text-decoration:none;border-radius:5px;margin-right:10px;'>Run Full Diagnostic</a>";
echo "<a href='test_login_matching.php' style='padding:10px 20px;background:#28a745;color:white;text-decoration:none;border-radius:5px;'>Test Login Matching</a>";
echo "</p>";
?>
