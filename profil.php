<?php
session_start();

// Redirection si l'utilisateur n'est ni étudiant ni prof
if (empty($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['etudiant', 'prof'])) {
    header('Location: login.php');
    exit();
}

require_once 'config.php';

$user_type = $_SESSION['user_type'];
$user_id = $_SESSION['user_id'];

if ($user_type === 'etudiant') {
    $stmt = $conn->prepare("SELECT nom, prenom, email, classe FROM etudiants WHERE id = ?");
} elseif ($user_type === 'prof') {
    $stmt = $conn->prepare("SELECT nom, prenom, email, matieres FROM profs WHERE id = ?");
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f8f9fa;
        }
        .profil-container {
            background-color: white;
            padding: 30px;
            max-width: 500px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        p {
            margin: 10px 0;
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="profil-container">
        <h1>Profil de <?php echo htmlspecialchars($user_data['prenom'] . ' ' . $user_data['nom']); ?></h1>
        <p><strong>Email :</strong> <?php echo htmlspecialchars($user_data['email']); ?></p>

        <?php if ($user_type === 'etudiant'): ?>
            <p><strong>Classe :</strong> <?php echo htmlspecialchars($user_data['classe']); ?></p>
        <?php elseif ($user_type === 'prof'): ?>
            <p><strong>Matières enseignées :</strong> <?php echo htmlspecialchars($user_data['matieres']); ?></p>
        <?php endif; ?>

        <a href="modifier_infos.php?type=<?php echo $user_type; ?>" class="btn">Modifier mes infos</a>
    </div>
</body>
</html>
