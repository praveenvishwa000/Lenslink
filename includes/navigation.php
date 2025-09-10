<?php
// Get the current page filename
$current_page = basename($_SERVER['PHP_SELF']);

// Initialize cart count - you may want to get this from your database/session
$cart_count = isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : 0;
?>
<nav class="bg-white shadow-lg">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex justify-between">
            <div class="flex space-x-7">
                <div>
                    <!-- Logo -->
                    <a href="index.php" class="flex items-center py-4 px-2">
                        <span class="font-semibold text-gray-900 text-2xl">
                            <i class="fas fa-camera-retro mr-2 text-blue-600"></i>LensLink
                        </span>
                    </a>
                </div>
                <!-- Primary Navbar items -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="index.php" class="py-4 px-2 <?php echo $current_page == 'index.php' ? 'text-blue-600 border-b-4 border-blue-600' : 'text-gray-700 hover:text-blue-600 transition duration-300'; ?>">Home</a>
                    <a href="gallery.php" class="py-4 px-2 <?php echo $current_page == 'gallery.php' ? 'text-blue-600 border-b-4 border-blue-600' : 'text-gray-700 hover:text-blue-600 transition duration-300'; ?>">Gallery</a>
                    <?php if (isset($_SESSION['user_id'])) { ?>
                        <a href="uploads.php" class="py-4 px-2 <?php echo $current_page == 'uploads.php' ? 'text-blue-600 border-b-4 border-blue-600' : 'text-gray-700 hover:text-blue-600 transition duration-300'; ?>">Uploads</a>
                        <a href="favorites.php" class="py-4 px-2 <?php echo $current_page == 'favorites.php' ? 'text-blue-600 border-b-4 border-blue-600' : 'text-gray-700 hover:text-blue-600 transition duration-300'; ?>">Favorites</a>
                    <?php } ?>
                </div>
            </div>
            <!-- Secondary Navbar items -->
            <?php
            if (isset($_SESSION['user_id'])) {
                // User is logged in
                echo '<div class="hidden md:flex items-center space-x-3">
                         <a href="cart.php" class="py-2 px-2 font-medium relative">
                            <i class="fas fa-shopping-cart text-xl text-gray-700 hover:text-blue-600 transition duration-300"></i>
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">' . $cart_count . '</span>
                         </a>
                         <a href="profile.php" class="py-2 px-2 font-medium text-gray-700 hover:text-blue-600 transition duration-300">
                            <i class="fas fa-user mr-1"></i> Profile
                         </a>
                         <a href="logout.php" class="py-2 px-2 font-medium text-white bg-blue-600 rounded hover:bg-blue-700 transition duration-300">Logout</a>
                       </div>';
            } else {
                // User is not logged in
                echo '<div class="hidden md:flex items-center space-x-3">
                         <a href="signin.php" class="py-2 px-2 font-medium ' . ($current_page == 'signin.php' ? 'text-blue-600' : 'text-gray-700 hover:text-blue-600 transition duration-300') . '">Sign In</a>
                         <a href="signup.php" class="py-2 px-2 font-medium text-white bg-blue-600 rounded hover:bg-blue-700 transition duration-300">Sign Up</a>
                       </div>';
            }
            ?>
            <!-- Mobile menu button -->
            <div class="md:hidden flex items-center">
                <button class="outline-none mobile-menu-button">
                    <svg class="w-6 h-6 text-gray-700 hover:text-blue-600"
                        x-show="!showMenu"
                        fill="none"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <!-- Mobile menu -->
    <div class="hidden mobile-menu">
        <ul class="">
            <li class="active"><a href="index.php" class="block text-sm px-2 py-4 text-white bg-blue-600 font-semibold">Home</a></li>
            <li><a href="gallery.php" class="block text-sm px-2 py-4 hover:bg-blue-600 hover:text-white transition duration-300">Gallery</a></li>
            <?php if (isset($_SESSION['user_id'])) { ?>
                <li><a href="uploads.php" class="block text-sm px-2 py-4 hover:bg-blue-600 hover:text-white transition duration-300">Uploads</a></li>
                <li><a href="favorites.php" class="block text-sm px-2 py-4 hover:bg-blue-600 hover:text-white transition duration-300">Favorites</a></li>
                <li><a href="profile.php" class="block text-sm px-2 py-4 hover:bg-blue-600 hover:text-white transition duration-300">Profile</a></li>
            <?php } ?>
            <li><a href="signin.php" class="block text-sm px-2 py-4 hover:bg-blue-600 hover:text-white transition duration-300">Sign In</a></li>
            <li><a href="signup.php" class="block text-sm px-2 py-4 hover:bg-blue-600 hover:text-white transition duration-300">Sign Up</a></li>
        </ul>
    </div>
</nav>