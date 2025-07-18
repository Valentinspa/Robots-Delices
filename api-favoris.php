<?php

session_start();
require_once 'connexionBDD.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:15050'); // Allow requests from any origin
header('Access-Control-Allow-Methods: POST'); // Allow POST and GET methods
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With'); // Allow specific headers
header("Access-Control-Allow-Credentials: true"); // Allow credentials (cookies, authorization headers, etc.)


if (isset($_SESSION['user_id']) && isset($_POST['action']) && $_POST['action'] === 'toggle_favorites') {
    $userId = $_SESSION['user_id'];
    $recetteId = isset($_POST['recette_id']) ? (int)$_POST['recette_id'] : 0;
    
    // Check if the recette ID is valid
    if ($recetteId <= 0) {
        echo json_encode(['status' => 'error','error' => 'Invalid recette ID']);
        exit();
    }
    try {
        // Check if the recette is already in favorites
        $stmt = $pdo->prepare("SELECT * FROM favorites WHERE user_id = ? AND recipe_id = ?");
        $stmt->execute([$userId, $recetteId]);
        $favoris = $stmt->fetch();

        if ($favoris) {
            // If it exists, remove it from favorites
            $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?");
            $stmt->execute([$userId, $recetteId]);
            echo json_encode(['status' => 'removed']);
        } else {
            // If it does not exist, add it to favorites
            $stmt = $pdo->prepare("INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)");
            $stmt->execute([$userId, $recetteId]);
            echo json_encode(['status' => 'added']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error','error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    // If user is not logged in, return an empty array
    echo json_encode(['status' => 'not_logged_in']);
}