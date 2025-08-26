<?php

// Function to check if any of the required inputs are empty
function is_input_empty($username, $email, $password, $role_type_id) {
    return empty($username) || empty($email) || empty($password) || empty($role_type_id);
}

// Function to validate if the email format is invalid
function is_email_invalid($email) {
    return !filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to check if the email is already registered in either Users or Customers/Leads tables
function is_email_registered($conn, $email) {
    return get_email_from_user($conn, $email) !== null || get_email_from_customer_lead($conn, $email) !== null;
}

// Function to handle signup errors (when a regular user signs up)
function handle_signup_errors($validData, $errors) {
    // Save error messages in the session
    $_SESSION["errors_signup"] = $errors;

    // Set flag to reopen signup form with previous data
    $_SESSION["show_signup_upon_signup_error"] = true;

    // Save valid user input except password into session for refilling the form
    $_SESSION["signup_data"] = $validData;

    // Redirect user back to signup page
    header("Location: ../index.php");    
    exit();
}

// Function to handle errors when an admin tries to add a user
function handle_add_user_errors($validData, $errors) {
    // Save error messages in the session
    $_SESSION["errors_signup"] = $errors;

    // Save valid input into session for refilling form fields
    $_SESSION["signup_data"] = $validData;

    // Redirect admin back to add user page
    header("Location: ../add_user.php");    
    exit();
}

// Function to handle successful signup (for normal users)
function handle_signup_success() {
    // Set success message in session
    $_SESSION["success_signup"] = "Signup Successful! You can now log in.";

    // Redirect user to login page
    header("Location: ../index.php");
    exit();
}

// Function to handle successful user addition (by an admin)
function handle_add_user_success() {
    // Set success message in session
    $_SESSION["success_add_user"] = "User added successfully!";

    // Redirect admin back to add user page
    header("Location: ../add_user.php");
    exit();
}
