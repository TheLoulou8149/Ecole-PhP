<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer EduConnect</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        /* Demo content pour montrer le footer en contexte */
        .demo-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            padding: 2rem;
        }

        /* Footer Styles */
        footer {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 3rem 0 1.5rem;
            margin-top: auto;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 3rem;
            margin-bottom: 2rem;
        }

        /* Section principale */
        .footer-main {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .footer-logo {
            font-size: 2.2rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer-logo:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }

        .footer-tagline {
            font-size: 1.1rem;
            opacity: 0.9;
            line-height: 1.6;
            max-width: 300px;
        }

        .footer-description {
            opacity: 0.8;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        /* Navigation footer */
        .footer-nav {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .footer-section-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: white;
        }

        .footer-nav-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 0.5rem 0;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            position: relative;
        }

        .footer-nav-link:hover {
            color: white;
            transform: translateX(5px);
        }

        .footer-nav-link::before {
            content: '→';
            position: absolute;
            left: -20px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .footer-nav-link:hover::before {
            opacity: 1;
        }

        /* Section contact/social */
        .footer-contact {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .contact-item {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }

        .social-link:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Barre de séparation */
        .footer-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            margin: 2rem 0 1.5rem;
        }

        /* Footer bottom */
        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .footer-legal {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
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

        .copyright {
            font-size: 0.85rem;
            opacity: 0.7;
        }

        /* Responsive Design */
        @media (max-width: 968px) {
            .footer-content {
                grid-template-columns: 1fr 1fr;
                gap: 2rem;
            }

            .footer-contact {
                grid-column: span 2;
                margin-top: 1rem;
            }
        }

        @media (max-width: 768px) {
            footer {
                padding: 2rem 0 1rem;
            }

            .footer-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .footer-bottom {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .footer-legal {
                justify-content: center;
            }

            .social-links {
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .footer-logo {
                font-size: 1.8rem;
            }

            .footer-legal {
                flex-direction: column;
                gap: 0.5rem;
            }

            .social-links {
                gap: 0.5rem;
            }

            .social-link {
                width: 36px;
                height: 36px;
            }
        }
    </style>
</head>
<body>
    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <!-- Section principale -->
                <div class="footer-main">
                    <a href="index.php" class="footer-logo">EduConnect</a>
                    <p class="footer-tagline">Votre plateforme éducative connectée</p>
                    <p class="footer-description">
                        Découvrez une nouvelle façon d'apprendre avec nos cours interactifs, 
                        nos outils collaboratifs et notre communauté d'étudiants passionnés.
                    </p>
                </div>

                <!-- Navigation -->
                <div class="footer-nav">
                    <h4 class="footer-section-title">Navigation</h4>
                    <a href="cours.php" class="footer-nav-link">Mes Cours</a>
                    <a href="matiere.php" class="footer-nav-link">Par Matière</a>
                    <a href="profil.php" class="footer-nav-link">Mon Profil</a>
                    <a href="" class="footer-nav-link">Centre d'aide</a>
                    <a href="" class="footer-nav-link">Contact</a>
                </div>

                <!-- Contact et réseaux sociaux -->
                <div class="footer-contact">
                    <h4 class="footer-section-title">Nous suivre</h4>
                    <div class="contact-info">
                        <div class="contact-item">
                            <span>📧</span>
                            <span>contact@educonnect.fr</span>
                        </div>
                        <div class="contact-item">
                            <span>📞</span>
                            <span>09 45 44 67 89</span>
                        </div>
                    </div>
                    
                    <div class="social-links">
                        <a href="#" class="social-link" aria-label="Facebook" title="Facebook">📘</a>
                        <a href="#" class="social-link" aria-label="Twitter" title="Twitter">🐦</a>
                        <a href="#" class="social-link" aria-label="LinkedIn" title="LinkedIn">💼</a>
                        <a href="#" class="social-link" aria-label="Instagram" title="Instagram">📷</a>
                        <a href="#" class="social-link" aria-label="YouTube" title="YouTube">📹</a>
                    </div>
                </div>
            </div>

            <!-- Ligne de séparation -->
            <div class="footer-divider"></div>

            <!-- Footer bottom -->
            <div class="footer-bottom">
            <div class="footer-legal">
                <span class="legal-text">Mentions légales</span>
                <span class="legal-text">CGU</span>
                <span class="legal-text">Confidentialité</span>
                <span class="legal-text">Cookies</span>
            </div>
    <p class="copyright">&copy; <?php echo date('Y'); ?> EduConnect. Tous droits réservés.</p>
            </div>
            </div>
    </footer>
</body>
</html>