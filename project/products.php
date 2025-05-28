<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get categories for the sidebar
$categories = getAllCategories($conn);

// Handle filtering and search
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$sale_only = isset($_GET['sale']) ? (int)$_GET['sale'] : 0;

// Get products based on filters
$products = getFilteredProducts($conn, $category_id, $search_term, $sale_only);

// Get current category name if category filter is applied
$category_name = '';
if ($category_id) {
    $stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $category_name = $row['name'];
    }
    $stmt->close();
}

// Set page title
$page_title = 'All Products';
if ($category_name) {
    $page_title = $category_name;
} elseif ($search_term) {
    $page_title = 'Search Results: ' . $search_term;
} elseif ($sale_only) {
    $page_title = 'Sale Items';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - UrbanWear</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="products-page">
        <div class="container">
            <div class="products-layout">
                <!-- Sidebar with filters -->
                <aside class="filters-sidebar">
                    <div class="filter-section">
                        <h3>Categories</h3>
                        <ul class="category-list">
                            <li>
                                <a href="products.php" <?= !$category_id ? 'class="active"' : '' ?>>All Products</a>
                            </li>
                            <?php foreach($categories as $category): ?>
                            <li>
                                <a href="products.php?category=<?= $category['id'] ?>" 
                                   <?= $category_id == $category['id'] ? 'class="active"' : '' ?>>
                                   <?= $category['name'] ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="filter-section">
                        <h3>Price Range</h3>
                        <div class="price-range">
                            <input type="range" id="priceRange" min="0" max="200" value="200" class="slider">
                            <div class="price-inputs">
                                <span>$0</span>
                                <span id="priceValue">$200</span>
                            </div>
                        </div>
                    </div>

                    <div class="filter-section">
                        <h3>Special Offers</h3>
                        <div class="checkbox-filter">
                            <label>
                                <input type="checkbox" id="saleFilter" <?= $sale_only ? 'checked' : '' ?>>
                                On Sale
                            </label>
                        </div>
                    </div>
                </aside>

                <!-- Product listing -->
                <section class="product-listing">
                    <header class="product-header">
                        <h1><?= $page_title ?></h1>
                        <div class="product-controls">
                            <div class="search-container">
                                <form action="products.php" method="GET" id="searchForm">
                                    <?php if ($category_id): ?>
                                    <input type="hidden" name="category" value="<?= $category_id ?>">
                                    <?php endif; ?>
                                    <?php if ($sale_only): ?>
                                    <input type="hidden" name="sale" value="1">
                                    <?php endif; ?>
                                    <input type="text" name="search" id="searchInput" 
                                           placeholder="Search products..." 
                                           value="<?= htmlspecialchars($search_term) ?>">
                                    <button type="submit"><i class="fas fa-search"></i></button>
                                </form>
                            </div>
                            <div class="sort-container">
                                <select id="sortOrder">
                                    <option value="default">Sort by: Featured</option>
                                    <option value="price-asc">Price: Low to High</option>
                                    <option value="price-desc">Price: High to Low</option>
                                    <option value="name-asc">Name: A to Z</option>
                                    <option value="name-desc">Name: Z to A</option>
                                </select>
                            </div>
                        </div>
                    </header>

                    <?php if (empty($products)): ?>
                    <div class="no-products">
                        <p>No products found. Try adjusting your filters or search terms.</p>
                        <a href="products.php" class="btn btn-secondary">View All Products</a>
                    </div>
                    <?php else: ?>
                    <div class="products-grid" id="productsGrid">
                        <?php foreach($products as $product): ?>
                        <div class="product-card" data-id="<?= $product['id'] ?>" data-price="<?= $product['price'] ?>" data-name="<?= $product['name'] ?>">
                            <div class="product-img">
                                <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
                                <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                                <span class="sale-badge">Sale</span>
                                <?php endif; ?>
                                <div class="product-actions">
                                    <button class="quick-view-btn" data-id="<?= $product['id'] ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="add-to-cart-btn" 
                                            data-id="<?= $product['id'] ?>" 
                                            data-name="<?= $product['name'] ?>" 
                                            data-price="<?= $product['sale_price'] ? $product['sale_price'] : $product['price'] ?>" 
                                            data-image="<?= $product['image'] ?>">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="product-info">
                                <h3><?= $product['name'] ?></h3>
                                <div class="product-price">
                                    <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                                    <span class="original-price">$<?= number_format($product['price'], 2) ?></span>
                                    <span class="sale-price">$<?= number_format($product['sale_price'], 2) ?></span>
                                    <?php else: ?>
                                    <span>$<?= number_format($product['price'], 2) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <!-- Quick View Modal -->
    <div id="quickViewModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div id="quickViewContent">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>

    <!-- Cart Sidebar -->
    <div id="cartSidebar" class="cart-sidebar">
        <div class="cart-header">
            <h3>Your Cart</h3>
            <button id="closeCart" class="close-cart">&times;</button>
        </div>
        <div id="cartItems" class="cart-items">
            <!-- Cart items will be loaded dynamically -->
        </div>
        <div class="cart-footer">
            <div class="cart-total">
                <span>Total:</span>
                <span id="cartTotal">$0.00</span>
            </div>
            <a href="checkout.php" class="btn btn-primary checkout-btn">Checkout</a>
        </div>
    </div>

    <script src="assets/js/app.js"></script>
    <script src="assets/js/products.js"></script>
</body>
</html>