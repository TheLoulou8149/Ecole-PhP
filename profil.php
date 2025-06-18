<?php
// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si le fichier config.php existe
if (!file_exists('config.php')) {
    die("Erreur : Le fichier config.php n'existe pas. Veuillez le créer avec la configuration de votre base de données.");
}

// Inclure le fichier de configuration
require_once 'config.php';

// Vérifier si $pdo est bien défini après l'inclusion
if (!isset($pdo) || $pdo === null) {
    // Si $pdo n'est pas défini, essayer de créer la connexion ici
    try {
        // Remplacez ces valeurs par vos paramètres de base de données
        $host = 'localhost';
        $dbname = 'ecole'; // Remplacez par le nom de votre base de données
        $username = 'root'; // Remplacez par votre nom d'utilisateur
        $password = ''; // Remplacez par votre mot de passe
        
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $pdo = new PDO($dsn, $username, $password, $options);
        
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage() . 
            "<br><br>Vérifiez votre fichier config.php ou les paramètres de connexion.");
    }
}

// Vérification finale de la connexion
if (!$pdo) {
    die("Erreur : Impossible d'établir la connexion à la base de données.");
}

// Debug : Afficher tous les étudiants pour trouver Jean Dupont
try {
    $debug_stmt = $pdo->prepare("SELECT id_etudiant, nom, prenom FROM etudiants ORDER BY id_etudiant");
    $debug_stmt->execute();
    $tous_etudiants = $debug_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div style='background: #1e40af; color: white; padding: 15px; margin-bottom: 20px; border-radius: 8px;'>";
    echo "<strong>🔍 LISTE DES ÉTUDIANTS DANS LA BASE :</strong><br>";
    if (empty($tous_etudiants)) {
        echo "Aucun étudiant trouvé dans la base de données.";
    } else {
        foreach ($tous_etudiants as $etud) {
            echo "ID: " . $etud['id_etudiant'] . " - " . htmlspecialchars($etud['prenom'] . ' ' . $etud['nom']) . "<br>";
        }
    }
    echo "</div>";
} catch(PDOException $e) {
    echo "<div style='background: red; color: white; padding: 10px;'>Erreur debug: " . $e->getMessage() . "</div>";
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_etudiant'])) {
    // CHANGEZ CET ID SELON CE QUI S'AFFICHE CI-DESSUS POUR JEAN DUPONT
    $id_etudiant = 1; // ← MODIFIEZ CE CHIFFRE avec l'ID de Jean Dupont
    
    echo "<div style='background: orange; color: white; padding: 15px; text-align: center; margin-bottom: 20px; border-radius: 8px;'>
            <strong>🧪 MODE TEST:</strong> Utilisation de l'étudiant ID=$id_etudiant pour les tests<br>
            <small>Changez cette valeur ligne 49 du code avec l'ID correct de Jean Dupont</small>
          </div>";
} else {
    $id_etudiant = $_SESSION['id_etudiant'];
}

