<?php
session_start();
require_once 'db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_SESSION['utilisateur_id'];

// Récupération des données de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    echo "Utilisateur introuvable.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
</head>
<body>
    <h1>Profil de <?php echo htmlspecialchars($user['nom']); ?></h1>
    <p>Email : <?php echo htmlspecialchars($user['email']); ?></p>
    <p>Type : <?php echo htmlspecialchars($user['type']); ?></p>
    <p>Autres infos : <?php echo nl2br(htmlspecialchars($user['autres_infos'])); ?></p>

    <?php if ($user['type'] == 'etudiant'): ?>
        <h2>Contenu réservé aux étudiants</h2>
        <!-- Ajoutez ici le contenu spécifique aux étudiants -->
    <?php elseif ($user['type'] == 'professeur'): ?>
        <h2>Contenu réservé aux professeurs</h2>
        <!-- Ajoutez ici le contenu spécifique aux professeurs -->
    <?php endif; ?>
</body>
</html>
