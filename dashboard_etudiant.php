<?php
require_once 'config.php';

// Vérifier si l'utilisateur est connecté et est un étudiant
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'etudiant') {
    header('Location: login.php');
    exit();
}

$user_name = $_SESSION['prenom'] . ' ' . $_SESSION['nom'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Étudiant</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }
        .header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            font-size: 1.5rem;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .logout-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
        }
        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        .welcome-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .welcome-card h2 {
            color: #333;
            margin-bottom: 1rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-card h3 {
            color: #4facfe;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .stat-card p {
            color: #666;
        }
        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .course-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .course-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 1rem;
        }
        .course-body {
            padding: 1.5rem;
        }
        .course-body h3 {
            color: #333;
            margin-bottom: 0.5rem;
        }
        .course-body p {
            color: #666;
            margin-bottom: 1rem;
        }
        .progress-bar {
            background: #f0f0f0;
            border-radius: 10px;
            height: 8px;
            margin-bottom: 1rem;
        }
        .progress-fill {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s;
        }
        .action-btn {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .action-btn:hover {
            transform: translateY(-2px);
        }
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        .action-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .action-card h3 {
            color: #333;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Plateforme de Cours - Étudiant</h1>
        <div class="user-info">
            <span>Bonjour, <?php echo htmlspecialchars($user_name); ?></span>
            <a href="logout.php" class="logout-btn">Déconnexion</a>
        </div>
    </div>

    <div class="container">
        <div class="welcome-card">
            <h2>Bienvenue dans votre espace étudiant</h2>
            <p>Accédez à vos cours, suivez votre progression et consultez vos résultats.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>3</h3>
                <p>Cours suivis</p>
            </div>
            <div class="stat-card">
                <h3>85%</h3>
                <p>Progression moyenne</p>
            </div>
            <div class="stat-card">
                <h3>16.2</h3>
                <p>Moyenne générale</p>
            </div>
            <div class="stat-card">
                <h3>4</h3>
                <p>Devoirs à rendre</p>
            </div>
        </div>

        <h2 style="color: #333; margin-bottom: 1rem;">Mes Cours</h2>
        <div class="course-grid">
            <div class="course-card">
                <div class="course-header">
                    <h3>Mathématiques Avancées</h3>
                    <p>Prof. Martin Durand</p>
                </div>
                <div class="course-body">
                    <p>Algèbre linéaire et calcul différentiel</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 75%;"></div>
                    </div>
                    <p style="font-size: 0.9rem; color: #888;">Progression: 75%</p>
                    <a href="#" class="action-btn">Continuer le cours</a>
                </div>
            </div>

            <div class="course-card">
                <div class="course-header">
                    <h3>Programmation PHP</h3>
                    <p>Prof. Sophie Leroy</p>
                </div>
                <div class="course-body">
                    <p>Développement web avec PHP et MySQL</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 90%;"></div>
                    </div>
                    <p style="font-size: 0.9rem; color: #888;">Progression: 90%</p>
                    <a href="#" class="action-btn">Continuer le cours</a>
                </div>
            </div>

            <div class="course-card">
                <div class="course-header">
                    <h3>Design Graphique</h3>
                    <p>Prof. Claire Garcia</p>
                </div>
                <div class="course-body">
                    <p>Principes du design et utilisation d'Adobe Creative Suite</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 60%;"></div>
                    </div>
                    <p style="font-size: 0.9rem; color: #888;">Progression: 60%</p>
                    <a href="#" class="action-btn">Continuer le cours</a>
                </div>
            </div>
        </div>

        <h2 style="color: #333; margin-bottom: 1rem;">Actions Rapides</h2>
        <div class="actions-grid">
            <div class="action-card">
                <h3>Mes Devoirs</h3>
                <p>Consultez vos devoirs en cours et rendez vos travaux avant la date limite.</p>
                <a href="#" class="action-btn">Voir mes devoirs</a>
            </div>

            <div class="action-card">
                <h3>Mes Notes</h3>
                <p>Consultez vos notes et suivez votre progression dans chaque matière.</p>
                <a href="#" class="action-btn">Voir mes notes</a>
            </div>

            <div class="action-card">
                <h3>Planning</h3>
                <p>Consultez votre planning de cours et les dates importantes à retenir.</p>
                <a href="#" class="action-btn">Voir le planning</a>
            </div>

            <div class="action-card">
                <h3>Messages</h3>
                <p>Communiquez avec vos professeurs et recevez des notifications importantes.</p>
                <a href="#" class="action-btn">Voir les messages</a>
            </div>

            <div class="action-card">
                <h3>Bibliothèque</h3>
                <p>Accédez aux ressources pédagogiques et documents de cours.</p>
                <a href="#" class="action-btn">Accéder à la bibliothèque</a>
            </div>

            <div class="action-card">
                <h3>Mon Profil</h3>
                <p>Modifiez vos informations personnelles et préférences de compte.</p>
                <a href="#" class="action-btn">Éditer le profil</a>
            </div>
        </div>
    </div>
</body>
</html>