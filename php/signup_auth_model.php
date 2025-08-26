<?php

function get_email_from_user($conn, $email) {
    $query = "SELECT email FROM Users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row["email"] ?? null; // Return null if email doesn't exist
}

function get_email_from_customer_lead($conn, $email) {
    $query = "SELECT email FROM Customers_Leads WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row["email"] ?? null; // Return null if email doesn't exist
}

function registerUser($conn, $username, $hashedPassword, $role_type_id, $email) {

    $conn->begin_transaction(); // Start transaction
    
    try {
        $insertQuery = "INSERT INTO Users (user_name, password, role_type_id, email, account_created_time) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($insertQuery);
        if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
        
        $stmt->bind_param("ssis", $username, $hashedPassword, $role_type_id, $email);
        if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
        
        $conn->commit(); // Only commit if everything succeeded
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Registration failed: " . $e->getMessage());
        return false;
    }
}
