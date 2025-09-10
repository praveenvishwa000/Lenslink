<?php
require_once 'config/db_conn.php';
require_once 'config/auth_check.php';
session_start();

// Fetch favorite images
$sql = "SELECT i.*, u.name as photographer_name, c.name as category_name 
        FROM favorites f
        INNER JOIN images i ON f.image_id = i.id
        LEFT JOIN users u ON i.user_id = u.id
        LEFT JOIN categories c ON i.category_id = c.id
        WHERE f.user_id = ?
        ORDER BY f.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$favorites = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Favorites - LensLink</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <?php include('includes/navigation.php'); ?>

    <!-- Favorites Page Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">My Favorite Images</h1>
        </div>

        <?php if ($favorites->num_rows === 0): ?>
            <!-- Message when no favorites -->
            <div class="text-center py-12">
                <i class="fas fa-heart text-5xl text-gray-300 mb-4"></i>
                <h2 class="text-2xl font-semibold text-gray-600 mb-2">No favorites yet</h2>
                <p class="text-gray-500 mb-4">You haven't liked any images yet. Start exploring our gallery!</p>
                <a href="gallery.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg inline-block">
                    <i class="fas fa-images mr-2"></i> Browse Gallery
                </a>
            </div>
        <?php else: ?>
            <!-- Favorites Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php while ($image = $favorites->fetch_assoc()): ?>
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <a href="image_details.php?id=<?= $image['id'] ?>">
                            <img src="<?= htmlspecialchars($image['image_url']) ?>"
                                alt="<?= htmlspecialchars($image['title']) ?>"
                                class="w-full h-48 object-cover">
                        </a>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                <a href="image_details.php?id=<?= $image['id'] ?>" class="hover:text-blue-600">
                                    <?= htmlspecialchars($image['title']) ?>
                                </a>
                            </h3>
                            <p class="text-gray-600 text-sm mb-2">By: <?= htmlspecialchars($image['photographer_name']) ?></p>
                            <div class="flex justify-between items-center">
                                <span class="text-blue-600 font-semibold">Lkr <?= number_format($image['price'], 2) ?></span>
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                    <?= htmlspecialchars($image['category_name']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include('includes/footer.php'); ?>
</body>

</html>