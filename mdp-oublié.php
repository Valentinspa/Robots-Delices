<!--
    FICHIER : mdp-oublié.php
    RÔLE : Page de réinitialisation de mot de passe (Interface uniquement)
    
    Cette page présente un formulaire pour la récupération de mot de passe oublié.
    ATTENTION : Il s'agit actuellement d'une interface utilisateur uniquement.
    Le traitement backend (/reset-password) n'est pas encore implémenté.
    
    CONCEPTS UTILISÉS :
    - Structure HTML sémantique
    - Formulaire de récupération avec validation côté client
    - Design responsive avec CSS
    - Integration avec le header commun du site
    
    FONCTIONNALITÉS ACTUELLES :
    - Interface utilisateur complète
    - Validation HTML5 (required sur l'email)
    - Lien de retour vers la connexion
    
    FONCTIONNALITÉS À IMPLÉMENTER :
    - Script PHP de traitement (/reset-password)
    - Génération et envoi d'email de réinitialisation
    - Système de tokens temporaires
    - Page de saisie du nouveau mot de passe
    
    SÉCURITÉ À PRÉVOIR (pour l'implémentation future) :
    - Protection CSRF sur le formulaire
    - Limitation du taux de tentatives (rate limiting)
    - Tokens de réinitialisation avec expiration
    - Validation de l'existence de l'email avant envoi
-->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Inclusion des feuilles de style -->
    <link rel="stylesheet" href="./navbar.css">      <!-- Styles de navigation -->
    <link rel="stylesheet" href="./auth-pages.css">  <!-- Styles des pages d'authentification -->
    <link rel="stylesheet" href="footer.css">        <!-- Styles du pied de page -->
    <title>Mot de passe oublié - Robots-Délices</title>
</head>
<body class="password-reset-page">
    <?php
    // Inclusion de l'en-tête de navigation commun
    require_once 'header.php';
    ?>
    
    <main>
        <div id="section-container">
            <!-- SECTION GAUCHE : Présentation et logo -->
            <div id="login-container">
                <img alt="Logo Robots-Délices" id="logo" src="./img/logo_robots_delices.png"/>
                <p>Récupérez l'accès à votre compte et retrouvez vos recettes favorites</p>
            </div>
            
            <!-- SECTION DROITE : Formulaire de réinitialisation -->
            <div class="right-section">
                <div id="form-container">
                    <h2>Mot de passe oublié</h2>
                    <p class="form-description">
                        Entrez votre adresse e-mail pour recevoir un lien de réinitialisation de votre mot de passe.
                    </p>
                    
                    <!-- FORMULAIRE DE RÉCUPÉRATION -->
                    <!-- ATTENTION : L'action pointe vers /reset-password qui n'est pas encore implémenté -->
                    <form id="reset-password-form" action="/reset-password" method="POST">
                        <div>
                            <label for="email">Adresse e-mail :</label>
                            <!-- Champ email avec validation HTML5 -->
                            <input type="email" id="email" name="email" required 
                                   placeholder="votre.email@exemple.com">
                        </div>
                        
                        <!-- NOTE : Ce bouton enverra vers une page non implémentée -->
                        <button type="submit">Envoyer le lien de réinitialisation</button>
                    </form>
                    
                    <!-- LIEN DE RETOUR -->
                    <!-- Permet à l'utilisateur de revenir à la page de connexion -->
                    <p>
                        Vous vous souvenez de votre mot de passe ? 
                        <a href="./login.php">Se connecter</a>
                    </p>
                </div>
            </div>
        </div>
    </main>
    
    <!-- PIED DE PAGE -->
    <footer>
        <p>© 2025 Robots-Délices. Tous droits réservés.</p>
    </footer>
</body>
</html>