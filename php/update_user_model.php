<?php

// Fetch an email from Users table, excluding a specific user_id if provided
function get_email_from_user($conn, $email, $user_id) {
    $query = "SELECT email FROM Users WHERE email = ?";
    $params = [$email];
    $types = "s";
    
    // If user_id is provided, exclude it from the check
    if ($user_id !== null) {
        $query .= " AND user_id != ?";
        $params[] = $user_id;
        $types .= "i";
    }
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row["email"] ?? null;// Return null if email not found
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

// Update a user's information (name, email, role) based on user_id
function updateUser($conn, $user_id, $name, $email, $role_type_id) {
    
    $query = $conn->prepare("UPDATE Users SET user_name=?, email=?, role_type_id=? WHERE user_id=?");
    $query->bind_param("ssii", $name, $email, $role_type_id, $user_id);

    return $query->execute(); // Return true if successful, false otherwise
}
