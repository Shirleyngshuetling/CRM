<?php

function check_interaction_update_errors() {
    if (isset($_SESSION['errors_interaction_update'])) {
        $errors = $_SESSION['errors_interaction_update']; // An array of errors

        
        echo "<br>";

        foreach ($errors as $error) {
            echo  $error.'<br>';
        }

        unset($_SESSION['errors_interaction_update']); // Unset the session variable
    } 

}

function check_interaction_update_success(){
    if (isset($_SESSION['success'])){
        echo '<p class="success-message">' . $_SESSION['success'] . '</p>';
        unset($_SESSION['success']);
    }
}