<?php
// Démarrer la session (déjà géré dans config.php)
require_once 'config.php';

// Récupérer l'utilisateur connecté (ici on force l'ID 1 pour le test)
$user_id = 1;
$user_type = 'etudiant'; // ou 'prof' selon votre besoin

try {
    // Récupérer les infos selon le type d'utilisateur
    if ($user_type == 'prof') {
        $stmt = $pdo->prepare("SELECT nom FROM profs WHERE id = ?");
        $stmt->execute([$user_id]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_name = $user_data['nom'] ?? 'Professeur';
    } else {
        $stmt = $pdo->prepare("SELECT nom, prenom FROM etudiants WHERE id = ?");
        $stmt->execute([$user_id]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_name = ($user_data['prenom'] ?? '') . ' ' . ($user_data['nom'] ?? 'Étudiant');
    }

    // Calculer les statistiques
    $stats = [];

    if ($user_type == 'prof') {
        // Statistiques pour les professeurs
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cours WHERE id_prof = ?");
        $stmt->execute([$user_id]);
        $stats['cours_crees'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT ce.id_etudiant) as count 
                              FROM cours_etudiants ce 
                              JOIN cours c ON ce.id_cours = c.id_cours 
                              WHERE c.id_prof = ?");
        $stmt->execute([$user_id]);
        $stats['etudiants_inscrits'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT id_matiere) as count 
                              FROM cours 
                              WHERE id_prof = ?");
        $stmt->execute([$user_id]);
        $stats['matieres_enseignees'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        $stats['nouvelles_inscriptions'] = rand(5, 20); // Valeur simulée
        
    } else {
        // Statistiques pour les étudiants
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cours_etudiants WHERE id_etudiant = ?");
        $stmt->execute([$user_id]);
        $stats['cours_suivis'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT c.id_matiere) as count 
                              FROM cours_etudiants ce 
                              JOIN cours c ON ce.id_cours = c.id_cours 
                              WHERE ce.id_etudiant = ?");
        $stmt->execute([$user_id]);
        $stats['matieres_suivies'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM diplomes WHERE id_etudiant = ?");
        $stmt->execute([$user_id]);
        $stats['diplomes_obtenus'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        $stats['progression'] = rand(60, 95); // Valeur simulée
    }

} catch (PDOException $e) {
    // Gestion des erreurs SQL
    die("Erreur de base de données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- [Le reste de votre code HTML reste inchangé] -->
</head>
<body>
    <!-- [Votre structure HTML reste inchangée] -->
</body>
</html>