<?php
require_once 'auth_check.php';
requireLogin();

$user = getCurrentUser();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Professeur</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
        }
        
        .header {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            color: white;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .logout-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        
        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .welcome-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .welcome-card h1 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .welcome-card p {
            color: #666;
            font-size: 16px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #764ba2;
            margin-bottom: 10px;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        
        .section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .section-title {
            color: #333;
            margin-bottom: 20px;
            font-size: 20px;
        }
        
        .course-item {
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: box-shadow 0.3s;
        }
        
        .course-item:hover {
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .course-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .course-description {
            color: #666;
            font-size: 14px;
        }
        
        .btn {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .student-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        
        .student-name {
            font-weight: 500;
            color: #333;
        }
        
        .student-progress {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="logo">Plateforme de Cours</div>
            <div class="user-info">
                <span>Bonjour, <?php echo htmlspecialchars($user['name']); ?> (Professeur)</span>
                <a href="logout.php" class="logout-btn">Déconnexion</a>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="welcome-card">
            <h1>Tableau de bord - Professeur</h1>
            <p>Gérez vos cours, suivez les progrès de vos étudiants et créez du contenu pédagogique.</p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">8</div>
                <div class="stat-label">Cours créés</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">156</div>
                <div class="stat-label">Étudiants inscrits</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">89%</div>
                <div class="stat-label">Taux de réussite</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">24</div>
                <div class="stat-label">Heures d'enseignement</div>
            </div>
        </div>
        
        <div class="section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 class="section-title" style="margin-bottom: 0;">Mes Cours</h2>
                <a href="#" class="btn">Créer un nouveau cours</a>
            </div>
            
            <div class="course-item">
                <div class="course-title">Mathématiques Avancées</div>
                <div class="course-description">32 étudiants inscrits - Dernière mise à jour: 15 juin 2025</div>
            </div>
            
            <div class="course-item">
                <div class="course-title">Programmation Web</div>
                <div class="course-description">45 étudiants inscrits - Dernière mise à jour: 14 juin 2025</div>
            </div>
            
            <div class="course-item">
                <div class="course-title">Base de données</div>
                <div class="course-description">28 étudiants inscrits - Dernière mise à jour: 12 juin 2025</div>
            </div>
        </div>
        
        <div class="section">
            <h2 class="section-title">Étudiants récents</h2>
            
            <div class="student-item">
                <div>
                    <div class="student-name">Pierre Durand</div>
                    <div class="student-progress">Mathématiques Avancées - 85% terminé</div>
                </div>
                <a href="#" class="btn">Voir le profil</a>
            </div>
            
            <div class="student-item">
                <div>
                    <div class="student-name">Marie Martin</div>
                    <div class="student-progress">Programmation Web - 92% terminé</div>
                </div>
                <a href="#" class="btn">Voir le profil</a>
            </div>
            
            <div class="student-item">
                <div>
                    <div class="student-name">Jean Dubois</div>
                    <div class="student-progress">Base de données - 67% terminé</div>
                </div>
                <a href="#" class="btn">Voir le profil</a>
            </div>
        </div>
    </div>
</body>
</html>