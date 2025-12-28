<?php
// Database Connection File
$db_host = 'localhost';
$db_username = 'root';
$db_password = 'root'; // Update this to match your MySQL Workbench password
$db_name = 'evote_db';

// Create connection
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8");

// Function to escape user inputs
function escape_input($data) {
    global $conn;
    return $conn->real_escape_string($data);
}

// Session management
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
