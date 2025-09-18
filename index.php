<<<<<<< HEAD
<?php
session_start();

include_once 'config/db_conn.php';

$allImages = [];

$imageSql = "SELECT i.*, u.name as photographer FROM images i 
            LEFT JOIN users u ON i.user_id = u.id 
            WHERE i.is_public = 1 
            ORDER BY i.created_at DESC 
            LIMIT 4";
$imageResult = $conn->query($imageSql);
if ($imageResult && $imageResult->num_rows > 0) {
    while ($row = $imageResult->fetch_assoc()) {
        $allImages[] = $row;
    }
} else {
    // Handle case where no images are found
    $allImages = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LensLink - Sell & Buy Beautiful Images</title>
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

        /* Slider styles */
        .slider-container {
            position: relative;
        }

        .slider-track {
            display: flex;
            width: 300%;
            animation: slide 15s infinite linear;
        }

        @keyframes slide {
            0% {
                transform: translateX(0);
            }

            33% {
                transform: translateX(-33.33%);
            }

            66% {
                transform: translateX(-66.66%);
            }

            100% {
                transform: translateX(0);
            }
        }

        .slider-slide {
            flex-shrink: 0;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <?php
    include('includes/navigation.php');
    ?>


    <!-- Homepage Hero Slider -->
    <div class="relative">
        <div class="slider-container overflow-hidden h-96">
            <div class="slider-track flex transition-transform duration-1000 ease-in-out">
                <div class="slider-slide min-w-full">
                    <img src="Images/cover (1).jpg" alt="Nature" class="w-full h-96 object-cover">
                </div>
                <div class="slider-slide min-w-full">
                    <img src="Images/cover (2).jpg" alt="Portrait" class="w-full h-96 object-cover">
                </div>
                <div class="slider-slide min-w-full">
                    <img src="Images/cover (4).jpg" alt="Travel" class="w-full h-96 object-cover">
                </div>
            </div>
        </div>
        <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-30">
            <div class="text-center text-white max-w-2xl px-4">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Welcome to LensLink</h1>
                <p class="text-xl mb-6">Discover and share beautiful photography from around the world</p>
                <a href="gallery.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg text-lg font-medium inline-block">Browse Gallery</a>
            </div>
        </div>
        <div class="absolute bottom-0 w-full flex justify-center mb-4">
            <button class="slider-prev mx-2 text-white text-2xl bg-black bg-opacity-50 rounded-full w-10 h-10"><i class="fas fa-chevron-left"></i></button>
            <button class="slider-next mx-2 text-white text-2xl bg-black bg-opacity-50 rounded-full w-10 h-10"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>

    <!-- Featured Photos -->
    <div class="container mx-auto px-4 py-12">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Featured Photos</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Explore some of our most popular images from talented photographers worldwide</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <?php
            foreach ($allImages as $image) {
                // Include the image card component
                $imageTitle = htmlspecialchars($image['title']);
                $imageDescription = htmlspecialchars($image['description']);
                $imageSrc = htmlspecialchars($image['image_url']);
                $imageId = $image['id'];

                include 'includes/imageCard.php';
            }
            ?>
        </div>
    </div>

    <!-- About Us Section -->
    <div class="bg-gray-100 py-16">
        <div class="container mx-auto px-4">
            <div class="md:flex md:items-center md:space-x-12">
                <div class="md:w-1/2 mb-8 md:mb-0">
                    <img src="./Images/capture.jpg" alt="About PhotoArt" class="rounded-lg shadow-lg w-full">
                </div>
                <div class="md:w-1/2">
                    <h2 class="text-3xl font-bold text-gray-800 mb-6">About LensLink</h2>
                    <p class="text-gray-600 mb-4">Welcome to LensLink, the premier destination for buying and selling high-quality photography. Our platform connects talented photographers with individuals and businesses looking for stunning visuals.</p>
                    <p class="text-gray-600 mb-6">Whether you need images for commercial projects, personal use, or simply want to appreciate beautiful photography, we have something for everyone. Join our growing community of creatives today!</p>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 text-blue-600 mt-1 mr-3">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <p class="text-gray-700">High-quality images from professional photographers</p>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 text-blue-600 mt-1 mr-3">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <p class="text-gray-700">Secure payment system and licensed downloads</p>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 text-blue-600 mt-1 mr-3">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <p class="text-gray-700">Global community of photography enthusiasts</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    </div>

    <!-- Footer -->
    <?php
    include 'includes/footer.php';
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
                <form>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="imageTitle">Image Title</label>
                        <input type="text" id="imageTitle" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="imageDescription">Description</label>
                        <textarea id="imageDescription" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="imageCategory">Category</label>
                        <select id="imageCategory" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                            <option>Nature</option>
                            <option>Portrait</option>
                            <option>Travel</option>
                            <option>Architecture</option>
                            <option>Food</option>
                            <option>Fashion</option>
                            <option>Abstract</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="imageTags">Tags (comma separated)</label>
                        <input type="text" id="imageTags" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="sunset, beach, ocean">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="imagePrice">Price ($)</label>
                        <input type="number" id="imagePrice" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="50.00">
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
                                <input type="file" id="imageUpload" class="hidden" accept="image/*">
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

        // Slider functionality
        const sliderTrack = document.querySelector('.slider-track');
        const slides = document.querySelectorAll('.slider-slide');
        const prevBtn = document.querySelector('.slider-prev');
        const nextBtn = document.querySelector('.slider-next');
        let currentIndex = 0;

        function goToSlide(index) {
            sliderTrack.style.transform = `translateX(-${index * 100}%)`;
            currentIndex = index;
        }

        function nextSlide() {
            currentIndex = (currentIndex + 1) % slides.length;
            goToSlide(currentIndex);
        }

        function prevSlide() {
            currentIndex = (currentIndex - 1 + slides.length) % slides.length;
            goToSlide(currentIndex);
        }

        nextBtn.addEventListener('click', nextSlide);
        prevBtn.addEventListener('click', prevSlide);

        // Auto-play slider
        let sliderInterval = setInterval(nextSlide, 5000);

        // Pause on hover
        const sliderContainer = document.querySelector('.slider-container');
        sliderContainer.addEventListener('mouseenter', () => {
            clearInterval(sliderInterval);
        });

        sliderContainer.addEventListener('mouseleave', () => {
            sliderInterval = setInterval(nextSlide, 5000);
        });

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
session_start();

include_once 'config/db_conn.php';

$allImages = [];

$imageSql = "SELECT i.*, u.name as photographer FROM images i 
            LEFT JOIN users u ON i.user_id = u.id 
            WHERE i.is_public = 1 
            ORDER BY i.created_at DESC 
            LIMIT 4";
$imageResult = $conn->query($imageSql);
if ($imageResult && $imageResult->num_rows > 0) {
    while ($row = $imageResult->fetch_assoc()) {
        $allImages[] = $row;
    }
} else {
    // Handle case where no images are found
    $allImages = [];
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

        /* Slider styles */
        .slider-container {
            position: relative;
        }

        .slider-track {
            display: flex;
            width: 300%;
            animation: slide 15s infinite linear;
        }

        @keyframes slide {
            0% {
                transform: translateX(0);
            }

            33% {
                transform: translateX(-33.33%);
            }

            66% {
                transform: translateX(-66.66%);
            }

            100% {
                transform: translateX(0);
            }
        }

        .slider-slide {
            flex-shrink: 0;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <?php
    include('includes/navigation.php');
    ?>


    <!-- Homepage Hero Slider -->
    <div class="relative">
        <div class="slider-container overflow-hidden h-96">
            <div class="slider-track flex transition-transform duration-1000 ease-in-out">
                <div class="slider-slide min-w-full">
                    <img src="Images/cover (1).jpg" alt="Nature" class="w-full h-96 object-cover">
                </div>
                <div class="slider-slide min-w-full">
                    <img src="Images/cover (2).jpg" alt="Portrait" class="w-full h-96 object-cover">
                </div>
                <div class="slider-slide min-w-full">
                    <img src="Images/cover (4).jpg" alt="Travel" class="w-full h-96 object-cover">
                </div>
            </div>
        </div>
        <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-30">
            <div class="text-center text-white max-w-2xl px-4">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Welcome to LensLink</h1>
                <p class="text-xl mb-6">Discover and share beautiful photography from around the world</p>
                <a href="gallery.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg text-lg font-medium inline-block">Browse Gallery</a>
            </div>
        </div>
        <div class="absolute bottom-0 w-full flex justify-center mb-4">
            <button class="slider-prev mx-2 text-white text-2xl bg-black bg-opacity-50 rounded-full w-10 h-10"><i class="fas fa-chevron-left"></i></button>
            <button class="slider-next mx-2 text-white text-2xl bg-black bg-opacity-50 rounded-full w-10 h-10"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>

    <!-- Featured Photos -->
    <div class="container mx-auto px-4 py-12">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Featured Photos</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Explore some of our most popular images from talented photographers worldwide</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <?php
            foreach ($allImages as $image) {
                // Include the image card component
                $imageTitle = htmlspecialchars($image['title']);
                $imageDescription = htmlspecialchars($image['description']);
                $imageSrc = htmlspecialchars($image['image_url']);
                $imageId = $image['id'];

                include 'includes/imageCard.php';
            }
            ?>
        </div>
    </div>

    <!-- About Us Section -->
    <div class="bg-gray-100 py-16">
        <div class="container mx-auto px-4">
            <div class="md:flex md:items-center md:space-x-12">
                <div class="md:w-1/2 mb-8 md:mb-0">
                    <img src="./Images/capture.jpg" alt="About PhotoArt" class="rounded-lg shadow-lg w-full">
                </div>
                <div class="md:w-1/2">
                    <h2 class="text-3xl font-bold text-gray-800 mb-6">About LensLink</h2>
                    <p class="text-gray-600 mb-4">Welcome to LensLink, the premier destination for buying and selling high-quality photography. Our platform connects talented photographers with individuals and businesses looking for stunning visuals.</p>
                    <p class="text-gray-600 mb-6">Whether you need images for commercial projects, personal use, or simply want to appreciate beautiful photography, we have something for everyone. Join our growing community of creatives today!</p>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 text-blue-600 mt-1 mr-3">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <p class="text-gray-700">High-quality images from professional photographers</p>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 text-blue-600 mt-1 mr-3">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <p class="text-gray-700">Secure payment system and licensed downloads</p>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 text-blue-600 mt-1 mr-3">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <p class="text-gray-700">Global community of photography enthusiasts</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    </div>

    <!-- Footer -->
    <?php
    include 'includes/footer.php';
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
                <form>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="imageTitle">Image Title</label>
                        <input type="text" id="imageTitle" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="imageDescription">Description</label>
                        <textarea id="imageDescription" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="imageCategory">Category</label>
                        <select id="imageCategory" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                            <option>Nature</option>
                            <option>Portrait</option>
                            <option>Travel</option>
                            <option>Architecture</option>
                            <option>Food</option>
                            <option>Fashion</option>
                            <option>Abstract</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="imageTags">Tags (comma separated)</label>
                        <input type="text" id="imageTags" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="sunset, beach, ocean">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="imagePrice">Price ($)</label>
                        <input type="number" id="imagePrice" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="50.00">
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
                                <input type="file" id="imageUpload" class="hidden" accept="image/*">
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

        // Slider functionality
        const sliderTrack = document.querySelector('.slider-track');
        const slides = document.querySelectorAll('.slider-slide');
        const prevBtn = document.querySelector('.slider-prev');
        const nextBtn = document.querySelector('.slider-next');
        let currentIndex = 0;

        function goToSlide(index) {
            sliderTrack.style.transform = `translateX(-${index * 100}%)`;
            currentIndex = index;
        }

        function nextSlide() {
            currentIndex = (currentIndex + 1) % slides.length;
            goToSlide(currentIndex);
        }

        function prevSlide() {
            currentIndex = (currentIndex - 1 + slides.length) % slides.length;
            goToSlide(currentIndex);
        }

        nextBtn.addEventListener('click', nextSlide);
        prevBtn.addEventListener('click', prevSlide);

        // Auto-play slider
        let sliderInterval = setInterval(nextSlide, 5000);

        // Pause on hover
        const sliderContainer = document.querySelector('.slider-container');
        sliderContainer.addEventListener('mouseenter', () => {
            clearInterval(sliderInterval);
        });

        sliderContainer.addEventListener('mouseleave', () => {
            sliderInterval = setInterval(nextSlide, 5000);
        });

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