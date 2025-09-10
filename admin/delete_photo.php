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
    
    // First, get the image URL to delete the file
    $query = "SELECT image_url FROM images WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $photo_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $image_path = "../" . $row['image_url'];
        
        // Begin transaction
        $conn->begin_transaction();
        
        try {
            // Delete from all related tables
            // Delete from favorites
            $query = "DELETE FROM favorites WHERE image_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $photo_id);
            $stmt->execute();
            
            // Delete from cart
            $query = "DELETE FROM cart WHERE image_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $photo_id);
            $stmt->execute();
            
            // Delete from orders (if exists)
            $query = "DELETE FROM order_items WHERE image_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $photo_id);
            $stmt->execute();
            
            // Finally delete the image record
            $query = "DELETE FROM images WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $photo_id);
            $stmt->execute();
            
            // If all queries successful, commit transaction
            $conn->commit();
            
            // Delete the physical file
            if (file_exists($image_path)) {
                unlink($image_path);
            }
            
            $_SESSION['success_msg'] = "Photo has been deleted successfully!";
            
        } catch (Exception $e) {
            // If any error occurs, rollback the transaction
            $conn->rollback();
            $_SESSION['error_msg'] = "Error deleting photo: " . $e->getMessage();
        }
        
    } else {
        $_SESSION['error_msg'] = "Photo not found!";
    }
    
    $stmt->close();
} else {
    $_SESSION['error_msg'] = "No photo ID provided!";
}

// Redirect back to photos page
header('Location: photos.php');
exit();
?>
