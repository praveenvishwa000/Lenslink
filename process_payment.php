<?php
require_once 'config/db_conn.php';
require_once 'config/auth_check.php';
session_start();
checkUserAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request method';
    header('Location: payment.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Validate payment form data
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: payment.php');
    exit();
}

// Validate required fields
$required_fields = ['card-number', 'expiry', 'cvv', 'card-name'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['error'] = 'All fields are required';
        header('Location: payment.php');
        exit();
    }
}

// Basic card validation (you should implement proper validation)
$card_number = preg_replace('/\s+/', '', $_POST['card-number']);
if (!preg_match('/^\d{16}$/', $card_number)) {
    $_SESSION['error'] = 'Invalid card number';
    header('Location: payment.php');
    exit();
}

// Validate required fields
$required_fields = ['card-number', 'expiry', 'cvv', 'card-name'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['error'] = 'All payment fields are required';
        header('Location: payment.php');
        exit();
    }
}

// Basic card validation
$card_number = preg_replace('/\s+/', '', $_POST['card-number']);
if (!preg_match('/^\d{16}$/', $card_number)) {
    $_SESSION['error'] = 'Invalid card number';
    header('Location: payment.php');
    exit();
}

try {
    // Start transaction
    $conn->begin_transaction();

    // Get cart items and total
    $cart_sql = "SELECT cart_items.*, images.title, images.price, images.image_url 
                 FROM cart_items 
                 JOIN images ON cart_items.image_id = images.id 
                 WHERE cart_items.user_id = ?";
    $cart_stmt = $conn->prepare($cart_sql);
    $cart_stmt->bind_param("i", $user_id);
    $cart_stmt->execute();
    $cart_result = $cart_stmt->get_result();

    $total = 0;
    $cart_items = [];
    while ($row = $cart_result->fetch_assoc()) {
        $cart_items[] = $row;
        $total += $row['price'];
    }

    if (empty($cart_items)) {
        throw new Exception('Cart is empty');
    }    // Get the last 4 digits of the card
    $card_last4 = substr($card_number, -4);

    // Create order record
    $order_sql = "INSERT INTO orders (user_id, total_amount, status, payment_method, card_last4) VALUES (?, ?, 'completed', 'credit_card', ?)";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param("ids", $user_id, $total, $card_last4);
    $order_stmt->execute();
    $order_id = $conn->insert_id;

    // Create order items
    $order_items_sql = "INSERT INTO order_items (order_id, image_id, price) VALUES (?, ?, ?)";
    $order_items_stmt = $conn->prepare($order_items_sql);

    foreach ($cart_items as $item) {
        $order_items_stmt->bind_param("iid", $order_id, $item['image_id'], $item['price']);
        $order_items_stmt->execute();
    }    // Create payment record
    $payment_sql = "INSERT INTO payments (order_id, amount, payment_method, payment_status) 
                    VALUES (?, ?, 'credit_card', 'completed')";
    $payment_stmt = $conn->prepare($payment_sql);
    $payment_stmt->bind_param("id", $order_id, $total);
    $payment_stmt->execute();

    // Clear cart
    $clear_cart_sql = "DELETE FROM cart_items WHERE user_id = ?";
    $clear_cart_stmt = $conn->prepare($clear_cart_sql);
    $clear_cart_stmt->bind_param("i", $user_id);
    $clear_cart_stmt->execute();

    // Commit transaction
    $conn->commit();

    // Store order info in session for success page
    $_SESSION['order_id'] = $order_id;
    $_SESSION['order_total'] = $total;
    $_SESSION['order_items'] = $cart_items;

    // Redirect to success page
    header('Location: payment_success.php');
    exit();
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    $_SESSION['error'] = 'Payment failed: ' . $e->getMessage();
    header('Location: payment.php');
    exit();
}
