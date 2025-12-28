<?php
/**
 * Export Database to SQL File for Online Hosting
 * Run this locally to create a backup SQL file
 */

// Database credentials (local)
$host = 'localhost';
$username = 'root';
$password = 'root';
$database = 'evote_db';

$outputFile = 'evote_db_export.sql';

echo "<h2>Database Export</h2>";
echo "<p>Exporting database: <strong>$database</strong></p>";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
}

$conn->set_charset("utf8mb4");

// Start SQL content
$sql = "-- E-VOTE Database Export\n";
$sql .= "-- Exported on: " . date('Y-m-d H:i:s') . "\n\n";
$sql .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
$sql .= "SET time_zone = \"+00:00\";\n\n";

// Create database
$sql .= "CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n";
$sql .= "USE `$database`;\n\n";

// Get all tables
$tables = array();
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

foreach ($tables as $table) {
    echo "<p>Exporting table: <strong>$table</strong></p>";
    
    // Drop table statement
    $sql .= "-- Table structure for `$table`\n";
    $sql .= "DROP TABLE IF EXISTS `$table`;\n";
    
    // Create table statement
    $result = $conn->query("SHOW CREATE TABLE `$table`");
    $row = $result->fetch_row();
    $sql .= $row[1] . ";\n\n";
    
    // Table data
    $result = $conn->query("SELECT * FROM `$table`");
    $numRows = $result->num_rows;
    
    if ($numRows > 0) {
        $sql .= "-- Dumping data for table `$table` ($numRows rows)\n";
        
        while ($row = $result->fetch_assoc()) {
            $columns = array_keys($row);
            $values = array_values($row);
            
            // Escape values
            foreach ($values as $key => $value) {
                if ($value === null) {
                    $values[$key] = 'NULL';
                } else {
                    $values[$key] = "'" . $conn->real_escape_string($value) . "'";
                }
            }
            
            $sql .= "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");\n";
        }
        $sql .= "\n";
    }
}

$conn->close();

// Write to file
if (file_put_contents($outputFile, $sql)) {
    $fileSize = round(filesize($outputFile) / 1024, 2);
    echo "<h3 style='color:green;'>✓ Export Successful!</h3>";
    echo "<p><strong>File created:</strong> $outputFile</p>";
    echo "<p><strong>File size:</strong> {$fileSize} KB</p>";
    echo "<p><a href='$outputFile' download style='background:#2563eb;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;display:inline-block;'>Download Export File</a></p>";
} else {
    echo "<p style='color:red;'>Failed to write export file</p>";
}

echo "<hr>";
echo "<h3>Next Steps for Online Hosting:</h3>";
echo "<ol>";
echo "<li>Download the exported SQL file above</li>";
echo "<li>Log into your hosting control panel (cPanel/Plesk)</li>";
echo "<li>Go to <strong>phpMyAdmin</strong></li>";
echo "<li>Create a new database (e.g., <code>evote_db</code>)</li>";
echo "<li>Click <strong>Import</strong> and upload the SQL file</li>";
echo "<li>Update <code>includes/db_connect.php</code> with your online database credentials</li>";
echo "</ol>";
?>
