<<<<<<< HEAD
<?php
require_once 'config/db_conn.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart']);
    exit;
}

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);
$image_id = isset($data['image_id']) ? (int)$data['image_id'] : 0;
$user_id = $_SESSION['user_id'];

if (!$image_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid image']);
    exit;
}

try {
    // Check if item already exists in cart
    $check_sql = "SELECT id FROM cart_items WHERE user_id = ? AND image_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $image_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Item already in cart']);
        exit;
    }

    // Add item to cart
    $sql = "INSERT INTO cart_items (user_id, image_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $image_id);

    if ($stmt->execute()) {
        // Update cart count in session
        $count_sql = "SELECT COUNT(*) as count FROM cart_items WHERE user_id = ?";
        $count_stmt = $conn->prepare($count_sql);
        $count_stmt->bind_param("i", $user_id);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $count = $count_result->fetch_assoc()['count'];
        $_SESSION['cart_count'] = $count;

        echo json_encode(['success' => true, 'message' => 'Added to cart', 'cart_count' => $count]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding item to cart']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
=======
<?php
require_once 'config/db_conn.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart']);
    exit;
}

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);
$image_id = isset($data['image_id']) ? (int)$data['image_id'] : 0;
$user_id = $_SESSION['user_id'];

if (!$image_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid image']);
    exit;
}

try {
    // Check if item already exists in cart
    $check_sql = "SELECT id FROM cart_items WHERE user_id = ? AND image_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $image_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Item already in cart']);
        exit;
    }

    // Add item to cart
    $sql = "INSERT INTO cart_items (user_id, image_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $image_id);

    if ($stmt->execute()) {
        // Update cart count in session
        $count_sql = "SELECT COUNT(*) as count FROM cart_items WHERE user_id = ?";
        $count_stmt = $conn->prepare($count_sql);
        $count_stmt->bind_param("i", $user_id);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $count = $count_result->fetch_assoc()['count'];
        $_SESSION['cart_count'] = $count;

        echo json_encode(['success' => true, 'message' => 'Added to cart', 'cart_count' => $count]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding item to cart']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
>>>>>>> 644698dfc1ca2b7d65e44b7ba9e874a5fe15fc50
