<?php
// Connexion à la base de données (à adapter selon tes identifiants)
$host = '10.96.16.82';
$dbname = 'ecole';
$user = 'colin';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Requête pour récupérer tous les profs
$stmt = $pdo->query("SELECT * FROM profs");
$profs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des professeurs</title>
</head>
<body>
    <h1>Liste des professeurs</h1>
    <table border="1">
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Matière</th>
        </tr>
        <?php foreach ($profs as $prof): ?>
            <tr>
                <td><?= htmlspecialchars($prof['nom']) ?></td>
                <td><?= htmlspecialchars($prof['prenom']) ?></td>
                <td><?= htmlspecialchars($prof['matiere']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
