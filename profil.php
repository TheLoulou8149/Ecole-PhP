<?php
// Démarrer la session
session_start();

// Vérifier si le fichier config.php existe
if (!file_exists('config.php')) {
    die("Erreur : Le fichier config.php est manquant.");
}

require_once 'config.php'; // Inclure le fichier de configuration

// Vérifier si la fonction getDBConnection() existe et récupérer la connexion PDO
if (!function_exists('getDBConnection')) {
    die("Erreur : La fonction getDBConnection() est absente du fichier config.php.");
}

$pdo = getDBConnection();

// Vérifier si la connexion PDO est valide
if (!$pdo instanceof PDO) {
    die("Erreur : La connexion à la base de données a échoué.");
}

// Vérifier si l'utilisateur est connecté ET que c'est un étudiant
if (empty($_SESSION['user_id']) || $_SESSION['user_type'] !== 'etudiant') {
    header('Location: login.php');
    exit();
}

// Récupérer l'ID de l'étudiant connecté
$id_etudiant = (int) $_SESSION['user_id'];

// Traitement de l'upload de photo
$upload_message = '';
if (isset($_POST['upload_photo']) && isset($_FILES['profile_photo'])) {
    $upload_dir = 'uploads/profiles/';
    
    // Créer le dossier s'il n'existe pas
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $file = $_FILES['profile_photo'];
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $max_size = 2 * 1024 * 1024; // 2MB
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
            $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = 'profile_' . $id_etudiant . '_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                try {
                    // Supprimer l'ancienne photo si elle existe
                    $stmt = $pdo->prepare("SELECT photo_profil FROM etudiants WHERE id_etudiant = ?");
                    $stmt->execute([$id_etudiant]);
                    $old_photo = $stmt->fetchColumn();
                    
                    if ($old_photo && file_exists($old_photo)) {
                        unlink($old_photo);
                    }
                    
                    // Mettre à jour la base de données
                    $stmt = $pdo->prepare("UPDATE etudiants SET photo_profil = ? WHERE id_etudiant = ?");
                    $stmt->execute([$upload_path, $id_etudiant]);
                    
                    $upload_message = '<div class="success-message">Photo de profil mise à jour avec succès !</div>';
                } catch (PDOException $e) {
                    $upload_message = '<div class="error-message">Erreur lors de la mise à jour : ' . $e->getMessage() . '</div>';
                }
            } else {
                $upload_message = '<div class="error-message">Erreur lors de l\'upload du fichier.</div>';
            }
        } else {
            $upload_message = '<div class="error-message">Fichier non autorisé. Seules les images JPEG, PNG et GIF de moins de 2MB sont acceptées.</div>';
        }
    } else {
        $upload_message = '<div class="error-message">Erreur lors de l\'upload : ' . $file['error'] . '</div>';
    }
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
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: 700;
            margin: 0 auto 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .profile-avatar:hover {
            transform: scale(1.05);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .avatar-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            color: white;
            font-size: 14px;
            font-weight: 500;
        }

        .profile-avatar:hover .avatar-overlay {
            opacity: 1;
        }

        .photo-upload-form {
            margin: 20px 0;
        }

        .file-input-wrapper {
            position: relative;
            display: inline-block;
            margin-bottom: 10px;
        }

        .file-input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-input-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 10px 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .file-input-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .upload-btn {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            margin-left: 10px;
            transition: all 0.3s ease;
        }

        .upload-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
        }

        .success-message {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }

        .error-message {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
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

        .logout-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .file-name {
            color: rgba(255, 255, 255, 0.8);
            font-size: 12px;
            margin-top: 5px;
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

            .logout-btn {
                position: static;
                display: block;
                width: 100%;
                margin-bottom: 20px;
                text-align: center;
            }

            .photo-upload-form {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <a href="logout.php" class="logout-btn">Déconnexion</a>
    
    <div class="container">
        <?php echo $upload_message; ?>
        
        <!-- Section de bienvenue -->
        <div class="welcome-section">
            <div class="profile-avatar" onclick="document.getElementById('profile_photo').click()">
                <?php if (!empty($etudiant['photo_profil']) && file_exists($etudiant['photo_profil'])): ?>
                    <img src="<?php echo htmlspecialchars($etudiant['photo_profil']); ?>" alt="Photo de profil">
                    <div class="avatar-overlay">Changer la photo</div>
                <?php else: ?>
                    <?php echo strtoupper(substr($etudiant['prenom'], 0, 1) . substr($etudiant['nom'], 0, 1)); ?>
                    <div class="avatar-overlay">Ajouter une photo</div>
                <?php endif; ?>
            </div>
            
            <form method="POST" enctype="multipart/form-data" class="photo-upload-form">
                <div class="file-input-wrapper">
                    <input type="file" id="profile_photo" name="profile_photo" class="file-input" 
                           accept="image/jpeg,image/jpg,image/png,image/gif" onchange="showFileName(this)">
                    <label for="profile_photo" class="file-input-btn">Choisir une photo</label>
                    <div id="file-name" class="file-name"></div>
                </div>
                <button type="submit" name="upload_photo" class="upload-btn">Mettre à jour</button>
            </form>
            
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
                        if (!empty($etudiant['date_naissance'])) {
                            $date = new DateTime($etudiant['date_naissance']);
                            echo $date->format('d/m/Y');
                        } else {
                            echo 'Non renseignée';
                        }
                        ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Âge</span>
                    <span class="info-value">
                        <?php 
                        if (!empty($etudiant['date_naissance'])) {
                            $naissance = new DateTime($etudiant['date_naissance']);
                            $aujourd_hui = new DateTime();
                            $age = $aujourd_hui->diff($naissance)->y;
                            echo $age . ' ans';
                        } else {
                            echo 'Non calculé';
                        }
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

    <script>
        function showFileName(input) {
            const fileNameDiv = document.getElementById('file-name');
            if (input.files && input.files[0]) {
                fileNameDiv.textContent = 'Fichier sélectionné: ' + input.files[0].name;
            } else {
                fileNameDiv.textContent = '';
            }
        }
    </script>
</body>
</html>