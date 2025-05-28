document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart from localStorage
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    updateCartCount();
    
    // Mobile menu toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            mobileMenu.classList.toggle('active');
        });
    }
    
    // Mobile search toggle
    const searchToggle = document.getElementById('searchToggle');
    const mobileSearch = document.querySelector('.mobile-search');
    
    if (searchToggle) {
        searchToggle.addEventListener('click', function() {
            mobileSearch.classList.toggle('active');
        });
    }
    
    // Mobile submenu toggle
    const hasSubmenu = document.querySelectorAll('.has-submenu');
    
    hasSubmenu.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            this.classList.toggle('active');
            const submenu = this.nextElementSibling;
            submenu.classList.toggle('active');
        });
    });
    
    // Cart sidebar toggle
    const cartToggle = document.getElementById('cartToggle');
    const cartSidebar = document.getElementById('cartSidebar');
    const closeCart = document.getElementById('closeCart');
    
    if (cartToggle) {
        cartToggle.addEventListener('click', function() {
            cartSidebar.classList.add('open');
            renderCart();
        });
    }
    
    if (closeCart) {
        closeCart.addEventListener('click', function() {
            cartSidebar.classList.remove('open');
        });
    }
    
    // Close cart when clicking outside
    document.addEventListener('click', function(event) {
        if (cartSidebar && cartSidebar.classList.contains('open') && 
            !cartSidebar.contains(event.target) && 
            !cartToggle.contains(event.target)) {
            cartSidebar.classList.remove('open');
        }
    });
    
    // Quick view modal
    const quickViewBtns = document.querySelectorAll('.quick-view-btn');
    const quickViewModal = document.getElementById('quickViewModal');
    const closeModal = document.querySelector('.close-modal');
    const quickViewContent = document.getElementById('quickViewContent');
    
    quickViewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            loadQuickView(productId);
        });
    });
    
    if (closeModal) {
        closeModal.addEventListener('click', function() {
            quickViewModal.style.display = 'none';
        });
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === quickViewModal) {
            quickViewModal.style.display = 'none';
        }
    });
    
    // Add to cart buttons
    const addToCartBtns = document.querySelectorAll('.add-to-cart-btn');
    
    addToCartBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            const productName = this.getAttribute('data-name');
            const productPrice = parseFloat(this.getAttribute('data-price'));
            const productImage = this.getAttribute('data-image');
            
            addToCart(productId, productName, productPrice, productImage, 1);
            
            // Show notification
            showNotification(`${productName} added to cart!`);
        });
    });
    
    // Search results functionality
    const headerSearchInput = document.getElementById('headerSearchInput');
    const searchResults = document.getElementById('searchResults');
    
    if (headerSearchInput) {
        headerSearchInput.addEventListener('input', debounce(function() {
            const searchTerm = this.value.trim();
            
            if (searchTerm.length < 2) {
                searchResults.style.display = 'none';
                return;
            }
            
            fetch(`ajax/search.php?term=${encodeURIComponent(searchTerm)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.products.length > 0) {
                        let resultsHtml = '';
                        
                        data.products.forEach(product => {
                            resultsHtml += `
                                <div class="search-result-item">
                                    <img src="${product.image}" alt="${product.name}">
                                    <div class="search-result-info">
                                        <h4>${product.name}</h4>
                                        <p>$${parseFloat(product.price).toFixed(2)}</p>
                                    </div>
                                </div>
                            `;
                        });
                        
                        searchResults.innerHTML = resultsHtml;
                        searchResults.style.display = 'block';
                        
                        // Add click event to search result items
                        const searchResultItems = document.querySelectorAll('.search-result-item');
                        searchResultItems.forEach(item => {
                            item.addEventListener('click', function() {
                                const productName = this.querySelector('h4').textContent;
                                headerSearchInput.value = productName;
                                document.getElementById('headerSearchForm').submit();
                            });
                        });
                        
                    } else {
                        searchResults.innerHTML = '<div class="no-results">No products found</div>';
                        searchResults.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }, 300));
        
        // Hide search results when clicking outside
        document.addEventListener('click', function(event) {
            if (!headerSearchInput.contains(event.target) && !searchResults.contains(event.target)) {
                searchResults.style.display = 'none';
            }
        });
    }
    
    // Functions
    
    // Load quick view content
    function loadQuickView(productId) {
        fetch(`ajax/quick-view.php?id=${productId}`)
            .then(response => response.text())
            .then(data => {
                quickViewContent.innerHTML = data;
                quickViewModal.style.display = 'block';
                
                // Add event listeners for quick view add to cart
                const quickViewAddBtn = document.getElementById('quickViewAddToCart');
                if (quickViewAddBtn) {
                    quickViewAddBtn.addEventListener('click', function() {
                        const productId = this.getAttribute('data-id');
                        const productName = this.getAttribute('data-name');
                        const productPrice = parseFloat(this.getAttribute('data-price'));
                        const productImage = this.getAttribute('data-image');
                        const quantity = parseInt(document.getElementById('quickViewQuantity').value);
                        
                        addToCart(productId, productName, productPrice, productImage, quantity);
                        
                        // Show notification
                        showNotification(`${productName} added to cart!`);
                        
                        // Close modal
                        quickViewModal.style.display = 'none';
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
    
    // Add product to cart
    function addToCart(id, name, price, image, quantity) {
        // Check if product already in cart
        const existingItemIndex = cart.findIndex(item => item.id === id);
        
        if (existingItemIndex > -1) {
            // Product exists, update quantity
            cart[existingItemIndex].quantity += quantity;
        } else {
            // Add new product to cart
            cart.push({
                id: id,
                name: name,
                price: price,
                image: image,
                quantity: quantity
            });
        }
        
        // Save cart to localStorage
        localStorage.setItem('cart', JSON.stringify(cart));
        
        // Update cart count
        updateCartCount();
        
        // Render cart if open
        if (cartSidebar && cartSidebar.classList.contains('open')) {
            renderCart();
        }
    }
    
    // Update cart item quantity
    function updateCartItemQuantity(index, quantity) {
        if (quantity < 1) return;
        
        cart[index].quantity = quantity;
        localStorage.setItem('cart', JSON.stringify(cart));
        renderCart();
        updateCartCount();
    }
    
    // Remove item from cart
    function removeCartItem(index) {
        cart.splice(index, 1);
        localStorage.setItem('cart', JSON.stringify(cart));
        renderCart();
        updateCartCount();
    }
    
    // Update cart count
    function updateCartCount() {
        const cartCount = document.getElementById('cartCount');
        if (cartCount) {
            const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
            cartCount.textContent = totalItems;
        }
    }
    
    // Render cart items
    function renderCart() {
        const cartItems = document.getElementById('cartItems');
        const cartTotal = document.getElementById('cartTotal');
        
        if (!cartItems || !cartTotal) return;
        
        if (cart.length === 0) {
            cartItems.innerHTML = `
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <p>Your cart is empty</p>
                    <a href="products.php" class="btn btn-primary">Shop Now</a>
                </div>
            `;
            cartTotal.textContent = '$0.00';
            return;
        }
        
        let itemsHtml = '';
        let total = 0;
        
        cart.forEach((item, index) => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;
            
            itemsHtml += `
                <div class="cart-item">
                    <div class="cart-item-img">
                        <img src="${item.image}" alt="${item.name}">
                    </div>
                    <div class="cart-item-info">
                        <h4 class="cart-item-name">${item.name}</h4>
                        <p class="cart-item-price">$${item.price.toFixed(2)}</p>
                        <div class="cart-item-quantity">
                            <button class="cart-quantity-btn minus" data-index="${index}">-</button>
                            <input type="text" value="${item.quantity}" readonly>
                            <button class="cart-quantity-btn plus" data-index="${index}">+</button>
                        </div>
                    </div>
                    <button class="cart-item-remove" data-index="${index}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
        });
        
        cartItems.innerHTML = itemsHtml;
        cartTotal.textContent = `$${total.toFixed(2)}`;
        
        // Add event listeners for cart item actions
        const plusBtns = document.querySelectorAll('.cart-quantity-btn.plus');
        const minusBtns = document.querySelectorAll('.cart-quantity-btn.minus');
        const removeBtns = document.querySelectorAll('.cart-item-remove');
        
        plusBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));
                updateCartItemQuantity(index, cart[index].quantity + 1);
            });
        });
        
        minusBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));
                if (cart[index].quantity > 1) {
                    updateCartItemQuantity(index, cart[index].quantity - 1);
                }
            });
        });
        
        removeBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));
                removeCartItem(index);
            });
        });
    }
    
    // Show notification
    function showNotification(message) {
        // Create notification element if it doesn't exist
        let notification = document.querySelector('.notification');
        
        if (!notification) {
            notification = document.createElement('div');
            notification.className = 'notification';
            document.body.appendChild(notification);
            
            // Add CSS for notification
            const style = document.createElement('style');
            style.textContent = `
                .notification {
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    background-color: var(--primary-color);
                    color: white;
                    padding: 12px 20px;
                    border-radius: 4px;
                    box-shadow: 0 3px 10px rgba(0,0,0,0.2);
                    z-index: 1200;
                    transform: translateY(100px);
                    opacity: 0;
                    transition: all 0.3s ease;
                }
                
                .notification.show {
                    transform: translateY(0);
                    opacity: 1;
                }
            `;
            document.head.appendChild(style);
        }
        
        notification.textContent = message;
        notification.classList.add('show');
        
        // Hide notification after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
        }, 3000);
    }
    
    // Debounce function for search
    function debounce(func, delay) {
        let timeout;
        return function() {
            const context = this;
            const args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    }
});