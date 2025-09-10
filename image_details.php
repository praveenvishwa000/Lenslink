<?php
require_once 'config/db_conn.php';
session_start();

// Get image ID from URL
$image_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$image_id) {
    header('Location: gallery.php');
    exit();
}

// Fetch image details
$sql = "SELECT images.*, users.name as photographer_name, categories.name as category_name 
        FROM images 
        LEFT JOIN users ON images.user_id = users.id 
        LEFT JOIN categories ON images.category_id = categories.id 
        WHERE images.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $image_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: gallery.php');
    exit();
}

$image = $result->fetch_assoc();

// Check if the current user has purchased this image
$has_purchased = false;
$is_favorited = false;
if (isset($_SESSION['user_id'])) {
    $purchase_check_sql = "SELECT 1 FROM images i 
                          INNER JOIN order_items oi ON i.id = oi.image_id
                          INNER JOIN orders o ON oi.order_id = o.id
                          INNER JOIN payments p ON o.id = p.order_id
                          WHERE o.user_id = ? AND i.id = ? AND p.payment_status = 'completed'
                          LIMIT 1";
    $check_stmt = $conn->prepare($purchase_check_sql);
    $check_stmt->bind_param("ii", $_SESSION['user_id'], $image_id);
    $check_stmt->execute();
    $has_purchased = $check_stmt->get_result()->num_rows > 0;

    // Check if image is favorited
    $fav_check_sql = "SELECT 1 FROM favorites WHERE user_id = ? AND image_id = ?";
    $fav_stmt = $conn->prepare($fav_check_sql);
    $fav_stmt->bind_param("ii", $_SESSION['user_id'], $image_id);
    $fav_stmt->execute();
    $is_favorited = $fav_stmt->get_result()->num_rows > 0;
}

// Fetch related images from the same category
$related_sql = "SELECT i.*, u.name as photographer_name 
                FROM images i 
                LEFT JOIN users u ON i.user_id = u.id 
                WHERE i.category_id = ? 
                AND i.id != ? 
                AND i.is_public = 1 
                ORDER BY RAND() 
                LIMIT 4";
$stmt = $conn->prepare($related_sql);
$stmt->bind_param("ii", $image['category_id'], $image_id);
$stmt->execute();
$related_images = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($image['title']) ?> - LensLink</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <?php include('includes/navigation.php'); ?>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="md:flex">
                <!-- Left Column - Image -->
                <div class="md:w-2/3 relative">
                    <img src="<?= htmlspecialchars($image['image_url']) ?>"
                        alt="<?= htmlspecialchars($image['title']) ?>"
                        class="w-full h-auto object-cover">
                    <div class="absolute bottom-4 right-4 bg-black bg-opacity-50 text-white px-4 py-2 rounded-full">
                        <i class="fas fa-download mr-2"></i> High Resolution Available
                    </div>
                </div>

                <!-- Right Column - Details -->
                <div class="md:w-1/3 p-6">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($image['title']) ?></h1>
                    <p class="text-gray-600 mb-4">By: <?= htmlspecialchars($image['photographer_name']) ?></p>

                    <div class="flex items-center mb-4">
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                            <?= htmlspecialchars($image['category_name']) ?>
                        </span>
                    </div>

                    <p class="text-gray-700 mb-6"><?= nl2br(htmlspecialchars($image['description'])) ?></p>

                    <div class="mb-6">
                        <p class="text-3xl font-bold text-blue-600">Lkr <?= number_format($image['price'], 2) ?></p>
                        <p class="text-sm text-gray-500">Includes commercial license</p>
                    </div> <?php if (isset($_SESSION['user_id'])): ?>
                        <form method="POST" action="toggle_favorite.php">
                            <input type="hidden" name="image_id" value="<?= $image_id ?>">
                            <button type="submit"
                                class="mb-4 flex items-center justify-center w-full px-4 py-2 text-sm font-medium rounded-md <?= $is_favorited ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700' ?> hover:bg-opacity-90 transition-colors">
                                <i class="fas fa-heart mr-2"></i>
                                <span><?= $is_favorited ? 'Remove from Favorites' : 'Add to Favorites' ?></span>
                            </button>
                        </form>
                    <?php endif; ?>

                    <div class="space-y-4">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($has_purchased): ?>
                                <a href="download_image.php?id=<?= $image['id'] ?>"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-download mr-2"></i> Download Image
                                </a> <?php else: ?>
                                <button onclick="addToCart(<?= $image['id'] ?>)"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-cart-plus mr-2"></i> Add to Cart
                                </button>
                            <?php endif; ?>
                            <div id="addToCartMessage" class="hidden text-center p-2 rounded-lg"></div>
                        <?php else: ?>
                            <a href="signin.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center justify-center">
                                <i class="fas fa-sign-in-alt mr-2"></i> Sign in to Purchase
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="mt-6 border-t pt-6">
                        <h3 class="font-semibold text-gray-900 mb-2">Image Details:</h3>
                        <ul class="text-sm text-gray-600 space-y-2">
                            <li><i class="fas fa-calendar mr-2"></i> Uploaded: <?= date('F j, Y', strtotime($image['created_at'])) ?></li>
                            <li><i class="fas fa-image mr-2"></i> High Resolution Available</li>
                            <li><i class="fas fa-check-circle mr-2"></i> Commercial License Included</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Images Section -->
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Related Images</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php while ($related_image = $related_images->fetch_assoc()): ?>
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden group">
                        <a href="image_details.php?id=<?= $related_image['id'] ?>" class="block relative">
                            <img src="<?= htmlspecialchars($related_image['image_url']) ?>"
                                alt="<?= htmlspecialchars($related_image['title']) ?>"
                                class="w-full h-48 object-cover">
                            <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                <div class="text-center text-white p-4">
                                    <h3 class="font-bold mb-2"><?= htmlspecialchars($related_image['title']) ?></h3>
                                    <p class="text-sm">Lkr <?= number_format($related_image['price'], 2) ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include('includes/footer.php'); ?>

    <script>
        function addToCart(imageId) {
            fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        image_id: imageId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    const messageDiv = document.getElementById('addToCartMessage');
                    messageDiv.classList.remove('hidden');

                    if (data.success) {
                        // Update cart count in navigation
                        const cartCountSpan = document.querySelector('.fa-shopping-cart + span');
                        if (cartCountSpan) {
                            cartCountSpan.textContent = data.cart_count;
                        }

                        messageDiv.classList.remove('bg-red-100', 'text-red-700');
                        messageDiv.classList.add('bg-green-100', 'text-green-700');
                        messageDiv.textContent = data.message;
                    } else {
                        messageDiv.classList.remove('bg-green-100', 'text-green-700');
                        messageDiv.classList.add('bg-red-100', 'text-red-700');
                        messageDiv.textContent = data.message;
                    }

                    // Hide message after 3 seconds
                    setTimeout(() => {
                        messageDiv.classList.add('hidden');
                    }, 3000);
                })
                .catch(error => {
                    console.error('Error:', error);
                    const messageDiv = document.getElementById('addToCartMessage');
                    messageDiv.classList.remove('hidden', 'bg-green-100', 'text-green-700');
                    messageDiv.classList.add('bg-red-100', 'text-red-700');
                    messageDiv.textContent = 'Error adding item to cart';
                });
        }
    </script>
</body>

</html>