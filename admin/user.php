<?php
session_start();
require_once '../config/db_conn.php';
?>

<?php
require_once '../config/db_conn.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize and collect form input
    $firstName = trim($_POST["first-name"]);
    $lastName = trim($_POST["last-name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm-password"];
    $role = $_POST["role"];

    // Combine first and last name
    $fullName = $firstName . ' ' . $lastName;

    // Validate passwords match
    if ($password !== $confirmPassword) {
        die("Passwords do not match.");
    }
    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $fullName, $email, $passwordHash);

    if ($stmt->execute()) {
        echo "<script>alert('User created successfully'); window.location.href = 'user.php';</script>";
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LensLink Admin - Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6c5ce7;
            --secondary-color: #a29bfe;
            --dark-color: #2d3436;
            --light-color: #f5f6fa;
            --success-color: #00b894;
            --info-color: #0984e3;
            --warning-color: #fdcb6e;
            --danger-color: #d63031;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            height: 100vh;
            position: fixed;
            padding-top: 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 5px;
            border-radius: 5px;
            padding: 10px 15px;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            margin-bottom: 20px;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .user-profile {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead {
            background-color: var(--primary-color);
            color: white;
        }

        .badge-photographer {
            background-color: #fd79a8;
            color: white;
        }

        .badge-buyer {
            background-color: #74b9ff;
            color: white;
        }

        .badge-admin {
            background-color: #00b894;
            color: white;
        }

        .search-box {
            position: relative;
            max-width: 300px;
        }

        .search-box .form-control {
            padding-left: 40px;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 12px;
            color: #6c757d;
        }

        .filter-dropdown .dropdown-menu {
            padding: 15px;
            min-width: 250px;
        }

        .user-details-card .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 15px;
            border: 5px solid white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .user-stats {
            display: flex;
            justify-content: space-around;
            text-align: center;
            margin-top: 20px;
        }

        .user-stat {
            padding: 10px;
        }

        .user-stat-value {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .user-stat-label {
            font-size: 0.8rem;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <?php
        include 'includes/sidebar.php';
        ?>

        <!-- Main Content -->
        <div class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Users Management</h2>
            </div>

            <!-- User Details Card (Visible when a user is selected) -->
            <div class="card user-details-card mb-4 d-none" id="userDetailsCard">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg" class="profile-img" id="userProfileImg">
                            <h5 id="userName">Sarah Johnson</h5>
                            <span class="badge badge-photographer rounded-pill" id="userRole">Photographer</span>
                            <div class="mt-3">
                                <button class="btn btn-sm btn-outline-primary me-2">
                                    <i class="fas fa-envelope"></i> Message
                                </button>
                                <button class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <h6 class="mb-3">User Information</h6>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <p class="mb-1 text-muted small">Email</p>
                                    <p id="userEmail">sarah.johnson@example.com</p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-1 text-muted small">Phone</p>
                                    <p id="userPhone">(555) 123-4567</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <p class="mb-1 text-muted small">Joined</p>
                                    <p id="userJoined">March 15, 2022</p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-1 text-muted small">Last Active</p>
                                    <p id="userLastActive">2 hours ago</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <p class="mb-1 text-muted small">Bio</p>
                                    <p id="userBio">Professional photographer specializing in landscape and portrait photography. Based in New York.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h6 class="mb-3">Statistics</h6>
                            <div class="user-stats">
                                <div class="user-stat">
                                    <div class="user-stat-value" id="userPhotos">247</div>
                                    <div class="user-stat-label">Photos</div>
                                </div>
                                <div class="user-stat">
                                    <div class="user-stat-value" id="userSales">42</div>
                                    <div class="user-stat-label">Sales</div>
                                </div>
                                <div class="user-stat">
                                    <div class="user-stat-value" id="userEarnings">$1,850</div>
                                    <div class="user-stat-label">Earnings</div>
                                </div>
                            </div>
                            <hr>
                            <h6 class="mb-3">Account Status</h6>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Verification</span>
                                <span class="badge bg-success rounded-pill">Verified</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Status</span>
                                <div>
                                    <div class="form-check form-switch d-inline-block">
                                        <input class="form-check-input" type="checkbox" id="userStatusToggle" checked>
                                    </div>
                                    <span class="badge bg-success rounded-pill">Active</span>
                                </div>
                            </div>
                            <hr>
                            <button class="btn btn-sm btn-outline-danger w-100">
                                <i class="fas fa-ban me-2"></i>Suspend Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Type</th>
                                    <th>Email</th>
                                    <th>Joined</th>
                                    <th>Status</th>
                                    <th>Count Photos</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Fetch users from database
                                $query = "SELECT u.id, u.name, u.email, u.created_at, COUNT(i.id) as photo_count 
                                        FROM users u 
                                        LEFT JOIN images i ON u.id = i.user_id 
                                        GROUP BY u.id";
                                $result = $conn->query($query);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                ?>
                                        <tr class="user-row" data-user-id="<?php echo $row['id']; ?>">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="https://randomuser.me/api/portraits/women/<?php echo $row['id']; ?>.jpg" class="user-profile me-2">
                                                    <div>
                                                        <h6 class="mb-0"><?php echo htmlspecialchars($row['name']); ?></h6>
                                                        <small class="text-muted">@<?php echo strtolower(str_replace(' ', '', $row['name'])); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="badge badge-photographer rounded-pill">User</span></td>
                                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                            <td><span class="badge bg-success rounded-pill">Active</span></td>
                                            <td><?php echo $row['photo_count']; ?></td>
                                            <td>
                                                <!-- <button class="btn btn-sm btn-outline-primary me-1 view-user">
                                                    <i class="fas fa-eye"></i>
                                                </button> -->
                                                <!-- <button class="btn btn-sm btn-outline-secondary me-1">
                                                    <i class="fas fa-edit"></i>
                                                </button> -->
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="7" class="text-center">No users found</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-ml">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="first-name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first-name" name="first-name" required>
                            </div>
                            <div class="col-md-12">
                                <label for="last-name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last-name" name="last-name" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="col-md-12">
                                <label for="confirm-password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm-password" name="confirm-password" required>
                            </div>
                        </div>

                        <!-- Hidden or Select field for role (edit as needed) -->
                        <input type="hidden" name="role" value="user">

                        <button type="submit" class="btn btn-primary">Register</button>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Add User</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show user details when view button is clicked
            document.querySelectorAll('.view-user').forEach(button => {
                button.addEventListener('click', function() {
                    const userRow = this.closest('.user-row');
                    const userId = userRow.getAttribute('data-user-id');

                    // In a real application, you would fetch user details from an API
                    // Here we're just showing the card with the existing data
                    document.getElementById('userDetailsCard').classList.remove('d-none');

                    // Scroll to the details card
                    document.getElementById('userDetailsCard').scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });

            // Close user details (in a real app, you'd have a close button)
            // For now, we'll just hide it when clicking anywhere outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#userDetailsCard') && !e.target.closest('.view-user')) {
                    document.getElementById('userDetailsCard').classList.add('d-none');
                }
            });
        });
    </script>
</body>

</html>