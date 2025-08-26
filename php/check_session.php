<?php
// Prevent caching of pages requiring authentication
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");

/**
 * Checks if user session exists.
 * If not, redirects to login page with error message.
 */
function php_check_session() {
    if (!isset($_SESSION["user_id"])) {
        // Store current page for redirect after login
        $_SESSION['login_redirect'] = $_SERVER['REQUEST_URI'];
        $_SESSION['login_error'] = "Your session expired. Please login again.";
        header("Location: ../index.php");
        exit();
        
    }
}

/**
 * Checks if both user ID and role ID are set in session.
 * If not, redirects to login page with error message.
 */
function check_session(){
    if (!isset($_SESSION["user_id"]) || !isset($_SESSION['role_type_id'])) {
        // Store current page for redirect after login
        $_SESSION['login_redirect'] = $_SERVER['REQUEST_URI'];
        $_SESSION['login_error'] = "Your session expired. Please login again.";
        header("Location: index.php");
        exit();
        
    }
}