<?php

// Check if any required input fields are empty
function is_input_empty($name, $company, $email, $phone) {
    return empty($name) || empty($company) || empty($email) || empty($phone);
}

// Validate email format
function is_email_invalid($email) {
    return !filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Check if email is already registered in either Customers_Leads or Users table
function is_email_registered($conn, $email, $customer_id) {
    return get_email_from_customer_lead($conn, $email, $customer_id) !== null || get_email_from_user($conn, $email) !== null;
}

// Validate phone number format
// Even if pattern="[0-9]{10}" is set in HTML form, users can bypass it via browser dev tools
function is_phone_invalid($phone){
    return !preg_match("/^[0-9]{7,15}$/", $phone);
}

// Check if phone number is already registered (excluding the current customer/lead)
function is_phone_registered($conn, $phone, $customer_id) {
    return get_phone($conn, $phone, $customer_id) !== null;
}

// Handle customer/lead update errors
function handle_customer_lead_update_errors($conn, $customer_id, $validData, $errors, $customer_type) {
    // Store the POST data in session for repopulating the form
    $_SESSION['form_data'] = $_POST;

    if ($customer_type == 1){ // Customer
        $_SESSION["errors_customer_update"] = $errors;
        $_SESSION["customer_update_data"] = $validData;
    }
    else { // Lead
        $_SESSION["errors_lead_update"] = $errors;
        $_SESSION["lead_update_data"] = $validData;
    }

    // Redirect back to update page
    if (!headers_sent()) {
        header("Location: ../update_customer_lead.php?id=" . (int)$customer_id);
        exit();
    } else {
        // Fallback if headers are already sent
        echo '<script>window.location.href="../update_customer_lead.php?id=' . (int)$customer_id . '";</script>';
        exit();
    }
}

// Handle success after updating a customer or lead
function handle_customer_lead_update_success($customer_type) {
    $_SESSION["success"] = ($customer_type == 1 ? "Customer" : "Lead") . " updated successfully!";
    header("Location: ../" . ($customer_type == 1 ? 'customer_list.php' : 'lead_list.php'));
    exit();
}
