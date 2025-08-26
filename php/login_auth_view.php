<?php

// Retrieve and clear login errors stored in the session
function get_login_errors() {
    if (isset($_SESSION['errors_login'])) {
        $errors = $_SESSION['errors_login']; // Retrieve the errors from the session

        // Ensure that the errors variable is an array
        if (!is_array($errors)) {
            $errors = [$errors]; // If it's a single error string, wrap it into an array
        }

        unset($_SESSION['errors_login']); // Remove the errors from the session after retrieving
        return $errors; // Return the errors array
    }
}
