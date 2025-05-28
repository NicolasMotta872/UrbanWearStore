<?php
// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_password = ''; // coloque a senha do seu root, se houver
$db_name = 'urbanwear_db';
$db_port = 3307;  // porta do seu MySQL no XAMPP

// Create database connection
$conn = new mysqli($db_host, $db_user, $db_password, $db_name, $db_port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: (" . $conn->connect_errno . ") " . $conn->connect_error);
}

// Set character set
$conn->set_charset("utf8mb4");
?>
