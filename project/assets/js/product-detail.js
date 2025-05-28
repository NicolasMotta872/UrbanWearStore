document.addEventListener('DOMContentLoaded', function() {
    // Thumbnail image gallery
    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainImage = document.getElementById('mainProductImage');
    
    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            // Remove active class from all thumbnails
            thumbnails.forEach(thumb => thumb.classList.remove('active'));
            
            // Add active class to clicked thumbnail
            this.classList.add('active');
            
            // Update main image
            const newImageSrc = this.getAttribute('data-image');
            mainImage.src = newImageSrc;
            
            // Add fade effect
            mainImage.style.opacity = '0';
            setTimeout(() => {
                mainImage.style.opacity = '1';
            }, 50);
        });
    });
    
    // Quantity selector
    const decreaseBtn = document.getElementById('decreaseQuantity');
    const increaseBtn = document.getElementById('increaseQuantity');
    const quantityInput = document.getElementById('quantity');
    
    if (decreaseBtn && increaseBtn && quantityInput) {
        decreaseBtn.addEventListener('click', function() {
            const currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        });
        
        increaseBtn.addEventListener('click', function() {
            const currentValue = parseInt(quantityInput.value);
            const maxValue = parseInt(quantityInput.getAttribute('max') || 10);
            if (currentValue < maxValue) {
                quantityInput.value = currentValue + 1;
            }
        });
        
        quantityInput.addEventListener('change', function() {
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
    }
    
    // Add to cart button
    const addToCartBtn = document.getElementById('addToCartBtn');
    
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            const productName = this.getAttribute('data-name');
            const productPrice = parseFloat(this.getAttribute('data-price'));
            const productImage = this.getAttribute('data-image');
            const quantity = parseInt(document.getElementById('quantity').value);
            
            // Get selected size and color if available
            let selectedSize = '';
            let selectedColor = '';
            
            const sizeInputs = document.querySelectorAll('input[name="size"]');
            if (sizeInputs.length > 0) {
                for (const input of sizeInputs) {
                    if (input.checked) {
                        selectedSize = input.value;
                        break;
                    }
                }
            }
            
            const colorInputs = document.querySelectorAll('input[name="color"]');
            if (colorInputs.length > 0) {
                for (const input of colorInputs) {
                    if (input.checked) {
                        selectedColor = input.value;
                        break;
                    }
                }
            }
            
            addToCart(productId, productName, productPrice, productImage, quantity, selectedSize, selectedColor);
        });
    }
    
    // Add product to cart function
    function addToCart(id, name, price, image, quantity, size = '', color = '') {
        // Get cart from localStorage
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        
        // Create a unique identifier for the product including size and color
        const productIdentifier = `${id}-${size}-${color}`;
        
        // Check if product already in cart with same options
        const existingItemIndex = cart.findIndex(item => 
            item.id === id && item.size === size && item.color === color
        );
        
        if (existingItemIndex > -1) {
            // Product exists with same options, update quantity
            cart[existingItemIndex].quantity += quantity;
        } else {
            // Add new product to cart
            cart.push({
                id: id,
                name: name,
                price: price,
                image: image,
                quantity: quantity,
                size: size,
                color: color
            });
        }
        
        // Save cart to localStorage
        localStorage.setItem('cart', JSON.stringify(cart));
        
        // Update cart count
        updateCartCount();
        
        // Show notification
        showNotification(`${name} added to cart!`);
    }
    
    // Update cart count
    function updateCartCount() {
        const cartCount = document.getElementById('cartCount');
        if (cartCount) {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
            cartCount.textContent = totalItems;
        }
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
});