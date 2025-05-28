<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Get dashboard statistics
$query_products = "SELECT COUNT(*) as total FROM products";
$query_categories = "SELECT COUNT(*) as total FROM categories";

$result_products = $conn->query($query_products);
$result_categories = $conn->query($query_categories);

$total_products = $result_products->fetch_assoc()['total'];
$total_categories = $result_categories->fetch_assoc()['total'];

// Get recent products
$query_recent = "SELECT p.*, c.name as category_name 
                FROM products p 
                JOIN categories c ON p.category_id = c.id 
                ORDER BY p.created_at DESC 
                LIMIT 5";
$result_recent = $conn->query($query_recent);
$recent_products = $result_recent->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - UrbanWear</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="admin-content">
            <?php include 'includes/header.php'; ?>

            <main class="dashboard">
                <h1>Dashboard</h1>
                
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-tshirt"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Products</h3>
                            <p><?= $total_products ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-tags"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Categories</h3>
                            <p><?= $total_categories ?></p>
                        </div>
                    </div>
                </div>

                <section class="recent-products">
                    <div class="section-header">
                        <h2>Recent Products</h2>
                        <a href="products.php" class="btn btn-small">View All</a>
                    </div>
                    
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_products)): ?>
                                <tr>
                                    <td colspan="5" class="empty-table">No products found</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($recent_products as $product): ?>
                                <tr>
                                    <td>
                                        <div class="product-img-small">
                                            <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
                                        </div>
                                    </td>
                                    <td><?= $product['name'] ?></td>
                                    <td><?= $product['category_name'] ?></td>
                                    <td>$<?= number_format($product['price'], 2) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="edit-product.php?id=<?= $product['id'] ?>" class="btn-icon edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn-icon delete" data-id="<?= $product['id'] ?>" onclick="confirmDelete(<?= $product['id'] ?>, 'product')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <div class="dashboard-actions">
                    <a href="add-product.php" class="action-card">
                        <i class="fas fa-plus"></i>
                        <span>Add New Product</span>
                    </a>
                    <a href="add-category.php" class="action-card">
                        <i class="fas fa-folder-plus"></i>
                        <span>Add New Category</span>
                    </a>
                    <a href="categories.php" class="action-card">
                        <i class="fas fa-list"></i>
                        <span>Manage Categories</span>
                    </a>
                </div>
            </main>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h2>Confirm Deletion</h2>
            <p>Are you sure you want to delete this item? This action cannot be undone.</p>
            <div class="modal-actions">
                <button id="cancelDelete" class="btn btn-secondary">Cancel</button>
                <button id="confirmDelete" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>

    <script src="../assets/js/admin.js"></script>
</body>
</html>