<?php
// Fonction pour assainir les données utilisateur
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Fonction pour valider une adresse email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Fonction pour rediriger proprement
function redirect($url) {
    header("Location: $url");
    exit;
}
?>
