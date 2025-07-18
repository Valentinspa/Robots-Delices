<?php

// $user = $_ENV['DB_USER'];
$user = 'root';
// $pass = $_ENV['DB_PASSWORD'];
$pass = 'root';
$host = $_ENV['DB_HOST'];
// $dbname = $_ENV['DB_NAME'];
$dbname = 'Robots-Délices';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}