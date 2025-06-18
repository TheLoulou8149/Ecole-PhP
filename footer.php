<!-- Footer -->
<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-logo">
                <a href="main.php" class="logo">EduConnect</a>
                <p class="footer-slogan">Votre plateforme éducative connectée</p>
            </div>
            
            <div class="footer-links">
                <div class="link-column">
                    <h3>Navigation</h3>
                    <ul>
                        <li><a href="main.php" class="footer-link">Accueil</a></li>
                        <li><a href="cours.php" class="footer-link">Mes Cours</a></li>
                        <li><a href="matiere.php" class="footer-link">Par Matière</a></li>
                        <li><a href="profil.php" class="footer-link">Profil</a></li>
                    </ul>
                </div>
                
                <div class="link-column">
                    <h3>Ressources</h3>
                    <ul>
                        <li><a href="#" class="footer-link">Bibliothèque</a></li>
                        <li><a href="#" class="footer-link">FAQ</a></li>
                        <li><a href="#" class="footer-link">Tutoriels</a></li>
                        <li><a href="#" class="footer-link">Support</a></li>
                    </ul>
                </div>
                
                <div class="link-column">
                    <h3>Légal</h3>
                    <ul>
                        <li><a href="#" class="footer-link">Mentions légales</a></li>
                        <li><a href="#" class="footer-link">CGU</a></li>
                        <li><a href="#" class="footer-link">Confidentialité</a></li>
                        <li><a href="#" class="footer-link">Cookies</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-social">
                <h3>Nous suivre</h3>
                <div class="social-icons">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> EduConnect. Tous droits réservés.</p>
            <p>Version <?php echo $version ?? '1.0.0'; ?></p>
        </div>
    </div>
</footer>

<!-- Font Awesome pour les icônes -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<script>
    // Script pour le menu mobile (identique à celui du header)
    function toggleMobileMenu() {
        const navMenu = document.getElementById('navMenu');
        navMenu.classList.toggle('active');
    }
</script>

<style>
    /* Styles pour le footer - complémentaires à ceux du header */
    footer {
        background: rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(10px);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        color: white;
        padding: 3rem 0 1rem;
    }
    
    .footer-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-bottom: 2rem;
    }
    
    .footer-logo .logo {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    
    .footer-slogan {
        opacity: 0.8;
        font-size: 0.9rem;
    }
    
    .link-column h3 {
        font-size: 1.2rem;
        margin-bottom: 1.5rem;
        position: relative;
        padding-bottom: 0.5rem;
    }
    
    .link-column h3::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 50px;
        height: 2px;
        background: rgba(255, 255, 255, 0.5);
    }
    
    .link-column ul {
        list-style: none;
    }
    
    .link-column li {
        margin-bottom: 0.8rem;
    }
    
    .footer-link {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }
    
    .footer-link:hover {
        color: white;
        padding-left: 5px;
    }
    
    .footer-social h3 {
        font-size: 1.2rem;
        margin-bottom: 1.5rem;
    }
    
    .social-icons {
        display: flex;
        gap: 1rem;
    }
    
    .social-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 35px;
        height: 35px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        color: white;
        transition: all 0.3s ease;
    }
    
    .social-icon:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-3px);
    }
    
    .footer-bottom {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 1.5rem;
        display: flex;
        justify-content: space-between;
        font-size: 0.8rem;
        opacity: 0.7;
    }
    
    @media (max-width: 768px) {
        .footer-content {
            grid-template-columns: 1fr;
        }
        
        .footer-bottom {
            flex-direction: column;
            text-align: center;
            gap: 0.5rem;
        }
    }
</style>