try {
    // Récupérer les informations de l'étudiant
    $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE id_etudiant = ?");
    $stmt->execute([$id_etudiant]);
    $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$etudiant) {
        die("Étudiant non trouvé pour l'ID: $id_etudiant");
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
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 50%, #8b5cf6 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .welcome-section {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px;
            margin-bottom: 30px;
            text-align: center;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: 700;
            margin: 0 auto 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }

        .welcome-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 12px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .welcome-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            font-weight: 400;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 24px;
            margin-bottom: 30px;
        }

        .profile-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 32px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .profile-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            background: rgba(255, 255, 255, 0.2);
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 24px;
        }

        .card-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: white;
            margin-bottom: 16px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .info-label {
            font-weight: 500;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.95rem;
        }

        .info-value {
            font-weight: 600;
            color: white;
            font-size: 1rem;
        }

        .courses-section {
            margin-top: 30px;
        }

        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
        }

        .section-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
            font-size: 20px;
        }

        .section-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: white;
        }

        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
        }

        .course-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 24px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .course-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.2);
        }

        .course-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .course-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: white;
            line-height: 1.3;
            flex: 1;
            margin-right: 12px;
        }

        .platform-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .course-details {
            space-y: 8px;
        }

        .course-detail {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .course-detail-label {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
        }

        .course-detail-value {
            color: white;
            font-weight: 600;
        }

        .no-courses {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 60px 40px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .no-courses-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.6;
        }

        .no-courses-text {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.1rem;
            font-weight: 500;
        }

        .test-notice {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 16px 24px;
            border-radius: 12px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }

        .debug-info {
            background: #1e40af;
            color: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-family: monospace;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 10px;
            }
            
            .welcome-section {
                padding: 24px;
                margin-bottom: 20px;
            }
            
            .welcome-title {
                font-size: 2rem;
            }
            
            .profile-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            
            .profile-card {
                padding: 20px;
            }
            
            .courses-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Section de bienvenue -->
        <div class="welcome-section">
            <div class="profile-avatar">
                <?php echo strtoupper(substr($etudiant['prenom'], 0, 1) . substr($etudiant['nom'], 0, 1)); ?>
            </div>
            <h1 class="welcome-title">Bienvenue, <?php echo htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']); ?>!</h1>
            <p class="welcome-subtitle">Consultez vos informations personnelles et suivez vos cours</p>
        </div>

        <!-- Grille des informations profil -->
        <div class="profile-grid">
            <!-- Carte Informations personnelles -->
            <div class="profile-card">
                <div class="card-icon">👤</div>
                <h3 class="card-title">Informations personnelles</h3>
                <div class="info-item">
                    <span class="info-label">Nom complet</span>
                    <span class="info-value"><?php echo htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email</span>
                    <span class="info-value"><?php echo htmlspecialchars($etudiant['email']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Date de naissance</span>
                    <span class="info-value">
                        <?php 
                        $date = new DateTime($etudiant['date_naissance']);
                        echo $date->format('d/m/Y');
                        ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Âge</span>
                    <span class="info-value">
                        <?php 
                        $naissance = new DateTime($etudiant['date_naissance']);
                        $aujourd_hui = new DateTime();
                        $age = $aujourd_hui->diff($naissance)->y;
                        echo $age . ' ans';
                        ?>
                    </span>
                </div>
            </div>

            <!-- Carte Statistiques -->
            <div class="profile-card">
                <div class="card-icon">📊</div>
                <h3 class="card-title">Mes statistiques</h3>
                <div class="info-item">
                    <span class="info-label">Nombre de cours</span>
                    <span class="info-value"><?php echo count($cours); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">ID Étudiant</span>
                    <span class="info-value">#<?php echo htmlspecialchars($etudiant['id_etudiant']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Statut</span>
                    <span class="info-value">Actif</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Dernière connexion</span>
                    <span class="info-value">Aujourd'hui</span>
                </div>
            </div>
        </div>

        <!-- Section des cours -->
        <div class="courses-section">
            <div class="section-header">
                <div class="section-icon">📚</div>
                <h2 class="section-title">Mes Cours (<?php echo count($cours); ?>)</h2>
            </div>
            
            <?php if (empty($cours)): ?>
                <div class="no-courses">
                    <div class="no-courses-icon">📭</div>
                    <p class="no-courses-text">Aucun cours inscrit pour le moment.</p>
                </div>
            <?php else: ?>
                <div class="courses-grid">
                    <?php foreach ($cours as $c): ?>
                        <div class="course-card">
                            <div class="course-header">
                                <h4 class="course-title"><?php echo htmlspecialchars($c['intitule']); ?></h4>
                                <span class="platform-badge"><?php echo htmlspecialchars($c['plateforme']); ?></span>
                            </div>
                            <div class="course-details">
                                <div class="course-detail">
                                    <span class="course-detail-label">Matière</span>
                                    <span class="course-detail-value"><?php echo htmlspecialchars($c['matiere_nom']); ?></span>
                                </div>
                                <div class="course-detail">
                                    <span class="course-detail-label">Professeur</span>
                                    <span class="course-detail-value"><?php echo htmlspecialchars($c['prof_nom']); ?></span>
                                </div>
                                <div class="course-detail">
                                    <span class="course-detail-label">Date</span>
                                    <span class="course-detail-value">
                                        <?php 
                                        $date = new DateTime($c['date']);
                        echo $date->format('d/m/Y');
                        ?>
                    </span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
</div>
</div>
</body>
</html>