<?php
// Simple authentication check
// Since this is a demo, we'll use a simple session-based authentication

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simple demo login - in production, this should be properly implemented
if (!isset($_SESSION['user_logged_in'])) {
    $_SESSION['user_logged_in'] = true;
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
    $_SESSION['user_role'] = 'admin';
}

// Function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}

// Function to require login
function require_login() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit();
    }
}

// Function to logout
function logout() {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// For demo purposes, auto-login as admin
if (!is_logged_in()) {
    $_SESSION['user_logged_in'] = true;
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
    $_SESSION['user_role'] = 'admin';
}
?>