<?php
// Démarrer la session : doit être la toute première instruction
session_start();

// Vérifier si l'utilisateur est connecté AVANT d'inclure le header
if (empty($_SESSION['user_type']) || empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Vérifier les types d'utilisateur possibles (plus flexible)
$valid_user_types = ['etudiant', 'prof', 'professeur', 'teacher', 'student'];
if (!in_array(strtolower($_SESSION['user_type']), $valid_user_types)) {
    header('Location: login.php');
    exit();
}

// Inclure la config
require_once 'config.php';

// Vérifier la fonction de connexion
if (!function_exists('getDBConnection')) {
    die("Erreur : La fonction getDBConnection() est absente du fichier config.php.");
}

$pdo = getDBConnection();
if (!$pdo instanceof PDO) {
    die("Erreur : La connexion à la base de données a échoué.");
}

// Récupérer l'ID utilisateur et le type depuis la session
$user_id = (int) $_SESSION['user_id'];
$user_type = strtolower($_SESSION['user_type']);

// Normaliser le type d'utilisateur pour la logique
if (in_array($user_type, ['professeur', 'teacher'])) {
    $user_type = 'prof';
} elseif (in_array($user_type, ['student'])) {
    $user_type = 'etudiant';
}

// Variables pour l'affichage
$user_data = [];
$cours = [];
$success_message = '';
$error_message = '';

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    try {
        if ($user_type === 'etudiant') {
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $date_naissance = $_POST['date_naissance'] ?? '';
            $nouveau_password = trim($_POST['nouveau_password'] ?? '');
            
            // Validation basique
            if (empty($nom) || empty($prenom) || empty($email)) {
                throw new Exception("Tous les champs obligatoires doivent être remplis.");
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("L'adresse email n'est pas valide.");
            }
            
            // Vérifier si l'email existe déjà pour un autre utilisateur
            $stmt = $pdo->prepare("SELECT id_etudiant FROM etudiants WHERE email = ? AND id_etudiant != ?");
            $stmt->execute([$email, $user_id]);
            if ($stmt->fetch()) {
                throw new Exception("Cette adresse email est déjà utilisée par un autre compte.");
            }
            
            // Préparer la requête de mise à jour
            if (!empty($nouveau_password)) {
                $password_hash = password_hash($nouveau_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE etudiants SET nom = ?, prenom = ?, email = ?, date_naissance = ?, password = ? WHERE id_etudiant = ?");
                $stmt->execute([$nom, $prenom, $email, $date_naissance, $password_hash, $user_id]);
            } else {
                $stmt = $pdo->prepare("UPDATE etudiants SET nom = ?, prenom = ?, email = ?, date_naissance = ? WHERE id_etudiant = ?");
                $stmt->execute([$nom, $prenom, $email, $date_naissance, $user_id]);
            }
            
        } else { // Professeur
            $nom = trim($_POST['nom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $specialite = trim($_POST['specialite'] ?? '');
            $nouveau_password = trim($_POST['nouveau_password'] ?? '');
            
            if (empty($nom) || empty($email)) {
                throw new Exception("Le nom et l'email sont obligatoires.");
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("L'adresse email n'est pas valide.");
            }
            
            // Vérifier si l'email existe déjà pour un autre utilisateur
            $stmt = $pdo->prepare("SELECT id_prof FROM profs WHERE email = ? AND id_prof != ?");
            $stmt->execute([$email, $user_id]);
            if ($stmt->fetch()) {
                throw new Exception("Cette adresse email est déjà utilisée par un autre compte.");
            }
            
            // Préparer la requête de mise à jour
            if (!empty($nouveau_password)) {
                $password_hash = password_hash($nouveau_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE profs SET nom = ?, email = ?, specialite = ?, password = ? WHERE id_prof = ?");
                $stmt->execute([$nom, $email, $specialite, $password_hash, $user_id]);
            } else {
                $stmt = $pdo->prepare("UPDATE profs SET nom = ?, email = ?, specialite = ? WHERE id_prof = ?");
                $stmt->execute([$nom, $email, $specialite, $user_id]);
            }
        }
        
        $success_message = "Vos informations ont été mises à jour avec succès !";
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Récupération des données utilisateur et cours (code existant)
try {
    if ($user_type === 'etudiant') {
        $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE id_etudiant = ?");
        $stmt->execute([$user_id]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user_data) {
            die("Étudiant non trouvé pour l'ID: $user_id");
        }

        $stmt = $pdo->prepare("
            SELECT c.*, m.intitule AS matiere_nom, p.nom AS prof_nom
            FROM cours c
            INNER JOIN cours_etudiants ce ON c.id_cours = ce.id_cours
            INNER JOIN matieres m ON c.id_matiere = m.id_matiere
            INNER JOIN profs p ON c.id_prof = p.id_prof
            WHERE ce.id_etudiant = ?
            ORDER BY c.date DESC
        ");
        $stmt->execute([$user_id]);
        $cours = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } elseif ($user_type === 'prof') {
        $stmt = $pdo->prepare("SELECT * FROM profs WHERE id_prof = ?");
        $stmt->execute([$user_id]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user_data) {
            die("Professeur non trouvé pour l'ID: $user_id");
        }

        $stmt = $pdo->prepare("
            SELECT c.*, m.intitule AS matiere_nom, 
                   COUNT(ce.id_etudiant) as nombre_etudiants
            FROM cours c
            INNER JOIN matieres m ON c.id_matiere = m.id_matiere
            LEFT JOIN cours_etudiants ce ON c.id_cours = ce.id_cours
            WHERE c.id_prof = ?
            GROUP BY c.id_cours
            ORDER BY c.date DESC
        ");
        $stmt->execute([$user_id]);
        $cours = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

// Fonctions utilitaires (code existant)
function getFullName($user_data, $user_type) {
    if ($user_type === 'etudiant') {
        return $user_data['prenom'] . ' ' . $user_data['nom'];
    } else {
        return $user_data['nom'];
    }
}

function getInitials($user_data, $user_type) {
    if ($user_type === 'etudiant') {
        return strtoupper(substr($user_data['prenom'], 0, 1) . substr($user_data['nom'], 0, 1));
    } else {
        $nom_parts = explode(' ', $user_data['nom']);
        if (count($nom_parts) >= 2) {
            return strtoupper(substr($nom_parts[0], 0, 1) . substr($nom_parts[1], 0, 1));
        } else {
            return strtoupper(substr($user_data['nom'], 0, 2));
        }
    }
}

require_once 'header.php';
?>

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
            padding-top: 0;
        }

        .main-content {
            padding: 20px;
            margin-bottom: 40px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Messages de notification */
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
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

        .user-type-badge {
            display: inline-block;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 10px;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .edit-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .edit-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
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

        /* Modal de modification */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #374151;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6b7280;
            padding: 5px;
            border-radius: 50%;
            transition: background 0.2s ease;
        }

        .close-btn:hover {
            background: rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.2s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .form-input:focus {
            outline: none;
            border-color: #7c3aed;
            background: white;
        }

        .password-note {
            font-size: 0.8rem;
            color: #6b7280;
            margin-top: 4px;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.95rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(124, 58, 237, 0.3);
        }

        .btn-secondary {
            background: rgba(0, 0, 0, 0.1);
            color: #374151;
        }

        .btn-secondary:hover {
            background: rgba(0, 0, 0, 0.2);
        }

        /* Styles existants pour les cours */
        .courses-section {
            margin-top: 30px;
            margin-bottom: 60px;
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

        @media (max-width: 768px) {
            .main-content {
                padding: 10px;
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

            .modal {
                padding: 20px;
                width: 95%;
            }
        }
    </style>

    <!-- Contenu principal -->
    <div class="main-content">
        <div class="container">
            <!-- Messages d'alerte -->
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <span>✅</span>
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-error">
                    <span>❌</span>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <!-- Section de bienvenue -->
            <div class="welcome-section">
                <div class="profile-avatar">
                    <?php echo getInitials($user_data, $user_type); ?>
                </div>
                <h1 class="welcome-title">Bienvenue, <?php echo htmlspecialchars(getFullName($user_data, $user_type)); ?>!</h1>
                <p class="welcome-subtitle">
                    <?php if ($user_type === 'etudiant'): ?>
                        Consultez vos informations personnelles et suivez vos cours
                    <?php else: ?>
                        Gérez vos cours et consultez vos informations
                    <?php endif; ?>
                </p>
                <span class="user-type-badge">
                    <?php echo $user_type === 'etudiant' ? 'Étudiant' : 'Professeur'; ?>
                </span>
            </div>

            <!-- Grille des informations profil -->
            <div class="profile-grid">
                <!-- Carte Informations personnelles -->
                <div class="profile-card">
                    <div class="card-icon">👤</div>
                    <h3 class="card-title">
                        Informations personnelles
                        <button class="edit-btn" onclick="openEditModal()">
                            ✏️ Modifier
                        </button>
                    </h3>
                    
                    <?php if ($user_type === 'etudiant'): ?>
                        <div class="info-item">
                            <span class="info-label">Nom complet</span>
                            <span class="info-value"><?php echo htmlspecialchars($user_data['prenom'] . ' ' . $user_data['nom']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email</span>
                            <span class="info-value"><?php echo htmlspecialchars($user_data['email']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Date de naissance</span>
                            <span class="info-value">
                                <?php 
                                if (!empty($user_data['date_naissance'])) {
                                    $date = new DateTime($user_data['date_naissance']);
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
                                if (!empty($user_data['date_naissance'])) {
                                    $naissance = new DateTime($user_data['date_naissance']);
                                    $aujourd_hui = new DateTime();
                                    $age = $aujourd_hui->diff($naissance)->y;
                                    echo $age . ' ans';
                                } else {
                                    echo 'Non calculé';
                                }
                                ?>
                            </span>
                        </div>
                    <?php else: // Professeur ?>
                        <div class="info-item">
                            <span class="info-label">Nom</span>
                            <span class="info-value"><?php echo htmlspecialchars($user_data['nom']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email</span>
                            <span class="info-value"><?php echo htmlspecialchars($user_data['email']); ?></span>
                        </div>
                        <?php if (isset($user_data['specialite'])): ?>
                        <div class="info-item">
                            <span class="info-label">Spécialité</span>
                            <span class="info-value"><?php echo htmlspecialchars($user_data['specialite']); ?></span>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
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
                        <span class="info-label">ID <?php echo $user_type === 'etudiant' ? 'Étudiant' : 'Professeur'; ?></span>
                        <span class="info-value">#<?php echo htmlspecialchars($user_id); ?></span>
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
                    <h2 class="section-title">
                        <?php echo $user_type === 'etudiant' ? 'Mes Cours' : 'Mes Cours Enseignés'; ?> 
                        (<?php echo count($cours); ?>)
                    </h2>
                </div>
                
                <?php if (empty($cours)): ?>
                    <div class="no-courses">
                        <div class="no-courses-icon">📭</div>
                        <p class="no-courses-text">
                            <?php echo $user_type === 'etudiant' ? 'Aucun cours inscrit pour le moment.' : 'Aucun cours assigné pour le moment.'; ?>
                        </p>
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
                                    
                                    <?php if ($user_type === 'etudiant'): ?>
                                        <div class="course-detail">
                                            <span class="course-detail-label">Professeur</span>
                                            <span class="course-detail-value"><?php echo htmlspecialchars($c['prof_nom']); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <div class="course-detail">
                                            <span class="course-detail-label">Étudiants inscrits</span>
                                            <span class="course-detail-value"><?php echo $c['nombre_etudiants']; ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
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
    </div>

    <!-- Modal de modification -->
    <div class="modal-overlay" id="editModal">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Modifier mes informations</h2>
                <button class="close-btn" onclick="closeEditModal()">&times;</button>
            </div>
            
            <form method="POST" action="">
                <input type="hidden" name="update_profile" value="1">
                
                <?php if ($user_type === 'etudiant'): ?>
                    <div class="form-group">
                        <label class="form-label" for="prenom">Prénom *</label>
                        <input type="text" id="prenom" name="prenom" class="form-input" 
                               value="<?php echo htmlspecialchars($user_data['prenom']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="nom">Nom *</label>
                        <input type="text" id="nom" name="nom" class="form-input" 
                               value="<?php echo htmlspecialchars($user_data['nom']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="email">Email *</label>
                        <input type="email" id="email" name="email" class="form-input" 
                               value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="date_naissance">Date de naissance</label>
                        <input type="date" id="date_naissance" name="date_naissance" class="form-input" 
                               value="<?php echo htmlspecialchars($user_data['date_naissance'] ?? ''); ?>">
                    </div>
                    
                <?php else: // Professeur ?>
                    <div class="form-group">
                        <label class="form-label" for="nom">Nom *</label>
                        <input type="text" id="nom" name="nom" class="form-input" 
                               value="<?php echo htmlspecialchars($user_data['nom']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="email">Email *</label>
                        <input type="email" id="email" name="email" class="form-input" 
                               value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="specialite">Spécialité</label>
                        <input type="text" id="specialite" name="specialite" class="form-input" 
                               value="<?php echo htmlspecialchars($user_data['specialite'] ?? ''); ?>">
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label class="form-label" for="nouveau_password">Nouveau mot de passe</label>
                    <input type="password" id="nouveau_password" name="nouveau_password" class="form-input" 
                           placeholder="Laissez vide pour ne pas changer">
                    <div class="password-note">Minimum 6 caractères. Laissez vide si vous ne souhaitez pas changer votre mot de passe.</div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal() {
            document.getElementById('editModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Fermer le modal en cliquant sur l'overlay
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        // Fermer le modal avec la touche Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('editModal').classList.contains('active')) {
                closeEditModal();
            }
        });

        // Auto-masquer les messages d'alerte après 5 secondes
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(function() {
                        alert.remove();
                    }, 300);
                }, 5000);
            });
        });
    </script>

</body>
</html>

<?php
require_once 'footer.php';
?>