<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Récupérer les informations de l'utilisateur depuis la session
$user_name = $_SESSION['user_name'] ?? 'Utilisateur';
$user_type = $_SESSION['user_type'] ?? 'etudiant'; // 'professeur' ou 'etudiant'
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - EduConnect</title>
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

        .nav-menu {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: white;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        /* Main Content */
        main {
            padding: 3rem 0;
        }

        .welcome-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 3rem;
            text-align: center;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .welcome-title {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .welcome-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .action-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: white;
        }

        .action-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .action-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .action-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .action-description {
            opacity: 0.9;
            line-height: 1.6;
        }

        .stats-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .main-cta {
            text-align: center;
            margin: 3rem 0;
        }

        .btn-primary {
            background: white;
            color: #667eea;
            padding: 1rem 2rem;
            border: none;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #f8f9ff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Footer */
        footer {
            background: rgba(0, 0, 0, 0.2);
            color: white;
            text-align: center;
            padding: 2rem 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-menu {
                flex-wrap: wrap;
                justify-content: center;
            }

            .welcome-title {
                font-size: 2rem;
            }

            .quick-actions {
                grid-template-columns: 1fr;
            }

            .stats-section {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Mobile menu toggle */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: block;
            }

            .nav-menu {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                flex-direction: column;
                padding: 1rem;
                border-radius: 0 0 15px 15px;
            }

            .nav-menu.active {
                display: flex;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="header-content">
                <a href="main.php" class="logo">EduConnect</a>
                
                <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">☰</button>
                
                <nav class="nav-menu" id="navMenu">
                    <a href="cours.php" class="nav-link">Mes Cours</a>
                    <a href="matiere.php" class="nav-link">Par Matière</a>
                    <a href="profil.php" class="nav-link">Profil</a>
                </nav>

                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                    </div>
                    <span><?php echo htmlspecialchars($user_name); ?></span>
                    <a href="logout.php" class="logout-btn">Déconnexion</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <div class="container">
            <!-- Welcome Section -->
            <section class="welcome-section">
                <h1 class="welcome-title">
                    Bienvenue, <?php echo htmlspecialchars($user_name); ?>!
                </h1>
                <p class="welcome-subtitle">
                    <?php if ($user_type == 'professeur'): ?>
                        Gérez vos cours et suivez les candidatures de vos étudiants
                    <?php else: ?>
                        Découvrez de nouveaux cours et continuez votre apprentissage
                    <?php endif; ?>
                </p>
            </section>

            <!-- Quick Actions -->
            <section class="quick-actions">
                <a href="cours.php" class="action-card">
                    <div class="action-icon">📚</div>
                    <h3 class="action-title">Consulter mes cours</h3>
                    <p class="action-description">
                        <?php if ($user_type == 'professeur'): ?>
                            Voir tous vos cours publiés et gérer les inscriptions
                        <?php else: ?>
                            Accéder à vos cours inscrits et voir vos progrès
                        <?php endif; ?>
                    </p>
                </a>

                <a href="matiere.php" class="action-card">
                    <div class="action-icon">🎯</div>
                    <h3 class="action-title">Parcourir par matière</h3>
                    <p class="action-description">
                        Explorez les cours organisés par domaine d'étude et spécialité
                    </p>
                </a>

                <?php if ($user_type == 'professeur'): ?>
                <a href="nouveau-cours.php" class="action-card">
                    <div class="action-icon">➕</div>
                    <h3 class="action-title">Créer un cours</h3>
                    <p class="action-description">
                        Ajoutez un nouveau cours et partagez vos connaissances
                    </p>
                </a>
                <?php else: ?>
                <a href="recherche.php" class="action-card">
                    <div class="action-icon">🔍</div>
                    <h3 class="action-title">Rechercher des cours</h3>
                    <p class="action-description">
                        Trouvez de nouveaux cours qui correspondent à vos intérêts
                    </p>
                </a>
                <?php endif; ?>
            </section>

            <!-- Stats Section -->
            <section class="stats-section">
                <div class="stat-card">
                    <div class="stat-number">
                        <?php 
                        // Ici vous pourrez ajouter une requête pour compter les cours
                        echo $user_type == 'professeur' ? '8' : '5'; 
                        ?>
                    </div>
                    <div class="stat-label">
                        <?php echo $user_type == 'professeur' ? 'Cours créés' : 'Cours suivis'; ?>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number">
                        <?php echo $user_type == 'professeur' ? '42' : '12'; ?>
                    </div>
                    <div class="stat-label">
                        <?php echo $user_type == 'professeur' ? 'Étudiants inscrits' : 'Heures d\'étude'; ?>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number">
                        <?php echo $user_type == 'professeur' ? '4.8' : '85%'; ?>
                    </div>
                    <div class="stat-label">
                        <?php echo $user_type == 'professeur' ? 'Note moyenne' : 'Progression'; ?>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number">
                        <?php echo $user_type == 'professeur' ? '15' : '3'; ?>
                    </div>
                    <div class="stat-label">
                        <?php echo $user_type == 'professeur' ? 'Nouvelles candidatures' : 'Certificats obtenus'; ?>
                    </div>
                </div>
            </section>

            <!-- Main CTA -->
            <section class="main-cta">
                <a href="cours.php" class="btn-primary">
                    <?php echo $user_type == 'professeur' ? 'Gérer mes cours' : 'Voir mes cours'; ?>
                </a>
            </section>
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