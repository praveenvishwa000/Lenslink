<?php
require_once 'config/db_conn.php';
require_once 'config/auth_check.php';
session_start();
checkUserAuth();

// Fetch cart items
$user_id = $_SESSION['user_id'];
$sql = "SELECT cart_items.*, images.title, images.image_url, images.price 
        FROM cart_items 
        JOIN images ON cart_items.image_id = images.id 
        WHERE cart_items.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Calculate total
$total = 0;
$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total += $row['price'];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - LensLink</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <?php include('includes/navigation.php'); ?>

    <!-- Cart Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Cart Items -->
            <div class="md:w-2/3">
                <div class="bg-white rounded-lg shadow-md p-6 mb-4">
                    <h2 class="text-2xl font-bold mb-6">Shopping Cart</h2>

                    <?php if (empty($cart_items)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-shopping-cart text-gray-400 text-5xl mb-4"></i>
                            <p class="text-gray-600 mb-4">Your cart is empty</p>
                            <a href="gallery.php" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                                Continue Shopping
                            </a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($cart_items as $item): ?>
                            <div class="flex items-center hover:bg-gray-100 px-6 py-5 border-b">
                                <div class="flex w-2/5">
                                    <div class="w-20">
                                        <img class="h-24 w-24 object-cover rounded"
                                            src="<?= htmlspecialchars($item['image_url']) ?>"
                                            alt="<?= htmlspecialchars($item['title']) ?>">
                                    </div>
                                    <div class="flex flex-col justify-between ml-4 flex-grow">
                                        <span class="font-bold text-sm"><?= htmlspecialchars($item['title']) ?></span>
                                        <span class="text-blue-600 text-xs">Digital Image</span>
                                        <button onclick="removeFromCart(<?= $item['id'] ?>)"
                                            class="font-semibold text-red-500 text-xs hover:text-red-600">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                                <span class="text-center w-1/5 font-semibold text-sm">
                                    License: Standard
                                </span>
                                <span class="text-center w-1/5 font-semibold text-sm">
                                    Lkr <?= number_format($item['price'], 2) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="md:w-1/3">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold mb-4">Order Summary</h2>
                    <div class="flex justify-between mb-2">
                        <span class="font-semibold text-sm">Subtotal</span>
                        <span class="font-bold">Lkr <?= number_format($total, 2) ?></span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="font-semibold text-sm">License Fees</span>
                        <span class="font-bold">Included</span>
                    </div>
                    <hr class="my-4">
                    <div class="flex justify-between mb-6">
                        <span class="font-semibold text-sm">Total</span>
                        <span class="font-bold text-xl">Lkr <?= number_format($total, 2) ?></span>
                    </div> <?php if (!empty($cart_items)): ?>
                        <button onclick="window.location.href='payment.php'"
                            class="bg-blue-600 text-white w-full px-6 py-3 rounded-lg hover:bg-blue-700">
                            <i class="fas fa-credit-card mr-2"></i>
                            Proceed to Payment
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Additional Information -->
                <div class="bg-white rounded-lg shadow-md p-6 mt-4">
                    <h3 class="font-bold text-sm mb-4">Purchase Information</h3>
                    <ul class="text-sm text-gray-600">
                        <li class="mb-2"><i class="fas fa-check text-green-500 mr-2"></i> High resolution images</li>
                        <li class="mb-2"><i class="fas fa-check text-green-500 mr-2"></i> Commercial license included</li>
                        <li class="mb-2"><i class="fas fa-check text-green-500 mr-2"></i> Immediate download after purchase</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i> 24/7 customer support</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include('includes/footer.php'); ?>

    <script>
        function removeFromCart(cartItemId) {
            if (confirm('Are you sure you want to remove this item from your cart?')) {
                fetch('remove_from_cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            cart_item_id: cartItemId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert('Error removing item from cart');
                        }
                    });
            }
        }
    </script>
</body>

</html>