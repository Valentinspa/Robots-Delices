<?php
/*
    FICHIER : logout.php
    RÔLE : Gestionnaire de déconnexion des utilisateurs
    
    Ce script simple mais important gère la déconnexion complète d'un utilisateur.
    Il nettoie toutes les données de session et redirige vers la page de connexion.
    
    CONCEPTS PHP UTILISÉS :
    - Sessions PHP : pour gérer l'état de connexion de l'utilisateur
    - Redirections HTTP : pour renvoyer l'utilisateur vers une page appropriée
    - Gestion des en-têtes HTTP : header() pour envoyer des instructions au navigateur
    
    SÉCURITÉ IMPLÉMENTÉE :
    - Destruction complète de la session (session_unset() + session_destroy())
    - Redirection automatique pour éviter que l'utilisateur reste sur une page protégée
    - Utilisation d'exit() pour s'assurer que le script s'arrête après la redirection
*/

// Démarrage de la session pour pouvoir la manipuler
session_start();

// ÉTAPE 1 : Suppression de toutes les variables de session
// session_unset() efface toutes les variables stockées dans $_SESSION
// Cela supprime les données comme user_id, username, etc.
session_unset();

// ÉTAPE 2 : Destruction complète de la session
// session_destroy() détruit la session côté serveur
// Le fichier de session sur le serveur est supprimé
session_destroy();

// ÉTAPE 3 : Redirection vers la page de connexion
// header('Location: ...') envoie une redirection HTTP 302 au navigateur
// L'utilisateur sera automatiquement renvoyé vers login.php
header('Location: login.php');

// ÉTAPE 4 : Arrêt du script
// exit() s'assure que aucun code supplémentaire ne sera exécuté après la redirection
// C'est une mesure de sécurité importante
exit();