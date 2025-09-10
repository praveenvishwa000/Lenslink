<?php
require_once 'config/db_conn.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);
$cart_item_id = isset($data['cart_item_id']) ? (int)$data['cart_item_id'] : 0;
$user_id = $_SESSION['user_id'];

if (!$cart_item_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart item']);
    exit;
}

// Remove item from cart
$sql = "DELETE FROM cart_items WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $cart_item_id, $user_id);

if ($stmt->execute()) {    // Update cart count in session
    $count_sql = "SELECT COUNT(*) as count FROM cart_items WHERE user_id = ?";
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param("i", $user_id);
    $count_stmt->execute();
    $result = $count_stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    $_SESSION['cart_count'] = $count;

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error removing item from cart']);
}

$stmt->close();
$conn->close();
