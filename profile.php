<<<<<<< HEAD
<?php
session_start();
require_once 'config/db_conn.php';
require_once 'config/auth_check.php';

// Check if user is logged in
checkUserAuth();

$user_id = $_SESSION['user_id'];

// Fetch user's purchased images
$sql = "SELECT i.*, p.payment_status, p.payment_date 
        FROM images i 
        INNER JOIN order_items oi ON i.id = oi.image_id
        INNER JOIN orders o ON oi.order_id = o.id
        INNER JOIN payments p ON o.id = p.order_id
        WHERE o.user_id = ? AND p.payment_status = 'completed' 
        ORDER BY p.payment_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch user details
$user_sql = "SELECT * FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_data = $user_stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - PhotoArt</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <?php include('includes/navigation.php'); ?>

    <div class="container mx-auto px-4 py-8">
        <!-- Profile Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center space-x-4">
                <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center">
                    <span class="text-3xl text-white font-bold">
                        <?php echo strtoupper(substr($user_data['name'], 0, 1)); ?>
                    </span>
                </div>
                <div class="flex-grow">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($user_data['name']); ?>'s Profile</h1>
                            <p class="text-gray-600">Member since <?php echo date('F Y', strtotime($user_data['created_at'])); ?></p>
                            <p class="text-gray-600"><?php echo htmlspecialchars($user_data['email']); ?></p>
                        </div>
                        <button onclick="openEditModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                            <i class="fas fa-edit mr-2"></i> Edit Profile
                        </button>
                    </div>
                    <?php
                    // Get count of purchased images
                    $count_sql = "SELECT COUNT(*) as total 
                                FROM payments p
                                INNER JOIN orders o ON p.order_id = o.id
                                WHERE o.user_id = ? AND p.payment_status = 'completed'";
                    $count_stmt = $conn->prepare($count_sql);
                    $count_stmt->bind_param("i", $user_id);
                    $count_stmt->execute();
                    $count_result = $count_stmt->get_result()->fetch_assoc();
                    ?>
                    <div class="mt-2 text-sm text-gray-600">
                        <span class="font-semibold"><?php echo $count_result['total']; ?></span> images purchased
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchased Images Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">My Purchased Images</h2>

            <?php if ($result->num_rows > 0): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <?php while ($image = $result->fetch_assoc()): ?>
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                            <img src="<?php echo htmlspecialchars($image['image_url']); ?>"
                                alt="<?php echo htmlspecialchars($image['title']); ?>"
                                class="w-full h-48 object-cover">
                            <div class="p-4">
                                <h3 class="font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($image['title']); ?></h3>
                                <p class="text-sm text-gray-600 mb-2">Purchased: <?php echo date('M d, Y', strtotime($image['payment_date'])); ?></p>
                                <a href="download_image.php?id=<?php echo $image['id']; ?>"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-flex items-center w-full justify-center">
                                    <i class="fas fa-download mr-2"></i> Download
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-shopping-bag text-gray-400 text-5xl mb-4"></i>
                    <p class="text-gray-600">You haven't purchased any images yet.</p>
                    <a href="index.php" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">Browse Images</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <!-- Edit Profile Modal -->
    <div id="editProfileModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeEditModal()"></div>
        <div class="relative max-w-md mx-auto my-8 p-4">
            <div class="bg-white rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold">Edit Profile</h2>
                    <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="editProfileForm" onsubmit="handleEditProfile(event)">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="name">Full Name</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user_data['name']); ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="newPassword">New Password (leave blank to keep current)</label>
                        <input type="password" id="newPassword" name="newPassword"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="confirmPassword">Confirm New Password</label>
                        <input type="password" id="confirmPassword" name="confirmPassword"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-100">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openEditModal() {
            document.getElementById('editProfileModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            document.getElementById('editProfileModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function handleEditProfile(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            // Check if passwords match if a new password is being set
            const newPassword = formData.get('newPassword');
            const confirmPassword = formData.get('confirmPassword');
            if (newPassword && newPassword !== confirmPassword) {
                alert('Passwords do not match!');
                return;
            }

            fetch('update_profile.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Profile updated successfully!');
                        closeEditModal();
                        window.location.reload();
                    } else {
                        alert('Error updating profile: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating profile');
                });
        }
    </script>
</body>

