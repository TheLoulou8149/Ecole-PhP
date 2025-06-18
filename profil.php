<?php
// Inclure le fichier de configuration
require_once 'config.php';

// Vérifier si l'utilisateur est connecté
//if (!isset($_SESSION['id_etudiant'])) {
//    header('Location: login.php');
//    exit();
//}

$id_etudiant = $_SESSION['id_etudiant'];

try {
    // Récupérer les informations de l'étudiant
    $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE id_etudiant = ?");
    $stmt->execute([$id_etudiant]);
    $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$etudiant) {
        die("Étudiant non trouvé");
    }

    // Récupérer les cours de l'étudiant avec les détails
    $stmt = $pdo->prepare("
        SELECT c.*, m.intitule as matiere_nom, p.nom as prof_nom
        FROM cours c
        INNER JOIN cours_etudiants ce ON c.id_cours = ce.id_cours
        INNER JOIN matieres m ON c.id_matiere = m.id_matiere
        INNER JOIN profs p ON c.id_prof = p.id_prof
        WHERE ce.id_etudiant = ?
        ORDER BY c.date DESC
    ");
    $stmt->execute([$id_etudiant]);
    $cours = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - <?php echo htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: bold;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.2em;
            opacity: 0.9;
        }

        .content {
            padding: 40px;
        }

        .info-section {
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 1.8em;
            color: #4f46e5;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #e5e7eb;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-card {
            background: #f8fafc;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #4f46e5;
        }

        .info-label {
            font-weight: bold;
            color: #374151;
            margin-bottom: 5px;
        }

        .info-value {
            color: #6b7280;
            font-size: 1.1em;
        }

        .cours-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
        }

        .cours-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .cours-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .cours-title {
            font-size: 1.3em;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
        }

        .cours-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .cours-info span {
            color: #6b7280;
        }

        .platform-badge {
            background: #4f46e5;
            color: white;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
        }

        .no-cours {
            text-align: center;
            color: #6b7280;
            font-style: italic;
            padding: 40px;
        }

        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.2s;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 10px;
            }
            
            .header {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 2em;
            }
            
            .content {
                padding: 20px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .cours-list {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="logout.php" class="logout-btn">Déconnexion</a>
            <div class="profile-pic">
                <?php echo strtoupper(substr($etudiant['prenom'], 0, 1) . substr($etudiant['nom'], 0, 1)); ?>
            </div>
            <h1><?php echo htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']); ?></h1>
            <p>Étudiant - ID: <?php echo htmlspecialchars($etudiant['id_etudiant']); ?></p>
        </div>

        <div class="content">
            <div class="info-section">
                <h2 class="section-title">Informations Personnelles</h2>
                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-label">Nom complet</div>
                        <div class="info-value"><?php echo htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']); ?></div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?php echo htmlspecialchars($etudiant['email']); ?></div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">Date de naissance</div>
                        <div class="info-value">
                            <?php 
                            $date = new DateTime($etudiant['date_naissance']);
                            echo $date->format('d/m/Y');
                            ?>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">Âge</div>
                        <div class="info-value">
                            <?php 
                            $naissance = new DateTime($etudiant['date_naissance']);
                            $aujourd_hui = new DateTime();
                            $age = $aujourd_hui->diff($naissance)->y;
                            echo $age . ' ans';
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-section">
                <h2 class="section-title">Mes Cours (<?php echo count($cours); ?>)</h2>
                <?php if (empty($cours)): ?>
                    <div class="no-cours">
                        Aucun cours inscrit pour le moment.
                    </div>
                <?php else: ?>
                    <div class="cours-list">
                        <?php foreach ($cours as $c): ?>
                            <div class="cours-card">
                                <div class="cours-title"><?php echo htmlspecialchars($c['intitule']); ?></div>
                                <div class="cours-info">
                                    <span><strong>Matière:</strong> <?php echo htmlspecialchars($c['matiere_nom']); ?></span>
                                    <span class="platform-badge"><?php echo htmlspecialchars($c['plateforme']); ?></span>
                                </div>
                                <div class="cours-info">
                                    <span><strong>Professeur:</strong> <?php echo htmlspecialchars($c['prof_nom']); ?></span>
                                    <span><strong>Date:</strong> <?php 
                                        $date = new DateTime($c['date']);
                                        echo $date->format('d/m/Y');
                                    ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>