<?php
require "config_session.php"; // Start session and load session settings
require_once 'check_session.php'; // Check if session is valid
require "db.php"; // Database connection

php_check_session(); // Ensure the user is authenticated

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Handle only POST requests

    $userId = $_SESSION["user_id"];
    $roleId = $_SESSION["role_type_id"];

    if ($roleId == 1) { // Admin role
        // Admins can see all interaction histories
        $query = $conn->prepare("SELECT IH.interaction_history_id, CT.customer_type_name, U.user_name, CL.name, CL.company, IH.interaction_date, IH.interaction_details, IT.interaction_type_name 
                                FROM Interaction_History AS IH 
                                LEFT JOIN Users AS U ON IH.creator_user_id = U.user_id
                                LEFT JOIN Customers_Leads AS CL ON IH.customer_lead_id = CL.customer_lead_id
                                LEFT JOIN Customer_Type AS CT ON CL.customer_type = CT.customer_type_id
                                LEFT JOIN Interaction_Type AS IT ON IH.interaction_type_id = IT.interaction_type_id");
    } else { // Sales Role
        // Sales users can see only their own interaction histories
        $query = $conn->prepare("SELECT IH.interaction_history_id, CT. customer_type_name, U.user_name, CL.name, CL.company, IH.interaction_date, IH.interaction_details, IT.interaction_type_name 
                                FROM Interaction_History AS IH 
                                LEFT JOIN Users AS U ON IH.creator_user_id = U.user_id
                                LEFT JOIN Customers_Leads AS CL ON IH.customer_lead_id = CL.customer_lead_id
                                LEFT JOIN Customer_Type AS CT ON CL.customer_type = CT.customer_type_id
                                LEFT JOIN Interaction_Type AS IT ON IH.interaction_type_id = IT.interaction_type_id
                                WHERE U.user_id = ?");
        $query->bind_param("i", $userId);
    }
    if (!$query) {
        // If query preparation fails, return JSON error
        echo json_encode(["error" => "Query preparation failed", "message" => $conn->error]);
        exit();
    }

    $query->execute();// Execute the query
    $result = $query->get_result();// Get the result set
    $interaction_histories = [];

    while ($row = $result->fetch_assoc()) {
        $interaction_histories[] = $row; // Append each record to the array
    }

    $query->close(); // Close the prepared statement
    $conn->close(); // Close the database connection

    // Output the data as JSON
    header('Content-Type: application/json');
    echo json_encode($interaction_histories, JSON_PRETTY_PRINT);
    exit();
}
else{
    // Redirect non-POST requests back to the dashboard
    header("Location: ../dashboard_main.php");
}





