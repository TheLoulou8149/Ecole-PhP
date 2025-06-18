<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'header.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo "<div class='container'><p>Accès non autorisé.</p></div>";
    require_once 'footer.php';
    exit;
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

try {
    $pdo = getDBConnection();

    // Récupérer toutes les matières
    $stmt = $pdo->query("SELECT * FROM matieres");
    $matieres = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<div class='matiere-page'>";
    echo "<h1 class='page-title'>Liste des matières et cours disponibles</h1>";
    echo "<div class='matiere-grid'>";

    foreach ($matieres as $matiere) {
        $id_matiere = $matiere['id_matiere'];
        $intitule = htmlspecialchars($matiere['intitule']);

        // Récupérer tous les cours de cette matière
        $sql = "
            SELECT c.*, p.nom AS nom_prof
            FROM cours c
            JOIN profs p ON c.id_prof = p.id_prof
            WHERE c.id_matiere = :id_matiere
        ";
        $coursStmt = $pdo->prepare($sql);
        $coursStmt->execute(['id_matiere' => $id_matiere]);
        $coursList = $coursStmt->fetchAll(PDO::FETCH_ASSOC);

        // Pour les étudiants : récupérer les cours auxquels il est inscrit
        $coursInscrits = [];
        if ($user_type === 'etudiant') {
            $sqlInscrits = "SELECT id_cours FROM cours_etudiants WHERE id_etudiant = :id";
            $inscritStmt = $pdo->prepare($sqlInscrits);
            $inscritStmt->execute(['id' => $user_id]);
            $coursInscrits = array_column($inscritStmt->fetchAll(PDO::FETCH_ASSOC), 'id_cours');
        }

        echo "<div class='matiere-item'>";
        echo "<div class='matiere-header'>";
        echo "<span class='matiere-nom'>$intitule</span>";
        echo "</div>";

        if (count($coursList) > 0) {
            foreach ($coursList as $cours) {
                $titre = htmlspecialchars($cours['intitule']);
                $date = htmlspecialchars($cours['date']);
                $plateforme = htmlspecialchars($cours['plateforme']);
                $nom_prof = htmlspecialchars($cours['nom_prof']);

                $isInscrit = ($user_type === 'etudiant' && in_array($cours['id_cours'], $coursInscrits));
                $isEnseignant = ($user_type === 'professeur' && $cours['id_prof'] == $user_id);

                $classeCours = "cours-details";
                if ($isInscrit) $classeCours .= " inscrit";
                if ($isEnseignant) $classeCours .= " enseignant";

                echo "<div class='$classeCours'>";
                echo "<p><strong>$titre</strong></p>";
                echo "<p>🗓️ $date</p>";
                echo "<p>💻 $plateforme</p>";
                echo "<p>👨‍🏫 Professeur : $nom_prof</p>";
                if ($isInscrit) {
                    echo "<p class='inscrit-badge'>✔ Vous êtes inscrit</p>";
                }
                if ($isEnseignant) {
                    echo "<p class='enseignant-badge'>✔ Vous enseignez ce cours</p>";
                }
                echo "</div>";
            }
        } else {
            echo "<p class='cours-indisponible'>Aucun cours disponible pour cette matière.</p>";
        }

        echo "</div>"; // fin .matiere-item
    }

    echo "</div>"; // .matiere-grid
    echo "</div>"; // .matiere-page

} catch (PDOException $e) {
    echo "<div class='container'><p>Erreur : " . $e->getMessage() . "</p></div>";
}

require_once 'footer.php';
?>

<style>
    .matiere-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .page-title {
        font-size: 2rem;
        margin-bottom: 2rem;
        font-weight: 600;
        text-align: center;
        color: white;
    }

    .matiere-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .matiere-item {
        background: rgba(255, 255, 255, 0.07);
        border-radius: 10px;
        padding: 1rem;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        transition: transform 0.2s ease;
        color: white;
    }

    .matiere-item:hover {
        transform: translateY(-4px);
    }

    .matiere-header {
        margin-bottom: 1rem;
    }

    .matiere-nom {
        font-size: 1.2rem;
        font-weight: 600;
    }

    .cours-details {
        margin-top: 0.8rem;
        padding: 0.8rem;
        background: rgba(255, 255, 255, 0.05);
        border-left: 3px solid #ccc;
        border-radius: 6px;
        font-size: 0.9rem;
    }

    .cours-details.inscrit {
        border-left-color: #a0ffbf;
        background: rgba(160, 255, 191, 0.05);
    }

    .cours-details.enseignant {
        border-left-color: #ffe680;
        background: rgba(255, 230, 128, 0.07);
    }

    .cours-details p {
        margin: 0.3rem 0;
    }

    .inscrit-badge {
        color: #a0ffbf;
        font-weight: 500;
    }

    .enseignant-badge {
        color: #ffe680;
        font-weight: 500;
    }

    .cours-indisponible {
        color: #ffb3b3;
        font-style: italic;
    }
</style>
