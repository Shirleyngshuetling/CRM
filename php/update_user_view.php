<?php
// Function to display user update errors if they exist in the session
function check_user_update_errors() {
    if (isset($_SESSION['errors_user_update'])) {
        $errors = $_SESSION['errors_user_update']; // An array of errors

        
        echo "<br>";

        foreach ($errors as $error) {
            echo  $error.'<br>';
        }

        unset($_SESSION['errors_user_update']); // Unset the session variable
    } 

}
// Function to display a success message if it exists in the session
function check_user_update_success(){
    if (isset($_SESSION['success'])){
        echo '<p class="success-message">' . $_SESSION['success'] . '</p>';
        unset($_SESSION['success']);
    }
}

