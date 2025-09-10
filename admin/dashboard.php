<?php
require_once '../config/db_conn.php';
session_start();

// Fetch total users count
$users_sql = "SELECT COUNT(*) as total FROM users";
$users_result = $conn->query($users_sql);
$total_users = $users_result->fetch_assoc()['total'];

// Fetch total images count
$images_sql = "SELECT COUNT(*) as total FROM images";
$images_result = $conn->query($images_sql);
$total_images = $images_result->fetch_assoc()['total'];

// Fetch total sales (completed payments)
$sales_sql = "SELECT COUNT(*) as total_sales, SUM(amount) as total_revenue 
              FROM payments 
              WHERE payment_status = 'completed'";
$sales_result = $conn->query($sales_sql);
$sales_data = $sales_result->fetch_assoc();
$total_sales = $sales_data['total_sales'];
$total_revenue = $sales_data['total_revenue'] ?? 0;

// Fetch recent orders
$recent_orders_sql = "SELECT o.id, o.total_amount, u.name as user_name, o.created_at, o.status
                      FROM orders o
                      JOIN users u ON o.user_id = u.id
                      ORDER BY o.created_at DESC
                      LIMIT 5";
$recent_orders = $conn->query($recent_orders_sql);

// Fetch recent photos
$recent_photos_sql = "SELECT i.*, u.name as photographer_name 
                      FROM images i
                      JOIN users u ON i.user_id = u.id
                      ORDER BY i.created_at DESC
                      LIMIT 5";
$recent_photos = $conn->query($recent_photos_sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LensLink Admin Dashboard</title>
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
            width: 100%;
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

        .card-icon {
            font-size: 2rem;
            opacity: 0.7;
        }

        .stat-card .card-body {
            display: flex;
            align-items: center;
            padding: 1.5rem;
        }

        .stat-card .icon-container {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }

        .stat-card .card-title {
            font-size: 1.75rem;
            margin-bottom: 0.25rem;
            font-weight: 600;
        }

        .stat-card .card-subtitle {
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .users-icon {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .photos-icon {
            background-color: #fce4ec;
            color: #c2185b;
        }

        .sales-icon {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }

        .revenue-icon {
            background-color: rgba(85, 239, 196, 0.2);
            color: #55efc4;
        }

        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead {
            background-color: #f8f9fa;
        }

        .table thead th {
            font-size: 0.875rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-top: none;
        }

        .table tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
        }

        .badge {
            padding: 0.5em 1em;
            font-weight: 500;
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, .075);
            padding: 1.25rem 1.5rem;
        }

        .card-title {
            margin-bottom: 0;
            color: #344767;
            font-size: 1.125rem;
        }

        .chart-container {
            height: 300px;
            position: relative;
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color);
        }

        .user-profile {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
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
            <div class="mb-4">
                <h2 class="h4 text-gray-900">Dashboard Overview</h2>
            </div><!-- Stats Cards -->
            <div class="row">
                <div class="col-md-6 col-lg-4">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="icon-container users-icon">
                                <i class="fas fa-users card-icon"></i>
                            </div>
                            <div>
                                <h6 class="card-subtitle mb-2 text-muted">Total Users</h6>
                                <h3 class="card-title"><?= number_format($total_users) ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="icon-container photos-icon">
                                <i class="fas fa-images card-icon"></i>
                            </div>
                            <div>
                                <h6 class="card-subtitle mb-2 text-muted">Total Photos</h6>
                                <h3 class="card-title"><?= number_format($total_images) ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="icon-container sales-icon">
                                <i class="fas fa-shopping-cart card-icon"></i>
                            </div>
                            <div>
                                <h6 class="card-subtitle mb-2 text-muted">Total Sales</h6>
                                <h3 class="card-title"><?= number_format($total_sales) ?></h3>
                                <p class="text-muted mb-0">Revenue: Lkr <?= number_format($total_revenue, 2) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- Recent Orders -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Recent Orders</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($order = $recent_orders->fetch_assoc()): ?>
                                            <tr>
                                                <td>#<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                                <td><?= htmlspecialchars($order['user_name']) ?></td>
                                                <td>Lkr <?= number_format($order['total_amount'], 2) ?></td>
                                                <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $order['status'] === 'completed' ? 'success' : 'warning' ?> rounded-pill">
                                                        <?= ucfirst($order['status']) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Photos -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Recently Added Photos</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Photo</th>
                                            <th>Title</th>
                                            <th>Photographer</th>
                                            <th>Price</th>
                                            <th>Added Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($photo = $recent_photos->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <img src="../<?= htmlspecialchars($photo['image_url']) ?>"
                                                        alt="<?= htmlspecialchars($photo['title']) ?>"
                                                        class="user-profile">
                                                </td>
                                                <td><?= htmlspecialchars($photo['title']) ?></td>
                                                <td><?= htmlspecialchars($photo['photographer_name']) ?></td>
                                                <td>Lkr <?= number_format($photo['price'], 2) ?></td>
                                                <td><?= date('M j, Y', strtotime($photo['created_at'])) ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // This is where you would initialize charts and other JavaScript functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize date range picker
            // Initialize charts
            // etc.
        });
    </script>
</body>

</html>