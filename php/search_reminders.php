<?php
require 'config_session.php';
require_once 'check_session.php';
require 'db.php';

// First check session before any output
php_check_session();

// Handle only POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ensure JSON response
    header('Content-Type: application/json');
    ob_start(); // Capture unexpected output

    // Retrieve session and input values
    $userId = $_SESSION['user_id'];
    $roleTypeId = $_SESSION['role_type_id'];
    $reminderSearch = trim($_POST['reminderSearch'] ?? ""); // Default to empty string
    
    // Prepare SQL Query based on user role
    if ($roleTypeId == 1) { // Admin: Search across all reminders
        if ($reminderSearch === "") {
            $query = $conn->prepare("SELECT R.reminder_date AS reminder_date, 
                                           IT.interaction_type_name AS interaction_type_name, 
                                           CT.customer_type_name AS customer_type_name, 
                                           CL.name AS name, 
                                           CL.company AS company, 
                                           U.user_name AS user_name, 
                                           R.notes AS notes
                                    FROM Reminder AS R
                                    LEFT JOIN Customers_Leads AS CL ON R.customer_lead_id = CL.customer_lead_id
                                    LEFT JOIN Customer_Type AS CT ON CL.customer_type = CT.customer_type_id
                                    LEFT JOIN Interaction_Type AS IT ON R.interaction_type_id = IT.interaction_type_id
                                    LEFT JOIN Users AS U ON R.creator_user_id = U.user_id");
        } else {
            $query = $conn->prepare("SELECT R.reminder_date AS reminder_date, 
                                           IT.interaction_type_name AS interaction_type_name, 
                                           CT.customer_type_name AS customer_type_name, 
                                           CL.name AS name, 
                                           CL.company AS company, 
                                           U.user_name AS user_name, 
                                           R.notes AS notes
                                    FROM Reminder AS R
                                    LEFT JOIN Customers_Leads AS CL ON R.customer_lead_id = CL.customer_lead_id
                                    LEFT JOIN Customer_Type AS CT ON CL.customer_type = CT.customer_type_id
                                    LEFT JOIN Interaction_Type AS IT ON R.interaction_type_id = IT.interaction_type_id
                                    LEFT JOIN Users AS U ON R.creator_user_id = U.user_id
                                    WHERE (R.reminder_date LIKE ? OR 
                                          IT.interaction_type_name LIKE ? OR 
                                          CT.customer_type_name LIKE ? OR 
                                          CL.name LIKE ? OR 
                                          CL.company LIKE ? OR 
                                          U.user_name LIKE ? OR 
                                          R.notes LIKE ?)");
            $likeReminderName = "%$reminderSearch%";
            $query->bind_param("sssssss", $likeReminderName, $likeReminderName, $likeReminderName, 
                              $likeReminderName, $likeReminderName, $likeReminderName, $likeReminderName);
        }
    } else { // Sales: Search only own reminders
        if ($reminderSearch === "") {
            $query = $conn->prepare("SELECT R.reminder_date AS reminder_date, 
                                           IT.interaction_type_name AS interaction_type_name, 
                                           CT.customer_type_name AS customer_type_name, 
                                           CL.name AS name, 
                                           CL.company AS company, 
                                           U.user_name AS user_name, 
                                           R.notes AS notes
                                    FROM Reminder AS R
                                    LEFT JOIN Customers_Leads AS CL ON R.customer_lead_id = CL.customer_lead_id
                                    LEFT JOIN Customer_Type AS CT ON CL.customer_type = CT.customer_type_id
                                    LEFT JOIN Interaction_Type AS IT ON R.interaction_type_id = IT.interaction_type_id
                                    LEFT JOIN Users AS U ON R.creator_user_id = U.user_id
                                    WHERE R.creator_user_id = ?");
            $query->bind_param("i", $userId);
        } else {
            $query = $conn->prepare("SELECT R.reminder_date AS reminder_date, 
                                           IT.interaction_type_name AS interaction_type_name, 
                                           CT.customer_type_name AS customer_type_name, 
                                           CL.name AS name, 
                                           CL.company AS company, 
                                           U.user_name AS user_name, 
                                           R.notes AS notes
                                    FROM Reminder AS R
                                    LEFT JOIN Customers_Leads AS CL ON R.customer_lead_id = CL.customer_lead_id
                                    LEFT JOIN Customer_Type AS CT ON CL.customer_type = CT.customer_type_id
                                    LEFT JOIN Interaction_Type AS IT ON R.interaction_type_id = IT.interaction_type_id
                                    LEFT JOIN Users AS U ON R.creator_user_id = U.user_id
                                    WHERE (R.reminder_date LIKE ? OR 
                                          IT.interaction_type_name LIKE ? OR 
                                          CT.customer_type_name LIKE ? OR 
                                          CL.name LIKE ? OR 
                                          CL.company LIKE ? OR 
                                          U.user_name LIKE ? OR 
                                          R.notes LIKE ?) 
                                    AND R.creator_user_id = ?");
            $likeReminderName = "%$reminderSearch%";
            $query->bind_param("sssssssi", $likeReminderName, $likeReminderName, $likeReminderName, 
                              $likeReminderName, $likeReminderName, $likeReminderName, $likeReminderName, $userId);
        }
    }

    // Check if query preparation failed
    if (!$query) {
        echo json_encode(["error" => "Query preparation failed", "message" => $conn->error]);
        exit();
    }

    // Execute query and check for errors
    if (!$query->execute()) {
        echo json_encode(["error" => "Query execution failed", "message" => $query->error]);
        exit();
    }

    $result = $query->get_result();
    $reminders = $result->fetch_all(MYSQLI_ASSOC);

    // Close database connections
    $query->close();
    $conn->close();

    // Capture unexpected output before sending JSON
    $output = ob_get_clean();
    if (!empty($output)) {
        echo json_encode(["error" => "Unexpected Output", "message" => $output]);
        exit();
    }

    // Return reminders as JSON
    echo json_encode($reminders);
    exit();
} else {
    header("Location: ../reminder_list.php"); // lead user back to reminder list page
    exit();
}