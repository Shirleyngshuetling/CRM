<?php

function is_input_empty($name, $email) {
    return empty($name) || empty($email) ;
}

function is_date_invalid($interaction_date) {
    try {
        $input_date = new DateTime($interaction_date);
        $current_date = new DateTime();
        
        // Compare date portions only (ignore time)
        return $input_date->format('Y-m-d') > $current_date->format('Y-m-d');
    } catch (Exception $e) {
        return true; // Invalid date format
    }
}

function handle_interaction_update_errors($conn,$validData,$errors) {

    $_SESSION["errors_interaction_update"] = $errors;

    $_SESSION["interaction_update_data"] = $validData;
    header("Location: ../update_interaction.php");
    exit;
        
}


function handle_interaction_update_success() {
    $_SESSION["success"] = "Interaction updated successfully!";
    header("Location: ../interaction_list.php");
    exit();
}