<header class="site-header">
    <div class="container">
        <div class="header-wrapper">
            <div class="logo">
                <a href="index.php">
                    <h1>UrbanWear</h1>
                </a>
            </div>
            
            <div class="search-bar">
                <form action="products.php" method="GET" id="headerSearchForm">
                    <input type="text" name="search" id="headerSearchInput" placeholder="Search for products...">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
                <div id="searchResults" class="search-results"></div>
            </div>
            
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li class="dropdown">
                        <a href="products.php">Loja <i class="fas fa-chevron-down"></i></a>
                        <div class="dropdown-menu">
                            <?php 
                            $nav_categories = getAllCategories($conn);
                            foreach($nav_categories as $category): 
                            ?>
                            <a href="products.php?category=<?= $category['id'] ?>"><?= $category['name'] ?></a>
                            <?php endforeach; ?>
                            <a href="products.php?sale=1" class="sale-link">Items</a>
                        </div>
                    </li>
                    <li><a href="about.php">Sobre</a></li>
                    <li><a href="contact.php">Contato</a></li>
                </ul>
            </nav>
            
            <div class="header-actions">
                <button id="searchToggle" class="mobile-search-toggle">
                    <i class="fas fa-search"></i>
                </button>
                <a href="#" class="nav-icon">
                    <i class="fas fa-user"></i>
                </a>
                <button id="cartToggle" class="nav-icon cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="cartCount" class="cart-count">0</span>
                </button>
                <button id="mobileMenuToggle" class="mobile-menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </div>
    
    <div class="mobile-search">
        <div class="container">
            <form action="products.php" method="GET">
                <input type="text" name="search" placeholder="Buscar produtos...">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>
    
    <div class="mobile-menu">
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li>
                    <a href="#" class="has-submenu">Loja <i class="fas fa-chevron-down"></i></a>
                    <ul class="submenu">
                        <?php foreach($nav_categories as $category): ?>
                        <li><a href="products.php?category=<?= $category['id'] ?>"><?= $category['name'] ?></a></li>
                        <?php endforeach; ?>
                        <li><a href="products.php?sale=1" class="sale-link">Items</a></li>
                    </ul>
                </li>
                <li><a href="about.php">Sobre</a></li>
                <li><a href="contact.php">Contato</a></li>
            </ul>
        </nav>
    </div>
</header>