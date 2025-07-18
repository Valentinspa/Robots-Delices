<?php
require_once "csrf.php";
require_once 'connexionBDD.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Redirige vers la page d'accueil si l'utilisateur n'est pas connecté
    exit();
}


$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $nom = htmlspecialchars(trim($_POST['nom']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm-password']);
    $error = null;

    // Validation des données
    if(empty($prenom) || $prenom === $user['firstname']) {
        $prenom = $user['firstname'];
    }
    
    if(empty($nom) || $nom === $user['lastname']) {
        $nom = $user['lastname'];
    }

    if (strlen($prenom) < 2 || strlen($nom) < 2) {
        $error = "Le prénom et le nom doivent contenir au moins 2 caractères.";
    } elseif (!preg_match('/^[a-zA-ZÀ-ÿ-]+$/', $prenom) || !preg_match('/^[a-zA-ZÀ-ÿ-]+$/', $nom)) {
        $error = "Le prénom et le nom ne doivent contenir que des lettres ou des tirets.";
    } 

    if(empty($email) || $email === $user['email']) {
        $email = $user['email'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
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

    if(empty($password)){
        $password = $user['password'];
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
    }

    if (verifyCsrfToken($_POST['csrf_token']) === false) {
        $error = "Token CSRF invalide. Veuillez réessayer.";
    }

     if (!isset($error)) {
        // Hachage du mot de passe
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insertion dans la base de données
        try {
            $stmt = $pdo->prepare("update users set firstname = ?, lastname = ?, email = ?, password = ? where id = ?");
            $stmt->execute([$prenom, $nom, $email, $hashedPassword, $_SESSION['user_id']]);
            header('Location: /'); // Redirection vers la page de connexion après l'inscription réussie
            exit();
        } catch (PDOException $e) {
            $error = "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
}

$token = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./navbar.css">
    <link rel="stylesheet" href="./auth-pages.css">
    <link rel="stylesheet" href="footer.css">
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
                <p>Voici notre communauté de passionnés de cuisine et partagez vos meilleures recettes</p>
            </div>
            
            <!-- Section droite avec formulaire de mise a jour du compte -->
            <div class="right-section">
                <div id="form-container">
                    <form id="update-form" action="update.php" method="POST">
                        <div>
                            <label for="prenom">Prénom :</label>
                            <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['firstname']); ?>">
                        </div>
                        <div>
                            <label for="nom">Nom :</label>
                            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user['lastname']); ?>">
                        </div>
                        <div>
                            <label for="email">Email :</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                        </div>
                        <div>
                            <label for="password">Mot de passe :</label>
                            <input type="password" id="password" name="password">
                        </div>
                        <div>
                            <label for="confirm-password">Confirmer le mot de passe :</label>
                            <input type="password" id="confirm-password" name="confirm-password">
                        </div>
                        <?php if (isset($error)): ?>
                            <p class="error-message"><?php echo $error; ?></p>
                        <?php endif; ?>
                        <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
                        <button type="submit">Mettre a jour</button>
                    </form>
                    <form action="./delete.php" method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                        <p>
                            Vous Souhaitez nous quitter ? <button type="submit" class="delete-account-btn">Supprimer mon compte</button>
                        </p>
                        
                        </form>
                    
                    
                </div>
            </div>
        </div>
    </main>
    <footer>
        <p>© 2025 Robots-Délices. Tous droits réservés.</p>
    </footer>
</body>
</html>