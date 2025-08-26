<?php
header('Content-Type: application/json'); // Set content type to JSON

// Include required files
require 'config_session.php';
require_once "check_session.php";
require "db.php";
require "add_interactions_model.php";
require "add_interactions_contr.php";

// Check if the request is an AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

php_check_session(); // Verify session is valid

// Initialize result array for JSON response
$result = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $creator_user_id = $_SESSION["user_id"];
    $customer_lead_id = $_POST["customer_lead_id"];
    $interaction_details = $_POST["interactionDetails"];
    $interaction_type_id = $_POST["interactionType"];
    $interaction_history_date = $_POST["interactionHistoryDate"];

    // Initialize error array
    $errors = [];
    
    // Store valid data for repopulating form if needed
    $validData = [
        "customer_lead_id" => $customer_lead_id,
        "interactionDetails" => $interaction_details,
        "interactionType" => $interaction_type_id,
        "interactionHistoryDate" => $interaction_history_date,
    ];

    // Check if required inputs are empty
    if (is_input_empty($customer_lead_id, $interaction_type_id, $interaction_history_date)) {
        $errors["empty_input"] = "Fill in all fields!";
    }
    // Check if the date is invalid (e.g., future date)
    else if (is_date_invalid($interaction_history_date)) {
        $errors["invalid_date"] = "Date cannot be in the future!";
        $validData["interactionHistoryDate"] = ''; // Clear invalid date
    }

    // Handle errors if any exist
    if (!empty($errors)) {
        if ($isAjax) {
            $result = ["success" => false, "errors" => $errors]; // Return errors via JSON
        } else {
            handle_interaction_history_insertion_errors($validData, $errors); // Handle regular form errors
        }
    } else {
        // Try inserting the interaction history
        if (insertInteractionHistory($conn, $creator_user_id, $customer_lead_id, $interaction_details, $interaction_type_id, $interaction_history_date)) {
            if ($isAjax) {
                $result = ["success" => true]; // Return success via JSON
            } else {
                handle_interaction_history_insertion_success(); // Handle regular success
            }
        } else {
            if ($isAjax) {
                $result = ["success" => false, "error" => "Error inserting interaction history."]; // Error for AJAX
            } else {
                echo "Error: Failed to add Interaction History."; // Error for regular request
            }
        }
    }
    
    // Close the database connection
    $conn->close();
    
    // If it was an AJAX request, return JSON result
    if ($isAjax) {
        echo json_encode($result);
        exit;
    }
} else {
    // If not a POST request
    if ($isAjax) {
        echo json_encode(["success" => false, "error" => "Invalid request method"]); // Error for AJAX
    } else {
        header("Location: ../add_interaction.php"); // Redirect for regular request
    }
    exit;
}
?>
