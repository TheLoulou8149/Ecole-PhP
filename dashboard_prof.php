<?php
require_once 'config.php';

// Vérifier si l'utilisateur est connecté et est un professeur
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'prof') {
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
    <title>Tableau de bord Professeur</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
            color: #667eea;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .stat-card p {
            color: #666;
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
        .action-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 1rem;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .action-btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Plateforme de Cours - Professeur</h1>
        <div class="user-info">
            <span>Bonjour, <?php echo htmlspecialchars($user_name); ?></span>
            <a href="logout.php" class="logout-btn">Déconnexion</a>
        </div>
    </div>

    <div class="container">
        <div class="welcome-card">
            <h2>Bienvenue dans votre espace professeur</h2>
            <p>Gérez vos cours, vos étudiants et suivez leurs progrès depuis ce tableau de bord.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>5</h3>
                <p>Cours actifs</p>
            </div>
            <div class="stat-card">
                <h3>42</h3>
                <p>Étudiants inscrits</p>
            </div>
            <div class="stat-card">
                <h3>12</h3>
                <p>Devoirs à corriger</p>
            </div>
            <div class="stat-card">
                <h3>98%</h3>
                <p>Taux de satisfaction</p>
            </div>
        </div>

        <div class="actions-grid">
            <div class="action-card">
                <h3>Mes Cours</h3>
                <p>Créez, modifiez et gérez vos cours en ligne. Ajoutez du contenu, des vidéos et des ressources.</p>
                <a href="#" class="action-btn">Gérer les cours</a>
            </div>

            <div class="action-card">
                <h3>Mes Étudiants</h3>
                <p>Consultez la liste de vos étudiants, suivez leurs progrès et communiquez avec eux.</p>
                <a href="#" class="action-btn">Voir les étudiants</a>
            </div>

            <div class="action-card">
                <h3>Devoirs & Évaluations</h3>
                <p>Créez des devoirs, des quiz et évaluez le travail de vos étudiants.</p>
                <a href="#" class="action-btn">Gérer les devoirs</a>
            </div>

            <div class="action-card">
                <h3>Messages</h3>
                <p>Consultez vos messages et communiquez avec vos étudiants et collègues.</p>
                <a href="#" class="action-btn">Voir les messages</a>
            </div>

            <div class="action-card">
                <h3>Statistiques</h3>
                <p>Analysez les performances de vos cours et le progrès de vos étudiants.</p>
                <a href="#" class="action-btn">Voir les stats</a>
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