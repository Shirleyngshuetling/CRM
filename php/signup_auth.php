<?php

require "db.php";
require "signup_auth_model.php";
require "signup_auth_contr.php";
require "config_session.php"; // Start the session
require_once 'check_session.php';

php_check_session();

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $role_type_id = $_POST["role"];
    $current_page = $_POST["currentPage"];

    // Error handlers
    $errors = [];

    // Prepare valid data array (except password)
    $validData = [
        'username' => $username,
        'email' => $email,
        'role_type_id' => $role_type_id
    ];

    // First, check if any field is empty (prevents unnecessary checks)
    if (is_input_empty($username, $email, $password, $role_type_id)) {
        $errors["empty_input"] = "Fill in all fields!";
    }

    // Then, validate email format (ONLY IF email is NOT empty)
    else if (is_email_invalid($email)) {
        $errors["invalid_email"] = "Invalid email used!";
        $validData['email'] = ""; // Clear the email field for security
    }

    // Then, check if the email is already registered (ONLY IF email is NOT empty and valid)
    else if (is_email_registered($conn, $email)) {
        $errors["email_used"] = "Email already registered!";
        $validData['email'] = "";
    }

    // Handle errors based on the page where signup is performed
    if (!empty($errors) && $current_page === "index") {
        handle_signup_errors($validData, $errors);
    }
    else if (!empty($errors) && $current_page === "add_user"){
        handle_add_user_errors($validData, $errors);
    }
    // Hash the password securely
    $options = ['cost' => 12];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT, $options);

    // Register the user
    if (registerUser($conn, $username, $hashedPassword, $role_type_id, $email)) {
        if (preg_match('/index/i', $current_page)){

            // Double-check the user exists
            $checkQuery = "SELECT 1 FROM Users WHERE email = ? LIMIT 1";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bind_param("s", $email);
            $checkStmt->execute();
            
            if ($checkStmt->get_result()->num_rows > 0) {
                handle_signup_success();
            } else {
                $errors["db_error"] = "Registration failed - please try again";
                handle_signup_errors($validData, $errors);
            }
            // handle_signup_success();
        }
        else if (preg_match('/add_user/i', $current_page)){
            handle_add_user_success();
        }
        
    } else {
        echo "Error: Registration failed.";
    }

    $conn->close(); // Close the database connection
} else {
    header("Location: ../index.php");
    exit();
}