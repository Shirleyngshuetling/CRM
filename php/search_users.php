<?php
require 'config_session.php';
require_once 'check_session.php';
require 'db.php';

php_check_session();
// Handle only POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ensure JSON response
    header('Content-Type: application/json');
    ob_start(); // Capture unexpected output

    $searchTerm = trim($_POST['userSearch'] ?? ""); // Default to empty string
    $userName = strtolower($searchTerm);
    
    // Prepare SQL Query based on user role
    if ($userName === "") {
        $query = $conn->prepare("SELECT u.user_id as 'user_id', u.user_name as 'user_name', r.role_type_name as 'role_type_name', u.email as 'email', u.user_status as 'user_status' FROM Users u
                        LEFT JOIN Role_Type r
                        ON u.role_type_id = r.role_type_id");
    } else {
        error_log("Search input: " . $userName);
        $query = $conn->prepare("SELECT u.user_id as 'user_id', u.user_name as 'user_name', r.role_type_name as 'role_type_name', u.email as 'email', u.user_status as 'user_status' FROM Users u
                                        LEFT JOIN Role_Type r
                                        ON u.role_type_id = r.role_type_id
                                        WHERE u.user_id LIKE ? OR u.user_name LIKE ? OR r.role_type_name LIKE ? OR LOWER(u.email) LIKE ? OR CAST(u.user_status AS CHAR) LIKE ?");
        $likeUserName = "%$userName%";
        $query->bind_param("sssss", $likeUserName, $likeUserName, $likeUserName, $likeUserName, $likeUserName);
    }

    // Check if query preparation failed
    if (!$query) {
        echo json_encode(["error" => "Query preparation failed", "message" => $conn->error]);
        exit();
    }

    // Execute query and fetch results
    $query->execute();
    $result = $query->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);

    // Close database connections
    $query->close();
    $conn->close();

    // Capture unexpected output before sending JSON
    $output = ob_get_clean();
    if (!empty($output)) {
        echo json_encode(["error" => "Unexpected Output", "message" => $output]);
        exit();
    }

    // Return users as JSON
    echo json_encode($users);
}
else{
    header("Location: ../dashboard_main.php"); // lead user back to dashboard
    exit();
}