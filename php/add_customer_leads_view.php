<?php

// Check and display customer registration errors if they exist
function check_customer_registration_errors() {
    if (isset($_SESSION['errors_customer_registration'])) {
        $errors = $_SESSION['errors_customer_registration']; // An array of errors

        echo "<br>";

        foreach ($errors as $error) {
            echo  $error . '<br>'; // Display each error
        }

        unset($_SESSION['errors_customer_registration']); // Clear the session errors after displaying
    } 
}

// Check and display a success message for customer or lead registration
function check_customer_lead_registration_success() {
    if (isset($_SESSION['success'])) {
        echo '<p class="success-message">' . $_SESSION['success'] . '</p>'; // Display success message
        unset($_SESSION['success']); // Clear the success message after displaying
    }
}

// Check and display lead registration errors if they exist
function check_lead_registration_errors() {
    if (isset($_SESSION['errors_lead_registration'])) {
        $errors = $_SESSION['errors_lead_registration']; // An array of errors

        echo "<br>";

        foreach ($errors as $error) {
            echo '<p class="error-message">' . $error . '</p>'; // Display each error inside a styled paragraph
        }

        unset($_SESSION['errors_lead_registration']); // Clear the session errors after displaying
    } 
}
