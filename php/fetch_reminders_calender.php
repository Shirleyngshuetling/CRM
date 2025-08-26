<?php
require "config_session.php"; // Start session and load session settings
require_once 'check_session.php'; // Check if session is valid
require "db.php"; // Include database connection

php_check_session(); // Ensure the user is authenticated

// Set response headers for JSON and allow all origins (CORS)
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    

    $userId = $_SESSION["user_id"];
    $roleId = $_SESSION["role_type_id"];

    if ($roleId == 1) {  // Admin role
        // Admin can view all reminders
        $query = $conn->prepare("SELECT U.user_name AS user_name, R.reminder_id As id, R.reminder_date AS reminder_date, IT.interaction_type_name AS interaction_type, CT.customer_type_name, CL.name, CL.company, R.notes AS notes
                                FROM Reminder AS R
                                LEFT JOIN Customers_Leads AS CL ON R.customer_lead_id = CL.customer_lead_id
                                LEFT JOIN Customer_Type AS CT ON CL.customer_type = CT.customer_type_id
                                LEFT JOIN Interaction_Type AS IT ON R.interaction_type_id = IT.interaction_type_id
                                LEFT JOIN Users AS U ON U.user_id = R.creator_user_id");
    } else { // Sales Role
        // Sales users can only view their own reminders
        $query = $conn->prepare("SELECT U.user_name AS user_name, R.reminder_id As id, R.reminder_date AS reminder_date, IT.interaction_type_name AS interaction_type, CT.customer_type_name, CL.name, CL.company, R.notes AS notes
                                FROM Reminder AS R
                                LEFT JOIN Customers_Leads AS CL ON R.customer_lead_id = CL.customer_lead_id
                                LEFT JOIN Customer_Type AS CT ON CL.customer_type = CT.customer_type_id
                                LEFT JOIN Interaction_Type AS IT ON R.interaction_type_id = IT.interaction_type_id
                                LEFT JOIN Users AS U ON U.user_id = R.creator_user_id
                                WHERE R.creator_user_id =?");
        $query->bind_param("i", $userId);
    }

    if (!$query) {
        // If query preparation fails, return an error as JSON
        echo json_encode(["error" => "Query preparation failed", "message" => $conn->error]);
        exit();
    }

    $query->execute(); // Execute the query
    $result = $query->get_result(); // Get result set
    $reminders = [];

    while ($row = $result->fetch_assoc()) {
        $reminders[] = $row;// Append each reminder to array
    }

    // Format for FullCalendar
    $events = [];
    foreach ($reminders as $reminder) {
        $events[] = [
            'id' => $reminder['id'],
            'title' => $reminder['interaction_type'] , // Display interaction type as event title
            'start' => $reminder['reminder_date'], // Event start date
            'color' => getColorForInteraction($reminder['interaction_type']), // Color based on interaction type
            'extendedProps' => [// Additional properties for FullCalendar
                'reminder_date' => $reminder['reminder_date'],
                'customer_type_name' => $reminder['customer_type_name'],
                'name' => $reminder['name'],
                'company' => $reminder['company'],
                'notes' => $reminder['notes'],
                'user_name' => $reminder['user_name']
            ]
        ];
    }
    $query->close();
    $conn->close();

    echo json_encode($events);
    exit();
}
else{
    header("Location:../dashboard_main.php");
}

// Helper function to assign colors based on interaction type
function getColorForInteraction($type) {
    $colors = [
        'Meeting' => '#f4b298',
        'Phone Call' => '#96d3ff',
        'Email' => '#dbdc72',
        'Video Conference' => '#eb89ac',
        'Social Media' => '#b498f4',
        'Default' => '#6b7280'
    ];
    return $colors[$type] ?? $colors['Default'];// Return color or default color
}




