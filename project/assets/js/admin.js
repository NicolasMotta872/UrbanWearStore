document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const adminSidebar = document.querySelector('.admin-sidebar');
    const adminContent = document.querySelector('.admin-content');
    
    if (sidebarToggle && adminSidebar && adminContent) {
        sidebarToggle.addEventListener('click', function() {
            adminSidebar.classList.toggle('expanded');
        });
    }
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 300);
        }, 5000);
        
        const closeBtn = alert.querySelector('.close-alert');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 300);
            });
        }
    });
    
    // Delete confirmation modal
    window.confirmDelete = function(id, type) {
        const deleteModal = document.getElementById('deleteModal');
        const confirmDelete = document.getElementById('confirmDelete');
        const cancelDelete = document.getElementById('cancelDelete');
        
        if (deleteModal) {
            deleteModal.style.display = 'block';
            
            if (confirmDelete) {
                confirmDelete.onclick = function() {
                    window.location.href = `delete-${type}.php?id=${id}`;
                };
            }
            
            if (cancelDelete) {
                cancelDelete.onclick = function() {
                    deleteModal.style.display = 'none';
                };
            }
            
            // Close modal when clicking outside
            window.onclick = function(event) {
                if (event.target === deleteModal) {
                    deleteModal.style.display = 'none';
                }
            };
        }
    };
});