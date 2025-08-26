<?php

// Check and display errors or success messages for interaction history insertion
function check_interaction_history_insertion_errors() {
    // If there are insertion errors stored in session
    if (isset($_SESSION['errors_interaction_history_insertion'])) {
        $errors = $_SESSION['errors_interaction_history_insertion']; // Retrieve the array of errors

        echo "<br>"; // Add a line break for spacing

        // Display each error message
        foreach ($errors as $error) {
            echo '<p class="error-message">' . $error . '</p>';
        }

        unset($_SESSION['errors_interaction_history_insertion']); // Clear the error session after displaying
    } 
    // If insertion was successful
    elseif (isset($_SESSION['success'])) {
        echo '<p class="success-message">' . $_SESSION['success'] . '</p>'; // Display success message
        unset($_SESSION['success']); // Clear the success session after displaying
    }
}
