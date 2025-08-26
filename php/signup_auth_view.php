<?php

// Function to retrieve signup errors from the session
function get_signup_errors() {
    if (isset($_SESSION['errors_signup'])) {
        $errors = $_SESSION['errors_signup']; // Get the array of errors
        
        unset($_SESSION['errors_signup']); // Clear the errors after retrieving
        return $errors;
    } 
}

// Function to retrieve signup success message from the session
function get_signup_success(){
    if (isset($_SESSION['success_signup'])) {
        $signup_success = $_SESSION['success_signup']; // Get the success message
        return $signup_success;
    }
}

// Function to check if a user was successfully added and display the success message
function check_add_user_success(){
    if (isset($_SESSION['success_add_user'])) {
        $add_success = $_SESSION['success_add_user']; // Get the success message
        echo '<p class="success-message">' . $_SESSION['success_add_user'] . '</p>'; // Display the message
        unset($_SESSION['success_add_user']); // Clear the success message after displaying
    }
}
