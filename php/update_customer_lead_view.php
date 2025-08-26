<?php

function check_customer_update_errors() {
    if (isset($_SESSION['errors_customer_update'])) {
        $errors = $_SESSION['errors_customer_update']; // An array of errors

        
        echo "<br>";

        // Loop through each error and display it
        foreach ($errors as $error) {
            echo  $error.'<br>';
        }

        unset($_SESSION['errors_customer_update']); // Unset the session variable
    } 

}

function check_customer_update_success(){
    if (isset($_SESSION['success'])){
        echo '<p class="success-message">' . $_SESSION['success'] . '</p>';
        unset($_SESSION['success']); // Unset success message after displaying
    }
}

function check_lead_update_errors() {
    if (isset($_SESSION['errors_lead_update'])) {
        $errors = $_SESSION['errors_lead_update']; // An array of errors

        
        echo "<br>";

        foreach ($errors as $error) {
            echo '<p class="error-message">' . $error . '</p>';
        }

        unset($_SESSION['errors_lead_update']); // Unset the session variable
    } 

}

function check_lead_update_success(){
    if (isset($_SESSION['success'])){
        echo '<p class="success-message">' . $_SESSION['success'] . '</p>';
        unset($_SESSION['success']);// Unset success message after displaying
    }
}