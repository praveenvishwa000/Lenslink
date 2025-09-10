<!-- Sidebar -->
<div class="sidebar d-flex flex-column flex-shrink-0 p-3" style="width: 250px;">
    <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <i class="fas fa-camera-retro fa-2x me-2"></i>
        <span class="fs-4">LensLink Admin</span>
    </a>
    <hr> <?php
            $current_page = basename($_SERVER['PHP_SELF']);
            ?>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
        </li>
        <li>
            <a href="user.php" class="nav-link <?= $current_page === 'user.php' ? 'active' : '' ?>">
                <i class="fas fa-users"></i>
                Users
            </a>
        </li>
        <li>
            <a href="photos.php" class="nav-link <?= $current_page === 'photos.php' ? 'active' : '' ?>">
                <i class="fas fa-images"></i>
                Photos
            </a>
        </li>

    </ul>
    <hr>
    <div class="dropdown mb-3">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Admin" class="rounded-circle me-2" width="32" height="32">
            <strong>Admin</strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
            <li><a class="dropdown-item" href="#">Profile</a></li>
            <li><a class="dropdown-item" href="#">Settings</a></li>
            <li>
                <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" href="../logout.php">Sign out</a></li>
        </ul>
    </div>
    <!-- Added Logout Button -->
    <a href="../logout.php" class="btn btn-danger w-100">
        <i class="fas fa-sign-out-alt me-2"></i>Logout
    </a>
</div>