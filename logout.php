<?php
require_once 'config.php';

// Détruire toutes les variables de session
session_unset();

// Détruire la session
session_destroy();

// Supprimer le cookie de session
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Rediriger vers la page de connexion
redirect('login.php');
?>