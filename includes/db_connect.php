<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cafe_ordering_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to ensure proper handling of all characters
$conn->set_charset("utf8mb4");
?>
