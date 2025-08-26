<?php
require 'config_session.php'; // Start session and configure session settings
require_once 'check_session.php'; // Check if the session is active
require "db.php"; // Include database connection

php_check_session(); // Ensure the user is authenticated

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SESSION['role_type_id'] == 1) { // Only allow POST request and Admin role (role_type_id = 1)

    $userId = $_SESSION["user_id"]; // Get current user's ID
    $roleId = $_SESSION["role_type_id"]; // Get current user's role ID
    
    // Prepare query to fetch all users along with their role names
    $query = $conn->prepare("SELECT u.user_id as 'user_id', u.user_name as 'user_name', r.role_type_name as 'role_type_name', u.email as 'email', u.user_status as 'user_status' 
                            FROM Users u
                            LEFT JOIN Role_Type r ON u.role_type_id = r.role_type_id");
    
    if (!$query) {
        // Return an error if query preparation failed
        echo json_encode(["error" => "Query preparation failed", "message" => $conn->error]);
        exit();
    }

    $query->execute(); // Execute the prepared query
    $result = $query->get_result(); // Get the result set
    $users = []; // Initialize an array to store users

    while ($row = $result->fetch_assoc()) {
        $users[] = $row; // Add each user row to the array
    }

    $query->close(); // Close the prepared statement
    $conn->close(); // Close the database connection

    header('Content-Type: application/json'); // Set response type to JSON
    echo json_encode($users, JSON_PRETTY_PRINT); // Output users as formatted JSON
}
else {
    // Redirect to dashboard if not POST or not Admin
    header("Location:../dashboard_main.php");
}
?>
