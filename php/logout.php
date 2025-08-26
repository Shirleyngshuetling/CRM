<?php
require 'config_session.php'; // Start the session

session_unset();  // Remove all session variables

session_destroy(); // Destroy the session completely

header("Location: ../index.php"); // Redirect to the login page

exit(); // Terminate the script execution
?>
