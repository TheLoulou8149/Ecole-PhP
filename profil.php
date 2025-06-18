<?php
session_start();

// Redirection si l'utilisateur n'est pas connecté comme étudiant ou prof
if (empty($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['etudiant', 'prof'])) {
    header('Location: login.php');
    exit();
}

require_once 'config.php'; // Connexion à la base de données

$user_type = $_SESSION['user_type'];
$user_id = $_SESSION['user_id']; // Vérifie que cet ID est bien défini dans la session

// Récupération des informations selon le type d'utilisateur
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
            background-color: #f0f2f5;
        }

        .profil-container {
            background-color: #fff;
            padding: 30px;
            max-width: 600px;
            margin: auto;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        p {
            font-size: 18px;
            margin: 12px 0;
            color: #444;
        }

        .label {
            font-weight: bold;
            color: #555;
        }

        .btn-container {
            text-align: center;
            margin-top: 30px;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="profil-container">
    <h1>Profil de <?php echo htmlspecialchars($user_data['prenom'] . ' ' . $user_data['nom']); ?></h1>

    <p><span class="label">Email :</span> <?php echo htmlspecialchars($user_data['email']); ?></p>

    <?php if ($user_type === 'etudiant'): ?>
        <p><span class="label">Classe :</span> <?php echo htmlspecialchars($user_data['classe']); ?></p>
    <?php elseif ($user_type === 'prof'): ?>
        <p><span class="label">Matières enseignées :</span> <?php echo htmlspecialchars($user_data['matieres']); ?></p>
    <?php endif; ?>

    <div class="btn-container">
        <a href="modifier_infos.php?type=<?php echo $user_type; ?>" class="btn">Modifier mes infos</a>
    </div>
</div>

</body>
</html>
