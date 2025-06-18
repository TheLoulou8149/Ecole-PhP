<!-- Footer -->
<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-main">
                <a href="main.php" class="footer-logo">EduConnect</a>
                <p class="footer-tagline">Votre plateforme éducative connectée</p>
                
                <div class="footer-nav">
                    <a href="cours.php" class="footer-nav-link">Mes Cours</a>
                    <a href="matiere.php" class="footer-nav-link">Par Matière</a>
                    <a href="profil.php" class="footer-nav-link">Profil</a>
                </div>
            </div>
            
            <div class="footer-secondary">
                <div class="footer-legal">
                    <a href="#" class="legal-link">Mentions légales</a>
                    <a href="#" class="legal-link">CGU</a>
                    <a href="#" class="legal-link">Confidentialité</a>
                </div>
                
                <div class="footer-social">
                    <a href="#" aria-label="Facebook" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="Instagram" class="social-link"><i class="fab fa-instagram"></i></a>
                </div>
                
                <p class="copyright">&copy; <?php echo date('Y'); ?> EduConnect. Tous droits réservés.</p>
            </div>
        </div>
    </div>
</footer>

<style>
    /* Styles pour le footer - en harmonie avec le header */
    footer {
        background: rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(10px);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        color: white;
        padding: 2.5rem 0;
    }
    
    .footer-content {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 2rem;
    }
    
    .footer-main {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        max-width: 400px;
    }
    
    .footer-logo {
        font-size: 2rem;
        font-weight: bold;
        color: white;
        text-decoration: none;
    }
    
    .footer-tagline {
        font-size: 1rem;
        opacity: 0.8;
        line-height: 1.5;
    }
    
    .footer-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .footer-nav-link {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }
    
    .footer-nav-link:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        transform: translateY(-2px);
    }
    
    .footer-secondary {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        align-items: flex-end;
        text-align: right;
    }
    
    .footer-legal {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        justify-content: flex-end;
    }
    
    .legal-link {
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        font-size: 0.85rem;
        transition: all 0.3s ease;
    }
    
    .legal-link:hover {
        color: white;
        text-decoration: underline;
    }
    
    .footer-social {
        display: flex;
        gap: 1rem;
    }
    
    .social-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        color: white;
        transition: all 0.3s ease;
    }
    
    .social-link:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-3px);
    }
    
    .copyright {
        font-size: 0.85rem;
        opacity: 0.7;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .footer-content {
            flex-direction: column;
        }
        
        .footer-secondary {
            align-items: flex-start;
            text-align: left;
            margin-top: 1.5rem;
        }
        
        .footer-legal {
            justify-content: flex-start;
        }
    }
</style>

<!-- Font Awesome pour les icônes -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>