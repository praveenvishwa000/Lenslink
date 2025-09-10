<?php
session_start();
require_once 'config/db_conn.php';
require_once 'config/auth_check.php';

// Check if user is logged in
checkUserAuth();

if (!isset($_GET['id'])) {
    header('Location: profile.php?error=No image specified');
    exit();
}

$image_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Check if user has purchased this image
$sql = "SELECT i.* FROM images i 
        INNER JOIN order_items oi ON i.id = oi.image_id
        INNER JOIN orders o ON oi.order_id = o.id
        INNER JOIN payments p ON o.id = p.order_id
        WHERE o.user_id = ? AND i.id = ? AND p.payment_status = 'completed'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $image_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: profile.php?error=You do not have permission to download this image');
    exit();
}

$image = $result->fetch_assoc();
$file_path = $image['image_url'];

if (!file_exists($file_path)) {
    header('Location: profile.php?error=Image file not found');
    exit();
}

// Get file information
$file_name = basename($file_path);
$file_extension = pathinfo($file_path, PATHINFO_EXTENSION);

// Set appropriate headers for download
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $file_name . '"');
header('Content-Length: ' . filesize($file_path));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: public');

// Output file
readfile($file_path);
exit();
