<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Get all categories
$query = "SELECT c.*, COUNT(p.id) as product_count 
          FROM categories c 
          LEFT JOIN products p ON c.id = p.category_id 
          GROUP BY c.id 
          ORDER BY c.name";
$result = $conn->query($query);
$categories = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - UrbanWear Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="admin-content">
            <?php include 'includes/header.php'; ?>

            <main>
                <div class="page-header">
                    <h1>Categories</h1>
                    <a href="add-category.php" class="btn btn-primary">Add New Category</a>
                </div>
                
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Products</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="5" class="empty-table">No categories found</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($categories as $category): ?>
                            <tr>
                                <td>
                                    <div class="product-img-small">
                                        <img src="../<?= $category['image'] ?>" alt="<?= $category['name'] ?>">
                                    </div>
                                </td>
                                <td><?= $category['name'] ?></td>
                                <td>
                                    <?= strlen($category['description']) > 100 ? substr($category['description'], 0, 100) . '...' : $category['description'] ?>
                                </td>
                                <td><?= $category['product_count'] ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="edit-category.php?id=<?= $category['id'] ?>" class="btn-icon edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn-icon delete" onclick="confirmDelete(<?= $category['id'] ?>, 'category')">
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
            </main>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h2>Confirm Deletion</h2>
            <p>Are you sure you want to delete this category? This will also delete all products in this category.</p>
            <div class="modal-actions">
                <button id="cancelDelete" class="btn btn-secondary">Cancel</button>
                <button id="confirmDelete" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>

    <script src="../assets/js/admin.js"></script>
</body>
</html>