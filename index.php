<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduConnect - Plateforme Éducative</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1rem 0;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 2rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }

        .auth-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn-login {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-login:hover {
            background: white;
            color: #667eea;
            transform: translateY(-2px);
        }

        .btn-signin {
            background: white;
            color: #667eea;
        }

        .btn-signin:hover {
            background: #f8f9ff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Main Content */
        main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4rem 0;
        }

        .hero {
            text-align: center;
            color: white;
            max-width: 800px;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            line-height: 1.8;
        }

        .hero-subtitle {
            font-size: 1.1rem;
            margin-bottom: 3rem;
            opacity: 0.8;
        }

        .main-buttons {
            display: flex;
            gap: 2rem;
            justify-content: center;
            margin-top: 3rem;
        }

        .btn-large {
            padding: 1rem 2rem;
            font-size: 1.1rem;
            min-width: 200px;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 4rem;
        }

        .feature {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .feature h3 {
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }

        /* Footer */
        footer {
            background: rgba(0, 0, 0, 0.2);
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: auto;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-links {
            display: flex;
            gap: 2rem;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }

        .footer-links a:hover {
            opacity: 1;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .main-buttons {
                flex-direction: column;
                align-items: center;
            }

            .footer-content {
                flex-direction: column;
                gap: 1rem;
            }

            .footer-links {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="header-content">
                <a href="#" class="logo">EduConnect</a>
                <div class="auth-buttons">
                    <a href="login.php" class="btn btn-login">Se connecter</a>
                    <a href="signin.php" class="btn btn-signin">S'inscrire</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <div class="container">
            <div class="hero">
                <h1>Bienvenue sur EduConnect</h1>
                <p>La plateforme qui connecte professeurs et étudiants pour un apprentissage en ligne innovant</p>
                <p class="hero-subtitle">Répertoriez vos cours ou trouvez la formation parfaite pour votre avenir</p>
                
                <div class="main-buttons">
                    <a href="login.php" class="btn btn-login btn-large">Se connecter</a>
                    <a href="signin.php" class="btn btn-signin btn-large">Créer un compte</a>
                </div>

                <div class="features">
                    <div class="feature">
                        <div class="feature-icon">👨‍🏫</div>
                        <h3>Pour les Professeurs</h3>
                        <p>Créez et gérez vos cours en ligne, suivez les candidatures et développez votre communauté d'apprenants</p>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">🎓</div>
                        <h3>Pour les Étudiants</h3>
                        <p>Découvrez des cours variés, postulez facilement et accédez à un apprentissage de qualité</p>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">🌐</div>
                        <h3>Plateforme Moderne</h3>
                        <p>Interface intuitive, outils collaboratifs et suivi personnalisé pour une expérience optimale</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-links">
                    <a href="#about">À propos</a>
                    <a href="#contact">Contact</a>
                    <a href="#help">Aide</a>
                    <a href="#privacy">Confidentialité</a>
                </div>
                <div>
                    <p>&copy; <?php echo date('Y'); ?> EduConnect. Tous droits réservés.</p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>