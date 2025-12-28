<?php
/**
 * Test login matching logic to verify voters can login after cleanup
 */

require_once 'includes/db_connect.php';

echo "<h2>Test Login Matching</h2>";
echo "<p>This page shows how voter emails/phones in the database match what users type during login.</p>";

// Get first 20 voters with email or phone
$query = "SELECT id, name, email, phone FROM voters WHERE (email != '' AND email IS NOT NULL) OR (phone != '' AND phone IS NOT NULL) LIMIT 20";
$result = $conn->query($query);

echo "<table border='1' cellpadding='8' style='border-collapse:collapse;width:100%;'>";
echo "<tr style='background:#2563eb;color:white;'>";
echo "<th>ID</th><th>Name</th><th>Database Email</th><th>Cleaned Email<br>(For Matching)</th><th>Database Phone</th><th>Cleaned Phone<br>(For Matching)</th><th>Can Login?</th>";
echo "</tr>";

while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $name = $row['name'];
    $dbEmail = $row['email'];
    $dbPhone = $row['phone'];
    
    // Simulate login cleaning (what happens when user types)
    $cleanEmail = strtolower(preg_replace('/\s+/', '', trim($dbEmail)));
    $cleanPhone = preg_replace('/\s+/', '', trim($dbPhone));
    
    // Check if can login
    $canLogin = true;
    $issues = [];
    
    // Check email
    if (!empty($dbEmail)) {
        // Check for whitespace/newlines in DB (PROBLEM!)
        if (preg_match('/[\s\n\r\t]/', $dbEmail)) {
            $canLogin = false;
            $issues[] = "Email has whitespace";
        }
        
        // Check if valid after cleaning
        if (!empty($cleanEmail) && !filter_var($cleanEmail, FILTER_VALIDATE_EMAIL)) {
            $canLogin = false;
            $issues[] = "Invalid email format";
        }
    }
    
    $statusColor = $canLogin ? '#efe' : '#fee';
    $statusIcon = $canLogin ? '✅' : '❌';
    $statusText = $canLogin ? 'YES' : 'NO';
    
    echo "<tr style='background:$statusColor;'>";
    echo "<td>{$id}</td>";
    echo "<td>" . htmlspecialchars($name) . "</td>";
    echo "<td style='font-family:monospace;font-size:11px;'>" . htmlspecialchars($dbEmail) . "</td>";
    echo "<td style='font-family:monospace;color:green;'>" . htmlspecialchars($cleanEmail) . "</td>";
    echo "<td style='font-family:monospace;'>" . htmlspecialchars($dbPhone) . "</td>";
    echo "<td style='font-family:monospace;color:green;'>" . htmlspecialchars($cleanPhone) . "</td>";
    echo "<td><strong>$statusIcon $statusText</strong>";
    if (!empty($issues)) {
        echo "<br><span style='color:red;font-size:11px;'>" . implode(', ', $issues) . "</span>";
    }
    echo "</td>";
    echo "</tr>";
}

echo "</table>";

// Summary
$query = "SELECT COUNT(*) as total FROM voters WHERE (email != '' AND email IS NOT NULL) OR (phone != '' AND phone IS NOT NULL)";
$result = $conn->query($query);
$total = $result->fetch_assoc()['total'];

// Count problematic
$query = "SELECT COUNT(*) as count FROM voters WHERE (email LIKE '% %' OR email LIKE '%\n%' OR email LIKE '%\r%' OR email LIKE '%\t%')";
$result = $conn->query($query);
$problematic = $result->fetch_assoc()['count'];

echo "<h3>Summary</h3>";
echo "<ul>";
echo "<li><strong>Total voters with email/phone:</strong> $total</li>";
echo "<li><strong>Voters with whitespace in email (CANNOT LOGIN):</strong> <span style='color:red;font-size:18px;font-weight:bold;'>$problematic</span></li>";
echo "</ul>";

if ($problematic > 0) {
    echo "<div style='background:#fee;border:3px solid red;padding:20px;'>";
    echo "<h4 style='color:red;'>⚠️ $problematic voters CANNOT login!</h4>";
    echo "<p>Their emails contain whitespace/line breaks that prevent matching.</p>";
    echo "<a href='fix_voters.php' style='background:green;color:white;padding:15px 30px;text-decoration:none;border-radius:8px;display:inline-block;font-size:18px;'>Fix Now</a>";
    echo "</div>";
} else {
    echo "<div style='background:#efe;border:3px solid green;padding:20px;'>";
    echo "<h4 style='color:green;'>✅ All voters can login!</h4>";
    echo "</div>";
}

$conn->close();

echo "<br><p><a href='repair_voters.php'>Back to Repair Center</a></p>";
?>
