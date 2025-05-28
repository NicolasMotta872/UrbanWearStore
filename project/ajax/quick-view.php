<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Get product ID from URL parameter
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get product details
$product = getProductById($conn, $product_id);

// If product not found, return error
if (!$product) {
    echo '<p>Product not found</p>';
    exit;
}
?>

<div class="quick-view-product">
    <div class="quick-view-img">
        <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
    </div>
    <div class="quick-view-info">
        <h2><?= $product['name'] ?></h2>
        
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
        
        <div class="product-actions">
            <div class="quantity-selector">
                <button id="quickViewDecreaseQty" class="quantity-btn">-</button>
                <input type="number" id="quickViewQuantity" value="1" min="1" max="10">
                <button id="quickViewIncreaseQty" class="quantity-btn">+</button>
            </div>
            <button id="quickViewAddToCart" class="btn btn-primary"
                    data-id="<?= $product['id'] ?>"
                    data-name="<?= $product['name'] ?>"
                    data-price="<?= $product['sale_price'] ? $product['sale_price'] : $product['price'] ?>"
                    data-image="<?= $product['image'] ?>">
                Add to Cart
            </button>
        </div>
        
        <div class="quick-view-meta">
            <p><strong>Category:</strong> <?= $product['category_name'] ?></p>
            <a href="product-detail.php?id=<?= $product['id'] ?>" class="view-details-link">View Full Details</a>
        </div>
    </div>
</div>

<script>
    // Quantity selector for quick view
    const quickViewDecreaseBtn = document.getElementById('quickViewDecreaseQty');
    const quickViewIncreaseBtn = document.getElementById('quickViewIncreaseQty');
    const quickViewQuantityInput = document.getElementById('quickViewQuantity');
    
    quickViewDecreaseBtn.addEventListener('click', function() {
        const currentValue = parseInt(quickViewQuantityInput.value);
        if (currentValue > 1) {
            quickViewQuantityInput.value = currentValue - 1;
        }
    });
    
    quickViewIncreaseBtn.addEventListener('click', function() {
        const currentValue = parseInt(quickViewQuantityInput.value);
        const maxValue = parseInt(quickViewQuantityInput.getAttribute('max') || 10);
        if (currentValue < maxValue) {
            quickViewQuantityInput.value = currentValue + 1;
        }
    });
    
    quickViewQuantityInput.addEventListener('change', function() {
        let value = parseInt(this.value);
        const min = parseInt(this.getAttribute('min') || 1);
        const max = parseInt(this.getAttribute('max') || 10);
        
        if (isNaN(value) || value < min) {
            value = min;
        } else if (value > max) {
            value = max;
        }
        
        this.value = value;
    });
</script>

<style>
    .quick-view-product {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        padding: 30px;
    }
    
    .quick-view-img {
        height: 400px;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .quick-view-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .quick-view-info h2 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .quick-view-meta {
        margin-top: 20px;
        font-size: 0.9rem;
        color: #666;
    }
    
    .view-details-link {
        display: inline-block;
        margin-top: 10px;
        color: var(--primary-color);
        font-weight: 500;
    }
    
    .view-details-link:hover {
        text-decoration: underline;
    }
    
    @media (max-width: 768px) {
        .quick-view-product {
            grid-template-columns: 1fr;
        }
        
        .quick-view-img {
            height: 300px;
        }
    }
</style>