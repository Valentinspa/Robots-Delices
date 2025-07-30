<?php
/*
    FICHIER : csrf.php
    RÔLE : Gestion de la protection CSRF (Cross-Site Request Forgery)
    
    Ce fichier contient les fonctions utilitaires pour générer et vérifier les tokens CSRF.
    La protection CSRF empêche les attaques où un site malveillant pourrait envoyer des requêtes
    non autorisées au nom d'un utilisateur connecté.
    
    CONCEPTS PHP UTILISÉS :
    - Sessions PHP : pour stocker le token CSRF côté serveur
    - Fonctions cryptographiques : random_bytes() pour générer des données aléatoures sécurisées
    - Fonctions de hachage : bin2hex() pour convertir les bytes en hexadécimal
    - Comparaison sécurisée : hash_equals() pour éviter les attaques de timing
    
    SÉCURITÉ IMPLÉMENTÉE :
    - Génération de tokens aléatoires cryptographiquement sécurisés (32 bytes = 256 bits)
    - Stockage du token dans la session (côté serveur uniquement)
    - Comparaison sécurisée avec hash_equals() pour éviter les fuites d'informations temporelles
    - Vérification de l'état de la session avant utilisation
*/

/**
 * FONCTION : generateCsrfToken()
 * RÔLE : Génère un token CSRF unique et le stocke dans la session
 * 
 * Cette fonction vérifie d'abord si une session est active, puis génère un token
 * CSRF s'il n'en existe pas déjà un. Le même token est réutilisé pendant toute la session.
 * 
 * RETOUR : Le token CSRF sous forme de chaîne hexadécimale
 */
function generateCsrfToken() {
    // Vérification si une session est déjà active
    // PHP_SESSION_NONE indique qu'aucune session n'est en cours
    if (session_status() == PHP_SESSION_NONE) {
        session_start(); // Démarre une nouvelle session si nécessaire
    }
    
    // Génère un nouveau token uniquement s'il n'en existe pas déjà un
    if (!isset($_SESSION['csrf_token'])) {
        // random_bytes(32) génère 32 bytes aléatoires cryptographiquement sécurisés
        // bin2hex() convertit ces bytes en représentation hexadécimale lisible
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * FONCTION : verifyCsrfToken($token)
 * RÔLE : Vérifie la validité d'un token CSRF fourni
 * 
 * Cette fonction compare le token fourni avec celui stocké dans la session.
 * Elle utilise hash_equals() pour une comparaison sécurisée qui évite les attaques de timing.
 * 
 * PARAMÈTRE : $token - Le token à vérifier (généralement depuis $_POST)
 * RETOUR : true si le token est valide, false sinon
 */
function verifyCsrfToken($token) {
    // Vérification si une session est active
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Vérification que le token existe dans la session ET qu'il correspond au token fourni
    // hash_equals() effectue une comparaison sécurisée qui prend toujours le même temps
    // Cela empêche les attaques par analyse temporelle
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

