<?php
// Définir les constantes en dehors de toute fonction
define('DB_HOST', '10.96.16.82');
define('DB_NAME', 'ecole');
define('DB_USER', 'colin');
define('DB_PASS', '');

function getDBConnection() {
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo; // Important : retourner la connexion PDO
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}
?>