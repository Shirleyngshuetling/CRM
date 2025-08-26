<?php

// Fetch an email from the 'users' table by email address
function get_email_from_user($conn, $email) {
    $query = "SELECT email FROM users WHERE email = ?";
    $stmt = $conn->prepare($query); // Prepare the SQL statement
    $stmt->bind_param("s", $email); // Bind the email parameter
    $stmt->execute(); // Execute the query

    $result = $stmt->get_result(); // Get the result set
    $row = $result->fetch_assoc(); // Fetch a single row
    return $row["email"] ?? null; // Return the email if found, otherwise null
}

// Fetch an email from the 'Customers_Leads' table by email address
function get_email_from_customer_lead($conn, $email) {
    $query = "SELECT email FROM Customers_Leads WHERE email = ?";
    $stmt = $conn->prepare($query); // Prepare the SQL statement
    $stmt->bind_param("s", $email); // Bind the email parameter
    $stmt->execute(); // Execute the query

    $result = $stmt->get_result(); // Get the result set
    $row = $result->fetch_assoc(); // Fetch a single row
    return $row["email"] ?? null; // Return the email if found, otherwise null
}

// Fetch a phone number from the 'Customers_Leads' table by phone number
function get_phone($conn, $phone) {
    $query = "SELECT phone_num FROM Customers_Leads WHERE phone_num = ?";
    $stmt = $conn->prepare($query); // Prepare the SQL statement
    $stmt->bind_param("s", $phone); // Bind the phone number parameter
    $stmt->execute(); // Execute the query

    $result = $stmt->get_result(); // Get the result set
    $row = $result->fetch_assoc(); // Fetch a single row
    return $row["phone_num"] ?? null; // Return the phone number if found, otherwise null
}

// Register a new customer or lead into the 'Customers_Leads' table
function registerCustomerLead($conn, $user_id, $name, $company, $email, $phone, $address, $notes, $status_id, $customer_type) {
    $query = "INSERT INTO Customers_Leads (user_id, name, company, email, phone_num, address, account_created_time, notes, status_id, customer_type) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?)";
    $stmt = $conn->prepare($query); // Prepare the SQL statement

    // Bind parameters to the prepared statement
    $stmt->bind_param("issssssii", $user_id, $name, $company, $email, $phone, $address, $notes, $status_id, $customer_type);

    return $stmt->execute(); // Execute the query and return true if successful, false otherwise
}
