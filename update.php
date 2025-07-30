<?php
/*
    FICHIER : update.php
    RÔLE : Page de mise à jour du profil utilisateur
    
    Cette page permet à un utilisateur connecté de modifier ses informations personnelles :
    prénom, nom, email et mot de passe. Elle inclut une validation complète des données
    et la possibilité de supprimer son compte.
    
    CONCEPTS PHP UTILISÉS :
    - Sessions PHP : pour l'authentification et maintenir l'état utilisateur
    - PDO (PHP Data Objects) : pour les interactions sécurisées avec la base de données
    - Validation de formulaire : vérification des données côté serveur
    - Hachage de mot de passe : password_hash() pour sécuriser les mots de passe
    - Protection CSRF : pour éviter les modifications non autorisées
    - Expressions régulières : pour valider le format des données
    
    SÉCURITÉ IMPLÉMENTÉE :
    - Authentification obligatoire (vérification session)
    - Protection CSRF sur toutes les modifications
    - Validation stricte des données d'entrée
    - Hachage sécurisé des mots de passe
    - Requêtes préparées pour éviter l'injection SQL
    - Échappement HTML pour éviter l'injection XSS
*/

// Inclusion des fichiers de dépendances
require_once "csrf.php";         // Fonctions de protection CSRF
require_once 'connexionBDD.php'; // Connexion à la base de données

// Démarrage de la session pour accéder aux données utilisateur
session_start();

// CONTRÔLE D'ACCÈS : Seuls les utilisateurs connectés peuvent accéder à cette page
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Redirige vers la page d'accueil si l'utilisateur n'est pas connecté
    exit();
}

