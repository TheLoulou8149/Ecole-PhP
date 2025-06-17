<?php
session_start();
require_once 'config.php';

// Récupérer les données du formulaire
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$error = '';

// Vérification dans la table profs
$stmt_prof = $pdo->prepare("SELECT * FROM profs WHERE email = ?");
$stmt_prof->execute([$email]);
$prof = $stmt_prof->fetch();

if ($prof) {
    if (isset($prof['password']) && password_verify($password, $prof['password'])) {
        $_SESSION['user_id'] = $prof['id'];
        $_SESSION['user_type'] = 'prof';
        $_SESSION['user_name'] = $prof['nom'];
        header('Location: dashboard_prof.php');
        exit();
    } else {
        $error = "Mot de passe incorrect pour le professeur.";
    }
} else {
    $error = "Email non trouvé.";
}

// Affichage de l’erreur si besoin
if ($error) {
    echo "<p style='color:red;'>$error</p>";
}
?>
