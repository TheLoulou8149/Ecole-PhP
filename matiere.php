<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'header.php';


// Vérifier que l'utilisateur est bien connecté et est un étudiant
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'etudiant') {
    echo "Accès non autorisé.";
    exit;
}

$id_etudiant = $_SESSION['user_id'];

try {
    $pdo = getDBConnection();

    // Récupérer toutes les matières
    $stmt = $pdo->query("SELECT * FROM matieres");
    $matieres = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h1>Liste des matières</h1>";
    echo "<ul>";

    foreach ($matieres as $matiere) {
        $id_matiere = $matiere['id_matiere'];
        $intitule = htmlspecialchars($matiere['intitule']);

        // Vérifier si l'étudiant a au moins un cours lié à cette matière
        $sql = "
            SELECT 1
            FROM cours c
            JOIN cours_etudiants ce ON c.id_cours = ce.id_cours
            WHERE c.id_matiere = :id_matiere AND ce.id_etudiant = :id_etudiant
            LIMIT 1
        ";
        $checkStmt = $pdo->prepare($sql);
        $checkStmt->execute([
            'id_matiere' => $id_matiere,
            'id_etudiant' => $id_etudiant
        ]);

        $a_cours = $checkStmt->fetch() ? true : false;

        echo "<li>";
        echo "$intitule - ";
        echo $a_cours ? "<strong>Vous avez des cours</strong>" : "Aucun cours pour vous";
        echo "</li>";
    }

    echo "</ul>";

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

require_once 'footer.php';
?>
