<?php
require 'config_session.php';
require_once 'check_session.php';
require "db.php";
require "update_customer_lead_model.php";
require "update_customer_lead_contr.php";

php_check_session();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    

    $customer_id = $_POST['customer_lead_id'];
    $name = trim($_POST["name"]); 
    $company = trim($_POST["company"]);
    $email = trim(filter_var($_POST["email"], FILTER_SANITIZE_EMAIL));
    $phone =trim($_POST["phone"]); //make sure phone num unique
    $address = trim($_POST["address"]);
    $notes = trim($_POST["notes"]);
    $customer_type = $_POST['customerType'];
    // Check if status_id is set in the form
    $status_id = isset($_POST['status_id']) && $_POST['status_id'] !== "" ? $_POST['status_id'] : NULL;

    // Error handlers
    $errors = [];
    
    $validData = [
        "name" => $name,
        "company" => $company,
        "phone_num" => $phone,
        "email" => $email,
        "address" => $address,
        "notes" => $notes,
        "status_id" => $status_id,
        "customer_type" => $customer_type
    ];
    // First, check if any field is empty (prevents unnecessary checks)
    if (is_input_empty($name, $company, $email, $phone)) {
        $errors["empty_input"] = "Fill in all fields!";
    }
    else{
        // Validate email format (ONLY IF email is NOT empty)
        if (is_email_invalid($email)) {
            $errors["invalid_email"] = "Invalid email used!";
            $validData['email'] = ""; // Clear the email field for security
        }

        // Then, check if the email is already registered (ONLY IF email is NOT empty and valid)
        elseif (is_email_registered($conn, $email, $customer_id)) {
            $errors["email_used"] = "Email already registered!";
            $validData["email"] = ""; 
        }

        // Validate phone number format
        if (is_phone_invalid($phone)) {
            $errors["invalid_phone"] = "Invalid phone used!";
            $validData["phone_num"] = "";
        }

        //  Only check phone uniqueness if phone is valid
        elseif (is_phone_registered($conn, $phone, $customer_id)) {
            $errors["phone_used"] = "Phone already registered!";
            $validData["phone_num"] = "";
        }
    }

    if (!empty($errors)) {

        handle_customer_lead_update_errors($conn,$customer_id, $validData,$errors, $customer_type);
    }

    if (updateCustomerLead($conn, $customer_id, $user_id, $name, $company, $email, $phone, $address, $notes, $status_id, $customer_type)) {
        handle_customer_lead_update_success($customer_type);
    } else {
        echo "Error: Registration failed.";
    }
    

    // Close connection
    $conn->close();
}
else{
    header("Location:../update_customer_lead.php");
}
