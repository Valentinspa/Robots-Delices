<?php

require_once 'connexionBDD.php';
require_once 'csrf.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Redirection vers la page d'accueil si l'utilisateur n'est pas connecté
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    header('Location: index.php'); // Redirection si la requête n'est pas POST ou le token CSRF est invalide
    exit();
}
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
// Détruire la session pour déconnecter l'utilisateur
session_destroy();
unset($_SESSION);
setcookie("PHPSESSID", "", time() - 3600, "/"); // Supprimer le cookie de session
// Rediriger vers la page d'accueil
header('Location: index.php');
exit();
