<?php
require_once 'connexionBDD.php';
require_once "csrf.php";
// Vérification si l'utilisateur est déjà connecté
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Redirige vers la page d'accueil si l'utilisateur est déjà connecté
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Vérification du brute force en bdd
    $stmt = $pdo->prepare("SELECT * FROM login_attempts WHERE email = ? AND attempt_time > NOW() - INTERVAL 15 MINUTE");
    $stmt->execute([$email]);
    $attempts = $stmt->fetchAll();
    if (count($attempts) >= 5) {
        $error = "Trop de tentatives de connexion échouées. Veuillez réessayer plus tard.";
    }
    else {
        // Enregistrement de la tentative de connexion
        $stmt = $pdo->prepare("INSERT INTO login_attempts (email, attempt_time) VALUES (?, NOW())");
        $stmt->execute([$email]);
    }
    // Validation des données
    if (empty($email) || empty($password)) {
        $error = "Tous les champs sont requis.";
    } elseif(verifyCsrfToken($_POST['csrf_token']) === false)
    {
        $error = "Token CSRF invalide. Veuillez réessayer.";
    }
    elseif(empty($error)) {
        // Vérification des identifiants dans la base de données
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Authentification réussie
                $_SESSION['user_id'] = $user['id'];
                // Enregistrement de la tentative de connexion réussie
                $stmt = $pdo->prepare("DELETE FROM login_attempts WHERE email = ?");
                $stmt->execute([$email]);
                header('Location: index.php'); // Redirection vers la page d'accueil après connexion réussie
                exit();
            } else {
                $error = "Identifiants incorrects.";
                sleep(1); // Pour éviter les attaques par force brute
            }
        } catch (PDOException $e) {
            $error = "Erreur lors de la connexion : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./navbar.css">
    <link rel="stylesheet" href="./auth-pages.css">
    <link rel="stylesheet" href="footer.css">
    <title>Connexion - Robots-Délices</title>
</head>
<body class="login-page">
    <?php
    require_once 'header.php';
    ?>
    <main>
        <div id="section-container">
            <!-- Section gauche avec logo et texte -->
            <div id="login-container">
                <img alt="Logo Robots-Délices" id="logo" src="./img/logo_robots_delices.png"/>
                <p>Rejoignez notre communauté de passionnés de cuisine et partagez vos meilleures recettes</p>
            </div>
            
            <!-- Section droite avec formulaire de connexion -->
            <div class="right-section">
                <div class="tabs-container">
                    <a href="./login.php" class="active">Connexion</a>
                    <a href="./register.php">Inscription</a>
                </div>
                <div id="form-container">
                    <form id="login-form" action="/login.php" method="POST">
                        <div>
                            <label for="email">Email :</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div>
                            <label for="password">Mot de passe :</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <?php if (isset($error)): ?>
                            <p class="error-message"><?php echo $error; ?></p>
                        <?php endif; ?>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <button type="submit">Se connecter</button>
                    </form>
                    <a href="./mdp-oublié.php">Mot de passe oublié ?</a>
                </div>
            </div>
        </div>
    </main>
    <footer>
        <p>© 2025 Robots-Délices. Tous droits réservés.</p>
    </footer>
</body>
</html>