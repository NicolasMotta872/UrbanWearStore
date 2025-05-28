<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get product ID from URL parameter
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get product details
$product = getProductById($conn, $product_id);

// If product not found, redirect to products page
if (!$product) {
    header('Location: products.php');
    exit;
}

// Get related products
$related_products = getRelatedProducts($conn, $product_id, $product['category_id'], 4);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $product['name'] ?> - UrbanWear</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="product-detail-page">
        <div class="container">
            <div class="breadcrumb">
                <a href="index.php">Home</a>
                <span>/</span>
                <a href="products.php?category=<?= $product['category_id'] ?>"><?= $product['category_name'] ?></a>
                <span>/</span>
                <span><?= $product['name'] ?></span>
            </div>

            <div class="product-detail">
                <div class="product-images">
                    <div class="main-image">
                        <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>" id="mainProductImage">
                    </div>
                    <?php if (!empty($product['gallery'])): ?>
                    <div class="thumbnail-images">
                        <div class="thumbnail active" data-image="<?= $product['image'] ?>">
                            <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
                        </div>
                        <?php foreach($product['gallery'] as $image): ?>
                        <div class="thumbnail" data-image="<?= $image ?>">
                            <img src="<?= $image ?>" alt="<?= $product['name'] ?>">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="product-info">
                    <h1><?= $product['name'] ?></h1>
                    
                    <div class="product-price">
                        <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                        <span class="original-price">$<?= number_format($product['price'], 2) ?></span>
                        <span class="sale-price">$<?= number_format($product['sale_price'], 2) ?></span>
                        <?php else: ?>
                        <span>$<?= number_format($product['price'], 2) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="product-description">
                        <p><?= $product['description'] ?></p>
                    </div>

                    <?php if (!empty($product['sizes'])): ?>
                    <div class="product-sizes">
                        <h3>Size</h3>
                        <div class="size-options">
                            <?php foreach($product['sizes'] as $size): ?>
                            <label class="size-option">
                                <input type="radio" name="size" value="<?= $size ?>" <?= $size === 'M' ? 'checked' : '' ?>>
                                <span><?= $size ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($product['colors'])): ?>
                    <div class="product-colors">
                        <h3>Color</h3>
                        <div class="color-options">
                            <?php foreach($product['colors'] as $color => $code): ?>
                            <label class="color-option" title="<?= ucfirst($color) ?>">
                                <input type="radio" name="color" value="<?= $color ?>" <?= $color === array_key_first($product['colors']) ? 'checked' : '' ?>>
                                <span style="background-color: <?= $code ?>"></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="product-actions">
                        <div class="quantity-selector">
                            <button id="decreaseQuantity" class="quantity-btn">-</button>
                            <input type="number" id="quantity" value="1" min="1" max="10">
                            <button id="increaseQuantity" class="quantity-btn">+</button>
                        </div>
                        <button id="addToCartBtn" class="btn btn-primary"
                                data-id="<?= $product['id'] ?>"
                                data-name="<?= $product['name'] ?>"
                                data-price="<?= $product['sale_price'] ? $product['sale_price'] : $product['price'] ?>"
                                data-image="<?= $product['image'] ?>">
                            Add to Cart
                        </button>
                    </div>

                    <div class="product-meta">
                        <p><strong>SKU:</strong> <?= $product['sku'] ?></p>
                        <p><strong>Category:</strong> <?= $product['category_name'] ?></p>
                        <?php if (!empty($product['tags'])): ?>
                        <p>
                            <strong>Tags:</strong> 
                            <?php foreach($product['tags'] as $index => $tag): ?>
                                <a href="products.php?search=<?= urlencode($tag) ?>" class="tag"><?= $tag ?></a><?= $index < count($product['tags']) - 1 ? ', ' : '' ?>
                            <?php endforeach; ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if (!empty($related_products)): ?>
            <section class="related-products">
                <h2>You May Also Like</h2>
                <div class="products-grid">
                    <?php foreach($related_products as $related): ?>
                    <div class="product-card" data-id="<?= $related['id'] ?>">
                        <div class="product-img">
                            <img src="<?= $related['image'] ?>" alt="<?= $related['name'] ?>">
                            <?php if ($related['sale_price'] && $related['sale_price'] < $related['price']): ?>
                            <span class="sale-badge">Sale</span>
                            <?php endif; ?>
                            <div class="product-actions">
                                <button class="quick-view-btn" data-id="<?= $related['id'] ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="add-to-cart-btn" 
                                        data-id="<?= $related['id'] ?>" 
                                        data-name="<?= $related['name'] ?>" 
                                        data-price="<?= $related['sale_price'] ? $related['sale_price'] : $related['price'] ?>" 
                                        data-image="<?= $related['image'] ?>">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3><?= $related['name'] ?></h3>
                            <div class="product-price">
                                <?php if ($related['sale_price'] && $related['sale_price'] < $related['price']): ?>
                                <span class="original-price">$<?= number_format($related['price'], 2) ?></span>
                                <span class="sale-price">$<?= number_format($related['sale_price'], 2) ?></span>
                                <?php else: ?>
                                <span>$<?= number_format($related['price'], 2) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
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
    <script src="assets/js/product-detail.js"></script>
</body>
</html>