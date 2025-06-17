<?php

require_once 'config.php';
require_once 'functions.php';

$error = '';
$debug = ''; // Pour le debugging

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];
    
    // Debug : afficher les données reçues
    $debug .= "Email: $email<br>";
    $debug .= "Type: $user_type<br>";
    
    // Validation des données
    if (empty($email) || empty($password) || empty($user_type)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif (!validateEmail($email)) {
        $error = "Format d'email invalide.";
    } else {
        try {
            $pdo = getDBConnection();
            
            // Déterminer la table selon le type d'utilisateur
            $table = ($user_type === 'professeur') ? 'professeurs' : 'etudiants';
            $debug .= "Table utilisée: $table<br>";
            
            // Rechercher l'utilisateur - CORRECTION: utiliser des requêtes préparées sécurisées
            $sql = "SELECT * FROM `$table` WHERE email = ? AND statut = 'actif'";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $debug .= "Utilisateur trouvé: " . ($user ? "Oui" : "Non") . "<br>";
            
            if ($user) {
                $debug .= "Hash en BDD: " . $user['mot_de_passe'] . "<br>";
                $debug .= "Mot de passe saisi: $password<br>";
                
                // Vérifier le mot de passe
                $password_check = password_verify($password, $user['mot_de_passe']);
                $debug .= "Vérification mot de passe: " . ($password_check ? "OK" : "ECHEC") . "<br>";
                
                if ($password_check) {
                    // Connexion réussie
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_type'] = $user_type;
                    $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
                    $_SESSION['user_email'] = $user['email'];
                    
                    // Redirection selon le type d'utilisateur
                    if ($user_type === 'professeur') {
                        header('Location: dashboard_professeur.php');
                        exit();
                    } else {
                        header('Location: dashboard_etudiant.php');
                        exit();
                    }
                } else {
                    // Vérification alternative si le mot de passe n'est pas hashé
                    if ($password === $user['mot_de_passe']) {
                        $debug .= "Mot de passe en clair détecté!<br>";
                        // Connexion réussie avec mot de passe en clair
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_type'] = $user_type;
                        $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
                        $_SESSION['user_email'] = $user['email'];
                        
                        if ($user_type === 'professeur') {
                            header('Location: dashboard_professeur.php');
                            exit();
                        } else {
                            header('Location: dashboard_etudiant.php');
                            exit();
                        }
                    } else {
                        $error = "Email ou mot de passe incorrect.";
                    }
                }
            } else {
                // Vérifier si l'utilisateur existe sans le filtre statut
                $sql_check = "SELECT * FROM `$table` WHERE email = ?";
                $stmt_check = $pdo->prepare($sql_check);
                $stmt_check->execute([$email]);
                $user_check = $stmt_check->fetch(PDO::FETCH_ASSOC);
                
                if ($user_check) {
                    $debug .= "Utilisateur trouvé mais statut: " . ($user_check['statut'] ?? 'NULL') . "<br>";
                    $error = "Compte inactif ou en attente de validation.";
                } else {
                    $error = "Email ou mot de passe incorrect.";
                }
            }
        } catch (PDOException $e) {
            $error = "Erreur de connexion. Veuillez réessayer.";
            $debug .= "Erreur PDO: " . $e->getMessage() . "<br>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Plateforme de Cours</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .login-header p {
            color: #666;
            font-size: 16px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .error {
            background-color: #fee;
            color: #c33;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #fcc;
        }
        
        .debug {
            background-color: #e8f4fd;
            color: #333;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #bde4ff;
            font-size: 12px;
        }
        
        .signup-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        
        .signup-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .signup-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Connexion</h1>
            <p>Accédez à votre plateforme de cours</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($debug && $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="debug">
                <strong>Debug Info:</strong><br>
                <?php echo $debug; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="user_type">Type d'utilisateur</label>
                <select id="user_type" name="user_type" required>
                    <option value="">Sélectionnez votre profil</option>
                    <option value="etudiant" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] === 'etudiant') ? 'selected' : ''; ?>>Étudiant</option>
                    <option value="professeur" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] === 'professeur') ? 'selected' : ''; ?>>Professeur</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">Se connecter</button>
        </form>
        
        <div class="signup-link">
            Pas encore de compte ? <a href="signin.php">S'inscrire</a>
        </div>
    </div>
</body>
</html>