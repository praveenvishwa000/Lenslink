<?php
require_once 'config/db_conn.php';
require_once 'config/auth_check.php';
session_start();
checkUserAuth();

$user_id = $_SESSION['user_id'];

// Fetch cart items
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

// If cart is empty, redirect back to cart page
if (empty($cart_items)) {
    header('Location: cart.php');
    exit();
}

// Fetch user details for pre-filling the form
$user_sql = "SELECT name, email FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PhotoArt - Payment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom styles */
        .card-input:focus+.card-icon {
            color: #3b82f6;
        }

        .payment-method:checked+.payment-label {
            border-color: #3b82f6;
            background-color: #f0f7ff;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <?php
    include('includes/navigation.php');
    ?>

    <!-- Payment Page Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Payment Form Section -->
            <div class="md:w-2/3">
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Payment Details</h2>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline"><?php echo $_SESSION['error']; ?></span>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <form action="process_payment.php" method="POST" id="payment-form" onsubmit="return validateForm();">
                        <!-- Payment Method Selection -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Payment Method</h3>
                            <div class="grid grid-cols-1 gap-4">
                                <!-- Credit Card -->
                                <div>
                                    <input type="radio" id="credit-card" name="payment-method" value="credit-card" class="hidden payment-method" checked>
                                    <label for="credit-card" class="payment-label block border-2 border-gray-200 rounded-lg p-4 text-center cursor-pointer hover:border-blue-500 transition duration-200">
                                        <i class="far fa-credit-card text-3xl text-blue-600 mb-2"></i>
                                        <span class="font-medium">Credit Card</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Credit Card Form (shown by default) -->
                        <div id="credit-card-form">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-2">
                                    <label for="card-number" class="block text-sm font-medium text-gray-700">Card Number</label>
                                    <input type="text" id="card-number" name="card-number" class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" maxlength="19" required>
                                </div>
                                <div>
                                    <label for="expiry" class="block text-sm font-medium text-gray-700">Expiry Date</label>
                                    <input type="text" id="expiry" name="expiry" placeholder="MM/YY" class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" maxlength="5" required>
                                </div>
                                <div>
                                    <label for="cvv" class="block text-sm font-medium text-gray-700">CVV</label>
                                    <input type="text" id="cvv" name="cvv" class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" maxlength="4" required>
                                </div>
                                <div class="col-span-2">
                                    <label for="card-name" class="block text-sm font-medium text-gray-700">Name on Card</label>
                                    <input type="text" id="card-name" name="card-name" class="mt-1 block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                </div>
                            </div>
                        </div>

                        <!-- Proceed to Payment Button -->
                        <div class="mt-8">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-lg text-lg transition duration-200">
                                Proceed to Payment
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Security Info -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-start">
                        <div class="text-green-500 mr-4 mt-1">
                            <i class="fas fa-lock text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Secure Payment</h3>
                            <p class="text-gray-600">All transactions are secure and encrypted. We do not store your credit card information.</p>
                            <div class="flex flex-wrap gap-4 mt-4">
                                <img src="https://via.placeholder.com/50x30?text=Visa" alt="Visa" class="h-8">
                                <img src="https://via.placeholder.com/50x30?text=MC" alt="Mastercard" class="h-8">
                                <img src="https://via.placeholder.com/50x30?text=Amex" alt="American Express" class="h-8">
                                <img src="https://via.placeholder.com/50x30?text=Discover" alt="Discover" class="h-8">
                                <img src="https://via.placeholder.com/50x30?text=PayPal" alt="PayPal" class="h-8">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary Section -->
            <div class="md:w-1/3">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Order Summary</h2> <!-- Items List -->
                    <div class="space-y-4 mb-6">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="flex justify-between items-start">
                                <div class="flex">
                                    <div class="w-20 h-20 rounded-lg overflow-hidden mr-4">
                                        <img src="<?= htmlspecialchars($item['image_url']) ?>"
                                            alt="<?= htmlspecialchars($item['title']) ?>"
                                            class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-800"><?= htmlspecialchars($item['title']) ?></h4>
                                        <p class="text-gray-600 text-sm">Digital Image</p>
                                        <p class="text-gray-600 text-sm">License: Standard</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium text-gray-800">Lkr <?= number_format($item['price'], 2) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Order Totals -->
                    <div class="border-t border-gray-200 pt-4 mb-6">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium">Lkr <?= number_format($total, 2) ?></span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">License Fees</span>
                            <span class="font-medium">Included</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg mt-4 pt-2 border-t border-gray-200">
                            <span>Total</span>
                            <span>Lkr <?= number_format($total, 2) ?></span>
                        </div>
                    </div> <!-- Help Link -->
                    <div class="text-center">
                        <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Need help? Contact our support</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php
    include 'includes/footer.php';
    ?>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.querySelector('.mobile-menu-button');
        const mobileMenu = document.querySelector('.mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Payment method toggle
        const paymentMethods = document.querySelectorAll('.payment-method');
        const creditCardForm = document.getElementById('credit-card-form');

        paymentMethods.forEach(method => {
            method.addEventListener('change', function() {
                // Hide all forms first
                creditCardForm.classList.add('hidden');

                // Show the selected form
                if (this.value === 'credit-card') {
                    creditCardForm.classList.remove('hidden');
                }
            });
        });

        // Format card number input
        const cardNumberInput = document.getElementById('card-number');
        cardNumberInput.addEventListener('input', function(e) {
            // Remove all non-digit characters
            let value = this.value.replace(/\D/g, '');

            // Add space after every 4 digits
            value = value.replace(/(\d{4})(?=\d)/g, '$1 ');

            // Update the input value
            this.value = value;
        }); // Format expiry date input
        const expiryInput = document.getElementById('expiry');
        expiryInput.addEventListener('input', function(e) {
            // Remove all non-digit characters
            let value = this.value.replace(/\D/g, '');

            // Add slash after 2 digits (MM/YY format)
            if (value.length > 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }

            // Update the input value
            this.value = value;
        });

        // Form validation
        function validateForm() {
            const cardNumber = document.getElementById('card-number').value.replace(/\s/g, '');
            const expiry = document.getElementById('expiry').value;
            const cvv = document.getElementById('cvv').value;
            const cardName = document.getElementById('card-name').value;

            // Validate card number (16 digits)
            if (!/^\d{16}$/.test(cardNumber)) {
                alert('Please enter a valid 16-digit card number');
                return false;
            }

            // Validate expiry date (MM/YY format)
            if (!/^\d{2}\/\d{2}$/.test(expiry)) {
                alert('Please enter a valid expiry date (MM/YY)');
                return false;
            }

            // Validate CVV (3 or 4 digits)
            if (!/^\d{3,4}$/.test(cvv)) {
                alert('Please enter a valid CVV');
                return false;
            }

            // Validate card name
            if (cardName.trim().length < 3) {
                alert('Please enter the name on card');
                return false;
            }

            return true;
        }
    </script>
</body>

</html>