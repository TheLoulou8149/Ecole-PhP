<?php
// config.php - Configuration de la base de données
session_start();

// Paramètres de connexion à la base de données
define('DB_HOST', '10.96.16.82');
define('DB_NAME', 'ecole');
define('DB_USER', 'colin'); // Remplacez par votre nom d'utilisateur MySQL
define('DB_PASS', ''); // Remplacez par votre mot de passe MySQL

// Fonction de connexion à la base de données
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch(PDOException $e) {
        die("Erreur de connexion à la base de données: " . $e->getMessage());
    }
}

// Fonction pour rediriger l'utilisateur
function redirect($url) {
    header("Location: $url");
    exit();
}

// Fonction pour nettoyer les données d'entrée
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Fonction pour valider l'email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
?>