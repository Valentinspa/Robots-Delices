<?php
require_once 'connexionBDD.php';
require_once 'csrf.php';
// Vérification si l'utilisateur est déjà connecté
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Redirige vers la page d'accueil si l'utilisateur est déjà connecté
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $nom = htmlspecialchars(trim($_POST['nom']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm-password']);

    // requête API pour reCAPTCHA en POST
    $recaptchaResponse = $_POST['g-recaptcha-response'];
    $secret = $_ENV['RECAPTCHA_SECRET_KEY']; // Clé secrète reCAPTCHA depuis le fichier .env
    $recaptchaUrl = "https://www.google.com/recaptcha/api/siteverify";
    // Envoi requête POST en CURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $recaptchaUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'secret' => $secret,
        'response' => $recaptchaResponse
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $responseData = json_decode($response, true);

    if ($responseData['success'] === false) {
        $error = "La vérification reCAPTCHA a échoué. Veuillez réessayer.";
    }
    // Validation des données
    if (empty($prenom) || empty($nom) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = "Tous les champs sont requis.";
    } elseif ($password !== $confirmPassword) {
        $error = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($password) < 8) {
        $error = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $error = "Le mot de passe doit contenir au moins une majuscule.";
    } elseif (!preg_match('/[a-z]/', $password)) {
        $error = "Le mot de passe doit contenir au moins une minuscule.";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $error = "Le mot de passe doit contenir au moins un chiffre.";
    } elseif (!preg_match('/[\W_]/', $password)) {
        $error = "Le mot de passe doit contenir au moins un caractère spécial.";
    }elseif (strlen($prenom) < 2 || strlen($nom) < 2) {
        $error = "Le prénom et le nom doivent contenir au moins 2 caractères.";
    } elseif (!preg_match('/^[a-zA-ZÀ-ÿ-]+$/', $prenom) || !preg_match('/^[a-zA-ZÀ-ÿ-]+$/', $nom)) {
        $error = "Le prénom et le nom ne doivent contenir que des lettres ou des tirets.";
    } elseif (verifyCsrfToken($_POST['csrf_token']) === false) {
        $error = "Token CSRF invalide. Veuillez réessayer.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "L'adresse e-mail n'est pas valide.";
    } else {
        // Vérification si l'email existe déjà
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $error = "Un compte avec cette adresse e-mail existe déjà.";
            }
        } catch (PDOException $e) {
            $error = "Erreur lors de la vérification de l'email : " . $e->getMessage();
        }
    }
    
    if (!isset($error)) {
        // Hachage du mot de passe
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insertion dans la base de données
        try {
            $stmt = $pdo->prepare("INSERT INTO users (firstname, lastname, email, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$prenom, $nom, $email, $hashedPassword]);
            header('Location: login.php'); // Redirection vers la page de connexion après l'inscription réussie
            exit();
        } catch (PDOException $e) {
            $error = "Erreur lors de l'inscription : " . $e->getMessage();
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
     <script src="https://www.google.com/recaptcha/api.js" defer></script>
      <script defer>
        function onSubmit(token) {
            document.getElementById("register-form").submit();
        }
    </script>

    <title>Inscription - Robots-Délices</title>
</head>
<body class="register-page">
    <?php
    require_once 'header.php';
    ?>
    <main>
        <div id="section-container">
            <!-- Section gauche -->
            <div id="login-container">
                <img alt="Logo Robots-Délices" id="logo" src="./img/logo_robots_delices.png"/>
                <p>Rejoignez notre communauté de passionnés de cuisine et partagez vos meilleures recettes</p>
            </div>
            
            <!-- Section droite avec formulaire d'inscription -->
            <div class="right-section">
                <div class="tabs-container">
                    <a href="./login.php">Connexion</a>
                    <a href="./register.php" class="active">Inscription</a>
                </div>
                <div id="form-container">
                    <form id="register-form" action="register.php" method="POST">
                        <div>
                            <label for="prenom">Prénom :</label>
                            <input type="text" id="prenom" name="prenom" required>
                        </div>
                        <div>
                            <label for="nom">Nom :</label>
                            <input type="text" id="nom" name="nom" required>
                        </div>
                        <div>
                            <label for="email">Email :</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div>
                            <label for="password">Mot de passe :</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <div>
                            <label for="confirm-password">Confirmer le mot de passe :</label>
                            <input type="password" id="confirm-password" name="confirm-password" required>
                        </div>
                        <?php if (isset($error)): ?>
                            <p class="error-message"><?php echo $error; ?></p>
                        <?php endif; ?>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <button type="submit" class="g-recaptcha" data-sitekey="<?php echo $_ENV['RECAPTCHA_SITE_KEY'] ?>" data-callback='onSubmit' data-action='submit'>S'inscrire</button>
                    </form>
                    <p>Déjà inscrit ? <a href="./login.php">Se connecter</a></p>
                </div>
            </div>
        </div>
    </main>
    <footer>
        <p>© 2025 Robots-Délices. Tous droits réservés.</p>
    </footer>
</body>
</html>