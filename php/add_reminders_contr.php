<?php

// Check if required fields are empty
function is_input_empty($customer_lead_id, $interaction_type_id, $reminder_date) {
    return empty($customer_lead_id) || empty($interaction_type_id) || empty($reminder_date);
}

// Validate reminder date (cannot be in the past)
function is_date_invalid($reminder_date) {
    try {
        $input_date = new DateTime($reminder_date);
        $current_date = new DateTime();
        
        // Compare date portions only (ignore time)
        return $input_date->format('Y-m-d') < $current_date->format('Y-m-d'); // Date cannot be before current date
    } catch (Exception $e) {
        return true; // Invalid date format
    }
}

// Handle reminder insertion errors
function handle_reminder_insertion_errors($validData, $errors) {
    $_SESSION["errors_reminder_insertion"] = $errors;

    // Store only valid data in session for repopulation
    $_SESSION["reminder_insertion_data"] = [
        "customer_lead_id" => isset($errors["empty_customer"]) ? '' : $validData["customer_lead_id"],
        "notes" => $validData["notes"], // Always keep notes
        "interactionType" => isset($errors["empty_type"]) ? '' : $validData["interactionType"],
        "reminder_date" => isset($errors["invalid_date"]) ? '' : $validData["reminder_date"]
    ];
    
    // Redirect back to form
    header("Location: ../add_reminder.php");    
    exit();
}

// Handle reminder insertion success
function handle_reminder_insertion_success() {
    $_SESSION["success"] = "Reminder added successfully!";
    header("Location: ../add_reminder.php");
    exit();
}
