<?php
session_start();
require_once 'config/db_conn.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);
$image_id = $data['image_id'] ?? null;

if (!$image_id) {
    echo json_encode(['success' => false, 'message' => 'Image ID is required']);
    exit();
}

// Check if the image belongs to the logged-in user
$user_id = $_SESSION['user_id'];
$check_sql = "SELECT image_url FROM images WHERE id = ? AND user_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $image_id, $user_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Image not found or unauthorized']);
    exit();
}

$image = $result->fetch_assoc();
$image_path = $image['image_url'];

// Delete the image record from the database
$delete_sql = "DELETE FROM images WHERE id = ? AND user_id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("ii", $image_id, $user_id);

if ($delete_stmt->execute()) {
    // Delete the actual image file
    if (file_exists($image_path)) {
        unlink($image_path);
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error deleting image']);
}

$delete_stmt->close();
$check_stmt->close();
$conn->close();
