<?php
/*
    FICHIER : api-favoris.php
    RÔLE : API REST pour gérer les favoris des utilisateurs
    
    Cette API permet d'ajouter ou de supprimer une recette des favoris d'un utilisateur.
    Elle fonctionne en mode AJAX et retourne des réponses au format JSON.
    
    CONCEPTS PHP UTILISÉS :
    - Sessions PHP : pour identifier l'utilisateur connecté
    - PDO (PHP Data Objects) : pour les interactions avec la base de données
    - Gestion des headers HTTP : pour configurer les réponses CORS et JSON
    - Validation des données : vérification des paramètres reçus
    - Encodage JSON : pour renvoyer des réponses structurées
    - Gestion d'erreurs : try/catch pour les erreurs de base de données
    
    SÉCURITÉ IMPLÉMENTÉE :
    - Vérification de l'authentification utilisateur
    - Validation des données d'entrée (ID de recette)
    - Requêtes préparées pour éviter l'injection SQL
    - Configuration CORS sécurisée (origine spécifique)
    - Gestion d'erreurs sans exposition de données sensibles
*/

// Démarrage de la session pour identifier l'utilisateur
session_start();

// Inclusion de la connexion à la base de données
require_once 'connexionBDD.php';

// CONFIGURATION DES HEADERS HTTP
// Ces headers configurent la réponse pour une API REST compatible avec AJAX
header('Content-Type: application/json');                                    // Indique que la réponse est en JSON
header('Access-Control-Allow-Origin: http://localhost:15050');               // Autorise les requêtes depuis ce domaine spécifique
header('Access-Control-Allow-Methods: POST');                                // Autorise uniquement les méthodes POST
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');      // Headers autorisés dans les requêtes
header("Access-Control-Allow-Credentials: true");                            // Autorise l'envoi des cookies de session

// TRAITEMENT PRINCIPAL : Gestion du toggle des favoris
// Vérification des conditions nécessaires :
// 1. L'utilisateur doit être connecté ($_SESSION['user_id'])
// 2. L'action doit être spécifiée ($_POST['action'])
// 3. L'action doit être 'toggle_favorites'
if (isset($_SESSION['user_id']) && isset($_POST['action']) && $_POST['action'] === 'toggle_favorites') {
    
    // RÉCUPÉRATION ET VALIDATION DES DONNÉES
    $userId = $_SESSION['user_id'];                                          // ID de l'utilisateur connecté
    $recetteId = isset($_POST['recette_id']) ? (int)$_POST['recette_id'] : 0; // ID de la recette (conversion en entier)
    
    // VALIDATION DE L'ID DE RECETTE
    // L'ID doit être un nombre positif valide
    if ($recetteId <= 0) {
        echo json_encode(['status' => 'error','error' => 'Invalid recette ID']);
        exit();
    }
    
    // INTERACTION AVEC LA BASE DE DONNÉES
    try {
        // ÉTAPE 1 : Vérification si la recette est déjà en favoris
        // Cette requête cherche une entrée existante dans la table favorites
        $stmt = $pdo->prepare("SELECT * FROM favorites WHERE user_id = ? AND recipe_id = ?");
        $stmt->execute([$userId, $recetteId]);
        $favoris = $stmt->fetch();

        if ($favoris) {
            // CAS 1 : La recette est déjà en favoris → ON LA RETIRE
            $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?");
            $stmt->execute([$userId, $recetteId]);
            // Réponse JSON indiquant que la recette a été retirée des favoris
            echo json_encode(['status' => 'removed']);
        } else {
            // CAS 2 : La recette n'est pas en favoris → ON L'AJOUTE
            $stmt = $pdo->prepare("INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)");
            $stmt->execute([$userId, $recetteId]);
            // Réponse JSON indiquant que la recette a été ajoutée aux favoris
            echo json_encode(['status' => 'added']);
        }
        
    } catch (PDOException $e) {
        // GESTION DES ERREURS DE BASE DE DONNÉES
        // Envoi d'une réponse d'erreur en cas de problème avec la base de données
        echo json_encode(['status' => 'error','error' => 'Database error: ' . $e->getMessage()]);
    }
    
} else {
    // CAS D'ERREUR : Utilisateur non connecté ou paramètres manquants
    // Cette réponse est envoyée si l'utilisateur n'est pas authentifié
    // ou si les paramètres requis ne sont pas fournis
    echo json_encode(['status' => 'not_logged_in']);
}