<?php
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
            <p>Vous êtes maintenant connecté à votre espace EduConnect.</p>
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

        <!-- Statistiques (optionnel) -->
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

<?php
// Inclusion du footer
require_once 'footer.php';
?>