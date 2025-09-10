<?php
require_once 'config/db_conn.php';
require_once 'config/auth_check.php';
session_start();
checkUserAuth();

// Redirect if no order information is available
if (!isset($_SESSION['order_id']) || !isset($_SESSION['order_items'])) {
    header('Location: index.php');
    exit();
}

$order_id = $_SESSION['order_id'];
$total = $_SESSION['order_total'];
$order_items = $_SESSION['order_items'];

// Clear order session data after displaying
unset($_SESSION['order_id']);
unset($_SESSION['order_total']);
unset($_SESSION['order_items']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success - LensLink</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <?php include('includes/navigation.php'); ?>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Success Message -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                        <i class="fas fa-check text-3xl text-green-500"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Payment Successful!</h2>
                    <p class="text-gray-600 mt-2">Thank you for your purchase</p>
                </div>

                <!-- Order Details -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Details</h3>
                    <div class="text-gray-600">
                        <p>Order ID: #<?= str_pad($order_id, 8, '0', STR_PAD_LEFT) ?></p>
                        <p>Date: <?= date('F j, Y') ?></p>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="mt-6">
                    <h4 class="text-md font-semibold text-gray-800 mb-4">Purchased Items</h4>
                    <div class="space-y-4">
                        <?php foreach ($order_items as $item): ?>
                            <div class="flex items-start justify-between">
                                <div class="flex items-center">
                                    <img src="<?= htmlspecialchars($item['image_url']) ?>"
                                        alt="<?= htmlspecialchars($item['title']) ?>"
                                        class="w-16 h-16 object-cover rounded">
                                    <div class="ml-4">
                                        <h5 class="font-medium text-gray-800"><?= htmlspecialchars($item['title']) ?></h5>
                                        <p class="text-sm text-gray-600">Digital Image - Standard License</p>
                                    </div>
                                </div>
                                <p class="font-medium text-gray-800">Lkr <?= number_format($item['price'], 2) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Total -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-800">Total</span>
                        <span class="text-lg font-bold text-gray-800">Lkr <?= number_format($total, 2) ?></span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 flex flex-col sm:flex-row gap-4">
                    <a href="gallery.php" class="flex-1 bg-blue-600 text-white text-center py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-200">
                        Continue Shopping
                    </a>
                    <button onclick="window.print()" class="flex-1 bg-gray-200 text-gray-800 py-3 px-4 rounded-lg hover:bg-gray-300 transition duration-200">
                        <i class="fas fa-print mr-2"></i> Print Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include('includes/footer.php'); ?>
</body>

</html>