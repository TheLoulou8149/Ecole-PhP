<?php
session_start();
require_once 'config.php'; // Inclut la connexion PDO

// Récupération des données du formulaire
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$error = '';

// Requête pour vérifier dans la table **profs**
$stmt_prof = $pdo->prepare("SELECT * FROM profs WHERE email = ?");
$stmt_prof->execute([$email]);
$prof = $stmt_prof->fetch();

// Requête pour vérifier dans la table etudiants
$stmt_etudiant = $pdo->prepare("SELECT * FROM etudiants WHERE email = ?");
$stmt_etudiant->execute([$email]);
$etudiant = $stmt_etudiant->fetch();

if ($prof) {
    if (password_verify($password, $prof['password'])) {
        // Connexion réussie pour un prof
        $_SESSION['user_id'] = $prof['id'];
        $_SESSION['user_type'] = 'prof';
        $_SESSION['user_name'] = $prof['nom'];
        header('Location: dashboard_prof.php');
        exit();
    } else {
        $error = "Mot de passe incorrect pour le professeur.";
    }
} elseif ($etudiant) {
    if (password_verify($password, $etudiant['password'])) {
        // Connexion réussie pour un étudiant
        $_SESSION['user_id'] = $etudiant['id'];
        $_SESSION['user_type'] = 'etudiant';
        $_SESSION['user_name'] = $etudiant['nom'];
        header('Location: dashboard_etudiant.php');
        exit();
    } else {
        $error = "Mot de passe incorrect pour l'étudiant.";
    }
} else {
    $error = "Email non trouvé.";
}

// Affichage d’un message d’erreur s’il y en a un
if ($error) {
    echo "<p style='color: red;'>$error</p>";
}
?>
