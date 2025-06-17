<?php
// Inclure le header qui contient déjà config.php et session_start()
require_once 'header.php';

// Récupérer ou définir les informations utilisateur
$user_id = 1; // ID de l'utilisateur connecté
$user_type = 'etudiant'; // ou 'prof'

try {
    // Récupérer les infos utilisateur depuis la BDD
    if ($user_type == 'prof') {
        $stmt = $pdo->prepare("SELECT nom FROM profs WHERE id = ?");
        $stmt->execute([$user_id]);
        $user_data = $stmt->fetch();
        $user_name = $user_data['nom'] ?? 'Professeur';
    } else {
        $stmt = $pdo->prepare("SELECT nom, prenom FROM etudiants WHERE id = ?");
        $stmt->execute([$user_id]);
        $user_data = $stmt->fetch();
        $user_name = ($user_data['prenom'] ?? '') . ' ' . ($user_data['nom'] ?? 'Étudiant');
    }

    // Calculer les statistiques
    $stats = [];

    if ($user_type == 'prof') {
        // Statistiques pour professeurs
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cours WHERE id_prof = ?");
        $stmt->execute([$user_id]);
        $stats['cours_crees'] = $stmt->fetch()['count'];
        
        // ... autres requêtes pour les stats ...
        
    } else {
        // Statistiques pour étudiants
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cours_etudiants WHERE id_etudiant = ?");
        $stmt->execute([$user_id]);
        $stats['cours_suivis'] = $stmt->fetch()['count'];
        
        // ... autres requêtes pour les stats ...
    }

} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}
?>

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
                        <?php echo $user_type == 'prof' ? $stats['cours_crees'] : $stats['cours_suivis']; ?>
                    </div>
                    <div class="stat-label">
                        <?php echo $user_type == 'prof' ? 'Cours créés' : 'Cours suivis'; ?>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number">
                        <?php echo $user_type == 'prof' ? $stats['etudiants_inscrits'] : $stats['matieres_suivies']; ?>
                    </div>
                    <div class="stat-label">
                        <?php echo $user_type == 'prof' ? 'Étudiants inscrits' : 'Matières suivies'; ?>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number">
                        <?php echo $user_type == 'prof' ? $stats['matieres_enseignees'] : $stats['progression'] . '%'; ?>
                    </div>
                    <div class="stat-label">
                        <?php echo $user_type == 'prof' ? 'Matières enseignées' : 'Progression moyenne'; ?>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number">
                        <?php echo $user_type == 'prof' ? $stats['nouvelles_inscriptions'] : $stats['diplomes_obtenus']; ?>
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

<?php
// Inclure le footer
require_once 'footer.php';
?>