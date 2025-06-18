<?php
require_once 'config.php';

// Définition de la fonction redirect manquante
function redirect($url) {
    header('Location: ' . $url);
    exit();
}

// Fonction pour vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
}

// Fonction pour vérifier si l'utilisateur est un professeur
function isProfesseur() {
    return isLoggedIn() && $_SESSION['user_type'] === 'professeur';
}

// Fonction pour vérifier si l'utilisateur est un étudiant
function isEtudiant() {
    return isLoggedIn() && $_SESSION['user_type'] === 'etudiant';
}

// Fonction pour rediriger vers la page de connexion si non authentifié
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        redirect('login.php');
    }
}

// Fonction pour rediriger les professeurs uniquement
function requireProfesseur() {
    requireLogin();
    if (!isProfesseur()) {
        redirect('dashboard_etudiant.php');
    }
}

// Fonction pour rediriger les étudiants uniquement
function requireEtudiant() {
    requireLogin();
    if (!isEtudiant()) {
        redirect('dashboard_professeur.php');
    }
}

// Fonction pour obtenir les informations de l'utilisateur connecté
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'type' => $_SESSION['user_type'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email']
    ];
}