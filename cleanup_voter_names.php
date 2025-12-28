<?php
/**
 * Clean up voter names - remove question marks and special characters
 */

require_once 'includes/db_connect.php';

echo "<h2>Cleaning Voter Names</h2>";
echo "<p>Removing question marks and special characters...</p>";

// Get all voters
$result = $conn->query("SELECT id, name FROM voters");

$updated = 0;
$skipped = 0;

echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
echo "<tr style='background:#2563eb;color:white;'><th>ID</th><th>Old Name</th><th>New Name</th><th>Status</th></tr>";

while ($row = $result->fetch_assoc()) {
    $oldName = $row['name'];
    $newName = $oldName;
    
    // Remove question marks from the beginning
    $newName = ltrim($newName, '?');
    
    // Remove any BOM characters
    $newName = str_replace("\xEF\xBB\xBF", '', $newName);
    
    // Remove invisible characters and trim
    $newName = preg_replace('/[\x00-\x1F\x7F]/u', '', $newName);
    $newName = trim($newName);
    
    // Check if name changed
    if ($oldName !== $newName && !empty($newName)) {
        $stmt = $conn->prepare("UPDATE voters SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $newName, $row['id']);
        $stmt->execute();
        $stmt->close();
        
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td style='color:red;'>" . htmlspecialchars($oldName) . "</td>";
        echo "<td style='color:green;'>" . htmlspecialchars($newName) . "</td>";
        echo "<td><span style='color:green;'>✓ Updated</span></td>";
        echo "</tr>";
        
        $updated++;
    } else {
        $skipped++;
    }
}

echo "</table>";

echo "<h3 style='color:green;'>✓ Cleanup Complete!</h3>";
echo "<ul>";
echo "<li><strong>Names updated:</strong> $updated</li>";
echo "<li><strong>Names unchanged:</strong> $skipped</li>";
echo "</ul>";

$conn->close();

echo "<p><a href='admin/voters.php'>View Voters List</a></p>";
?>
