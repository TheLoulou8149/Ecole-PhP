<?php
require_once 'config.php';

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit();
    }
}

// Fonctions de vérification d'authentification (inchangées)
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
}

function isProfesseur() {
    return isLoggedIn() && $_SESSION['user_type'] === 'professeur';
}

function isEtudiant() {
    return isLoggedIn() && $_SESSION['user_type'] === 'etudiant';
}

// Nouvelle version de requireLogin avec sécurité anti-boucle
function requireLogin() {
    $current_page = basename($_SERVER['PHP_SELF']);
    
    // Si non connecté ET pas déjà sur la page de login
    if (!isLoggedIn() && $current_page !== 'login.php') {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        redirect('login.php');
    }
    
    // Si déjà connecté ET sur la page de login
    if (isLoggedIn() && $current_page === 'login.php') {
        redirect('main.php');
    }
}

// Fonctions de vérification de rôle (inchangées mais optimisées)
function requireProfesseur() {
    requireLogin(); // S'assure que l'utilisateur est connecté d'abord
    
    if (!isProfesseur()) {
        // Ajout d'un message pour expliquer la redirection
        $_SESSION['flash_message'] = "Accès réservé aux professeurs";
        redirect('dashboard_etudiant.php');
    }
}

function requireEtudiant() {
    requireLogin();
    
    if (!isEtudiant()) {
        $_SESSION['flash_message'] = "Accès réservé aux étudiants";
        redirect('dashboard_professeur.php');
    }
}

// Fonction pour obtenir l'utilisateur courant (optimisée)
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'type' => $_SESSION['user_type'],
        'name' => $_SESSION['user_name'] ?? 'Utilisateur',
        'email' => $_SESSION['user_email'] ?? ''
    ];
}