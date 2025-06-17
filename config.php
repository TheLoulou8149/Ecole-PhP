<?php
// config.php - Configuration de la base de données
session_start();

// Paramètres de connexion à la base de données
define('DB_HOST', '10.96.16.82');
define('DB_NAME', 'ecole');
define('DB_USER', 'colin'); // Remplacez par votre nom d'utilisateur MySQL
define('DB_PASS', ''); // Remplacez par v.otre mot de passe MySQL

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Démarrer la session seulement si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>