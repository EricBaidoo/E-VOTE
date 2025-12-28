<?php
/**
 * Check for duplicate voter credentials - same name AND email/phone
 */

require_once 'includes/db_connect.php';

echo "<h2>🔍 Duplicate Credentials Checker</h2>";
echo "<p>Checking for TRUE duplicates: same name + same email/phone...</p>";

// Check 1: Same Name + Same Email
echo "<h3>🔴 Critical: Same Name + Same Email</h3>";
$query = "
    SELECT 
        MIN(name) as name,
        MIN(email) as email,
        COUNT(*) as count,
        GROUP_CONCAT(id ORDER BY id) as ids
    FROM voters 
    WHERE email != '' AND email IS NOT NULL
    GROUP BY LOWER(TRIM(name)), LOWER(TRIM(REPLACE(email, ' ', '')))
    HAVING count > 1
    ORDER BY count DESC
";

$result = $conn->query($query);

if ($result->num_rows == 0) {
    echo "<div style='background:#d4edda;padding:15px;border:2px solid green;border-radius:5px;margin:10px 0;'>";
    echo "<p style='color:green;margin:0;'><strong>✅ No duplicate name+email combinations</strong></p>";
    echo "</div>";
} else {
    echo "<div style='background:#f8d7da;padding:20px;border:3px solid red;border-radius:10px;margin:10px 0;'>";
    echo "<h4 style='color:red;'>⚠️ PROBLEM: {$result->num_rows} duplicate name+email combinations!</h4>";
    echo "<p>These voters share the same name AND email. Login may choose the wrong account!</p>";
    echo "</div>";
    
    echo "<table border='1' cellpadding='8' style='border-collapse:collapse;width:100%;'>";
    echo "<tr style='background:#dc3545;color:white;'><th>Name</th><th>Email</th><th>Voter IDs</th><th>Count</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr style='background:#fee;'>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td style='font-family:monospace;'>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td style='font-family:monospace;'>" . htmlspecialchars($row['ids']) . "</td>";
        echo "<td style='text-align:center;color:red;font-weight:bold;'>{$row['count']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Check 2: Same Name + Same Phone
echo "<h3>🔴 Critical: Same Name + Same Phone</h3>";
$query = "
    SELECT 
        MIN(name) as name,
        MIN(phone) as phone,
        COUNT(*) as count,
        GROUP_CONCAT(id ORDER BY id) as ids
    FROM voters 
    WHERE phone != '' AND phone IS NOT NULL
    GROUP BY LOWER(TRIM(name)), TRIM(REPLACE(phone, ' ', ''))
    HAVING count > 1
    ORDER BY count DESC
";

$result = $conn->query($query);

if ($result->num_rows == 0) {
    echo "<div style='background:#d4edda;padding:15px;border:2px solid green;border-radius:5px;margin:10px 0;'>";
    echo "<p style='color:green;margin:0;'><strong>✅ No duplicate name+phone combinations</strong></p>";
    echo "</div>";
} else {
    echo "<div style='background:#f8d7da;padding:20px;border:3px solid red;border-radius:10px;margin:10px 0;'>";
    echo "<h4 style='color:red;'>⚠️ PROBLEM: {$result->num_rows} duplicate name+phone combinations!</h4>";
    echo "<p>These voters share the same name AND phone. Login may choose the wrong account!</p>";
    echo "</div>";
    
    echo "<table border='1' cellpadding='8' style='border-collapse:collapse;width:100%;'>";
    echo "<tr style='background:#dc3545;color:white;'><th>Name</th><th>Phone</th><th>Voter IDs</th><th>Count</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr style='background:#fee;'>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td style='font-family:monospace;'>" . htmlspecialchars($row['phone']) . "</td>";
        echo "<td style='font-family:monospace;'>" . htmlspecialchars($row['ids']) . "</td>";
        echo "<td style='text-align:center;color:red;font-weight:bold;'>{$row['count']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Info: Same names but different contacts (NOT a problem)
echo "<h3>ℹ️ Info: Same Name but Different Email/Phone (OK)</h3>";
$query = "
    SELECT 
        MIN(name) as name,
        COUNT(*) as count,
        GROUP_CONCAT(id ORDER BY id) as ids,
        GROUP_CONCAT(DISTINCT email ORDER BY email SEPARATOR ' | ') as emails,
        GROUP_CONCAT(DISTINCT phone ORDER BY phone SEPARATOR ' | ') as phones
    FROM voters 
    GROUP BY LOWER(TRIM(name))
    HAVING count > 1
    ORDER BY count DESC
    LIMIT 20
";

$result = $conn->query($query);

if ($result->num_rows == 0) {
    echo "<div style='background:#d4edda;padding:15px;border:2px solid green;border-radius:5px;margin:10px 0;'>";
    echo "<p style='color:green;margin:0;'><strong>✅ All names are unique</strong></p>";
    echo "</div>";
} else {
    echo "<div style='background:#d1ecf1;padding:15px;border:2px solid #0c5460;border-radius:5px;margin:10px 0;'>";
    echo "<p><strong>ℹ️ Found {$result->num_rows}+ names appearing multiple times</strong></p>";
    echo "<p style='color:green;'>✅ This is OK! Login will work correctly if each person has unique contact info.</p>";
    echo "</div>";
    
    echo "<table border='1' cellpadding='8' style='border-collapse:collapse;width:100%;font-size:13px;'>";
    echo "<tr style='background:#17a2b8;color:white;'><th>Name</th><th>Count</th><th>IDs</th><th>Emails</th><th>Phones</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr style='background:#e7f3ff;'>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td style='text-align:center;'>{$row['count']}</td>";
        echo "<td style='font-family:monospace;font-size:11px;'>" . htmlspecialchars($row['ids']) . "</td>";
        echo "<td style='font-family:monospace;font-size:10px;'>" . htmlspecialchars($row['emails']) . "</td>";
        echo "<td style='font-family:monospace;font-size:11px;'>" . htmlspecialchars($row['phones']) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    echo "<p><small>Showing first 20. These are NOT a problem for login.</small></p>";
}

// Summary
echo "<div style='background:#fff3cd;padding:20px;margin:20px 0;border-radius:10px;'>";
echo "<h3>📊 Summary</h3>";
echo "<p><strong>What matters for login:</strong></p>";
echo "<ul>";
echo "<li>🔴 <strong>Name + Email</strong> must be unique together (CRITICAL)</li>";
echo "<li>🔴 <strong>Name + Phone</strong> must be unique together (CRITICAL)</li>";
echo "<li>✅ Same name with different email/phone is OK</li>";
echo "</ul>";

echo "<h3>⚠️ How Login Works</h3>";
echo "<ol>";
echo "<li>User enters: <strong>Name + Email (or Phone)</strong></li>";
echo "<li>System finds ALL voters with that name</li>";
echo "<li>System checks which one has matching email/phone</li>";
echo "<li>Logs in the CORRECT voter with matching credentials</li>";
echo "</ol>";

echo "<p style='color:red;'><strong>Problem only if:</strong> Two people have BOTH same name AND same email/phone</p>";
echo "</div>";

// Show total voters
$total = $conn->query("SELECT COUNT(*) as count FROM voters")->fetch_assoc()['count'];
echo "<p style='margin-top:20px;'><strong>Total voters:</strong> $total</p>";

$conn->close();

echo "<br><p>";
echo "<a href='repair_voters.php' style='padding:10px 20px;background:#007bff;color:white;text-decoration:none;border-radius:5px;margin-right:10px;'>Back to Repair Center</a>";
echo "<a href='admin/voters.php' style='padding:10px 20px;background:#28a745;color:white;text-decoration:none;border-radius:5px;'>View Voters List</a>";
echo "</p>";
?>