=======
<?php
session_start();
require_once 'config/db_conn.php';
require_once 'config/auth_check.php';

// Check if user is logged in
checkUserAuth();

$user_id = $_SESSION['user_id'];

// Fetch user's purchased images
$sql = "SELECT i.*, p.payment_status, p.payment_date 
        FROM images i 
        INNER JOIN order_items oi ON i.id = oi.image_id
        INNER JOIN orders o ON oi.order_id = o.id
        INNER JOIN payments p ON o.id = p.order_id
        WHERE o.user_id = ? AND p.payment_status = 'completed' 
        ORDER BY p.payment_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch user details
$user_sql = "SELECT * FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_data = $user_stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - PhotoArt</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <?php include('includes/navigation.php'); ?>

    <div class="container mx-auto px-4 py-8">
        <!-- Profile Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center space-x-4">
                <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center">
                    <span class="text-3xl text-white font-bold">
                        <?php echo strtoupper(substr($user_data['name'], 0, 1)); ?>
                    </span>
                </div>
                <div class="flex-grow">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($user_data['name']); ?>'s Profile</h1>
                            <p class="text-gray-600">Member since <?php echo date('F Y', strtotime($user_data['created_at'])); ?></p>
                            <p class="text-gray-600"><?php echo htmlspecialchars($user_data['email']); ?></p>
                        </div>
                        <button onclick="openEditModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                            <i class="fas fa-edit mr-2"></i> Edit Profile
                        </button>
                    </div>
                    <?php
                    // Get count of purchased images
                    $count_sql = "SELECT COUNT(*) as total 
                                FROM payments p
                                INNER JOIN orders o ON p.order_id = o.id
                                WHERE o.user_id = ? AND p.payment_status = 'completed'";
                    $count_stmt = $conn->prepare($count_sql);
                    $count_stmt->bind_param("i", $user_id);
                    $count_stmt->execute();
                    $count_result = $count_stmt->get_result()->fetch_assoc();
                    ?>
                    <div class="mt-2 text-sm text-gray-600">
                        <span class="font-semibold"><?php echo $count_result['total']; ?></span> images purchased
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchased Images Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">My Purchased Images</h2>

            <?php if ($result->num_rows > 0): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <?php while ($image = $result->fetch_assoc()): ?>
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                            <img src="<?php echo htmlspecialchars($image['image_url']); ?>"
                                alt="<?php echo htmlspecialchars($image['title']); ?>"
                                class="w-full h-48 object-cover">
                            <div class="p-4">
                                <h3 class="font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($image['title']); ?></h3>
                                <p class="text-sm text-gray-600 mb-2">Purchased: <?php echo date('M d, Y', strtotime($image['payment_date'])); ?></p>
                                <a href="download_image.php?id=<?php echo $image['id']; ?>"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-flex items-center w-full justify-center">
                                    <i class="fas fa-download mr-2"></i> Download
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-shopping-bag text-gray-400 text-5xl mb-4"></i>
                    <p class="text-gray-600">You haven't purchased any images yet.</p>
                    <a href="index.php" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">Browse Images</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <!-- Edit Profile Modal -->
    <div id="editProfileModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeEditModal()"></div>
        <div class="relative max-w-md mx-auto my-8 p-4">
            <div class="bg-white rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold">Edit Profile</h2>
                    <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="editProfileForm" onsubmit="handleEditProfile(event)">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="name">Full Name</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user_data['name']); ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="newPassword">New Password (leave blank to keep current)</label>
                        <input type="password" id="newPassword" name="newPassword"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="confirmPassword">Confirm New Password</label>
                        <input type="password" id="confirmPassword" name="confirmPassword"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-100">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openEditModal() {
            document.getElementById('editProfileModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            document.getElementById('editProfileModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function handleEditProfile(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            // Check if passwords match if a new password is being set
            const newPassword = formData.get('newPassword');
            const confirmPassword = formData.get('confirmPassword');
            if (newPassword && newPassword !== confirmPassword) {
                alert('Passwords do not match!');
                return;
            }

            fetch('update_profile.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Profile updated successfully!');
                        closeEditModal();
                        window.location.reload();
                    } else {
                        alert('Error updating profile: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating profile');
                });
        }
    </script>
</body>

>>>>>>> 644698dfc1ca2b7d65e44b7ba9e874a5fe15fc50
</html>