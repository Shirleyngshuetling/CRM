<?php


$host = "localhost";
$dbusername = "root"; // Database username
$dbpassword = "root"; // Database password for mac is root, for window is ""
$database = "CRM";

// Create object to establish database connection
$conn = new mysqli($host, $dbusername, $dbpassword, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
