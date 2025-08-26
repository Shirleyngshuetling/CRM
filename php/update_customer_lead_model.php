<?php

// Fetch email from Users table based on provided email
function get_email_from_user($conn, $email) {
    $query = "SELECT email FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row["email"] ?? null; // Return null if email doesn't exist
}

// Fetch email from Customers_Leads table excluding a specific customer/lead ID
function get_email_from_customer_lead($conn, $email, $cust_lead_id) {
    $query = "SELECT email FROM Customers_Leads WHERE email = ? AND customer_lead_id != ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $email, $cust_lead_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row["email"] ?? null; // Return null if email doesn't exist
}

// Fetch phone number from Customers_Leads table excluding a specific customer/lead ID
function get_phone($conn, $phone, $cust_lead_id) {
    $query = "SELECT phone_num FROM Customers_Leads WHERE phone_num = ? AND customer_lead_id != ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $phone, $cust_lead_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row["phone_num"] ?? null; // Return null if phone doesn't exist
}

// Update customer or lead information
function updateCustomerLead($conn, $cust_lead_id, $user_id, $name, $company, $email, $phone, $address, $notes, $status_id, $customer_type) {
    
    if ($customer_type == 1) { // If Customer (no status_id update needed)
        $query = $conn->prepare("UPDATE Customers_Leads SET name=?, company=?, email=?, phone_num=?, address=?, notes=? WHERE customer_lead_id=?");
        $query->bind_param("ssssssi", $name, $company, $email, $phone, $address, $notes, $cust_lead_id);
    } else { // If Lead (status_id included)
        $query = $conn->prepare("UPDATE Customers_Leads SET name=?, company=?, email=?, phone_num=?, address=?, notes=?, status_id=? WHERE customer_lead_id=?");
        $query->bind_param("ssssssii", $name, $company, $email, $phone, $address, $notes, $status_id, $cust_lead_id);
    }

    return $query->execute(); // Return true if successful, false otherwise
}

// Get all customer/lead information based on customer_lead_id
function getCustomerLeadInfo($conn, $cust_lead_id) {
    $query = "SELECT * FROM Customers_Leads WHERE customer_lead_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $cust_lead_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();
    return $customer ?? null; // Return null if customer/lead does not exist
}
