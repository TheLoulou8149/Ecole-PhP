<?php
$host = 'localhost';
$db   = 'nom_de_ta_bdd';
$user = 'root';
$pass = '';
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
