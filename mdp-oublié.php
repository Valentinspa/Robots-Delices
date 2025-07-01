<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./navbar.css">
    <link rel="stylesheet" href="./auth-pages.css">
    <link rel="stylesheet" href="footer.css">
    <title>Mot de passe oublié - Robots-Délices</title>
</head>
<body class="password-reset-page">
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
            
            <!-- Section droite avec formulaire de réinitialisation -->
            <div class="right-section">
                <div id="form-container">
                    <h2>Mot de passe oublié</h2>
                    <p class="form-description">Entrez votre adresse e-mail pour réinitialiser votre mot de passe.</p>
                    
                    <form id="reset-password-form" action="/reset-password" method="POST">
                        <div>
                            <label for="email">Email :</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <button type="submit">Réinitialiser le mot de passe</button>
                    </form>
                    
                    <p>Vous vous souvenez de votre mot de passe ? <a href="./login.php">Se connecter</a></p>
                </div>
            </div>
        </div>
    </main>
    <footer>
        <p>© 2025 Robots-Délices. Tous droits réservés.</p>
    </footer>
</body>
</html>