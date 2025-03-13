<?php
// config.php - Database configuration
$servername = "localhost";
$username = "root";
$password = ""; // Replace with a secure password in production
$dbname = "bs_trader";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>