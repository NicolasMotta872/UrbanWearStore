<aside class="admin-sidebar">
    <div class="sidebar-header">
        <h2>UrbanWear</h2>
        <p>Admin Painel</p>
    </div>
    
    <nav class="sidebar-nav">
        <ul>
            <li>
                <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="products.php" class="<?= (basename($_SERVER['PHP_SELF']) === 'products.php' || basename($_SERVER['PHP_SELF']) === 'add-product.php' || basename($_SERVER['PHP_SELF']) === 'edit-product.php') ? 'active' : '' ?>">
                    <i class="fas fa-tshirt"></i>
                    <span>Produtos</span>
                </a>
            </li>
            <li>
                <a href="categories.php" class="<?= (basename($_SERVER['PHP_SELF']) === 'categories.php' || basename($_SERVER['PHP_SELF']) === 'add-category.php' || basename($_SERVER['PHP_SELF']) === 'edit-category.php') ? 'active' : '' ?>">
                    <i class="fas fa-tags"></i>
                    <span>Categorias</span>
                </a>
            </li>
            <li>
                <a href="settings.php" class="<?= basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : '' ?>">
                    <i class="fas fa-cogs"></i>
                    <span>Configurações</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <div class="sidebar-footer">
        <a href="../index.php" target="_blank">
            <i class="fas fa-external-link-alt"></i>
            <span>Ver Site</span>
        </a>
        <a href="logout.php">
            <i class="fas fa-sign-out-alt"></i>
            <span>Sair</span>
        </a>
    </div>
</aside>