<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$featured_products = getFeaturedProducts($conn, 8);
$categories = getAllCategories($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UrbanWear - Modern Fashion Store</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <!-- Hero Banner -->
        <section class="hero">
            <div class="hero-content">
                <h1>Descubra seu estilo</h1>
                <p>Explore as ultimas modas mais usadas</p>
                <a href="#featured" class="btn btn-primary">Compre agora</a>
            </div>
        </section>

        <!-- Categories Section -->
        <section class="categories-section">
            <div class="container">
                <h2 class="section-title">Compre por categoria</h2>
                <div class="categories-grid">
                    <?php foreach($categories as $category): ?>
                    <a href="products.php?category=<?= $category['id'] ?>" class="category-card">
                        <div class="category-img">
                            <img src="<?= $category['image'] ?>" alt="<?= $category['name'] ?>">
                        </div>
                        <h3><?= $category['name'] ?></h3>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Featured Products -->
        <section id="featured" class="featured-products">
            <div class="container">
                <h2 class="section-title">Produtos destaques</h2>
                <div class="products-grid">
                    <?php foreach($featured_products as $product): ?>
                    <div class="product-card" data-id="<?= $product['id'] ?>">
                        <div class="product-img">
                            <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
                            <div class="product-actions">
                                <button class="quick-view-btn" data-id="<?= $product['id'] ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="add-to-cart-btn" data-id="<?= $product['id'] ?>" data-name="<?= $product['name'] ?>" data-price="<?= $product['price'] ?>" data-image="<?= $product['image'] ?>">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3><?= $product['name'] ?></h3>
                            <p class="product-price">$<?= number_format($product['price'], 2) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="view-more-container">
                    <a href="products.php" class="btn btn-secondary">Ver todos produtos</a>
                </div>
            </div>
        </section>

        <!-- Promotion Banner -->
        <section class="promotion-banner">
            <div class="promotion-content">
                <h2>Promoção de verão</h2>
                <p>Desconto 50% nos itens selecionados</p>
                <a href="products.php?sale=1" class="btn btn-accent">Produtos</a>
            </div>
        </section>

        <!-- Features -->
        <section class="features">
            <div class="container">
                <div class="features-grid">
                    <div class="feature">
                        <i class="fas fa-truck"></i>
                        <h3>Frete grátis</h3>
                        <p>Em compras acima de R$50</p>
                    </div>
                    <div class="feature">
                        <i class="fas fa-undo"></i>
                        <h3>Retorno fácil</h3>
                        <p>Política de 30 dias de retorno</p>
                    </div>
                    <div class="feature">
                        <i class="fas fa-shield-alt"></i>
                        <h3>Pagamento seguro</h3>
                        <p>Protegido por criptografia</p>
                    </div>
                    <div class="feature">
                        <i class="fas fa-headset"></i>
                        <h3>24/7 Suporte</h3>
                        <p>Fale conosco</p>
                    </div>
                </div>
            </div>
        </section>
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
            <h3>Seu carrinho</h3>
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
</body>
</html>