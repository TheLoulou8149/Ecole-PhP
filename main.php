<?php
session_start();
require_once 'config.php';

// --- Simulation de l'utilisateur connecté ---
// if (!isset($_SESSION['user_id'])) {
//     echo "Utilisateur non connecté.";
//     exit;
// }

// Temporairement, on force l'ID à 1 (Luc Bernard)
$_SESSION['user_id'] = 1;

// Connexion à la base
$pdo = getDBConnection();

// Récupération des infos de l'étudiant
$id_etudiant = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE id_etudiant = :id");
    $stmt->execute(['id' => $id_etudiant]);
    $etudiant = $stmt->fetch();

    if ($etudiant) {
        echo "<h1>Bienvenue " . htmlspecialchars($etudiant['prenom']) . " " . htmlspecialchars($etudiant['nom']) . "</h1>";
        echo "<p>Email : " . htmlspecialchars($etudiant['email']) . "</p>";
        echo "<p>Date de naissance : " . htmlspecialchars($etudiant['date_naissance']) . "</p>";
    } else {
        echo "Aucun étudiant trouvé avec cet ID.";
    }
} catch (PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage();
}
?>
