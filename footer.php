<!-- Footer -->
<footer>
    <div class="container">
        <div class="footer-grid">
            <!-- Colonne Logo + Description -->
            <div class="footer-brand">
                <a href="main.php" class="footer-logo">EduConnect</a>
                <p class="footer-tagline">Votre plateforme éducative connectée</p>
                <div class="footer-social">
                    <a href="#" aria-label="Facebook" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="Instagram" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="LinkedIn" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

            <!-- Colonne Liens -->
            <div class="footer-links">
                <div class="links-group">
                    <h3 class="links-title">Navigation</h3>
                    <ul>
                        <li><a href="main.php" class="footer-link">Accueil</a></li>
                        <li><a href="cours.php" class="footer-link">Mes Cours</a></li>
                        <li><a href="matiere.php" class="footer-link">Par Matière</a></li>
                        <li><a href="profil.php" class="footer-link">Profil</a></li>
                    </ul>
                </div>

                <div class="links-group">
                    <h3 class="links-title">Ressources</h3>
                    <ul>
                        <li><a href="#" class="footer-link">Bibliothèque</a></li>
                        <li><a href="#" class="footer-link">FAQ</a></li>
                        <li><a href="#" class="footer-link">Tutoriels</a></li>
                        <li><a href="#" class="footer-link">Support</a></li>
                    </ul>
                </div>

                <div class="links-group">
                    <h3 class="links-title">Légal</h3>
                    <ul>
                        <li><a href="#" class="footer-link">Mentions légales</a></li>
                        <li><a href="#" class="footer-link">CGU</a></li>
                        <li><a href="#" class="footer-link">Confidentialité</a></li>
                        <li><a href="#" class="footer-link">Cookies</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p class="copyright">&copy; <?php echo date('Y'); ?> EduConnect. Tous droits réservés.</p>
            <div class="legal-links">
                <a href="#" class="legal-link">Politique de confidentialité</a>
                <span class="separator">|</span>
                <a href="#" class="legal-link">Conditions d'utilisation</a>
            </div>
        </div>
    </div>
</footer>

<style>
    /* Styles modernes pour le footer */
    footer {
        background: rgba(0, 0, 0, 0.25);
        backdrop-filter: blur(12px);
        color: white;
        padding: 4rem 0 1.5rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .footer-grid {
        display: grid;
        grid-template-columns: 1.5fr 2fr;
        gap: 3rem;
        margin-bottom: 3rem;
    }

    .footer-brand {
        display: flex;
        flex-direction: column;
    }

    .footer-logo {
        font-size: 2.2rem;
        font-weight: 700;
        color: white;
        text-decoration: none;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .footer-logo:hover {
        opacity: 0.9;
    }

    .footer-tagline {
        font-size: 1rem;
        opacity: 0.8;
        margin-bottom: 2rem;
        line-height: 1.6;
    }

    .footer-social {
        display: flex;
        gap: 1rem;
    }

    .social-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
        color: white;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .social-link:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-3px);
    }

    .footer-links {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
    }

    .links-group {
        display: flex;
        flex-direction: column;
    }

    .links-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        position: relative;
        padding-bottom: 0.5rem;
    }

    .links-title::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 40px;
        height: 2px;
        background: rgba(255, 255, 255, 0.3);
    }

    .footer-links ul {
        list-style: none;
    }

    .footer-links li {
        margin-bottom: 0.8rem;
    }

    .footer-link {
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        position: relative;
    }

    .footer-link:hover {
        color: white;
        padding-left: 8px;
    }

    .footer-link::before {
        content: '→';
        position: absolute;
        left: -10px;
        opacity: 0;
        transition: all 0.3s ease;
    }

    .footer-link:hover::before {
        left: 0;
        opacity: 1;
    }

    .footer-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 2rem;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
    }

    .copyright {
        font-size: 0.85rem;
        opacity: 0.7;
    }

    .legal-links {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .legal-link {
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        font-size: 0.85rem;
        transition: all 0.3s ease;
    }

    .legal-link:hover {
        color: white;
    }

    .separator {
        opacity: 0.3;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .footer-grid {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
    }

    @media (max-width: 768px) {
        .footer-links {
            grid-template-columns: 1fr;
            gap: 2.5rem;
        }

        .footer-bottom {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }
    }
</style>