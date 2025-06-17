<?php
session_start();

// Paramètres de connexion
define('DB_HOST', 'localhost');
define('DB_NAME', 'ecole');
define('DB_USER', 'root');
define('DB_PASS', '');

// Fonction de connexion PDO
function getDBConnection() {
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}
?>
