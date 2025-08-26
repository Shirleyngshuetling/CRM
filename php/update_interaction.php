<?php
require 'config_session.php';
require_once 'check_session.php';
require "db.php";
require "update_interaction_model.php";
require "update_interaction_contr.php";

php_check_session();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    

    $interaction_history_id = $_POST['interaction_history_id'];
    $customer_lead_id = $_POST["customer_lead_id"]; 
    $interaction_type_id = $_POST['interactionType'];
    $interaction_date = $_POST['interactionHistoryDate'];
    $details = $_POST['interactionDetails'];

    // Error handlers
    $errors = [];
    
    $validData = [
        "customer_lead_id" => $customer_lead_id,
        "interaction_type_id" => $interaction_type_id,
        "interaction_date" => $interaction_date,
        "interaction_details" => $details
    ];
    // First, check if any field is empty (prevents unnecessary checks)
    if (is_input_empty($customer_lead_id, $interaction_type_id, $interaction_date)) {
        $errors["empty_input"] = "Fill in all fields!";
    }
    else if (is_date_invalid($interaction_history_date)){
        $errors["invalid_date"] = "Date cannot be in the future!";
        $validData["interaction_date"] = '';
    }

    if (!empty($errors)) {

        handle_interaction_update_errors($conn,$validData,$errors);
    }

    if (updateInteraction($conn, $interaction_history_id, $customer_lead_id, $interaction_type_id, $interaction_date, $details)) {
        handle_interaction_update_success();
    } else {
        echo "Error: Registration failed.";
    }
    

    // Close connection
    $conn->close();
}
else{
    header("Location:../update_interaction.php");
}
