<?php

// Check for reminder insertion errors and display them
function check_reminder_insertion_errors() {
    if (isset($_SESSION['errors_reminder_insertion'])) {
        $errors = $_SESSION['errors_reminder_insertion']; // An array of errors

        echo "<br>";

        foreach ($errors as $error) {
            echo '<p class="error-message">' . $error . '</p>';
        }

        unset($_SESSION['errors_reminder_insertion']); // Unset the session variable
    } 
    // If no errors, check for success message
    elseif (isset($_SESSION['success'])) {
        echo '<p class="success-message">' . $_SESSION['success'] . '</p>';
        unset($_SESSION['success']);
    }
}
