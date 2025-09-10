<?php
session_start();
require_once '../config/db_conn.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../signin.php');
    exit();
}

if (isset($_GET['id'])) {
    $photo_id = $_GET['id'];
    
    // Update the photo status to public
    $query = "UPDATE images SET is_public = 1 WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $photo_id);
    
    if ($stmt->execute()) {
        // Success
        $_SESSION['success_msg'] = "Photo has been approved successfully!";
    } else {
        // Error
        $_SESSION['error_msg'] = "Error approving photo: " . $conn->error;
    }
    
    $stmt->close();
} else {
    $_SESSION['error_msg'] = "No photo ID provided!";
}

// Redirect back to photos page
header('Location: photos.php');
exit();
?>
