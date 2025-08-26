<?php

// Retrieve user information by email
function get_user_by_email($conn, $email) {
    $sql = "SELECT user_id, password, role_type_id, email, user_name FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return null; // Return null if there's a database error
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc(); // Return user data as an associative array
    }

    return null; // Return null if no user is found
}

// Retrieve the status (e.g., active/inactive) of a user by user ID
function get_user_status ($conn, $user_id) {
    $sql = "SELECT user_status FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return null; // Return null if there's a database error
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc(); // Fetch the row as an associative array
        return $row['user_status']; // Return only the active_status
    }

    return null; // Return null if no user is found
}