// RÉCUPÉRATION DES DONNÉES UTILISATEUR ACTUELLES
// Requête pour obtenir les informations actuelles de l'utilisateur connecté
// Ces données serviront à pré-remplir le formulaire
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// TRAITEMENT DU FORMULAIRE DE MISE À JOUR
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // RÉCUPÉRATION ET NETTOYAGE DES DONNÉES DU FORMULAIRE
    // htmlspecialchars() convertit les caractères spéciaux en entités HTML pour éviter l'injection XSS
    // trim() supprime les espaces en début et fin de chaîne
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $nom = htmlspecialchars(trim($_POST['nom']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = trim($_POST['password']);                    // Pas d'échappement HTML pour le mot de passe
    $confirmPassword = trim($_POST['confirm-password']);     // Il sera haché
    $error = null;  // Variable pour stocker les erreurs de validation

    // VALIDATION DU PRÉNOM ET DU NOM
    // Si le champ est vide ou identique à la valeur actuelle, on garde l'ancienne valeur
    if(empty($prenom) || $prenom === $user['firstname']) {
        $prenom = $user['firstname'];
    }
    
    if(empty($nom) || $nom === $user['lastname']) {
        $nom = $user['lastname'];
    }

    // Validation de la longueur minimale (2 caractères)
    if (strlen($prenom) < 2 || strlen($nom) < 2) {
        $error = "Le prénom et le nom doivent contenir au moins 2 caractères.";
    } 
    // Validation du format avec expression régulière
    // ^[a-zA-ZÀ-ÿ-]+$ accepte uniquement les lettres (avec accents) et tirets
    elseif (!preg_match('/^[a-zA-ZÀ-ÿ-]+$/', $prenom) || !preg_match('/^[a-zA-ZÀ-ÿ-]+$/', $nom)) {
        $error = "Le prénom et le nom ne doivent contenir que des lettres ou des tirets.";
    } 

    // VALIDATION DE L'EMAIL
    if(empty($email) || $email === $user['email']) {
        $email = $user['email'];  // Garde l'email actuel si pas de changement
    } 
    // Validation du format email avec le filtre PHP intégré
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "L'adresse e-mail n'est pas valide.";
    } 
    else {
        // VÉRIFICATION DE L'UNICITÉ DE L'EMAIL
        // On vérifie que le nouvel email n'est pas déjà utilisé par un autre compte
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $error = "Un compte avec cette adresse e-mail existe déjà.";
            }
        } catch (PDOException $e) {
            // Gestion des erreurs de base de données
            $error = "Erreur lors de la vérification de l'email : " . $e->getMessage();
        }
    }

    // VALIDATION DU MOT DE PASSE
    if(empty($password)){
        // Si pas de nouveau mot de passe, on garde l'ancien (déjà haché)
        $password = $user['password'];
    } 
    // VÉRIFICATION DE LA CORRESPONDANCE DES MOTS DE PASSE
    elseif ($password !== $confirmPassword) {
        $error = "Les mots de passe ne correspondent pas.";
    } 
    // VALIDATION DE LA COMPLEXITÉ DU MOT DE PASSE
    // Longueur minimale : 8 caractères
    elseif (strlen($password) < 8) {
        $error = "Le mot de passe doit contenir au moins 8 caractères.";
    } 
    // Au moins une majuscule (A-Z)
    elseif (!preg_match('/[A-Z]/', $password)) {
        $error = "Le mot de passe doit contenir au moins une majuscule.";
    } 
    // Au moins une minuscule (a-z)
    elseif (!preg_match('/[a-z]/', $password)) {
        $error = "Le mot de passe doit contenir au moins une minuscule.";
    } 
    // Au moins un chiffre (0-9)
    elseif (!preg_match('/[0-9]/', $password)) {
        $error = "Le mot de passe doit contenir au moins un chiffre.";
    } 
    // Au moins un caractère spécial (\W_ = tout sauf lettres et chiffres)
    elseif (!preg_match('/[\W_]/', $password)) {
        $error = "Le mot de passe doit contenir au moins un caractère spécial.";
    }

    // VALIDATION DU TOKEN CSRF
    // Vérification obligatoire pour éviter les attaques CSRF
    if (verifyCsrfToken($_POST['csrf_token']) === false) {
        $error = "Token CSRF invalide. Veuillez réessayer.";
    }

    // MISE À JOUR EN BASE DE DONNÉES
    // Si aucune erreur n'a été détectée, on procède à la mise à jour
    if (!isset($error)) {
        // Hachage du nouveau mot de passe (si fourni)
        // PASSWORD_DEFAULT utilise l'algorithme de hachage le plus sécurisé disponible
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // EXÉCUTION DE LA REQUÊTE DE MISE À JOUR
        try {
            // Requête préparée pour éviter l'injection SQL
            $stmt = $pdo->prepare("update users set firstname = ?, lastname = ?, email = ?, password = ? where id = ?");
            $stmt->execute([$prenom, $nom, $email, $hashedPassword, $_SESSION['user_id']]);
            
            // Redirection vers la page d'accueil après mise à jour réussie
            header('Location: /'); 
            exit();
        } catch (PDOException $e) {
            // Gestion des erreurs de base de données
            $error = "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    }
}

// GÉNÉRATION DU TOKEN CSRF POUR LE FORMULAIRE
// Ce token sera inclus dans le formulaire HTML pour valider les soumissions
$token = generateCsrfToken();
?>
<!-- 
    SECTION HTML : Interface utilisateur pour la mise à jour du profil
    
    Cette partie génère l'interface graphique permettant à l'utilisateur de :
    - Modifier ses informations personnelles (prénom, nom, email)
    - Changer son mot de passe
    - Supprimer son compte
    
    Le formulaire inclut :
    - Pré-remplissage avec les données actuelles
    - Protection CSRF via un champ caché
    - Validation côté client (HTML5) et serveur (PHP)
    - Affichage des erreurs de validation
-->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Inclusion des feuilles de style -->
    <link rel="stylesheet" href="./navbar.css">      <!-- Styles pour la navigation -->
    <link rel="stylesheet" href="./auth-pages.css">  <!-- Styles pour les pages d'authentification -->
    <link rel="stylesheet" href="footer.css">       <!-- Styles pour le pied de page -->
    <title>Mise à jour du profil - Robots-Délices</title>
</head>
<body class="register-page">
    <?php
    // Inclusion de l'en-tête de navigation
    require_once 'header.php';
    ?>
    <main>
        <div id="section-container">
            <!-- SECTION GAUCHE : Présentation et logo -->
            <div id="login-container">
                <img alt="Logo Robots-Délices" id="logo" src="./img/logo_robots_delices.png"/>
                <p>Mettez à jour vos informations personnelles et gérez votre compte</p>
            </div>
            
            <!-- SECTION DROITE : Formulaires de mise à jour et suppression -->
            <div class="right-section">
                <div id="form-container">
                    <!-- FORMULAIRE PRINCIPAL : Mise à jour des informations -->
                    <form id="update-form" action="update.php" method="POST">
                        <!-- Champ Prénom - Pré-rempli avec la valeur actuelle -->
                        <div>
                            <label for="prenom">Prénom :</label>
                            <input type="text" id="prenom" name="prenom" 
                                   value="<?php echo htmlspecialchars($user['firstname']); ?>">
                        </div>
                        
                        <!-- Champ Nom - Pré-rempli avec la valeur actuelle -->
                        <div>
                            <label for="nom">Nom :</label>
                            <input type="text" id="nom" name="nom" 
                                   value="<?php echo htmlspecialchars($user['lastname']); ?>">
                        </div>
                        
                        <!-- Champ Email - Pré-rempli avec la valeur actuelle -->
                        <div>
                            <label for="email">Email :</label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>">
                        </div>
                        
                        <!-- Champ Mot de passe - Vide par défaut (optionnel) -->
                        <div>
                            <label for="password">Nouveau mot de passe :</label>
                            <input type="password" id="password" name="password"
                                   placeholder="Laissez vide pour ne pas changer">
                        </div>
                        
                        <!-- Confirmation du mot de passe -->
                        <div>
                            <label for="confirm-password">Confirmer le mot de passe :</label>
                            <input type="password" id="confirm-password" name="confirm-password"
                                   placeholder="Confirmez le nouveau mot de passe">
                        </div>
                        
                        <!-- AFFICHAGE DES ERREURS -->
                        <!-- Cette section s'affiche uniquement s'il y a des erreurs de validation -->
                        <?php if (isset($error)): ?>
                            <p class="error-message"><?php echo $error; ?></p>
                        <?php endif; ?>
                        
                        <!-- TOKEN CSRF CACHÉ -->
                        <!-- Ce champ protège contre les attaques CSRF -->
                        <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
                        
                        <button type="submit">Mettre à jour</button>
                    </form>
                    
                    <!-- FORMULAIRE DE SUPPRESSION DE COMPTE -->
                    <!-- Formulaire séparé pour la suppression du compte -->
                    <form action="./delete.php" method="post">
                        <!-- Protection CSRF pour la suppression aussi -->
                        <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                        <p>
                            Vous souhaitez nous quitter ? 
                            <button type="submit" class="delete-account-btn" 
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')">
                                Supprimer mon compte
                            </button>
                        </p>
                    </form>
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