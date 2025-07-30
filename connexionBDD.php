<?php
// Fichier de connexion à la base de données MySQL
// Ce fichier crée une connexion PDO qui sera utilisée dans tous les autres fichiers

// Configuration des paramètres de connexion à la base de données
// TODO : Utiliser les variables d'environnement pour plus de sécurité en production
// $user = $_ENV['DB_USER']; // Ligne commentée, version sécurisée pour production
$user = 'root'; // Nom d'utilisateur MySQL (à changer en production)

// $pass = $_ENV['DB_PASSWORD']; // Ligne commentée, version sécurisée pour production  
$pass = 'root'; // Mot de passe MySQL (à changer en production)

$host = $_ENV['DB_HOST']; // Adresse du serveur MySQL (récupérée depuis variable d'environnement)

// $dbname = $_ENV['DB_NAME']; // Ligne commentée, version sécurisée pour production
$dbname = 'Robots-Délices'; // Nom de la base de données

// Tentative de connexion à la base de données avec gestion d'erreurs
try {
    // Création d'une nouvelle instance PDO (PHP Data Objects)
    // PDO permet d'interagir avec différents types de bases de données de façon uniforme
    // mysql: spécifie le driver MySQL
    // host=$host : adresse du serveur de base de données
    // dbname=$dbname : nom de la base de données à utiliser
    // charset=utf8mb4 : encodage pour supporter tous les caractères (emojis inclus)
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    
    // Configuration des attributs PDO pour un comportement sécurisé et prévisible
    
    // ERRMODE_EXCEPTION : Lance des exceptions en cas d'erreur SQL (plutôt que des warnings)
    // Cela permet de gérer les erreurs avec try/catch
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // FETCH_ASSOC : Par défaut, récupère les résultats sous forme de tableau associatif
    // ['colonne' => 'valeur'] au lieu de [0 => 'valeur']
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // EMULATE_PREPARES false : Utilise les vraies requêtes préparées du serveur MySQL
    // Plus sécurisé contre les injections SQL
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
} catch (PDOException $e) {
    // Si la connexion échoue, arrête le script et affiche le message d'erreur
    // die() arrête complètement l'exécution du script
    // $e->getMessage() récupère le message d'erreur détaillé
    die("Connection failed: " . $e->getMessage());
}