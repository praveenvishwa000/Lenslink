<?php
session_start();
require_once '../config/db_conn.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LensLink Admin - Photos</title>
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

        .photo-thumbnail {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px 8px 0 0;
        }

        .photo-description {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-profile {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
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

        .photo-details-container {
            display: flex;
            flex-wrap: wrap;
        }

        .photo-preview {
            flex: 0 0 60%;
            max-width: 60%;
            padding-right: 20px;
        }

        .photo-info {
            flex: 0 0 40%;
            max-width: 40%;
        }

        .photo-preview img {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .tag {
            display: inline-block;
            background-color: #e0e0e0;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-right: 5px;
            margin-bottom: 5px;
        }

        @media (max-width: 992px) {
            .photo-details-container {
                flex-direction: column;
            }

            .photo-preview,
            .photo-info {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .photo-preview {
                padding-right: 0;
                margin-bottom: 20px;
            }
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
                <h2>Photos Management</h2>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>image</th>
                                    <th>user name</th>
                                    <th>image title</th>
                                    <th>description</th>
                                    <th>Price</th>
                                    <th>Category name</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody> <?php
                                    // Fetch images with user and category details
                                    $query = "SELECT i.id, i.user_id, i.title, i.description, i.image_url, i.price,
                                        i.is_public, i.created_at, u.name as user_name, u.email,
                                        c.name as category_name 
                                        FROM images i
                                        LEFT JOIN users u ON i.user_id = u.id
                                        LEFT JOIN categories c ON i.category_id = c.id
                                        ORDER BY i.created_at DESC";
                                    $result = $conn->query($query);

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                    ?>
                                        <tr class="image-row" data-image-id="<?php echo $row['id']; ?>">
                                            <td><?php echo $row['id']; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="../<?php echo $row['image_url']; ?>" class="user-profile me-2" alt="<?php echo htmlspecialchars($row['title']); ?>">
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                                            <td><?php echo htmlspecialchars(substr($row['description'], 0, 50)) . '...'; ?></td>
                                            <td>$<?php echo number_format($row['price'], 2); ?></td>
                                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                                            <td>
                                                <?php if ($row['is_public']): ?>
                                                    <span class="badge bg-success rounded-pill">Public</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning rounded-pill">Private</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                            <td>
                                                <!-- <button class="btn btn-sm btn-outline-primary me-1 view-image">
                                                    <i class="fas fa-eye"></i>
                                                </button> -->
                                                <?php if (!$row['is_public']): ?>
                                                    <a href="approve_photo.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-success me-1" onclick="return confirm('Are you sure you want to approve this photo?')">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <a href="delete_photo.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this photo?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="7" class="text-center">No iamges found</td></tr>';
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Upload Photo Modal -->
    <div class="modal fade" id="uploadPhotoModal" tabindex="-1" aria-labelledby="uploadPhotoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadPhotoModalLabel">Upload New Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="photoFile" class="form-label">Photo File</label>
                            <input class="form-control" type="file" id="photoFile" accept="image/*" required>
                            <div class="form-text">Maximum file size: 20MB. Supported formats: JPG, PNG, HEIC</div>
                        </div>

                        <div class="mb-3">
                            <label for="photoTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="photoTitle" required>
                        </div>

                        <div class="mb-3">
                            <label for="photoDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="photoDescription" rows="3" required></textarea>
                            <div class="form-text">Tell the story behind this photo, equipment used, and any special techniques.</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="photoCategory" class="form-label">Category</label>
                                <select class="form-select" id="photoCategory" required>
                                    <option value="">Select category</option>
                                    <option value="landscape">Landscape</option>
                                    <option value="portrait">Portrait</option>
                                    <option value="wildlife">Wildlife</option>
                                    <option value="street">Street</option>
                                    <option value="architecture">Architecture</option>
                                    <option value="food">Food</option>
                                    <option value="travel">Travel</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="photoPrice" class="form-label">Price ($)</label>
                                <input type="number" class="form-control" id="photoPrice" min="5" max="500" value="50" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="photoTags" class="form-label">Tags</label>
                            <input type="text" class="form-control" id="photoTags" placeholder="landscape, sunset, nature">
                            <div class="form-text">Separate tags with commas (up to 10 tags)</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">EXIF Data</label>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" placeholder="Camera Model">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" placeholder="Lens">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" placeholder="Focal Length">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" placeholder="Aperture">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" placeholder="Shutter Speed">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" placeholder="ISO">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Upload Photo</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show photo details when a photo card is clicked
            document.querySelectorAll('.photo-card').forEach(card => {
                card.addEventListener('click', function() {
                    const photoId = this.getAttribute('data-photo-id');

                    // Hide the grid and show the details card
                    document.getElementById('photosGrid').classList.add('d-none');
                    document.getElementById('photoDetailsCard').classList.remove('d-none');

                    // In a real application, you would fetch photo details from an API
                    // Here we're just showing the card with the existing data

                    // Scroll to the top of the details card
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            });

            // Back to photos button
            document.getElementById('backToPhotos').addEventListener('click', function() {
                document.getElementById('photosGrid').classList.remove('d-none');
                document.getElementById('photoDetailsCard').classList.add('d-none');
            });
        });
    </script>
</body>

</html>