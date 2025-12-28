<?php
// Database Connection File
// Auto-detect environment (local vs online)
$is_local = (
    $_SERVER['SERVER_NAME'] === 'localhost' || 
    $_SERVER['SERVER_ADDR'] === '127.0.0.1' || 
    strpos($_SERVER['SERVER_NAME'], 'localhost') !== false
);

if ($is_local) {
    // Local XAMPP credentials
    $db_host = 'localhost';
    $db_username = 'root';
    $db_password = 'root';
    $db_name = 'evote_db';
} else {
    // Online Hostinger credentials
    $db_host = 'localhost';
    $db_username = 'u420775839_evote_db_user';
    $db_password = 'Eric0056';
    $db_name = 'u420775839_evote_db';
}

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
