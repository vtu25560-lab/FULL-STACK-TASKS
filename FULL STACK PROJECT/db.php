<?php
// Database configuration for XAMPP
$host = "localhost";
$user = "root";
$password = ""; // Default XAMPP password is empty
$dbname = "campus_portal"; // Your database name

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 for special character support
$conn->set_charset("utf8mb4");
?>