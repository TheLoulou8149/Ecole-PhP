        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> EduConnect. Tous droits réservés.</p>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            const navMenu = document.getElementById('navMenu');
            navMenu.classList.toggle('active');
        }

        // Fermer le menu mobile si on clique ailleurs
        document.addEventListener('click', function(event) {
            const navMenu = document.getElementById('navMenu');
            const toggleButton = document.querySelector('.mobile-menu-toggle');
            
            if (!navMenu.contains(event.target) && !toggleButton.contains(event.target)) {
                navMenu.classList.remove('active');
            }
        });
    </script>
</body>
</html>