<?php

// Check if any of the required input fields are empty
function is_input_empty($customer_lead_id, $interaction_type_id, $interaction_date) {
    return empty($customer_lead_id) || empty($interaction_type_id) || empty($interaction_date);
}

// Validate that the interaction date is not in the future
function is_date_invalid($interaction_date) {
    try {
        $input_date = new DateTime($interaction_date); // Create DateTime object from input
        $current_date = new DateTime(); // Current date

        // Compare only the date parts (ignore time)
        return $input_date->format('Y-m-d') > $current_date->format('Y-m-d');
    } catch (Exception $e) {
        return true; // If exception occurs, date is invalid
    }
}

// Handle errors during interaction history insertion
function handle_interaction_history_insertion_errors($validData, $errors) {
    $_SESSION["errors_interaction_history_insertion"] = $errors; // Store errors in session

    // Store only valid data in session for form repopulation
    $_SESSION["interaction_history_insertion_data"] = [
        "customer_lead_id" => isset($errors["empty_customer"]) ? '' : $validData["customer_lead_id"], // Clear if customer field had an error
        "interactionDetails" => $validData["interactionDetails"], // Always keep interaction details
        "interactionType" => isset($errors["empty_type"]) ? '' : $validData["interactionType"], // Clear if type field had an error
        "interactionHistoryDate" => isset($errors["invalid_date"]) ? '' : $validData["interactionHistoryDate"] // Clear if date was invalid
    ];
    
    header("Location: ../add_interaction.php"); // Redirect back to form
    exit();
}

// Handle success after successfully inserting interaction history
function handle_interaction_history_insertion_success() {
    $_SESSION["success"] = "Interaction History added successfully!"; // Set success message
    header("Location: ../add_interaction.php"); // Redirect back to form
    exit();
}
