<?php

// Check if email or password input is empty
function is_input_empty($email, $password) {
    return empty($email) || empty($password);
}

// Validate email format
function is_email_invalid($email) {
    return !filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Verify if the provided password matches the hashed password from database
function is_password_correct($password, $hashedPassword) {
    return password_verify($password, $hashedPassword);
}

// Check if the user account is inactive
function is_user_inactive($conn, $user_id) {
    $status = get_user_status($conn, $user_id); // Fetch user status from database
    return $status === 'inactive' ? true : false;
}

// Handle login errors: save error messages and input data, then redirect back to login
function handle_login_errors($error, $email) {
    $_SESSION["errors_login"] = $error; // Save the error message
    $_SESSION['show_login_upon_login_error'] = true; // Set flag to show login popup
    $_SESSION["login_data"] = [ // Save email input (except password) to session
        "email" => $email,
    ];
    header("Location: ../index.php"); // Redirect back to login page
    exit();
}

// Handle successful login: save user data in session and redirect to dashboard
function handle_login_success($user) {
    $_SESSION["user_id"] = $user["user_id"]; // Store user ID
    $_SESSION["role_type_id"] = $user["role_type_id"]; // Store role type (admin/sales)
    $_SESSION["email"] = $user["email"]; // Store email
    $_SESSION["username"] = $user["user_name"]; // Store username

    // Redirect to main dashboard
    // $redirectPage = ($user["role_type_id"] == 1) ? "../admin_dashboard.php" : "../sales_dashboard.php";
    header("Location: ../dashboard_main.php");
    exit();
}
