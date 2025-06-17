<?php
// Partie de votre code login.php corrigée

// Récupération des données du formulaire
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Requête pour vérifier dans la table prof
$stmt_prof = $pdo->prepare("SELECT * FROM prof WHERE email = ?");
$stmt_prof->execute([$email]);
$prof = $stmt_prof->fetch();

// Requête pour vérifier dans la table etudiants
$stmt_etudiant = $pdo->prepare("SELECT * FROM etudiants WHERE email = ?");
$stmt_etudiant->execute([$email]);
$etudiant = $stmt_etudiant->fetch();

if ($prof) {
    // Vérification du mot de passe pour prof
    if (password_verify($password, $prof['password'])) {
        // Connexion réussie pour prof
        $_SESSION['user_id'] = $prof['id'];
        $_SESSION['user_type'] = 'prof';
        $_SESSION['user_name'] = $prof['nom'];
        header('Location: dashboard_prof.php');
        exit();
    } else {
        $error = "Mot de passe incorrect";
    }
} elseif ($etudiant) {
    // Vérification du mot de passe pour étudiant
    if (password_verify($password, $etudiant['password'])) {
        // Connexion réussie pour étudiant
        $_SESSION['user_id'] = $etudiant['id'];
        $_SESSION['user_type'] = 'etudiant';
        $_SESSION['user_name'] = $etudiant['nom'];
        header('Location: dashboard_etudiant.php');
        exit();
    } else {
        $error = "Mot de passe incorrect";
    }
} else {
    $error = "Email non trouvé";
}
?>