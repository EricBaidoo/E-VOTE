<?php
/**
 * Diagnose voter login issues
 */

require_once 'includes/db_connect.php';

echo "<h2>Voter Login Diagnostic Report</h2>";

// Check voters with problematic data
$query = "SELECT id, name, email, phone FROM voters ORDER BY id LIMIT 50";
$result = $conn->query($query);

echo "<h3>Checking voter data for login issues...</h3>";
echo "<p><strong>Focus: Voters with email/phone that can't login due to data formatting</strong></p>";
echo "<table border='1' cellpadding='5' style='border-collapse:collapse;width:100%;'>";
echo "<tr style='background:#2563eb;color:white;'>";
echo "<th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Issues</th><th>Clean Email</th><th>Clean Phone</th>";
echo "</tr>";

$issues = [];
$count = 0;
$cannotLogin = 0;

while ($row = $result->fetch_assoc()) {
    $count++;
    $problems = [];
    
    $email = $row['email'];
    $phone = $row['phone'];
    $hasContact = !empty(trim($email)) || !empty(trim($phone));
    
    // Focus on voters who HAVE contact info but it's problematic
    
    // Check for whitespace/newlines in email (MAJOR ISSUE)
    if (!empty($email) && preg_match('/[\s\n\r\t]/', $email)) {
        $problems[] = "🔴 Email has whitespace/newlines";
        $cannotLogin++;
    }
    
    // Check for invalid email format (but has email)
    if (!empty($email)) {
        $cleanEmail = preg_replace('/[\s\n\r\t]+/', '', $email);
        if (!filter_var($cleanEmail, FILTER_VALIDATE_EMAIL)) {
            $problems[] = "🔴 Invalid email format";
            $cannotLogin++;
        }
    }
    
    // Check for special characters in phone
    if (!empty($phone) && preg_match('/[^\d\+\-\(\)\s]/', $phone)) {
        $problems[] = "⚠️ Phone has special characters";
    }
    
    // Check for whitespace in phone
    if (!empty($phone) && preg_match('/\s/', $phone)) {
        $problems[] = "⚠️ Phone has spaces";
    }
    
    // Check for empty name
    if (empty(trim($row['name']))) {
        $problems[] = "❌ Empty name";
        $cannotLogin++;
    }
    
    // Clean versions for comparison
    $cleanEmail = preg_replace('/[\s\n\r\t]+/', '', $email);
    $cleanPhone = preg_replace('/\s+/', '', $phone);
    
    // Only show rows with problems OR if they have contact info
    if (!empty($problems) && $hasContact) {
        $rowClass = 'style="background:#fee;"';
        
        echo "<tr $rowClass>";
        echo "<td>{$row['id']}</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td style='font-family:monospace;'>" . htmlspecialchars($email) . "</td>";
        echo "<td>" . htmlspecialchars($phone) . "</td>";
        echo "<td>" . implode('<br>', $problems) . "</td>";
        echo "<td style='color:green;font-family:monospace;'>" . htmlspecialchars($cleanEmail) . "</td>";
        echo "<td style='color:green;'>" . htmlspecialchars($cleanPhone) . "</td>";
        echo "</tr>";
        
        $issues[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'email' => $email,
            'phone' => $phone,
            'cleanEmail' => $cleanEmail,
            'cleanPhone' => $cleanPhone,
            'problems' => $problems
        ];
    }
}

echo "</table>";

echo "<h3>Summary</h3>";
echo "<ul>";
echo "<li><strong>Total voters checked:</strong> $count</li>";
echo "<li><strong>Voters with problematic email/phone (CANNOT LOGIN):</strong> <span style='color:red;font-size:20px;font-weight:bold;'>" . count($issues) . "</span></li>";
echo "</ul>";

if (!empty($issues)) {
    echo "<div style='background:#fee;border:3px solid red;padding:20px;margin:20px 0;'>";
    echo "<h3 style='color:red;'>🔴 CRITICAL: " . count($issues) . " voters have email/phone but CANNOT login!</h3>";
    echo "<p><strong>These voters have contact information, but it contains formatting errors that prevent login matching:</strong></p>";
    echo "<ul style='font-size:14px;'>";
    foreach ($issues as $issue) {
        echo "<li><strong>ID {$issue['id']}: {$issue['name']}</strong><br>";
        echo "Email: " . htmlspecialchars($issue['email']) . " → " . htmlspecialchars($issue['cleanEmail']) . "<br>";
        echo "Problems: " . implode(', ', $issue['problems']) . "</li>";
    }
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='text-align:center;margin:30px 0;'>";
    echo "<a href='fix_voters.php' style='background:green;color:white;padding:20px 40px;border:none;cursor:pointer;font-size:20px;text-decoration:none;border-radius:10px;display:inline-block;'>🔧 FIX ALL ISSUES NOW</a>";
    echo "</div>";
} else {
    echo "<div style='background:#efe;border:3px solid green;padding:20px;margin:20px 0;'>";
    echo "<h3 style='color:green;'>✅ All voters with email/phone can login!</h3>";
    echo "<p>No formatting issues detected in voter contact information.</p>";
    echo "</div>";
}

$conn->close();
?>
