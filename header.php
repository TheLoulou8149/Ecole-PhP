<?php
// Démarrer la session si pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclure la configuration de la base de données
require_once 'config.php';

// Récupérer les informations de l'utilisateur depuis la session
$user_name = $_SESSION['user_name'] ?? "Jean Dupont"; // Valeur par défaut si non connecté
$user_type = $_SESSION['user_type'] ?? "etudiant";    // Valeur par défaut si non connecté
$user_initial = strtoupper(substr($user_name, 0, 1)); // Première lettre du nom pour l'avatar
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - EduConnect</title>
    <style>
        /* [Vos styles CSS existants - inchangés] */
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

        /* [Tous vos autres styles restent exactement les mêmes] */
        /* ... */
        
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
                        <?php echo $user_initial; ?>
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