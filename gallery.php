<?php
session_start();

include_once 'config/db_conn.php';

// Get search and sort parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Base query
$sql = "SELECT i.*, u.name as photographer FROM images i 
        LEFT JOIN users u ON i.user_id = u.id 
        WHERE i.is_public = 1";

// Add search condition if search term is provided
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $sql .= " AND (i.title LIKE '%$search%' OR i.description LIKE '%$search%')";
}

// Add sorting
switch ($sort) {
    case 'oldest':
        $sql .= " ORDER BY i.created_at ASC";
        break;
    case 'price-low':
        $sql .= " ORDER BY i.price ASC";
        break;
    case 'price-high':
        $sql .= " ORDER BY i.price DESC";
        break;
    default: // newest
        $sql .= " ORDER BY i.created_at DESC";
        break;
}

$result = $conn->query($sql);
$images = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LensLink - Gallery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .image-card:hover .image-overlay {
            opacity: 1;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <?php include 'includes/navigation.php'; ?>

    <!-- Gallery Header -->
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-8 px-4">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Photo Gallery</h1>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <!-- Sort Controls -->
                <div class="flex items-center space-x-4">
                    <select id="sort" name="sort" onchange="updateFilters()"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                        <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest First
                        </option>
                        <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Oldest First
                        </option>
                        <option value="price-low" <?php echo $sort === 'price-low' ? 'selected' : ''; ?>>Price: Low to
                            High</option>
                        <option value="price-high" <?php echo $sort === 'price-high' ? 'selected' : ''; ?>>Price: High
                            to Low</option>
                    </select>
                </div>
                <!-- Search Bar -->
                <div class="relative flex-1 max-w-lg">
                    <form id="searchForm" onsubmit="return false;">
                        <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>"
                            placeholder="Search images..." onkeyup="handleSearch(event)"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 pl-10">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Gallery Grid -->
    <div class="max-w-7xl mx-auto py-12 px-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($images as $image): ?>
                <div class="image-card relative group overflow-hidden rounded-lg shadow-lg">
                    <img src="<?php echo htmlspecialchars($image['image_url']); ?>"
                        alt="<?php echo htmlspecialchars($image['title']); ?>"
                        class="w-full h-64 object-cover">
                    <div class="image-overlay absolute inset-0 bg-black bg-opacity-50 opacity-0 transition-opacity duration-300 flex flex-col justify-end p-4">
                        <h3 class="text-white text-lg font-semibold mb-1"><?php echo htmlspecialchars($image['title']); ?></h3>
                        <p class="text-white text-sm mb-1">By: <?php echo htmlspecialchars($image['photographer']); ?></p>
                        <div class="flex justify-between items-center">
                            <p class="text-white text-sm"><?php echo substr(htmlspecialchars($image['description']), 0, 50) . '...'; ?></p>
                            <p class="text-white font-bold">Lkr <?php echo number_format($image['price'], 2); ?></p>
                        </div>
                        <a href="image_details.php?id=<?php echo $image['id']; ?>"
                            class="mt-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm text-center">
                            View Details
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <div class="mt-12 flex justify-center">
            <nav class="flex items-center space-x-2">
                <button class="px-3 py-2 border rounded-lg hover:bg-gray-100">Previous</button>
                <button class="px-3 py-2 bg-blue-600 text-white rounded-lg">1</button>
                <button class="px-3 py-2 border rounded-lg hover:bg-gray-100">2</button>
                <button class="px-3 py-2 border rounded-lg hover:bg-gray-100">3</button>
                <button class="px-3 py-2 border rounded-lg hover:bg-gray-100">Next</button>
            </nav>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script>
        // Mobile menu functionality
        const mobileMenuButton = document.querySelector('.mobile-menu-button');
        const mobileMenu = document.querySelector('.mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        let searchTimeout;

        function handleSearch(event) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                updateFilters();
            }, 500); // Wait for 500ms after user stops typing
        }

        function updateFilters() {
            const sort = document.getElementById('sort').value;
            const search = document.getElementById('search').value;

            // Build the URL with parameters
            const params = new URLSearchParams();
            if (sort) params.set('sort', sort);
            if (search) params.set('search', search);

            // Redirect to the new URL
            window.location.href = 'gallery.php?' + params.toString();
        }
    </script>
</body>

</html>