<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $user_type = $_POST['user_type'];
    
    // Validation
    if (empty($nom) || empty($prenom) || empty($email) || empty($password) || empty($user_type)) {
        $error = 'Tous les champs sont obligatoires.';
    } elseif ($password !== $confirm_password) {
        $error = 'Les mots de passe ne correspondent pas.';
    } elseif (strlen($password) < 6) {
        $error = 'Le mot de passe doit contenir au moins 6 caractères.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format d\'email invalide.';
    } else {
        try {
            // Obtenir la connexion à la base de données
            $pdo = getDBConnection();
            
            // Déterminer la table selon le type d'utilisateur
            $table = ($user_type == 'professeur') ? 'profs' : 'etudiants';
            
            // Vérifier si l'email existe déjà
            $sql_check = "SELECT * FROM $table WHERE email = :email";
            $stmt_check = $pdo->prepare($sql_check);
            $stmt_check->bindParam(':email', $email);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() > 0) {
                $error = 'Cet email est déjà utilisé.';
            } else {
                // Insérer le nouvel utilisateur (mot de passe en clair pour être cohérent avec login.php)
                // Requête générique qui s'adapte à la table déterminée ci-dessus
                $sql_insert = "INSERT INTO $table (nom, prenom, email, password) VALUES (:nom, :prenom, :email, :password)";
                
                $stmt_insert = $pdo->prepare($sql_insert);
                $stmt_insert->bindParam(':nom', $nom);
                $stmt_insert->bindParam(':prenom', $prenom);
                $stmt_insert->bindParam(':email', $email);
                $stmt_insert->bindParam(':password', $password);
                
                if ($stmt_insert->execute()) {
                    $success = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
                    // Optionnel : vider les champs après succès
                    $_POST = array();
                } else {
                    $error = 'Erreur lors de l\'inscription.';
                }
            }
        } catch(PDOException $e) {
            $error = 'Erreur lors de l\'inscription : ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Plateforme de cours</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .signin-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
        }
        .signin-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .signin-header h1 {
            color: #333;
            margin-bottom: 0.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #667eea;
        }
        .form-row {
            display: flex;
            gap: 1rem;
        }
        .form-row .form-group {
            flex: 1;
        }
        .btn {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .error {
            background: #fee;
            color: #c33;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            border: 1px solid #fcc;
        }
        .success {
            background: #efe;
            color: #383;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            border: 1px solid #cfc;
        }
        .links {
            text-align: center;
            margin-top: 1rem;
        }
        .links a {
            color: #667eea;
            text-decoration: none;
        }
        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="signin-container">
        <div class="signin-header">
            <h1>Inscription</h1>
            <p>Créez votre compte</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="user_type">Type de compte :</label>
                <select name="user_type" id="user_type" required>
                    <option value="">Choisissez votre type de compte</option>
                    <option value="etudiant" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] == 'etudiant') ? 'selected' : ''; ?>>Étudiant</option>
                    <option value="professeur" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] == 'professeur') ? 'selected' : ''; ?>>Professeur</option>
                </select>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="nom">Nom :</label>
                    <input type="text" id="nom" name="nom" value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="prenom">Prénom :</label>
                    <input type="text" id="prenom" name="prenom" value="<?php echo isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : ''; ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>
                <small style="color: #666;">Minimum 6 caractères</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe :</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="btn">S'inscrire</button>
        </form>
        
        <div class="links">
            <p>Déjà un compte ? <a href="login.php">Se connecter</a></p>
        </div>
    </div>
</body>
</html>