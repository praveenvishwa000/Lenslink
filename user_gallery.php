<<<<<<< HEAD
<?php
require_once 'config/db_conn.php';
session_start();

// Fetch categories
$categories = [];
$sql = "SELECT id, name FROM categories ORDER BY name ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

if (isset($_SESSION['user_id'])) {
    $userid = $_SESSION['user_id'];
    $imgsql = "SELECT images.*, categories.name AS category_name FROM images 
            LEFT JOIN categories ON images.category_id = categories.id 
            WHERE is_public = 1 AND user_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($imgsql);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    header("Location: signin.php");
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PhotoArt - Sell & Buy Beautiful Images</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom styles */
        .image-card:hover .image-overlay {
            opacity: 1;
        }
        .modal {
            transition: opacity 0.3s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
     <?php
       include('includes/navigation.php');
     ?>

    <!-- Gallery Page Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">My Photo Gallery</h1>
            <?php
            if (isset($_SESSION['user_id'])) {
                echo '<button onclick="openUploadModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">';
                echo '<i class="fas fa-plus mr-2"></i> Upload New Image';
                echo '</button>';
            }
            ?>
        </div>
        
        <!-- Search and Filter -->
        <div class="flex flex-col md:flex-row justify-between mb-8">
            <div class="relative mb-4 md:mb-0">
                <input type="text" placeholder="Search images..." class="w-full md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
            </div>
            <div class="flex space-x-2">
                <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <option>All Categories</option>
                    <option>Nature</option>
                    <option>Portrait</option>
                    <option>Travel</option>
                    <option>Architecture</option>
                </select>
                <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <option>Sort By</option>
                    <option>Newest</option>
                    <option>Oldest</option>
                    <option>Most Popular</option>
                    <option>Price: Low to High</option>
                    <option>Price: High to Low</option>
                </select>
            </div>
        </div>
        
        <!-- Image Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <!-- Image Card 1 -->
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="relative overflow-hidden rounded-lg shadow-lg group image-card">
                    <img src="<?= htmlspecialchars($row['image_url']) ?>" 
                        alt="<?= htmlspecialchars($row['title']) ?>" 
                        class="w-full h-64 object-cover">

                    <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div class="text-center p-4">
                            <h3 class="text-white font-bold text-xl mb-2"><?= htmlspecialchars($row['title']) ?></h3>
                            <p class="text-white mb-1">Lkr <?= number_format($row['price'], 2) ?></p>
                            <p class="text-white text-sm mb-4"><?= htmlspecialchars($row['category_name']) ?></p>
                            <div class="flex justify-center space-x-4">
                                <button onclick="openImageModal('<?= htmlspecialchars($row['image_url']) ?>')" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-full">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <button class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-full">
                                    <i class="fas fa-heart"></i> Like
                                </button>
                                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-full">
                                    <i class="fas fa-cart-plus"></i> Buy
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
            
        </div>
        
        <!-- Pagination -->
        <div class="flex justify-center mt-10">
            <nav class="inline-flex rounded-md shadow">
                <a href="#" class="px-3 py-2 rounded-l-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                    Previous
                </a>
                <a href="#" class="px-3 py-2 border-t border-b border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                    1
                </a>
                <a href="#" class="px-3 py-2 border-t border-b border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                    2
                </a>
                <a href="#" class="px-3 py-2 border-t border-b border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                    3
                </a>
                <a href="#" class="px-3 py-2 rounded-r-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                    Next
                </a>
            </nav>
        </div>
    </div>

   <?php
   include 'includes/footer.php'
   ?>

    <!-- Image Preview Modal -->
    <div id="imageModal" class="fixed inset-0 z-50 hidden modal">
        <div class="absolute inset-0 bg-black bg-opacity-75" onclick="closeImageModal()"></div>
        <div class="relative max-w-4xl mx-auto my-8 p-4">
            <button onclick="closeImageModal()" class="absolute -top-12 right-0 text-white text-2xl hover:text-gray-300">
                <i class="fas fa-times"></i>
            </button>
            <div class="bg-white rounded-lg overflow-hidden">
                <div class="md:flex">
                    <div class="md:w-2/3">
                        <img id="modalImage" src="" alt="Preview" class="w-full h-auto">
                    </div>
                    <div class="md:w-1/3 p-6">
                        <h2 id="modalTitle" class="text-2xl font-bold mb-2">Beautiful Sunset</h2>
                        <p id="modalPhotographer" class="text-gray-600 mb-2">By: John Doe</p>
                        <div class="flex items-center mb-4">
                            <div class="flex text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <span class="text-gray-600 ml-2">(24 reviews)</span>
                        </div>
                        <p id="modalDescription" class="text-gray-700 mb-4">Stunning sunset over the ocean with vibrant colors reflecting on the water. Perfect for travel websites, wall art, or nature-related projects.</p>
                        <p id="modalPrice" class="text-3xl font-bold text-blue-600 mb-4">$45.00</p>
                        <div class="mb-4">
                            <h3 class="font-semibold mb-2">Available Sizes:</h3>
                            <div class="flex space-x-2">
                                <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">Small</button>
                                <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">Medium</button>
                                <button class="px-3 py-1 border border-blue-600 text-blue-600 rounded">Large</button>
                                <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">X-Large</button>
                            </div>
                        </div>
                        <div class="flex space-x-4 mb-4">
                            <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex-1 text-center">
                                <i class="fas fa-cart-plus mr-2"></i> Add to Cart
                            </button>
                            <button class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-lg">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                        <div class="text-sm text-gray-500">
                            <p><i class="fas fa-info-circle mr-2"></i> License included with purchase</p>
                            <p><i class="fas fa-download mr-2"></i> Instant download after payment</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Image Modal -->
    <div id="uploadModal" class="fixed inset-0 z-50 hidden modal">
        <div class="absolute inset-0 bg-black bg-opacity-75" onclick="closeUploadModal()"></div>
        <div class="relative max-w-2xl mx-auto my-8 p-4">
            <div class="bg-white rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold">Upload New Image</h2>
                    <button onclick="closeUploadModal()" class="text-gray-500 hover:text-gray-700 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form action="add_images.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="imageTitle">Image Title</label>
                        <input type="text" id="imageTitle" name="imageTitle" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="imageDescription">Description</label>
                        <textarea id="imageDescription" name="imageDescription" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="imageCategory">Category</label>
                        <select id="imageCategory" name="imageCategory" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                             <option value="">-- Select Category --</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category['id']) ?>">
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="imagePrice">Price ($)</label>
                        <input type="number" id="imagePrice" name="imagePrice" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="50.00">
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-700 mb-2">Upload Image</label>
                        <div class="border-2 border-dashed border-gray-300 rounded p-8 text-center">
                            <div id="uploadPreview" class="hidden mb-4">
                                <img id="previewImage" src="#" alt="Preview" class="max-h-48 mx-auto">
                            </div>
                            <div id="uploadPrompt" class="flex flex-col items-center">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                <p class="text-gray-600 mb-2">Drag & drop your image here or click to browse</p>
                                <input type="file" id="imageUpload" name="imageUpload" class="hidden" accept="image/*">
                                <label for="imageUpload" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded cursor-pointer">
                                    Select Image
                                </label>
                                <p class="text-xs text-gray-500 mt-2">JPG, PNG or GIF. Max size 10MB.</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" onclick="closeUploadModal()" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-100">
                            Cancel
                        </button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
                            Upload Image
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.querySelector('.mobile-menu-button');
        const mobileMenu = document.querySelector('.mobile-menu');
        
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Image modal functions
        function openImageModal(imageSrc) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            modalImage.src = imageSrc;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Upload modal functions
        function openUploadModal() {
            const modal = document.getElementById('uploadModal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeUploadModal() {
            const modal = document.getElementById('uploadModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Image upload preview
        const imageUpload = document.getElementById('imageUpload');
        const uploadPreview = document.getElementById('uploadPreview');
        const previewImage = document.getElementById('previewImage');
        const uploadPrompt = document.getElementById('uploadPrompt');

        imageUpload.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewImage.src = event.target.result;
                    uploadPreview.classList.remove('hidden');
                    uploadPrompt.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
=======
<?php
require_once 'config/db_conn.php';
session_start();

// Fetch categories
$categories = [];
$sql = "SELECT id, name FROM categories ORDER BY name ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

if (isset($_SESSION['user_id'])) {
    $userid = $_SESSION['user_id'];
    $imgsql = "SELECT images.*, categories.name AS category_name FROM images 
            LEFT JOIN categories ON images.category_id = categories.id 
            WHERE is_public = 1 AND user_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($imgsql);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    header("Location: signin.php");
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PhotoArt - Sell & Buy Beautiful Images</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom styles */
        .image-card:hover .image-overlay {
            opacity: 1;
        }
        .modal {
            transition: opacity 0.3s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
     <?php
       include('includes/navigation.php');
     ?>

    <!-- Gallery Page Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">My Photo Gallery</h1>
            <?php
            if (isset($_SESSION['user_id'])) {
                echo '<button onclick="openUploadModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">';
                echo '<i class="fas fa-plus mr-2"></i> Upload New Image';
                echo '</button>';
            }
            ?>
        </div>
        
        <!-- Search and Filter -->
        <div class="flex flex-col md:flex-row justify-between mb-8">
            <div class="relative mb-4 md:mb-0">
                <input type="text" placeholder="Search images..." class="w-full md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
            </div>
            <div class="flex space-x-2">
                <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <option>All Categories</option>
                    <option>Nature</option>
                    <option>Portrait</option>
                    <option>Travel</option>
                    <option>Architecture</option>
                </select>
                <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <option>Sort By</option>
                    <option>Newest</option>
                    <option>Oldest</option>
                    <option>Most Popular</option>
                    <option>Price: Low to High</option>
                    <option>Price: High to Low</option>
                </select>
            </div>
        </div>
        
        <!-- Image Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <!-- Image Card 1 -->
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="relative overflow-hidden rounded-lg shadow-lg group image-card">
                    <img src="<?= htmlspecialchars($row['image_url']) ?>" 
                        alt="<?= htmlspecialchars($row['title']) ?>" 
                        class="w-full h-64 object-cover">

                    <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div class="text-center p-4">
                            <h3 class="text-white font-bold text-xl mb-2"><?= htmlspecialchars($row['title']) ?></h3>
                            <p class="text-white mb-1">Lkr <?= number_format($row['price'], 2) ?></p>
                            <p class="text-white text-sm mb-4"><?= htmlspecialchars($row['category_name']) ?></p>
                            <div class="flex justify-center space-x-4">
                                <button onclick="openImageModal('<?= htmlspecialchars($row['image_url']) ?>')" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-full">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <button class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-full">
                                    <i class="fas fa-heart"></i> Like
                                </button>
                                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-full">
                                    <i class="fas fa-cart-plus"></i> Buy
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
            
        </div>
        
        <!-- Pagination -->
        <div class="flex justify-center mt-10">
            <nav class="inline-flex rounded-md shadow">
                <a href="#" class="px-3 py-2 rounded-l-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                    Previous
                </a>
                <a href="#" class="px-3 py-2 border-t border-b border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                    1
                </a>
                <a href="#" class="px-3 py-2 border-t border-b border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                    2
                </a>
                <a href="#" class="px-3 py-2 border-t border-b border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                    3
                </a>
                <a href="#" class="px-3 py-2 rounded-r-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                    Next
                </a>
            </nav>
        </div>
    </div>

   <?php
   include 'includes/footer.php'
   ?>

    <!-- Image Preview Modal -->
    <div id="imageModal" class="fixed inset-0 z-50 hidden modal">
        <div class="absolute inset-0 bg-black bg-opacity-75" onclick="closeImageModal()"></div>
        <div class="relative max-w-4xl mx-auto my-8 p-4">
            <button onclick="closeImageModal()" class="absolute -top-12 right-0 text-white text-2xl hover:text-gray-300">
                <i class="fas fa-times"></i>
            </button>
            <div class="bg-white rounded-lg overflow-hidden">
                <div class="md:flex">
                    <div class="md:w-2/3">
                        <img id="modalImage" src="" alt="Preview" class="w-full h-auto">
                    </div>
                    <div class="md:w-1/3 p-6">
                        <h2 id="modalTitle" class="text-2xl font-bold mb-2">Beautiful Sunset</h2>
                        <p id="modalPhotographer" class="text-gray-600 mb-2">By: John Doe</p>
                        <div class="flex items-center mb-4">
                            <div class="flex text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <span class="text-gray-600 ml-2">(24 reviews)</span>
                        </div>
                        <p id="modalDescription" class="text-gray-700 mb-4">Stunning sunset over the ocean with vibrant colors reflecting on the water. Perfect for travel websites, wall art, or nature-related projects.</p>
                        <p id="modalPrice" class="text-3xl font-bold text-blue-600 mb-4">$45.00</p>
                        <div class="mb-4">
                            <h3 class="font-semibold mb-2">Available Sizes:</h3>
                            <div class="flex space-x-2">
                                <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">Small</button>
                                <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">Medium</button>
                                <button class="px-3 py-1 border border-blue-600 text-blue-600 rounded">Large</button>
                                <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">X-Large</button>
                            </div>
                        </div>
                        <div class="flex space-x-4 mb-4">
                            <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex-1 text-center">
                                <i class="fas fa-cart-plus mr-2"></i> Add to Cart
                            </button>
                            <button class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-lg">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                        <div class="text-sm text-gray-500">
                            <p><i class="fas fa-info-circle mr-2"></i> License included with purchase</p>
                            <p><i class="fas fa-download mr-2"></i> Instant download after payment</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Image Modal -->
    <div id="uploadModal" class="fixed inset-0 z-50 hidden modal">
        <div class="absolute inset-0 bg-black bg-opacity-75" onclick="closeUploadModal()"></div>
        <div class="relative max-w-2xl mx-auto my-8 p-4">
            <div class="bg-white rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold">Upload New Image</h2>
                    <button onclick="closeUploadModal()" class="text-gray-500 hover:text-gray-700 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form action="add_images.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="imageTitle">Image Title</label>
                        <input type="text" id="imageTitle" name="imageTitle" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="imageDescription">Description</label>
                        <textarea id="imageDescription" name="imageDescription" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="imageCategory">Category</label>
                        <select id="imageCategory" name="imageCategory" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                             <option value="">-- Select Category --</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category['id']) ?>">
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="imagePrice">Price ($)</label>
                        <input type="number" id="imagePrice" name="imagePrice" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="50.00">
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-700 mb-2">Upload Image</label>
                        <div class="border-2 border-dashed border-gray-300 rounded p-8 text-center">
                            <div id="uploadPreview" class="hidden mb-4">
                                <img id="previewImage" src="#" alt="Preview" class="max-h-48 mx-auto">
                            </div>
                            <div id="uploadPrompt" class="flex flex-col items-center">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                <p class="text-gray-600 mb-2">Drag & drop your image here or click to browse</p>
                                <input type="file" id="imageUpload" name="imageUpload" class="hidden" accept="image/*">
                                <label for="imageUpload" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded cursor-pointer">
                                    Select Image
                                </label>
                                <p class="text-xs text-gray-500 mt-2">JPG, PNG or GIF. Max size 10MB.</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" onclick="closeUploadModal()" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-100">
                            Cancel
                        </button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
                            Upload Image
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.querySelector('.mobile-menu-button');
        const mobileMenu = document.querySelector('.mobile-menu');
        
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Image modal functions
        function openImageModal(imageSrc) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            modalImage.src = imageSrc;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Upload modal functions
        function openUploadModal() {
            const modal = document.getElementById('uploadModal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeUploadModal() {
            const modal = document.getElementById('uploadModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Image upload preview
        const imageUpload = document.getElementById('imageUpload');
        const uploadPreview = document.getElementById('uploadPreview');
        const previewImage = document.getElementById('previewImage');
        const uploadPrompt = document.getElementById('uploadPrompt');

        imageUpload.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewImage.src = event.target.result;
                    uploadPreview.classList.remove('hidden');
                    uploadPrompt.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
>>>>>>> 644698dfc1ca2b7d65e44b7ba9e874a5fe15fc50
</html>