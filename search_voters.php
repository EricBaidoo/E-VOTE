<?php
/**
 * Search for voter names - find similar or partial matches
 */

require_once 'includes/db_connect.php';

echo "<h2>🔍 Search Voter Names</h2>";

// Search form
echo '<form method="post" style="background:#f0f0f0;padding:20px;margin:20px 0;border-radius:10px;">';
echo '<div style="margin:10px 0;">';
echo '<label><strong>Search for Name:</strong></label><br>';
echo '<input type="text" name="search_name" value="' . htmlspecialchars($_POST['search_name'] ?? '') . '" style="padding:10px;width:400px;font-size:16px;" placeholder="Enter full or partial name">';
echo '</div>';
echo '<button type="submit" style="background:#007bff;color:white;padding:12px 30px;border:none;cursor:pointer;font-size:16px;border-radius:5px;">Search</button>';
echo '</form>';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search_name'])) {
    $searchName = trim($_POST['search_name']);
    
    echo "<h3>Search Results for: \"" . htmlspecialchars($searchName) . "\"</h3>";
    
    // Try different search methods
    
    // 1. Exact match (case-insensitive)
    $stmt = $conn->prepare("SELECT id, name, email, phone FROM voters WHERE LOWER(name) = LOWER(?) LIMIT 10");
    $stmt->bind_param("s", $searchName);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<div style='background:#d4edda;padding:15px;border:2px solid green;border-radius:5px;margin:10px 0;'>";
        echo "<h4 style='color:green;'>✅ Exact Match Found!</h4>";
        echo "<table border='1' cellpadding='8' style='border-collapse:collapse;width:100%;'>";
        echo "<tr style='background:#28a745;color:white;'><th>ID</th><th>Name</th><th>Email</th><th>Phone</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td><strong>" . htmlspecialchars($row['name']) . "</strong></td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
        $stmt->close();
    } else {
        $stmt->close();
        
        echo "<p style='color:orange;'><strong>⚠️ No exact match found. Searching for similar names...</strong></p>";
        
        // 2. Partial match (contains search term)
        $likeTerm = '%' . $searchName . '%';
        $stmt = $conn->prepare("SELECT id, name, email, phone FROM voters WHERE name LIKE ? ORDER BY name LIMIT 20");
        $stmt->bind_param("s", $likeTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo "<div style='background:#fff3cd;padding:15px;border:2px solid orange;border-radius:5px;margin:10px 0;'>";
            echo "<h4 style='color:#856404;'>🔍 Similar Names Found ({$result->num_rows}):</h4>";
            echo "<table border='1' cellpadding='8' style='border-collapse:collapse;width:100%;'>";
            echo "<tr style='background:#ffc107;'><th>ID</th><th>Name in Database</th><th>Email</th><th>Phone</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td><strong>" . htmlspecialchars($row['name']) . "</strong></td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "</div>";
        } else {
            // 3. Try searching by first word
            $firstWord = explode(' ', $searchName)[0];
            $likeFirst = $firstWord . '%';
            $stmt->close();
            $stmt = $conn->prepare("SELECT id, name, email, phone FROM voters WHERE name LIKE ? ORDER BY name LIMIT 20");
            $stmt->bind_param("s", $likeFirst);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo "<div style='background:#d1ecf1;padding:15px;border:2px solid #0c5460;border-radius:5px;margin:10px 0;'>";
                echo "<h4 style='color:#0c5460;'>🔍 Names starting with \"" . htmlspecialchars($firstWord) . "\" ({$result->num_rows}):</h4>";
                echo "<table border='1' cellpadding='8' style='border-collapse:collapse;width:100%;'>";
                echo "<tr style='background:#17a2b8;color:white;'><th>ID</th><th>Name in Database</th><th>Email</th><th>Phone</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['id']}</td>";
                    echo "<td><strong>" . htmlspecialchars($row['name']) . "</strong></td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
                echo "</div>";
            } else {
                echo "<div style='background:#f8d7da;padding:15px;border:2px solid red;border-radius:5px;margin:10px 0;'>";
                echo "<h4 style='color:red;'>❌ No matches found!</h4>";
                echo "<p>The name \"" . htmlspecialchars($searchName) . "\" doesn't exist in the database.</p>";
                echo "<p><strong>Suggestions:</strong></p>";
                echo "<ul>";
                echo "<li>Check spelling carefully</li>";
                echo "<li>Try searching by first name only</li>";
                echo "<li>Try searching by last name only</li>";
                echo "<li>Check if the voter was actually imported</li>";
                echo "</ul>";
                echo "</div>";
            }
        }
        $stmt->close();
    }
    
    // Show total voters for reference
    $total = $conn->query("SELECT COUNT(*) as count FROM voters")->fetch_assoc()['count'];
    echo "<p style='margin-top:20px;'><strong>Total voters in database:</strong> $total</p>";
}

// Also show option to browse all names
echo "<div style='background:#e7f3ff;padding:15px;margin:20px 0;border-radius:5px;'>";
echo "<h4>💡 Quick Actions:</h4>";
echo "<ul>";
echo "<li><a href='?show_all=1'>Show all voter names (first 100)</a></li>";
echo "<li><a href='admin/voters.php'>View full voters list (Admin)</a></li>";
echo "<li><a href='debug_login.php'>Debug a specific login</a></li>";
echo "</ul>";
echo "</div>";

// Show all names if requested
if (isset($_GET['show_all'])) {
    echo "<h3>First 100 Voters in Database:</h3>";
    echo "<table border='1' cellpadding='8' style='border-collapse:collapse;width:100%;font-size:13px;'>";
    echo "<tr style='background:#2563eb;color:white;'><th>ID</th><th>Name</th><th>Email</th><th>Phone</th></tr>";
    
    $result = $conn->query("SELECT id, name, email, phone FROM voters ORDER BY id LIMIT 100");
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td style='font-family:monospace;font-size:11px;'>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

$conn->close();

echo "<br><p><a href='repair_voters.php'>Back to Repair Center</a></p>";
?>
