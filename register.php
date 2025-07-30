<?php
// Page d'inscription - Permet aux nouveaux utilisateurs de créer un compte
// Inclut validation des données, vérification reCAPTCHA et protection CSRF

// Inclusion des fichiers nécessaires
require_once 'connexionBDD.php'; // Connexion à la base de données
require_once 'csrf.php'; // Protection contre les attaques CSRF

// Démarrage de la session
session_start();

// Vérification si l'utilisateur est déjà connecté
// Si oui, pas besoin de s'inscrire, redirection vers l'accueil
if (isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Redirection HTTP
    exit(); // Arrête l'exécution
}

// Traitement du formulaire d'inscription (seulement en méthode POST)
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et nettoyage des données du formulaire
    // htmlspecialchars() échappe les caractères HTML pour éviter les attaques XSS
    // trim() supprime les espaces en début et fin
    $prenom = htmlspecialchars(trim($_POST['prenom'])); // Prénom de l'utilisateur
    $nom = htmlspecialchars(trim($_POST['nom'])); // Nom de famille
    $email = htmlspecialchars(trim($_POST['email'])); // Adresse email
    $password = trim($_POST['password']); // Mot de passe (pas d'échappement, sera hashé)
    $confirmPassword = trim($_POST['confirm-password']); // Confirmation du mot de passe

    // Vérification reCAPTCHA pour éviter les robots/spam
    // Google reCAPTCHA vérifie que l'utilisateur est bien humain
    $recaptchaResponse = $_POST['g-recaptcha-response']; // Token reçu du widget reCAPTCHA
    $secret = $_ENV['RECAPTCHA_SECRET_KEY']; // Clé secrète (dans variables d'environnement)
    $recaptchaUrl = "https://www.google.com/recaptcha/api/siteverify"; // URL de l'API Google
    
    // Envoi d'une requête POST à l'API Google reCAPTCHA avec CURL
    $ch = curl_init(); // Initialise une session cURL
    curl_setopt($ch, CURLOPT_URL, $recaptchaUrl); // Définit l'URL de destination
    curl_setopt($ch, CURLOPT_POST, true); // Méthode POST
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([ // Données à envoyer
        'secret' => $secret, // Clé secrète de votre site
        'response' => $recaptchaResponse // Token du widget reCAPTCHA
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retourner la réponse au lieu de l'afficher
    $response = curl_exec($ch); // Exécuter la requête
    curl_close($ch); // Fermer la session cURL
    $responseData = json_decode($response, true); // Décoder la réponse JSON

    // Si reCAPTCHA échoue, définir une erreur
    if ($responseData['success'] === false) {
        $error = "La vérification reCAPTCHA a échoué. Veuillez réessayer.";
    }
    // Validation complète des données reçues du formulaire
    // Chaque condition vérifie un critère différent
    
    // Vérification que tous les champs obligatoires sont remplis
    if (empty($prenom) || empty($nom) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = "Tous les champs sont requis.";
    } 
    // Vérification que les deux mots de passe sont identiques
    elseif ($password !== $confirmPassword) {
        $error = "Les mots de passe ne correspondent pas.";
    } 
    // Vérification de la longueur minimale du mot de passe
    elseif (strlen($password) < 8) {
        $error = "Le mot de passe doit contenir au moins 8 caractères.";
    } 
    // Vérification présence d'au moins une majuscule avec regex
    // [A-Z] = n'importe quelle lettre majuscule
    elseif (!preg_match('/[A-Z]/', $password)) {
        $error = "Le mot de passe doit contenir au moins une majuscule.";
    } 
    // Vérification présence d'au moins une minuscule
    // [a-z] = n'importe quelle lettre minuscule
    elseif (!preg_match('/[a-z]/', $password)) {
        $error = "Le mot de passe doit contenir au moins une minuscule.";
    } 
    // Vérification présence d'au moins un chiffre
    // [0-9] = n'importe quel chiffre de 0 à 9
    elseif (!preg_match('/[0-9]/', $password)) {
        $error = "Le mot de passe doit contenir au moins un chiffre.";
    } 
    // Vérification présence d'au moins un caractère spécial
    // [\W_] = tout caractère non-alphanumérique (ponctuation, symboles)
    elseif (!preg_match('/[\W_]/', $password)) {
        $error = "Le mot de passe doit contenir au moins un caractère spécial.";
    }
    // Vérification longueur minimale du prénom et nom
    elseif (strlen($prenom) < 2 || strlen($nom) < 2) {
        $error = "Le prénom et le nom doivent contenir au moins 2 caractères.";
    } 
    // Vérification que prénom et nom ne contiennent que des lettres/tirets
    // [a-zA-ZÀ-ÿ-] = lettres (avec accents) et tirets seulement
    elseif (!preg_match('/^[a-zA-ZÀ-ÿ-]+$/', $prenom) || !preg_match('/^[a-zA-ZÀ-ÿ-]+$/', $nom)) {
        $error = "Le prénom et le nom ne doivent contenir que des lettres ou des tirets.";
    } 
    // Vérification du token CSRF pour éviter les attaques cross-site
    elseif (verifyCsrfToken($_POST['csrf_token']) === false) {
        $error = "Token CSRF invalide. Veuillez réessayer.";
    }
    // Vérification que l'email a un format valide
    // filter_var avec FILTER_VALIDATE_EMAIL vérifie le format d'email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "L'adresse e-mail n'est pas valide.";
    } else {
        // Vérification de l'unicité de l'email en base de données
        // Un utilisateur ne peut pas avoir le même email qu'un autre
        try {
            // Recherche d'un utilisateur avec cet email
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            // Si au moins un utilisateur existe avec cet email
            if ($stmt->rowCount() > 0) {
                $error = "Un compte avec cette adresse e-mail existe déjà.";
            }
        } catch (PDOException $e) {
            // Gestion des erreurs de base de données
            $error = "Erreur lors de la vérification de l'email : " . $e->getMessage();
        }
    }
    
    // Si aucune erreur de validation, proc\u00e9der \u00e0 la cr\u00e9ation du compte\n    
    if (!isset($error)) {        // Hachage s\u00e9curis\u00e9 du mot de passe\n        // password_hash() utilise un algorithme s\u00e9curis\u00e9 (bcrypt par d\u00e9faut)\n        // Le mot de passe n'est JAMAIS stock\u00e9 en clair dans la base\n        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);        // Insertion du nouvel utilisateur dans la base de donn\u00e9es\n        
        try {            // Requ\u00eate pr\u00e9par\u00e9e pour ins\u00e9rer les donn\u00e9es de fa\u00e7on s\u00e9curis\u00e9e\n            
            $stmt = $pdo->prepare("INSERT INTO users (firstname, lastname, email, password) VALUES (?, ?, ?, ?)");            
            $stmt->execute([$prenom, $nom, $email, $hashedPassword]);// Inscription r\u00e9ussie : redirection vers la page de connexion\n            
            header('Location: login.php');        
            exit(); // Important : arr\u00eater le script apr\u00e8s redirection\n        
            } catch (PDOException $e) {           // Gestion des erreurs d'insertion en base\n            
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