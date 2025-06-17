<?php
// index.php - Page principale avec connexion
session_start();
require_once 'db.php';

$message = '';

if ($_POST) {
    if (isset($_POST['connexion'])) {
        $email = trim($_POST['email']);
        $mot_de_passe = $_POST['mot_de_passe'];
        
        $dbHelper = new DatabaseHelper();
        $user = $dbHelper->verifierConnexion($email, $mot_de_passe);
        
        if ($user) {
            $_SESSION['user'] = $user;
            header('Location: agenda.php');
            exit();
        } else {
            $message = "Email ou mot de passe incorrect.";
        }
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Scolaire - Connexion</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f4f4f4; }
        .container { max-width: 400px; margin: 50px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #333; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input[type="email"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; }
        .btn { width: 100%; padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; }
        .btn:hover { background-color: #0056b3; }
        .message { padding: 10px; margin-bottom: 20px; border-radius: 5px; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📅 Agenda Scolaire</h1>
        
        <?php if ($message): ?>
            <div class="message error"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="mot_de_passe">Mot de passe :</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>
            </div>
            
            <button type="submit" name="connexion" class="btn">Se connecter</button>
        </form>
        
        <div class="info" style="margin-top: 20px;">
            <strong>Comptes de test :</strong><br>
            Professeur : marie.dupont@ecole.fr<br>
            Élève : sophie.durand@eleve.fr<br>
            Mot de passe : password123
        </div>
    </div>
</body>
</html>

<?php
// agenda.php - Page de l'agenda
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

$user = $_SESSION['user'];
$dbHelper = new DatabaseHelper();

// Récupérer la semaine courante
$semaine = isset($_GET['semaine']) ? $_GET['semaine'] : date('Y-m-d');
$debut_semaine = date('Y-m-d', strtotime('monday this week', strtotime($semaine)));
$fin_semaine = date('Y-m-d', strtotime('sunday this week', strtotime($semaine)));

// Récupérer les cours selon le type d'utilisateur
if ($user['type_utilisateur'] == 'eleve') {
    $cours = $dbHelper->getCoursEleve($user['classe_id'], $debut_semaine, $fin_semaine);
} else {
    $cours = $dbHelper->getCoursProfesseur($user['id'], $debut_semaine, $fin_semaine);
}

// Récupérer les cours exceptionnels
$cours_exceptionnels = $dbHelper->getCoursExceptionnels($debut_semaine, $fin_semaine, $user['id'], $user['type_utilisateur']);

// Organiser les cours par jour
$jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
$planning = [];

foreach ($jours as $jour) {
    $planning[$jour] = [];
}

// Ajouter les cours réguliers
foreach ($cours as $c) {
    $planning[$c['jour_semaine']][] = $c;
}

// Fonction pour générer les créneaux horaires
function genererCreneaux() {
    $creneaux = [];
    for ($h = 8; $h <= 17; $h++) {
        $creneaux[] = sprintf('%02d:00', $h);
        if ($h < 17) $creneaux[] = sprintf('%02d:30', $h);
    }
    return $creneaux;
}

// Fonction pour afficher un cours
function afficherCours($cours, $type_user) {
    $couleur = substr(md5($cours['code_matiere']), 0, 6);
    echo "<div class='cours' style='background-color: #{$couleur}20; border-left: 4px solid #{$couleur};'>";
    echo "<strong>" . htmlspecialchars($cours['nom_matiere']) . "</strong><br>";
    echo "<small>" . $cours['heure_debut'] . " - " . $cours['heure_fin'] . "</small><br>";
    echo "<small>📍 " . htmlspecialchars($cours['nom_salle']) . "</small><br>";
    
    if ($type_user == 'eleve') {
        echo "<small>👨‍🏫 " . htmlspecialchars($cours['nom_professeur']) . "</small>";
    } else {
        echo "<small>🎓 " . htmlspecialchars($cours['nom_classe']) . "</small>";
    }
    echo "</div>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Agenda - <?php echo ucfirst($user['type_utilisateur']); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f8f9fa; }
        .header { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header h1 { margin: 0; color: #333; }
        .user-info { float: right; color: #666; }
        .nav-semaine { text-align: center; margin: 20px 0; }
        .nav-semaine a { margin: 0 10px; padding: 8px 16px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
        .nav-semaine a:hover { background: #0056b3; }
        .planning { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
        .jour { background: white; border-radius: 10px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .jour h3 { margin-top: 0; padding-bottom: 10px; border-bottom: 2px solid #eee; text-align: center; }
        .cours { margin: 10px 0; padding: 10px; border-radius: 5px; }
        .logout { float: right; background: #dc3545; color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px; }
        .logout:hover { background: #c82333; }
        .date-semaine { color: #666; font-size: 14px; }
        @media (max-width: 768px) {
            .planning { grid-template-columns: 1fr; }
            .user-info { float: none; margin-top: 10px; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📅 Mon Agenda</h1>
        <div class="user-info">
            Bonjour <strong><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></strong>
            (<?php echo ucfirst($user['type_utilisateur']); ?>)
            <a href="index.php?logout=1" class="logout">Déconnexion</a>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="nav-semaine">
        <a href="?semaine=<?php echo date('Y-m-d', strtotime('-1 week', strtotime($debut_semaine))); ?>">← Semaine précédente</a>
        <span>Semaine du <?php echo date('d/m/Y', strtotime($debut_semaine)); ?> au <?php echo date('d/m/Y', strtotime($fin_semaine)); ?></span>
        <a href="?semaine=<?php echo date('Y-m-d', strtotime('+1 week', strtotime($debut_semaine))); ?>">Semaine suivante →</a>
    </div>

    <div class="planning">
        <?php foreach ($jours as $index => $jour): ?>
            <div class="jour">
                <h3>
                    <?php echo ucfirst($jour); ?>
                    <div class="date-semaine">
                        <?php echo date('d/m', strtotime($debut_semaine . ' +' . $index . ' days')); ?>
                    </div>
                </h3>
                
                <?php if (empty($planning[$jour])): ?>
                    <p style="color: #999; text-align: center; font-style: italic;">Aucun cours</p>
                <?php else: ?>
                    <?php 
                    // Trier les cours par heure
                    usort($planning[$jour], function($a, $b) {
                        return strcmp($a['heure_debut'], $b['heure_debut']);
                    });
                    
                    foreach ($planning[$jour] as $cours): 
                        afficherCours($cours, $user['type_utilisateur']);
                    endforeach; 
                    ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($cours_exceptionnels)): ?>
        <div style="margin-top: 30px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3>📢 Cours exceptionnels cette semaine</h3>
            <?php foreach ($cours_exceptionnels as $ce): ?>
                <div style="margin: 10px 0; padding: 10px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 5px;">
                    <strong><?php echo date('d/m/Y', strtotime($ce['date_cours'])); ?></strong> - 
                    <?php echo $ce['heure_debut']; ?> à <?php echo $ce['heure_fin']; ?><br>
                    <strong><?php echo htmlspecialchars($ce['nom_matiere']); ?></strong> 
                    (<?php echo ucfirst($ce['statut']); ?>)<br>
                    <?php if ($ce['commentaire']): ?>
                        <small><?php echo htmlspecialchars($ce['commentaire']); ?></small>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</body>
</html>