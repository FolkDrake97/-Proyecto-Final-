<?php
/**
 * Footer común para todas las páginas
 * Archivo: includes/footer.php
 */
?>
            </div> <!-- Cierre content-wrapper -->
        </main>
    </div> <!-- Cierre main-wrapper -->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/dashboard.js"></script>
    
    <script>
        // Toggle sidebar
        document.getElementById('toggleSidebar')?.addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });

        // Mobile menu
        if (window.innerWidth <= 768) {
            document.getElementById('toggleSidebar')?.addEventListener('click', function() {
                const sidebar = document.getElementById('sidebar');
                sidebar.classList.toggle('open');
            });
        }
    </script>
</body>
</html>