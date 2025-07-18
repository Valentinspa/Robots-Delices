<?php
session_start();

require_once 'connexionBDD.php';


// Si l'utilisateur est connecté, on vérifie si les recettes populaires sont dans ses favoris
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    // Récupérer les recettes favorites de l'utilisateur
    $stmt = $pdo->prepare("SELECT recipe_id FROM favorites WHERE user_id = ?");
    $stmt->execute([$userId]);
    $favorites = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
} else {
    $favorites = [];
}
// récupérer les recettes populaires
$stmt = $pdo->prepare("SELECT recipes.* FROM recipes WHERE popular = 1 ORDER BY created_at DESC LIMIT 3");
$stmt->execute();
$recipes = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./navbar.css">
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="footer.css">
    <script src="api-favoris.js"defer></script>
    <title>Robots-Délices</title>
</head>
<body>
    <?php
    require_once 'header.php';
    ?>
    <main>
        <div id="section-container">
            <section id="accueil">
                <div>
                    <h1>Bienvenue sur Robots-Délices</h1>
                    <p>Découvrez et partagez des recettes délicieuses avec notre communauté passionnée</p>
                </div>
                <div id="search-bar">
                    <input type="text" name="search" placeholder="Rechercher une recette, un ingrédient..."/>
                    <button type="submit">Rechercher</button>
                </div>
            </section>
            <section id="categories">
                <div id="categories-title">
                    <h2>Explorer par Catégorie</h2>
                    <p>Trouvez l'inspiration pour votre prochain repas</p>
                </div>
                <div id="categories-grid">
                    <div class="categories-card">
                        <div class="categories-icons">🥗</div>
                        <h3>Entrées</h3>
                        <p>Salades et apéritifs</p>
                    </div>
                    <div class="categories-card">
                        <div class="categories-icons">🍖</div>
                        <h3>Plats</h3>
                        <p>Viandes et poissons</p>
                    </div>
                    <div class="categories-card">
                        <div class="categories-icons">🍰</div>
                        <h3>Desserts</h3>
                        <p>Douceurs sucrées</p>
                    </div>
                    <div class="categories-card">
                        <div class="categories-icons">🥤</div>
                        <h3>Boissons</h3>
                        <p>Cocktails et smoothies</p>
                    </div>
                    <div class="categories-card">
                        <div class="categories-icons">🌱</div>
                        <h3>Végétarien</h3>
                        <p>Sans viande</p>
                    </div>
                    <div class="categories-card">
                        <div class="categories-icons">⚡</div>
                        <h3>Rapide</h3>
                        <p> Moins de 30 minutes</p>
                    </div>  
                </div>
            </section>
            <section id="recettes">
                <div id="recettes-title">
                    <h2>Recettes Populaires</h2>
                    <p>Les favoris de notre communauté</p>
                </div>
                <div id="recettes-grid">
                    <?php foreach ($recipes as $recipe): ?>
                        <div class="recette-card">
                            <div class="recette-image">
                                <a href="./recette.php?recette=<?php echo $recipe['slug']; ?>"><img src="<?php echo $recipe['photo']; ?>" alt="<?php echo $recipe['title']; ?>" /></a>
                            </div>
                            <div class="recettes-content">
                                <div class="recette-summarize">
                                    <h3><?php echo $recipe['title']; ?></h3>
                                    <p><?php echo $recipe['description']; ?></p>
                                    <span class="bouton-favoris" data-id="<?php echo $recipe['id']; ?>">
                                        <?php echo in_array($recipe['id'], $favorites) ? '❤️' : '🤍'; ?>
                                    </span>
                                </div>
                                <div class="recette-meta">
                                    <span>⏱️ <?php echo $recipe['cooking_time']; ?></span>
                                    <span>👥 <?php echo $recipe['number_persons']; ?> pers</span>
                                    <span>⭐ 4.5</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </main>
    <footer>
        <p>© 2025 Robots-Délices. Tous droits réservés.</p>
    </footer>
</body>
</html>