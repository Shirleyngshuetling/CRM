<?php
// First set proper content type
header('Content-Type: application/json');

// Include required files
require 'config_session.php';
require_once 'check_session.php';
require "db.php";
require "add_reminders_model.php";
require "add_reminders_contr.php";

// Check for AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

php_check_session(); // Check if user session is valid

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Retrieve form data
    $creator_user_id = $_SESSION["user_id"];
    $customer_lead_id = $_POST["customer_lead_id"];
    $reminder_date = $_POST["reminderDate"];
    $notes = $_POST["reminderNotes"];
    $interaction_type_id = $_POST["interactionType"];

    // Initialize error array
    $errors = [];
    
    // Store valid data to repopulate form if needed
    $validData = [
        "customer_lead_id" => $customer_lead_id,
        "notes" => $notes,
        "interactionType" => $interaction_type_id,
        "reminder_date" => $reminder_date,
    ];

    // First, check if any field is empty (prevents unnecessary checks)
    if (is_input_empty($customer_lead_id, $interaction_type_id, $reminder_date)) {
        $errors["empty_input"] = "Fill in all fields!";
    }
    // Then, check if reminder date is invalid (past date)
    else if (is_date_invalid($reminder_date)){
        $errors["invalid_date"] = "Date cannot be in the past!";
        $validData["reminder_date"] = ''; // Clear invalid date
    }

    // If there are any errors
    if (!empty($errors)) {
        // If AJAX request, return JSON error
        if ($isAjax) {
            echo json_encode(["success" => false, "errors" => $errors]);
            exit;
        } else {
            // For regular form submission
            handle_reminder_insertion_errors($validData, $errors);
            exit;
        }
    }

    // Try inserting the reminder
    if (insertReminder($conn, $creator_user_id, $customer_lead_id, $reminder_date, $notes, $interaction_type_id)) {
        // If AJAX request, return JSON success
        if ($isAjax) {
            echo json_encode(["success" => true]);
            exit;
        } else {
            // For regular form submission
            handle_reminder_insertion_success();
            exit;
        }
    } else {
        // If failed to insert, return error
        if ($isAjax) {
            echo json_encode(["success" => false, "error" => "Error inserting reminder."]);
            exit;
        } else {
            echo "Error: Failed to add Reminder.";
            exit;
        }
    }

    // Close database connection
    $conn->close();
}
else {
    // If request method is not POST
    if ($isAjax) {
        echo json_encode(["success" => false, "error" => "Invalid request method"]);
        exit;
    } else {
        // For regular browser access, redirect to reminder form
        header("Location:../add_reminder.php");
        exit;
    }
}
?>
