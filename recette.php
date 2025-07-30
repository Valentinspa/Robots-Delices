<?php
// Page de détail d'une recette - Affiche les informations complètes d'une recette
// Récupère la recette via son slug (nom simplifié) dans l'URL

// Démarrage de la session pour vérifier si l'utilisateur est connecté
session_start();
require_once 'connexionBDD.php'; 

// Vérification de la présence du paramètre 'recette' dans l'URL
// $_GET['recette'] contient le slug de la recette (ex: tarte-aux-pommes)
if (!isset($_GET['recette']) || empty($_GET['recette'])) {
    // Si pas de slug, rediriger vers l'accueil
    header('Location: index.php');
    exit();
}

// Vérification de l'état de connexion de l'utilisateur
// Si connecté : récupère son ID pour les favoris
// Si non connecté : utilise 0 comme ID par défaut
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Récupération des détails de la recette depuis la base de données
$slug = $_GET['recette']; // Slug de la recette depuis l'URL

// Requête complexe qui récupère :
// - Toutes les colonnes de la table recipes
// - Le nom et logo de la catégorie (LEFT JOIN)
// - Si la recette est en favoris pour cet utilisateur (sous-requête)
$stmt = $pdo->prepare("SELECT recipes.*, category.category_name, category.category_logo, (recipes.id IN (SELECT recipe_id FROM favorites WHERE user_id = ?) ) as isFavorite FROM recipes LEFT JOIN category ON recipes.category_id = category.id WHERE slug = ?");
$stmt->execute([$userId, $slug]); // Premier ? = userId, deuxième ? = slug
$recipe = $stmt->fetch(); // Récupère la recette ou false si pas trouvée

// Si la recette n'existe pas, rediriger vers l'accueil
if (!$recipe) {
    header('Location: index.php');
    exit();
}

// Traitement des ingrédients : conversion de chaîne en tableau
// Les ingrédients sont stockés comme "ingrédient1,ingrédient2,ingrédient3"
$ingredients = explode(',', $recipe['ingredients']); // Sépare par virgules

// Traitement des instructions de préparation
// Conversion des différents types de retours à la ligne en \n standard
$preparation = str_replace(["\r\n","\r"], "\n", $recipe['instructions']);
$preparation = explode("\n", $preparation); // Sépare chaque ligne
$preparation = array_map('trim', $preparation); // Supprime espaces début/fin de chaque ligne

// Supprime les lignes vides
$preparation = array_filter($preparation, function($step) {
    return !empty($step); // Garde seulement les lignes non-vides
});
$preparation = array_values($preparation); // Réindexe le tableau (0,1,2...)

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./navbar.css">
    <link rel="stylesheet" href="./recettes.css">
    <link rel="stylesheet" href="./footer.css">
    <script src="api-favoris.js"defer></script>
    <script src="share.js"defer></script>
    <title><?php echo $recipe['title']?>- Robots-Délices</title>
</head>
<body>
    <?php
    require_once 'header.php';
    ?>
    <main>
        <div class="container">
            <!-- Breadcrumb -->
            <div class="breadcrumb">
                <a href="./index.php">← Accueil</a>
                <span>/</span>
                <a href="./desserts.php"><?php echo $recipe['category_name']?></a>
                <span>/</span>
                <span><?php echo $recipe['title']?></span>
            </div>

            <!-- Recipe Hero -->
            <section class="recipe-hero">
                <h1><?php echo $recipe['title']?></h1>
                <p><?php echo $recipe['description']?></p>
            </section>

            <!-- Recipe Image -->
            <section class="recipe-image-section">
                <div class="recipe-image">
                    <img src="<?php echo $recipe['photo']?>" alt="<?php echo $recipe['title']?>">
                </div>
                <p class="image-caption"><?php echo $recipe['image_caption']?></p>
            </section>

            <!-- Recipe Meta -->
            <section class="recipe-meta-grid">
                <div class="meta-item">
                    <span class="meta-icon">⏱️</span>
                    <span class="meta-value"><?php echo $recipe['cooking_time']?></span>
                    <span class="meta-label">Temps total</span>
                </div>
                <div class="meta-item">
                    <span class="meta-icon">👥</span>
                    <span class="meta-value"><?php echo $recipe['number_persons']?></span>
                    <span class="meta-label">Portions</span>
                </div>
                <div class="meta-item">
                    <span class="meta-icon">📈</span>
                    <span class="meta-value"><?php echo $recipe['difficulty']?></span>
                    <span class="meta-label">Difficulté</span>
                </div>
                <div class="meta-item">
                    <span class="meta-icon"><?php echo $recipe['category_logo']?></span>
                    <span class="meta-value"><?php echo $recipe['category_name']?></span>
                    <span class="meta-label">Catégorie</span>
                </div>
            </section>

            <!-- Recipe Content -->
            <div class="recipe-content-grid">
                <!-- Ingredients Sidebar -->
                <aside class="ingredients-sidebar">
                    <h2 class="ingredients-title">Ingrédients</h2>
                    <ul class="ingredients-list">
                    <?php foreach ($ingredients as $ingredient): ?>
                    <li class="ingredient-item">
                            <div class="ingredient-checkbox"></div>
                            <span><?php echo $ingredient?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </aside>

                <!-- Instructions Content -->
                <section class="instructions-content">
                    <h2 class="instructions-title">Préparation</h2>
                    <?php foreach ($preparation as $index => $step): ?>
                    <div class="instruction-step">
                        <div class="step-number"><?php echo $index+1 ?></div>
                        <div class="step-content">
                            <?php echo $step?>
                        </div>
                    </div>
                    <?php endforeach; ?>

                </section>
            </div>

            <!-- Recipe Footer -->
            <section class="recipe-footer">
                <div class="rating-section">
                    <div class="rating-stars">★★★★★</div>
                    <p class="rating-text">4.8/5 basé sur 127 avis</p>
                </div>

                <div class="recipe-actions">
                    <a href="#" class="action-btn" onclick="event.preventDefault();window.print()">🖨️ Imprimer</a>
                    <a href="#" class="action-btn share-btn">📤 Partager</a>
                    <a href="#" class="action-btn bouton-favoris" data-id="<?= $recipe['id'] ?>"><?= $recipe["isFavorite"]?"❤️":"🤍" ?><span> Favoris</span></a>
                </div>
            </section>
        </div>
    </main>

    <footer>
        <p>© 2025 Robots-Délices. Tous droits réservés.</p>
    </footer>
</body>
</html>