<?php
// Définir les constantes en dehors de toute fonction
define('DB_HOST', '10.96.16.82'); // l'adresse IP du serveur MySQL
define('DB_NAME', 'ecole'); // le nom de la base de données
define('DB_USER', 'colin'); // nom d'utilisateur 
define('DB_PASS', '');

function getDBConnection() { // Fonction pour obtenir une connexion PDO à la base de données
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