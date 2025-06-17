<?php
$host = '10.96.16.82';
$db   = 'ecole';
$user = 'colin';
$pass = '';
$dsn = "mysql:host=$host;port=3306;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
