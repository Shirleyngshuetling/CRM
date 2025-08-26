<?php

// Check if any of the input fields are empty
function is_input_empty($name, $company, $email, $phone) {
    return empty($name) || empty($company) || empty($email) || empty($phone);
}

// Validate if the email address is in a correct format
function is_email_invalid($email) {
    return !filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Check if the email is already registered either in customer leads or users
function is_email_registered($conn, $email) {
    return get_email_from_customer_lead($conn, $email) !== null || get_email_from_user($conn, $email) !== null;
}

// Validate phone number
// Even if we set pattern="[0-9]{10}" in our HTML form, users can still bypass it by:
// Inspecting the HTML (F12) and removing the pattern attribute
function is_phone_invalid ($phone){
    return !preg_match("/^[0-9]{7,15}$/", $phone);
}

// Check if the phone number is already registered
function is_phone_registered($conn, $phone) {
    return get_phone($conn, $phone) !== null;
}

// Handle registration errors for either customers or leads
function handle_customer_lead_registration_errors($validData, $errors, $customer_type) {
    if ($customer_type == 1){ // Customer
        // Store errors in session for customer registration
        $_SESSION["errors_customer_registration"] = $errors;

        // Store valid input data in the session (except password)
        $_SESSION["customer_registration_data"] = $validData;
        
        // Redirect back to add customer page
        header("Location: ../add_customer.php");
    }
    else { //Lead
        $_SESSION["errors_lead_registration"] = $errors;

        // Store valid input data in the session (except password)
        $_SESSION["lead_registration_data"] = $validData;

        // Redirect back to add lead page
        header("Location: ../add_lead.php");
    }
    exit();
}

function handle_customer_lead_registration_success($customer_type) {
    
    // Redirect based on form type
    if ($customer_type === 2) { //lead
        // Set success message
        $_SESSION["success"] = "Lead added successfully!";
        // Redirect back to add lead page
        header("Location: ../add_lead.php");
    } else { //customer
        // Set success message
        $_SESSION["success"] = "Customer added successfully!";
        // Redirect back to add customer page
        header("Location: ../add_customer.php");
    }
    exit();
}