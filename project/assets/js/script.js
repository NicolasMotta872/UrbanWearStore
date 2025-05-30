/**
 * StoreScobars - Frontend JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Carrinho: Atualizações de quantidade
    const quantityBtns = document.querySelectorAll('.quantity-btn');
    if (quantityBtns.length > 0) {
        quantityBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.parentElement.querySelector('input');
                const currentValue = parseInt(input.value);
                
                if (this.classList.contains('decrease') && currentValue > 1) {
                    input.value = currentValue - 1;
                } else if (this.classList.contains('increase')) {
                    input.value = currentValue + 1;
                }
            });
        });
    }

    // Carrinho: Formulário de atualização
    const cartUpdateForm = document.getElementById('cart-update-form');
    if (cartUpdateForm) {
        cartUpdateForm.addEventListener('submit', function(e) {
            // Verificação para evitar quantidades zero ou negativas
            const quantityInputs = this.querySelectorAll('input[name^="quantidade"]');
            let hasError = false;

            quantityInputs.forEach(input => {
                if (parseInt(input.value) <= 0) {
                    hasError = true;
                    alert('A quantidade deve ser maior que zero');
                }
            });

            if (hasError) {
                e.preventDefault();
            }
        });
    }

    // Produtos: Galeria de imagens
    const productThumbs = document.querySelectorAll('.product-thumb');
    if (productThumbs.length > 0) {
        const mainImage = document.querySelector('.product-main-image');
        
        productThumbs.forEach(thumb => {
            thumb.addEventListener('click', function() {
                const imgSrc = this.getAttribute('data-src');
                mainImage.src = imgSrc;
                
                // Remove active class from all thumbs
                productThumbs.forEach(t => t.classList.remove('active'));
                // Add active class to clicked thumb
                this.classList.add('active');
            });
        });
    }

    // Animação suave para mensagens de alerta
    const alerts = document.querySelectorAll('.alert');
    if (alerts.length > 0) {
        setTimeout(() => {
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 500);
            });
        }, 3000);
    }

    // Validação de formulários
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let hasError = false;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    hasError = true;
                } else {
                    field.classList.remove('is-invalid');
                }
                
                // Validação específica para e-mail
                if (field.type === 'email' && field.value.trim()) {
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailPattern.test(field.value)) {
                        field.classList.add('is-invalid');
                        hasError = true;
                    }
                }
            });

            if (hasError) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios corretamente.');
            }
        });
    }
});