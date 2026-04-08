<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /marketplace/login.php");
        exit();
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header("Location: /marketplace/index.php");
        exit();
    }
}
?>
