<?php
// Set proper content type for JSON response
header('Content-Type: application/json');

require 'config_session.php';
require_once 'check_session.php';
require "db.php";
require "add_reminders_model.php";
require "add_reminders_contr.php";

// Check for AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

php_check_session();

// Initialize result variable
$result = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $creator_user_id = $_SESSION["user_id"];
    $customer_lead_id = $_POST["customer_lead_id"];
    $reminder_date = $_POST["reminderDate"];
    $notes = $_POST["reminderNotes"];
    $interaction_type_id = $_POST["interactionType"];

    // Error handlers
    $errors = [];
    
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
    // Then, validate reminder date
    else if (is_date_invalid($reminder_date)) {
        $errors["invalid_date"] = "Date cannot be in the past!";
        $validData["reminder_date"] = '';
    }

    if (!empty($errors)) {
        if ($isAjax) {
            $result = ["success" => false, "errors" => $errors];
        } else {
            handle_reminder_insertion_errors($validData, $errors);
        }
    } else {
        if (insertReminder($conn, $creator_user_id, $customer_lead_id, $reminder_date, $notes, $interaction_type_id)) {
            if ($isAjax) {
                $result = ["success" => true];
            } else {
                handle_reminder_insertion_success();
            }
        } else {
            if ($isAjax) {
                $result = ["success" => false, "error" => "Error inserting reminder."];
            } else {
                echo "Error: Failed to add Reminder.";
            }
        }
    }
    
    // Close connection - now this line is reachable
    $conn->close();
    
    // For AJAX requests, return JSON response
    if ($isAjax) {
        echo json_encode($result);
        exit;
    }
} else {
    // If AJAX request, return JSON error for wrong method
    if ($isAjax) {
        echo json_encode(["success" => false, "error" => "Invalid request method"]);
    } else {
        // For regular browser access, redirect to the form
        header("Location:../add_reminder.php");
    }
    exit;
}
?>