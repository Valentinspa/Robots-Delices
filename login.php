<?php
// Page de connexion - Permet aux utilisateurs de se connecter à leur compte
// Inclut des protections contre le brute force et les attaques CSRF

// Inclusion des fichiers nécessaires
require_once 'connexionBDD.php'; // Connexion à la base de données
require_once "csrf.php"; // Protection contre les attaques CSRF

// Démarrage de la session pour gérer l'état de connexion
session_start();

// Vérification si l'utilisateur est déjà connecté
// Si oui, on le redirige vers la page d'accueil (pas besoin de se reconnecter)
if (isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Redirection HTTP vers index.php
    exit(); // Arrête l'exécution du script après la redirection
}
// Traitement du formulaire seulement si la méthode est POST
// $_SERVER['REQUEST_METHOD'] contient la méthode HTTP utilisée
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et nettoyage des données du formulaire
    // trim() supprime les espaces en début et fin de chaîne
    $email = trim($_POST['email']); // Email saisi par l'utilisateur
    $password = trim($_POST['password']); // Mot de passe saisi

    // Protection contre le brute force (tentatives répétées de connexion)
    // On vérifie combien de tentatives ont été faites dans les 15 dernières minutes
    $stmt = $pdo->prepare("SELECT * FROM login_attempts WHERE email = ? AND attempt_time > NOW() - INTERVAL 15 MINUTE");
    $stmt->execute([$email]);
    $attempts = $stmt->fetchAll(); // Récupère toutes les tentatives récentes
    
    // Si plus de 5 tentatives en 15 minutes, on bloque temporairement
    if (count($attempts) >= 5) {
        $error = "Trop de tentatives de connexion échouées. Veuillez réessayer plus tard.";
    }
    else {
        // Enregistrement de cette tentative de connexion dans la BDD
        // NOW() insère la date/heure actuelle
        $stmt = $pdo->prepare("INSERT INTO login_attempts (email, attempt_time) VALUES (?, NOW())");
        $stmt->execute([$email]);
    }
    // Validation des données reçues du formulaire
    // Vérification que les champs obligatoires ne sont pas vides
    if (empty($email) || empty($password)) {
        $error = "Tous les champs sont requis.";
    } 
    // Vérification du token CSRF pour éviter les attaques cross-site
    // Le token CSRF est généré côté serveur et vérifié à chaque soumission
    elseif(verifyCsrfToken($_POST['csrf_token']) === false)
    {
        $error = "Token CSRF invalide. Veuillez réessayer.";
    }
    // Si pas d'erreur jusqu'ici, on procède à l'authentification
    elseif(empty($error)) {
        // Tentative d'authentification avec gestion d'erreurs
        try {
            // Recherche de l'utilisateur dans la base de données par email
            // On utilise une requête préparée pour éviter les injections SQL
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(); // Récupère l'utilisateur ou false si non trouvé

            // Vérification de l'existence de l'utilisateur ET du mot de passe
            // password_verify() compare le mot de passe en clair avec le hash stocké
            if ($user && password_verify($password, $user['password'])) {
                // Authentification réussie !
                // On stocke l'ID de l'utilisateur dans la session
                $_SESSION['user_id'] = $user['id'];
                
                // Nettoyage des tentatives de connexion échouées précédentes
                $stmt = $pdo->prepare("DELETE FROM login_attempts WHERE email = ?");
                $stmt->execute([$email]);
                
                // Redirection vers la page d'accueil
                header('Location: index.php');
                exit(); // Important : arrêter le script après redirection
            } else {
                // Identifiants incorrects
                $error = "Identifiants incorrects.";
                // Délai d'1 seconde pour ralentir les attaques par force brute
                sleep(1);
            }
        } catch (PDOException $e) {
            // Gestion des erreurs de base de données
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