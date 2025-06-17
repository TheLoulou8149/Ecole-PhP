<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Inclure le fichier de connexion à la base de données
require_once 'db.php';

// Récupérer les informations de l'utilisateur
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'] ?? 'etudiant'; // 'prof' ou 'etudiant'

// Récupérer les infos selon le type d'utilisateur
if ($user_type == 'prof') {
    $stmt = $pdo->prepare("SELECT nom FROM profs WHERE id_prof = ?");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_name = $user_data['nom'] ?? 'Professeur';
} else {
    $stmt = $pdo->prepare("SELECT nom, prenom FROM etudiants WHERE id_etudiant = ?");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_name = ($user_data['prenom'] ?? '') . ' ' . ($user_data['nom'] ?? 'Étudiant');
}

// Calculer les statistiques
$stats = [];

if ($user_type == 'prof') {
    // Statistiques pour les professeurs
    // Nombre de cours créés
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cours WHERE id_prof = ?");
    $stmt->execute([$user_id]);
    $stats['cours_crees'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Nombre total d'étudiants inscrits à tous ses cours
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT ce.id_etudiant) as count 
        FROM cours_etudiants ce 
        JOIN cours c ON ce.id_cours = c.id_cours 
        WHERE c.id_prof = ?
    ");
    $stmt->execute([$user_id]);
    $stats['etudiants_inscrits'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Nombre de matières enseignées
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT c.id_matiere) as count 
        FROM cours c 
        WHERE c.id_prof = ?
    ");
    $stmt->execute([$user_id]);
    $stats['matieres_enseignees'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Nouvelles inscriptions cette semaine (simulation)
    $stats['nouvelles_inscriptions'] = rand(5, 20);
    
} else {
    // Statistiques pour les étudiants
    // Nombre de cours suivis
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cours_etudiants WHERE id_etudiant = ?");
    $stmt->execute([$user_id]);
    $stats['cours_suivis'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Nombre de matières différentes
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT c.id_matiere) as count 
        FROM cours_etudiants ce 
        JOIN cours c ON ce.id_cours = c.id_cours 
        WHERE ce.id_etudiant = ?
    ");
    $stmt->execute([$user_id]);
    $stats['matieres_suivies'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Diplômes obtenus
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM diplomes WHERE id_etudiant = ?");
    $stmt->execute([$user_id]);
    $stats['diplomes_obtenus'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Progression simulée
    $stats['progression'] = rand(60, 95);
}
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
                    <?php if ($user_type == 'prof'): ?>
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
                        <?php if ($user_type == 'prof'): ?>
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

                <?php if ($user_type == 'prof'): ?>
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
                        if ($user_type == 'prof') {
                            echo $stats['cours_crees'];
                        } else {
                            echo $stats['cours_suivis'];
                        }
                        ?>
                    </div>
                    <div class="stat-label">
                        <?php echo $user_type == 'prof' ? 'Cours créés' : 'Cours suivis'; ?>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number">
                        <?php 
                        if ($user_type == 'prof') {
                            echo $stats['etudiants_inscrits'];
                        } else {
                            echo $stats['matieres_suivies'];
                        }
                        ?>
                    </div>
                    <div class="stat-label">
                        <?php echo $user_type == 'prof' ? 'Étudiants inscrits' : 'Matières suivies'; ?>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number">
                        <?php 
                        if ($user_type == 'prof') {
                            echo $stats['matieres_enseignees'];
                        } else {
                            echo $stats['progression'] . '%';
                        }
                        ?>
                    </div>
                    <div class="stat-label">
                        <?php echo $user_type == 'prof' ? 'Matières enseignées' : 'Progression moyenne'; ?>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number">
                        <?php 
                        if ($user_type == 'prof') {
                            echo $stats['nouvelles_inscriptions'];
                        } else {
                            echo $stats['diplomes_obtenus'];
                        }
                        ?>
                    </div>
                    <div class="stat-label">
                        <?php echo $user_type == 'prof' ? 'Nouvelles inscriptions' : 'Diplômes obtenus'; ?>
                    </div>
                </div>
            </section>

            <!-- Main CTA -->
            <section class="main-cta">
                <a href="cours.php" class="btn-primary">
                    <?php echo $user_type == 'prof' ? 'Gérer mes cours' : 'Voir mes cours'; ?>
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