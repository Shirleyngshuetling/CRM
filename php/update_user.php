<?php
require 'config_session.php';
require_once 'check_session.php';
require "db.php";
require "update_user_model.php";
require "update_user_contr.php";

php_check_session();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    

    $user_id = $_POST['userId'];
    $name = trim($_POST["name"]);
    $email = trim(filter_var($_POST["email"], FILTER_SANITIZE_EMAIL));
    $role = $_POST['role'];

    //check if user is updating their own details
    if ($user_id == $_SESSION['user_id']){
        // Update session username if name was changed
        if ($_SESSION['username'] !== $name) { // Only update if name actually changed
            $_SESSION['username'] = $name;
        }
    }
    
    // Error handlers
    $errors = [];
    
    // Prepare valid data for use in case of errors
    $validData = [
        "user_id" => $user_id,
        "user_name" => $name,
        "email" => $email,
        "role_type_id" => $role
    ];
    // First, check if any field is empty (prevents unnecessary checks)
    if (is_input_empty($name, $email)) {
        $errors["empty_input"] = "Fill in all fields!";
    }
    else{
        // Validate email format (ONLY IF email is NOT empty)
        if (is_email_invalid($email)) {
            $errors["invalid_email"] = "Invalid email used!";
            $validData['email'] = ""; // Clear the email field for security
        }

        // Then, check if the email is already registered (ONLY IF email is NOT empty and valid)
        elseif (is_email_registered($conn, $email, $user_id)) {
            $errors["email_used"] = "Email already registered!";
            $validData["email"] = ""; 
        }
    }

    // If there are errors, redirect back with error messages
    if (!empty($errors)) {

        handle_user_update_errors($conn,$validData,$errors);
        exit;
    }

    // Attempt to update user information
    if (updateUser($conn, $user_id??$validData['user_id'], $name, $email, $role)) {
        handle_user_update_success();
    } else {
        $errors["update_failed"] = "Error: User update failed";
        handle_user_update_errors($conn, $validData, $errors);
    }
    

    // Close connection
    $conn->close();
}
else{
    header("Location:../update_user.php");
    exit;
}
