<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Get total products count
$count_query = "SELECT COUNT(*) as total FROM products";
$count_result = $conn->query($count_query);
$total_products = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_products / $limit);

// Get products with pagination
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          JOIN categories c ON p.category_id = c.id 
          ORDER BY p.created_at DESC 
          LIMIT ?, ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - UrbanWear Admin</title>
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
                    <h1>Products</h1>
                    <a href="add-product.php" class="btn btn-primary">Add New Product</a>
                </div>

                <div class="table-actions">
                    <div class="search-container">
                        <input type="text" id="productSearch" placeholder="Search products...">
                        <button id="searchBtn"><i class="fas fa-search"></i></button>
                    </div>
                    <div class="filter-container">
                        <select id="categoryFilter">
                            <option value="">All Categories</option>
                            <?php
                            $cat_query = "SELECT * FROM categories ORDER BY name";
                            $cat_result = $conn->query($cat_query);
                            while ($category = $cat_result->fetch_assoc()) {
                                echo "<option value=\"{$category['id']}\">{$category['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                
                <div class="table-container">
                    <table class="data-table" id="productsTable">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="6" class="empty-table">No products found</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <div class="product-img-small">
                                        <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
                                    </div>
                                </td>
                                <td><?= $product['name'] ?></td>
                                <td><?= $product['category_name'] ?></td>
                                <td>$<?= number_format($product['price'], 2) ?></td>
                                <td><?= $product['stock_quantity'] ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="edit-product.php?id=<?= $product['id'] ?>" class="btn-icon edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn-icon delete" onclick="confirmDelete(<?= $product['id'] ?>, 'product')">
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

                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>" class="page-link">&laquo; Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="page-link <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?>" class="page-link">Next &raquo;</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h2>Confirm Deletion</h2>
            <p>Are you sure you want to delete this product? This action cannot be undone.</p>
            <div class="modal-actions">
                <button id="cancelDelete" class="btn btn-secondary">Cancel</button>
                <button id="confirmDelete" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>

    <script src="../assets/js/admin.js"></script>
    <script>
        // Filter products by category and search
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('productSearch');
            const searchBtn = document.getElementById('searchBtn');
            const categoryFilter = document.getElementById('categoryFilter');
            
            function filterProducts() {
                const searchTerm = searchInput.value.toLowerCase();
                const categoryId = categoryFilter.value;
                
                // AJAX request to filter products
                fetch(`ajax/filter-products.php?search=${encodeURIComponent(searchTerm)}&category=${categoryId}`)
                    .then(response => response.json())
                    .then(data => {
                        const tableBody = document.querySelector('#productsTable tbody');
                        tableBody.innerHTML = '';
                        
                        if (data.products.length === 0) {
                            tableBody.innerHTML = '<tr><td colspan="6" class="empty-table">No products found</td></tr>';
                            return;
                        }
                        
                        data.products.forEach(product => {
                            tableBody.innerHTML += `
                                <tr>
                                    <td>
                                        <div class="product-img-small">
                                            <img src="${product.image}" alt="${product.name}">
                                        </div>
                                    </td>
                                    <td>${product.name}</td>
                                    <td>${product.category_name}</td>
                                    <td>$${parseFloat(product.price).toFixed(2)}</td>
                                    <td>${product.stock_quantity}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="edit-product.php?id=${product.id}" class="btn-icon edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn-icon delete" onclick="confirmDelete(${product.id}, 'product')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });
                    })
                    .catch(error => console.error('Error:', error));
            }
            
            searchBtn.addEventListener('click', filterProducts);
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    filterProducts();
                }
            });
            categoryFilter.addEventListener('change', filterProducts);
        });
    </script>
</body>
</html>