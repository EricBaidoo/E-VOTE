<?php
/**
 * Debug Login Matching - See exactly what's being compared
 */

require_once 'includes/db_connect.php';

echo "<h2>Debug Login Matching</h2>";
echo "<p>This shows exactly how your input is cleaned and compared with the database.</p>";

// Test form
echo '<form method="post" style="background:#f0f0f0;padding:20px;margin:20px 0;border-radius:10px;">';
echo '<h3>Test Login</h3>';
echo '<div style="margin:10px 0;">';
echo '<label><strong>Name:</strong></label><br>';
echo '<input type="text" name="test_name" style="padding:10px;width:300px;font-size:16px;" placeholder="Enter voter name">';
echo '</div>';
echo '<div style="margin:10px 0;">';
echo '<label><strong>Email or Phone:</strong></label><br>';
echo '<input type="text" name="test_contact" style="padding:10px;width:300px;font-size:16px;" placeholder="Enter email or phone">';
echo '</div>';
echo '<button type="submit" style="background:#28a745;color:white;padding:12px 30px;border:none;cursor:pointer;font-size:16px;border-radius:5px;">Test Login Match</button>';
echo '</form>';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['test_name']) && isset($_POST['test_contact'])) {
    $testName = $_POST['test_name'];
    $testContact = $_POST['test_contact'];
    
    echo "<div style='background:#fff3cd;border:2px solid #ffc107;padding:20px;margin:20px 0;border-radius:10px;'>";
    echo "<h3>🔍 Login Matching Analysis</h3>";
    
    // Show what user entered
    echo "<h4>1. What You Entered:</h4>";
    echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
    echo "<tr><th>Field</th><th>Raw Value</th><th>Hex/Chars</th></tr>";
    echo "<tr>";
    echo "<td><strong>Name</strong></td>";
    echo "<td style='font-family:monospace;'>" . htmlspecialchars($testName) . "</td>";
    echo "<td style='font-size:10px;'>";
    for ($i = 0; $i < strlen($testName); $i++) {
        echo sprintf("%02X ", ord($testName[$i]));
    }
    echo "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td><strong>Contact</strong></td>";
    echo "<td style='font-family:monospace;'>" . htmlspecialchars($testContact) . "</td>";
    echo "<td style='font-size:10px;'>";
    for ($i = 0; $i < strlen($testContact); $i++) {
        echo sprintf("%02X ", ord($testContact[$i]));
    }
    echo "</td>";
    echo "</tr>";
    echo "</table>";
    
    // Show how it's cleaned (same as login.php)
    $cleanContact = trim($testContact);
    $cleanContact = preg_replace('/[\s\n\r\t]+/', '', $cleanContact);
    $cleanContact = preg_replace('/[\x00-\x1F\x7F]/u', '', $cleanContact);
    $cleanContactLower = strtolower($cleanContact);
    
    echo "<h4>2. After Cleaning (What Login Script Uses):</h4>";
    echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
    echo "<tr><th>Original</th><th>Cleaned</th><th>Lowercase</th></tr>";
    echo "<tr>";
    echo "<td style='font-family:monospace;background:#fee;'>" . htmlspecialchars($testContact) . "</td>";
    echo "<td style='font-family:monospace;background:#efe;'>" . htmlspecialchars($cleanContact) . "</td>";
    echo "<td style='font-family:monospace;background:#efe;font-weight:bold;'>" . htmlspecialchars($cleanContactLower) . "</td>";
    echo "</tr>";
    echo "</table>";
    
    // Search database - try exact match first
    $stmt = $conn->prepare("SELECT id, name, email, phone FROM voters WHERE LOWER(name) = LOWER(?)");
    $stmt->bind_param("s", $testName);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "<h4>3. Database Records Found for Name: \"" . htmlspecialchars($testName) . "\"</h4>";
    
    if ($result->num_rows == 0) {
        // No exact match - try fuzzy matching
        echo "<p style='color:orange;'><strong>⚠️ NO EXACT MATCH - Searching for similar names...</strong></p>";
        
        // Try with extra spaces removed
        $cleanName = preg_replace('/\s+/', ' ', trim($testName));
        $stmt->close();
        $stmt = $conn->prepare("SELECT id, name, email, phone FROM voters WHERE REPLACE(LOWER(name), '  ', ' ') = LOWER(?)");
        $stmt->bind_param("s", $cleanName);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            // Try partial match
            $stmt->close();
            $likeName = '%' . $testName . '%';
            $stmt = $conn->prepare("SELECT id, name, email, phone FROM voters WHERE name LIKE ? LIMIT 10");
            $stmt->bind_param("s", $likeName);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 0) {
                echo "<p style='color:red;'><strong>❌ NO SIMILAR NAMES FOUND!</strong></p>";
                echo "<p>Try: <a href='search_voters.php?search=" . urlencode($testName) . "'>Search for \"" . htmlspecialchars($testName) . "\"</a></p>";
                $stmt->close();
                $conn->close();
                echo "<br><p><a href='repair_voters.php'>Back to Repair Center</a></p>";
                exit;
            } else {
                echo "<div style='background:#fff3cd;padding:15px;border:2px solid orange;margin:10px 0;'>";
                echo "<p><strong>🔍 Found similar names (partial match):</strong></p>";
                echo "</div>";
            }
        } else {
            echo "<div style='background:#d1ecf1;padding:15px;border:2px solid #0c5460;margin:10px 0;'>";
            echo "<p><strong>✓ Found match with cleaned spacing!</strong></p>";
            echo "</div>";
        }
    } else {
        echo "<table border='1' cellpadding='8' style='border-collapse:collapse;width:100%;'>";
        echo "<tr style='background:#2563eb;color:white;'>";
        echo "<th>ID</th><th>Name in DB</th><th>Name Match?</th><th>DB Email</th><th>Cleaned DB Email</th><th>DB Phone</th><th>Cleaned DB Phone</th><th>Login Works?</th></tr>";
        
        $matchFound = false;
        
        while ($voter = $result->fetch_assoc()) {
            // Check if names actually match
            $dbName = $voter['name'];
            $nameMatch = (strtolower(trim($testName)) === strtolower(trim($dbName)));
            $nameCleanMatch = (strtolower(preg_replace('/\s+/', '', $testName)) === strtolower(preg_replace('/\s+/', '', $dbName)));
            $voterEmail = trim($voter['email'] ?? '');
            $voterPhone = trim($voter['phone'] ?? '');
            
            // Clean database email (same as login.php)
            $dbEmail = preg_replace('/[\s\n\r\t]+/', '', $voterEmail);
            $dbEmail = preg_replace('/[\x00-\x1F\x7F]/u', '', $dbEmail);
            $dbEmailLower = strtolower($dbEmail);
            
            // Clean database phone
            $dbPhone = preg_replace('/[\s\n\r\t]+/', '', $voterPhone);
            $dbPhone = preg_replace('/[\x00-\x1F\x7F]/u', '', $dbPhone);
            
            // Check if matches
            $emailMatch = ($cleanContactLower === $dbEmailLower && !empty($dbEmailLower));
            $phoneMatch = ($cleanContact === $dbPhone && !empty($dbPhone));
            $anyMatch = $emailMatch || $phoneMatch;
            
            if ($anyMatch) $matchFound = true;
            
            $rowColor = $anyMatch ? '#d4edda' : '#fff';
            
            echo "<tr style='background:$rowColor;'>";
            echo "<td>{$voter['id']}</td>";
            echo "<td style='font-family:monospace;'>" . htmlspecialchars($dbName) . "</td>";
            echo "<td style='text-align:center;'>";
            if ($nameMatch) {
                echo "✅ Exact";
            } elseif ($nameCleanMatch) {
                echo "⚠️ Similar<br><small>(spacing diff)</small>";
            } else {
                echo "❌ Different";
            }
            echo "</td>";
            echo "<td style='font-family:monospace;font-size:11px;'>" . htmlspecialchars($voterEmail) . "</td>";
            echo "<td style='font-family:monospace;font-weight:bold;'>" . htmlspecialchars($dbEmailLower) . "</td>";
            echo "<td style='font-family:monospace;'>" . htmlspecialchars($voterPhone) . "</td>";
            echo "<td style='font-family:monospace;font-weight:bold;'>" . htmlspecialchars($dbPhone) . "</td>";
            echo "<td style='font-size:18px;text-align:center;'>";
            if ($anyMatch && $nameMatch) {
                echo "✅ <strong>YES</strong>";
                if ($emailMatch) echo "<br><small>Email match</small>";
                if ($phoneMatch) echo "<br><small>Phone match</small>";
            } elseif ($anyMatch && !$nameMatch) {
                echo "⚠️ <strong>PARTIAL</strong><br><small>Name differs</small>";
            } else {
                echo "❌ NO";
            }
            echo "</td>";
            echo "</tr>";
            
            // Show comparison details
            if (!$anyMatch || !$nameMatch) {
                echo "<tr style='background:#fff3cd;'>";
                echo "<td colspan='8'>";
                echo "<small><strong>Debug Info:</strong><br>";
                
                if (!$nameMatch) {
                    echo "⚠️ <strong>Name issue:</strong><br>";
                    echo "You typed: <code>" . htmlspecialchars($testName) . "</code><br>";
                    echo "In database: <code>" . htmlspecialchars($dbName) . "</code><br>";
                    if ($nameCleanMatch) {
                        echo "✓ Names match when spaces removed - <strong style='color:green;'>Use exact spacing from database!</strong><br>";
                    }
                    echo "<br>";
                }
                
                if (!empty($voterEmail)) {
                    echo "Email comparison:<br>";
                    echo "Your input (cleaned): <code>" . htmlspecialchars($cleanContactLower) . "</code><br>";
                    echo "Database (cleaned): <code>" . htmlspecialchars($dbEmailLower) . "</code><br>";
                    echo "Match? " . ($cleanContactLower === $dbEmailLower ? "YES" : "NO") . "<br>";
                }
                if (!empty($voterPhone)) {
                    echo "Phone comparison:<br>";
                    echo "Your input (cleaned): <code>" . htmlspecialchars($cleanContact) . "</code><br>";
                    echo "Database (cleaned): <code>" . htmlspecialchars($dbPhone) . "</code><br>";
                    echo "Match? " . ($cleanContact === $dbPhone ? "YES" : "NO");
                }
                echo "</small>";
                echo "</td>";
                echo "</tr>";
            }
        }
        
        echo "</table>";
        
        // Final verdict
        echo "<div style='margin:20px 0;padding:20px;border-radius:10px;border:3px solid ";
        echo $matchFound ? "green;background:#d4edda;" : "red;background:#f8d7da;";
        echo "'>";
        echo "<h3 style='margin:0;'>";
        if ($matchFound) {
            echo "✅ <span style='color:green;'>LOGIN WILL SUCCEED!</span>";
        } else {
            echo "❌ <span style='color:red;'>LOGIN WILL FAIL!</span>";
        }
        echo "</h3>";
        if (!$matchFound) {
            echo "<p><strong>Reason:</strong> The email/phone you entered doesn't match the database after cleaning.</p>";
            echo "<p><strong>Fix:</strong> Either update the database email/phone, or ensure you're typing it exactly as stored.</p>";
        }
        echo "</div>";
    }
    
    $stmt->close();
    echo "</div>";
}

$conn->close();

echo "<br><p><a href='repair_voters.php'>Back to Repair Center</a></p>";
?>
