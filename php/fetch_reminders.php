<?php
require "config_session.php"; // Start session and load session settings
require_once 'check_session.php'; // Check if session is active and valid
require "db.php"; // Include database connection

php_check_session(); // Ensure user is authenticated

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Handle only POST requests
    

    $userId = $_SESSION["user_id"]; // Get current logged-in user's ID
    $roleId = $_SESSION["role_type_id"]; // Get current user's role

    if ($roleId == 1) {  // Admin role
        // Admins can see all reminders
        $query = $conn->prepare("SELECT R.reminder_date, IT.interaction_type_name, CT.customer_type_name, CL.name, CL.company, U.user_name, R.notes
                                FROM Reminder AS R
                                LEFT JOIN Customers_Leads AS CL ON R.customer_lead_id = CL.customer_lead_id
                                LEFT JOIN Customer_Type AS CT ON CL.customer_type = CT.customer_type_id
                                LEFT JOIN Interaction_Type AS IT ON R.interaction_type_id = IT.interaction_type_id
                                LEFT JOIN Users AS U ON U.user_id = R.creator_user_id");
    } else { // Sales Role
        // Sales users can only see their own reminders
        $query = $conn->prepare("SELECT R.reminder_date, IT.interaction_type_name, CT.customer_type_name, CL.name, CL.company, U.user_name, R.notes
                                FROM Reminder AS R
                                LEFT JOIN Customers_Leads AS CL ON R.customer_lead_id = CL.customer_lead_id
                                LEFT JOIN Customer_Type AS CT ON CL.customer_type = CT.customer_type_id
                                LEFT JOIN Interaction_Type AS IT ON R.interaction_type_id = IT.interaction_type_id
                                LEFT JOIN Users AS U ON U.user_id = R.creator_user_id
                                WHERE R.creator_user_id =?");
        $query->bind_param("i", $userId);
    }

    if (!$query) {
        echo json_encode(["error" => "Query preparation failed", "message" => $conn->error]);
        exit();
    }

    $query->execute();
    $result = $query->get_result();
    $reminders = [];

    while ($row = $result->fetch_assoc()) {
        $reminders[] = $row;
    }

    $query->close();
    $conn->close();

    header('Content-Type: application/json');
    echo json_encode($reminders, JSON_PRETTY_PRINT);
    exit();
}
else{
    header("Location:../dashboard_main.php");
}





