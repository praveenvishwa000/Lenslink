<?php
session_start();
require_once 'config/db_conn.php';
require_once 'config/auth_check.php';

// Check if user is logged in
checkUserAuth();

// Set up response array
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $name = trim($_POST['name']);
    $newPassword = $_POST['newPassword'];

    // Validate inputs
    if (empty($name)) {
        $response['message'] = 'Name is required';
        echo json_encode($response);
        exit;
    }    // Start building the update query
    $sql_parts = ["UPDATE users SET name = ?"];
    $types = "s";
    $params = [$name];

    // Add password update if provided
    if (!empty($newPassword)) {
        $sql_parts[] = "password = ?";
        $types .= "s";
        $params[] = password_hash($newPassword, PASSWORD_DEFAULT);
    }

    // Add user_id to parameters
    $types .= "i";
    $params[] = $user_id;

    // Complete the query
    $sql = implode(", ", $sql_parts) . " WHERE id = ?";

    // Prepare and execute the update
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Profile updated successfully';
    } else {
        $response['message'] = 'Error updating profile: ' . $conn->error;
    }

    $stmt->close();
} else {
    $response['message'] = 'Invalid request method';
}

$conn->close();
echo json_encode($response);
