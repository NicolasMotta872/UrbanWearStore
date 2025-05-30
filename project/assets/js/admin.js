/**
 * StoreScobars - Admin JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Confirmação para exclusão
    const deleteButtons = document.querySelectorAll('.delete-btn');
    if (deleteButtons.length > 0) {
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Tem certeza que deseja excluir este item? Esta ação não pode ser desfeita.')) {
                    e.preventDefault();
                }
            });
        });
    }

    // Preview de imagem para upload
    const imageInput = document.getElementById('imagem');
    const imagePreview = document.getElementById('image-preview');
    
    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.addEventListener('load', function() {
                    imagePreview.src = this.result;
                    imagePreview.style.display = 'block';
                });
                
                reader.readAsDataURL(file);
            }
        });
    }

    // Formatação de valores monetários
    const priceInput = document.getElementById('preco');
    if (priceInput) {
        priceInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            value = (value / 100).toFixed(2) + '';
            value = value.replace(".", ",");
            value = value.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
            this.value = value;
        });
        
        // Ao enviar o formulário, converter de volta para formato que o PHP espera
        const form = priceInput.closest('form');
        if (form) {
            form.addEventListener('submit', function() {
                let value = priceInput.value.replace(/\./g, '').replace(',', '.');
                priceInput.value = value;
            });
        }
    }

    // Exibir alertas com fadeout
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
    const adminForms = document.querySelectorAll('.admin-form');
    if (adminForms.length > 0) {
        adminForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const requiredFields = this.querySelectorAll('[required]');
                let hasError = false;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        hasError = true;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                if (hasError) {
                    e.preventDefault();
                    alert('Por favor, preencha todos os campos obrigatórios.');
                }
            });
        });
    }
});