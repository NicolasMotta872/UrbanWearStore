document.addEventListener('DOMContentLoaded', function() {
    // Price range slider
    const priceRange = document.getElementById('priceRange');
    const priceValue = document.getElementById('priceValue');
    
    if (priceRange && priceValue) {
        priceRange.addEventListener('input', function() {
            priceValue.textContent = `$${this.value}`;
            filterProducts();
        });
    }
    
    // Sale filter
    const saleFilter = document.getElementById('saleFilter');
    
    if (saleFilter) {
        saleFilter.addEventListener('change', function() {
            filterProducts();
        });
    }
    
    // Sort products
    const sortOrder = document.getElementById('sortOrder');
    
    if (sortOrder) {
        sortOrder.addEventListener('change', function() {
            sortProducts(this.value);
        });
    }
    
    // Filter products based on price and sale
    function filterProducts() {
        const productsGrid = document.getElementById('productsGrid');
        const products = document.querySelectorAll('.product-card');
        const maxPrice = parseInt(priceRange.value);
        const showSaleOnly = saleFilter.checked;
        
        let visibleCount = 0;
        
        products.forEach(product => {
            const price = parseFloat(product.getAttribute('data-price'));
            const hasSaleBadge = product.querySelector('.sale-badge') !== null;
            
            const matchesPrice = price <= maxPrice;
            const matchesSale = !showSaleOnly || (showSaleOnly && hasSaleBadge);
            
            if (matchesPrice && matchesSale) {
                product.style.display = 'block';
                visibleCount++;
            } else {
                product.style.display = 'none';
            }
        });
        
        // Show no products message if no visible products
        if (visibleCount === 0) {
            let noProductsEl = document.querySelector('.no-products');
            
            if (!noProductsEl) {
                noProductsEl = document.createElement('div');
                noProductsEl.className = 'no-products';
                noProductsEl.innerHTML = `
                    <p>No products found. Try adjusting your filters.</p>
                    <button class="btn btn-secondary reset-filters">Reset Filters</button>
                `;
                productsGrid.parentNode.insertBefore(noProductsEl, productsGrid.nextSibling);
                
                // Add event listener to reset filters button
                const resetButton = noProductsEl.querySelector('.reset-filters');
                resetButton.addEventListener('click', resetFilters);
            }
        } else {
            const noProductsEl = document.querySelector('.no-products');
            if (noProductsEl) {
                noProductsEl.remove();
            }
        }
    }
    
    // Reset filters
    function resetFilters() {
        if (priceRange) priceRange.value = priceRange.max;
        if (priceValue) priceValue.textContent = `$${priceRange.max}`;
        if (saleFilter) saleFilter.checked = false;
        if (sortOrder) sortOrder.value = 'default';
        
        filterProducts();
        sortProducts('default');
    }
    
    // Sort products
    function sortProducts(order) {
        const productsGrid = document.getElementById('productsGrid');
        const products = Array.from(document.querySelectorAll('.product-card'));
        
        if (products.length === 0) return;
        
        products.sort((a, b) => {
            const priceA = parseFloat(a.getAttribute('data-price'));
            const priceB = parseFloat(b.getAttribute('data-price'));
            const nameA = a.getAttribute('data-name');
            const nameB = b.getAttribute('data-name');
            
            switch(order) {
                case 'price-asc':
                    return priceA - priceB;
                case 'price-desc':
                    return priceB - priceA;
                case 'name-asc':
                    return nameA.localeCompare(nameB);
                case 'name-desc':
                    return nameB.localeCompare(nameA);
                default:
                    return 0; // Keep original order
            }
        });
        
        // Clear products grid
        productsGrid.innerHTML = '';
        
        // Append sorted products
        products.forEach(product => {
            productsGrid.appendChild(product);
        });
    }
});