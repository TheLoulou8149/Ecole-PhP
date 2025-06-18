<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'header.php';

// Vérifier que l'utilisateur est bien connecté et est un étudiant
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'etudiant') {
    echo "<div class='container'><p>Accès non autorisé.</p></div>";
    require_once 'footer.php';
    exit;
}

$id_etudiant = $_SESSION['user_id'];

try {
    $pdo = getDBConnection();

    // Récupérer toutes les matières
    $stmt = $pdo->query("SELECT * FROM matieres");
    $matieres = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<div class='matiere-page'>";
    echo "<h1 class='page-title'>Liste des matières</h1>";
    echo "<div class='matiere-grid'>";

    foreach ($matieres as $matiere) {
        $id_matiere = $matiere['id_matiere'];
        $intitule = htmlspecialchars($matiere['intitule']);

        // Récupérer les cours liés à cette matière pour l'étudiant
        $sql = "
            SELECT * FROM cours c
            JOIN cours_etudiants ce ON c.id_cours = ce.id_cours
            WHERE c.id_matiere = :id_matiere AND ce.id_etudiant = :id_etudiant
        ";
        $checkStmt = $pdo->prepare($sql);
        $checkStmt->execute([
            'id_matiere' => $id_matiere,
            'id_etudiant' => $id_etudiant
        ]);

        $coursDetails = $checkStmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<div class='matiere-item'>";
        echo "<div class='matiere-header'>";
        echo "<span class='matiere-nom'>$intitule</span>";

        if (count($coursDetails) > 0) {
            echo "<span class='cours-disponible'>✔ Vous avez des cours</span>";
        } else {
            echo "<span class='cours-indisponible'>✖ Aucun cours</span>";
        }

        echo "</div>";

        if (count($coursDetails) > 0) {
            foreach ($coursDetails as $cours) {
                $titre = htmlspecialchars($cours['intitule']);
                $date = htmlspecialchars($cours['date']);
                $plateforme = htmlspecialchars($cours['plateforme']);

                echo "<div class='cours-details'>";
                echo "<p><strong>$titre</strong></p>";
                echo "<p>🗓️ Date : $date</p>";
                echo "<p>💻 Plateforme : $plateforme</p>";
                echo "</div>";
            }
        }

        echo "</div>"; // fin .matiere-item
    }

    echo "</div>"; // fin .matiere-grid
    echo "</div>"; // fin .matiere-page

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
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
    }

    @media (max-width: 1024px) {
        .matiere-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 700px) {
        .matiere-grid {
            grid-template-columns: 1fr;
        }
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
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .matiere-nom {
        font-size: 1.2rem;
        font-weight: 600;
    }

    .cours-disponible {
        color: #a0ffbf;
        font-weight: 500;
    }

    .cours-indisponible {
        color: #ffb3b3;
        font-weight: 500;
    }

    .cours-details {
        margin-top: 0.8rem;
        padding: 0.8rem;
        background: rgba(255, 255, 255, 0.05);
        border-left: 3px solid #a0ffbf;
        border-radius: 6px;
        font-size: 0.9rem;
    }

    .cours-details p {
        margin: 0.3rem 0;
    }
</style>
