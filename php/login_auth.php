<?php
require 'config_session.php'; // Start the session
require 'db.php'; // Database connection
require 'login_auth_model.php'; // Include model functions (database queries)
require 'login_auth_contr.php'; // Include controller functions (logic/validation)

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Check if the form was submitted via POST
    // Sanitize user input
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Error handlers
    $errors = [];

    if (is_input_empty($email, $password)) {
        $errors[] = "Fill in all fields!";
    }
    if (is_email_invalid($email)) {
        $errors[] = "Invalid email format!";
    }

    // Fetch user data from the database
    $user = get_user_by_email($conn, $email);

    if (!$user) {
        // If no user is found, add an error
        $errors[] = "User not found.";
    } else {
        // Check if the user account is inactive
        if (is_user_inactive($conn, $user['user_id'])) {
            $errors[] = "User is deactivated! Please sign up or use another valid account.";
        } 
        // Verify if the entered password matches the stored hashed password
        elseif (!is_password_correct($password, $user['password'])) {
            $errors[] = "Incorrect Email or Password.";
        }
    }

    // If there are any errors, handle them and redirect back
    if (!empty($errors)) {
        handle_login_errors($errors, $email);
    }

    // Handle successful login
    handle_login_success($user);
} else {
    header("Location: ../index.php");
    exit();
}
