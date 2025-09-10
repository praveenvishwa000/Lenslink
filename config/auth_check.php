<?php
function checkUserAuth()
{
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        // Get current page URL for redirect after login
        $current_page = basename($_SERVER['PHP_SELF']);
        header("Location: signin.php?redirect=" . $current_page);
        exit();
    }

    return true;
}
