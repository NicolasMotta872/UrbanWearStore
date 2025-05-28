<footer class="site-footer">
    <div class="footer-top">
        <div class="container">
            <div class="footer-widgets">
                <div class="footer-widget">
                    <h3>UrbanWear</h3>
                    <p>Moda moderna para o estilo de vida urbano. Descubra as últimas tendências e expresse seu estilo único.</p>
                    <div class="social-icons">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Pinterest"><i class="fab fa-pinterest-p"></i></a>
                    </div>
                </div>
                
                <div class="footer-widget">
                    <h3>Links</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="products.php">Loja</a></li>
                        <li><a href="about.php">Sobre</a></li>
                        <li><a href="contact.php">Contato</a></li>
                        <li><a href="products.php?sale=1">Vendas</a></li>
                    </ul>
                </div>
                
                <div class="footer-widget">
                    <h3>Categories</h3>
                    <ul class="footer-links">
                        <?php 
                        $footer_categories = getAllCategories($conn);
                        foreach($footer_categories as $category): 
                        ?>
                        <li><a href="products.php?category=<?= $category['id'] ?>"><?= $category['name'] ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="footer-widget">
                    <h3>Contact Us</h3>
                    <ul class="contact-info">
                        <li><i class="fas fa-map-marker-alt"></i> Bauru-SP Mary Dota</li>
                        <li><i class="fas fa-phone"></i> (123) 456-7890</li>
                        <li><i class="fas fa-envelope"></i> info@urbanwear.com</li>
                        <li><i class="fas fa-envelope"></i> <a href="admin/index.php">Area Admin</a></li>
                        <li><i class="fas fa-clock"></i> Segunda á sexta das 9hrs ás 18hrs</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-content">
                <p>&copy; <?= date('Y') ?> UrbanWear. Todos os direitos reservados</p>
                <div class="footer-cards">
                    <i class="fab fa-cc-visa"></i>
                    <i class="fab fa-cc-mastercard"></i>
                    <i class="fab fa-cc-amex"></i>
                    <i class="fab fa-cc-paypal"></i>
                </div>
            </div>
        </div>
    </div>
</footer>