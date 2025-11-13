</div> <!-- .content-wrapper -->
    </main>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script para toggle del sidebar -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggleSidebar');
            const sidebar = document.getElementById('sidebar');
            
            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('open');
                    
                    // En móvil, agregar overlay
                    if (window.innerWidth <= 768) {
                        if (sidebar.classList.contains('open')) {
                            const overlay = document.createElement('div');
                            overlay.className = 'sidebar-overlay';
                            overlay.onclick = function() {
                                sidebar.classList.remove('open');
                                this.remove();
                            };
                            document.body.appendChild(overlay);
                        } else {
                            const overlay = document.querySelector('.sidebar-overlay');
                            if (overlay) overlay.remove();
                        }
                    }
                });
            }
        });
        
        // Notificaciones (sistema simple)
        const Notificacion = {
            exito: function(mensaje) {
                alert('✓ ' + mensaje);
            },
            error: function(mensaje) {
                alert('✗ ' + mensaje);
            },
            info: function(mensaje) {
                alert('ℹ ' + mensaje);
            }
        };
    </script>
    
    <!-- Custom JS -->
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>