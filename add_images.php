<?php
session_start();
require_once 'config/db_conn.php';
require_once 'config/auth_check.php';

// Check if user is logged in
checkUserAuth();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];    // Validate required fields
    if (empty($_POST['imageTitle']) || empty($_POST['imageCategory']) || empty($_POST['imagePrice'])) {
        header("Location: uploads.php?error=All fields are required");
        exit();
    }

    $title = trim($_POST['imageTitle']);
    $description = trim($_POST['imageDescription']);
    $category_id = (int)$_POST['imageCategory'];
    $price = floatval($_POST['imagePrice']);
    $is_public = isset($_POST['isPublic']) ? 1 : 0;    // Validate price
    if ($price <= 0) {
        header("Location: uploads.php?error=Price must be greater than zero");
        exit();
    }

    // Validate category exists
    $cat_check = $conn->prepare("SELECT id FROM categories WHERE id = ?");
    $cat_check->bind_param("i", $category_id);
    $cat_check->execute();
    if (!$cat_check->get_result()->fetch_assoc()) {
        $cat_check->close();
        header("Location: uploads.php?error=Invalid category");
        exit();
    }
    $cat_check->close();    // Handle file upload
    if (!isset($_FILES['imageUpload']) || $_FILES['imageUpload']['error'] !== UPLOAD_ERR_OK) {
        header("Location: uploads.php?error=No image file uploaded or upload error");
        exit();
    }

    $fileTmpPath = $_FILES['imageUpload']['tmp_name'];
    $fileName = $_FILES['imageUpload']['name'];
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
    $newFileName = uniqid() . '.' . $fileExtension;
    $uploadFileDir = 'uploads/';
    $dest_path = $uploadFileDir . $newFileName;

    if (!move_uploaded_file($fileTmpPath, $dest_path)) {
        header("Location: gallery.php?error=Failed to upload image file");
        exit();
    }

    $image_url = $dest_path;

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO images (user_id, category_id, title, description, image_url, price, is_public) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssdi", $user_id, $category_id, $title, $description, $image_url, $price, $is_public);
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: uploads.php?msg=Image uploaded successfully");
        exit();
    }

    $stmt->close();
    $conn->close();
    header("Location: uploads.php?error=Database error: " . urlencode($stmt->error));
    exit();
}
