 <header>
        <div id="section">
            <div id="header-title">
                <a href="./index.php">
                    <img id="logo" alt="Logo Robots-Délices" src="./img/logo_robots_delices.png">
                </a>
            </div>
            <input id="menu-toggle" type="checkbox" />
            <label class="burger-menu" for="menu-toggle">
                <span class="burger-line"></span>
                <span class="burger-line"></span>
                <span class="burger-line"></span>
            </label>
            <div id="nav-container" class="nav-container">
                <ul class="nav-menu">
                    <li class="li"><a href="./index.php">Accueil</a></li>
                    <li class="li"><a href="./index.php#recettes">Recettes</a></li>
                    <li class="li"><a href="./index.php#categories">Catégories</a></li>
                    <?php
                    if (isset($_SESSION['user_id'])) {
                        echo '<li class="li"><a href="./favoris.php">Mes favoris</a></li>
                        <li class="li red-btn"><a href="./ajout-recette.php" class="ajouter-btn">+ Ajouter une recette</a></li>
                        <li class="li"><a href="./update.php">Mettre à jour mon compte</a></li>
                        <li class="li red-btn"><a href="./logout.php" class="deconnexion-btn">Déconnexion</a></li>';
                    } else {
                        echo '<li class="li red-btn"><a href="./login.php" class="connexion-btn">Connexion</a></li>';
                    }
                    ?>
                </ul>
            </div>
            <div class="menu-overlay"></div>
            
        </div>
    </header>