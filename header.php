 <!--
    FICHIER : header.php
    RÔLE : Composant d'en-tête réutilisable pour toutes les pages du site
    
    Ce fichier contient la structure HTML de la barre de navigation du site Robots-Délices.
    Il génère dynamiquement les liens de navigation en fonction de l'état de connexion de l'utilisateur.
    
    CONCEPTS PHP UTILISÉS :
    - Variables de session ($_SESSION) : pour vérifier si un utilisateur est connecté
    - Structures conditionnelles (if/else) : pour afficher différents menus selon l'état de connexion
    - Echo : pour générer du HTML dynamiquement
    
    SÉCURITÉ :
    - Le fichier vérifie l'existence de la variable de session 'user_id' pour déterminer si l'utilisateur est connecté
    - Aucune donnée utilisateur n'est affichée directement, ce qui évite les risques d'injection
-->

<header>
        <div id="section">
            <!-- Section du logo - Cliquable pour retourner à l'accueil -->
            <div id="header-title">
                <a href="./index.php">
                    <img id="logo" alt="Logo Robots-Délices" src="./img/logo_robots_delices.png">
                </a>
            </div>
            
            <!-- Menu hamburger responsive - Case à cocher cachée pour gérer l'ouverture/fermeture -->
            <input id="menu-toggle" type="checkbox" />
            <label class="burger-menu" for="menu-toggle">
                <span class="burger-line"></span>
                <span class="burger-line"></span>
                <span class="burger-line"></span>
            </label>
            
            <!-- Container de la navigation principale -->
            <div id="nav-container" class="nav-container">
                <ul class="nav-menu">
                    <!-- Liens de navigation communs à tous les utilisateurs -->
                    <li class="li"><a href="./index.php">Accueil</a></li>
                    <li class="li"><a href="./index.php#recettes">Recettes</a></li>
                    <li class="li"><a href="./index.php#categories">Catégories</a></li>
                    
                    <?php
                    // LOGIQUE DE NAVIGATION CONDITIONNELLE
                    // Vérification si l'utilisateur est connecté en vérifiant la présence de 'user_id' dans la session
                    if (isset($_SESSION['user_id'])) {
                        // Menu pour les utilisateurs CONNECTÉS
                        // Ces liens sont uniquement disponibles pour les utilisateurs authentifiés
                        echo '<li class="li"><a href="./favoris.php">Mes favoris</a></li>
                        <li class="li red-btn"><a href="./ajout-recette.php" class="ajouter-btn">+ Ajouter une recette</a></li>
                        <li class="li"><a href="./update.php">Mettre à jour mon compte</a></li>
                        <li class="li red-btn"><a href="./logout.php" class="deconnexion-btn">Déconnexion</a></li>';
                    } else {
                        // Menu pour les utilisateurs NON CONNECTÉS
                        // Seul le lien de connexion est affiché
                        echo '<li class="li red-btn"><a href="./login.php" class="connexion-btn">Connexion</a></li>';
                    }
                    ?>
                </ul>
            </div>
            
            <!-- Overlay pour fermer le menu en cliquant à côté (mobile) -->
            <div class="menu-overlay"></div>
            
        </div>
    </header>