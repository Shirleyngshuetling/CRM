<?php

// Check if name or email fields are empty
function is_input_empty($name, $email) {
    return empty($name) || empty($email) ;
}

// Validate if the email format is correct
function is_email_invalid($email) {
    return !filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Check if the email is already registered either in customers or users, excluding current user (the one to be updated)
function is_email_registered($conn, $email, $user_id) {
    return get_email_from_customer_lead($conn, $email) !== null || get_email_from_user($conn, $email, $user_id) !== null;
}

// Handle errors during user update by saving errors and valid data to session, then redirect back to update page
function handle_user_update_errors($conn,$validData,$errors) {

    $_SESSION["errors_user_update"] = $errors;

    $_SESSION["user_update_data"] = $validData;
    header("Location: ../update_user.php");
    exit;
        
}

// Handle successful user update by setting success message and redirecting to user list page
function handle_user_update_success() {
    $_SESSION["success"] = "User updated successfully!";
    header("Location: ../user_list.php");
    exit();
}