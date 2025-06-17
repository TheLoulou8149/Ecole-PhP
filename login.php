<?php
require_once 'config.php';

$error = '';

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];
    
    // Validation des données
    if (empty($email) || empty($password) || empty($user_type)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif (!validateEmail($email)) {
        $error = "Format d'email invalide.";
    } else {
        try {
            $pdo = getDBConnection();
            
            // Déterminer la table selon le type d'utilisateur
            // Remplacez ces noms par les vrais noms de vos tables
            $table = ($user_type === 'professeur') ? 'profs' : 'etudiants'; // ADAPTEZ ICI
            
            // Rechercher l'utilisateur - ADAPTEZ les noms de colonnes selon votre BDD
            $query = "SELECT * FROM $table WHERE email = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                // IMPORTANT : Adaptez cette partie selon votre structure
                // Si vos mots de passe sont en clair (non hashés) :
                $password_valid = ($user['mot_de_passe'] === $password); // Mot de passe en clair
                
                // Si vos mots de passe sont hashés, utilisez plutôt :
                // $password_valid = password_verify($password, $user['mot_de_passe']);
                
                if ($password_valid) {
                    // Connexion réussie
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_type'] = $user_type;
                    
                    // Adaptez selon les noms de colonnes de votre BDD
                    $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
                    $_SESSION['user_email'] = $user['email'];
                    
                    // Redirection selon le type d'utilisateur
                    if ($user_type === 'professeur') {
                        redirect('dashboard_professeur.php');
                    } else {
                        redirect('dashboard_etudiant.php');
                    }
                } else {
                    $error = "Email ou mot de passe incorrect.";
                }
            } else {
                $error = "Email ou mot de passe incorrect.";
            }
        } catch (PDOException $e) {
            $error = "Erreur de connexion : " . $e->getMessage();
            // Pour déboguer, affichez l'erreur complète temporairement
        }
    }
}

// Fonction pour tester la connexion à la base
function testConnection() {
    try {
        $pdo = getDBConnection();
        echo "Connexion réussie !<br>";
        
        // Lister les tables disponibles
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "Tables disponibles : " . implode(', ', $tables) . "<br>";
        
        return true;
    } catch (Exception $e) {
        echo "Erreur de connexion : " . $e->getMessage() . "<br>";
        return false;
    }
}

// Décommentez cette ligne pour tester la connexion
// testConnection();
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
        
        .debug-info {
            background-color: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 12px;
            color: #666;
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
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Informations de débogage -->
        <div class="debug-info">
            <strong>Instructions :</strong><br>
            1. Modifiez config.php avec le nom de votre base de données<br>
            2. Dans ce fichier, ligne 27, remplacez 'prof' et 'etudiant' par vos vrais noms de tables<br>
            3. Vérifiez que les noms de colonnes correspondent (ligne 42)<br>
            4. Décommentez la ligne 67 pour tester la connexion
        </div>
        
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