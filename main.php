<?php

// Redirection si utilisateur non connecté
//require_once 'auth_check.php';
//requireLogin();


// Démarrage de session et inclusion du header
require_once 'header.php';



// Récupération des informations de l'utilisateur connecté
$welcomeName = htmlspecialchars($_SESSION['user_name'] ?? 'Utilisateur');
$userType = $_SESSION['user_type'] ?? 'étudiant';
?>

<main>
    <div class="container">
        <!-- Section de bienvenue -->
        <section class="welcome-section">
            <h1 class="welcome-title">Connexion réussie !</h1>
            <p class="welcome-subtitle">Bienvenue <?php echo $welcomeName; ?> (<?php echo $userType; ?>)</p>
            <p class="welcome-text">Vous êtes maintenant connecté à votre espace EduConnect.</p>
        </section>

        <!-- Actions rapides -->
        <div class="quick-actions">
            <a href="cours.php" class="action-card">
                <div class="action-icon">📚</div>
                <h3 class="action-title">Mes Cours</h3>
                <p class="action-description">Consultez vos cours et le planning</p>
            </a>

            <a href="matiere.php" class="action-card">
                <div class="action-icon">🧪</div>
                <h3 class="action-title">Par Matière</h3>
                <p class="action-description">Explorez les ressources par discipline</p>
            </a>

            <a href="profil.php" class="action-card">
                <div class="action-icon">👤</div>
                <h3 class="action-title">Mon Profil</h3>
                <p class="action-description">Gérez vos informations personnelles</p>
            </a>
        </div>

        <!-- Statistiques -->
        <div class="stats-section">
            <div class="stat-card">
                <div class="stat-number">5</div>
                <div class="stat-label">Cours aujourd'hui</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">12</div>
                <div class="stat-label">Nouveaux messages</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">3</div>
                <div class="stat-label">Devoirs à rendre</div>
            </div>
        </div>
    </div>
</main>

<style>
    /* Styles pour la section principale */
    main {
        padding: 2rem 0;
        color: white;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* Section de bienvenue */
    .welcome-section {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 2.5rem;
        margin-bottom: 2.5rem;
        text-align: center;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .welcome-title {
        font-size: 2.2rem;
        margin-bottom: 1rem;
        font-weight: 700;
        color: white;
    }

    .welcome-subtitle {
        font-size: 1.3rem;
        margin-bottom: 1rem;
        opacity: 0.9;
    }

    .welcome-text {
        font-size: 1.1rem;
        opacity: 0.85;
        line-height: 1.6;
    }

    /* Cartes d'action */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .action-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(10px);
        border-radius: 14px;
        padding: 2rem;
        text-align: center;
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
        color: white;
        text-decoration: none;
        min-height: 220px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .action-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.12);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .action-icon {
        font-size: 2.8rem;
        margin-bottom: 1.2rem;
    }

    .action-title {
        font-size: 1.4rem;
        margin-bottom: 0.8rem;
        font-weight: 600;
    }

    .action-description {
        opacity: 0.85;
        line-height: 1.6;
        font-size: 0.95rem;
    }

    /* Statistiques */
    .stats-section {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1.2rem;
        margin-bottom: 3rem;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .stat-number {
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 0.4rem;
    }

    .stat-label {
        opacity: 0.85;
        font-size: 0.9rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .welcome-section {
            padding: 1.8rem;
        }
        
        .welcome-title {
            font-size: 1.8rem;
        }
        
        .quick-actions {
            grid-template-columns: 1fr;
        }
        
        .stats-section {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .stats-section {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php
// Inclusion du footer
require_once 'footer.php';
?>