<?php
/*
    FICHIER : delete.php
    RÔLE : Gestionnaire de suppression de compte utilisateur
    
    Ce script gère la suppression définitive d'un compte utilisateur.
    Il inclut plusieurs couches de sécurité pour s'assurer que seul l'utilisateur
    connecté peut supprimer son propre compte via une requête POST sécurisée.
    
    CONCEPTS PHP UTILISÉS :
    - Sessions PHP : pour vérifier l'identité de l'utilisateur
    - PDO (PHP Data Objects) : pour interagir de manière sécurisée avec la base de données
    - Protection CSRF : pour éviter les suppressions non autorisées
    - Gestion des cookies : pour nettoyer complètement la session
    - Redirections HTTP : pour renvoyer l'utilisateur après l'action
    
    SÉCURITÉ IMPLÉMENTÉE :
    - Vérification de l'authentification (session user_id)
    - Validation de la méthode HTTP (POST uniquement)
    - Protection CSRF obligatoire
    - Requête préparée pour éviter l'injection SQL
    - Nettoyage complet de la session après suppression
*/

// Inclusion des fichiers de dépendances
require_once 'connexionBDD.php';  // Connexion à la base de données
require_once 'csrf.php';          // Fonctions de protection CSRF

// Démarrage de la session pour accéder aux données utilisateur
session_start();

// SÉCURITÉ 1 : Vérification de l'authentification
// Seuls les utilisateurs connectés peuvent supprimer leur compte
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Redirection vers la page d'accueil si l'utilisateur n'est pas connecté
    exit();
}

// SÉCURITÉ 2 : Vérification de la méthode HTTP et du token CSRF
// Triple vérification pour s'assurer que la requête est légitime :
// 1. Méthode POST uniquement (pas GET qui pourrait être accidentel)
// 2. Présence du token CSRF dans les données POST
// 3. Validation du token CSRF avec la fonction verifyCsrfToken()
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    header('Location: index.php'); // Redirection si la requête n'est pas POST ou le token CSRF est invalide
    exit();
}

// SUPPRESSION DU COMPTE EN BASE DE DONNÉES
// Utilisation d'une requête préparée pour éviter l'injection SQL
// La requête ne supprime QUE le compte de l'utilisateur actuellement connecté
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);

// NETTOYAGE COMPLET DE LA SESSION
// Après suppression du compte, il faut déconnecter complètement l'utilisateur

// Destruction de la session côté serveur
session_destroy();

// Suppression des variables de session en mémoire
unset($_SESSION);

// Suppression du cookie de session côté navigateur
// setcookie avec un time() dans le passé supprime le cookie
setcookie("PHPSESSID", "", time() - 3600, "/");

// REDIRECTION FINALE
// L'utilisateur est renvoyé vers la page d'accueil
// Son compte n'existe plus et il n'est plus connecté
header('Location: index.php');
exit();
