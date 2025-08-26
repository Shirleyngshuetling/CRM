<?php
require 'config_session.php'; // Start session configuration
require_once 'check_session.php'; // Include session checking function
require "db.php"; // Ensures database connection is established
require "add_customer_leads_model.php"; // Include model functions (DB queries)
require "add_customer_leads_contr.php"; // Include controller functions (validations, handlers)

php_check_session(); // Check if user is logged in

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Retrieve form data
    $user_id = $_SESSION["user_id"]; // Get user ID from session
    $name = trim($_POST["name"]); // Trim whitespace
    $company = trim($_POST["company"]);
    $email = trim(filter_var($_POST["email"], FILTER_SANITIZE_EMAIL)); // Sanitize email
    $phone = trim($_POST["phone_num"]); // Phone number (should be unique)
    $address = trim($_POST["address"]);
    $customer_type = (int)$_POST["customerType"]; // 1 = customer, 2 = lead
    $notes = $_POST["notes"]; // Notes are optional
    $status_id = ($customer_type == 1) ? NULL : $_POST["status_id"]; // Status is required only for leads

    // Error handlers
    $errors = []; // Initialize error array
    
    $validData = [
        "name" => $name,
        "company" => $company,
        "phone_num" => $phone,
        "email" => $email,
        "address" => $address,
        "notes" => $notes,
        "status_id" => $status_id
    ];

    // First, check if any field is empty (prevents unnecessary checks)
    if (is_input_empty($name, $company, $email, $phone)) {
        $errors["empty_input"] = "Fill in all fields!";
    } 
    else {
        // Validate email format (ONLY if email is NOT empty)
        if (is_email_invalid($email)) {
            $errors["invalid_email"] = "Invalid email used!";
            $validData['email'] = ""; // Clear the email field for security
        }
        // Check if email is already registered (ONLY if email is valid)
        elseif (is_email_registered($conn, $email)) {
            $errors["email_used"] = "Email already registered!";
            $validData["email"] = ""; 
        }

        // Validate phone number format
        if (is_phone_invalid($phone)) {
            $errors["invalid_phone"] = "Invalid phone used!";
            $validData["phone_num"] = "";
        }
        // Check phone uniqueness ONLY if phone format is valid
        elseif (is_phone_registered($conn, $phone)) {
            $errors["phone_used"] = "Phone already registered!";
            $validData["phone_num"] = "";
        }
    }

    // If there are any errors, handle them (redirect back with errors and valid data)
    if (!empty($errors)) {
        handle_customer_lead_registration_errors($validData, $errors, $customer_type);
    }

    // If no errors, attempt to register the customer/lead
    if (registerCustomerLead($conn, $user_id, $name, $company, $email, $phone, $address, $notes, $status_id, $customer_type)) {
        handle_customer_lead_registration_success($customer_type);
    } else {
        echo "Error: Registration failed.";
    }
    
    // Close the database connection
    $conn->close();
} 
else {
    // If the request is not POST, redirect to add_customer page
    header("Location: ../add_customer.php");
}

// We don't create a closing tag for a pure PHP file,
// to avoid accidentally adding unwanted whitespace or output that could break headers
