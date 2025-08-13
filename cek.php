<?php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


function is_logged_in() {
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}


function require_login() {
    if (!is_logged_in()) {
        header("Location: Login.php");
        exit();
    }
}


function logout() {
    session_unset();
    session_destroy();
    header("Location: Login.php");
    exit();
}
?>