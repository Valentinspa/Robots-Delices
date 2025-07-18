<?php
session_start();
// Include database connection
require_once 'connexionBDD.php'; 
require_once "csrf.php"; // Include CSRF protection

$categories = $pdo->query("SELECT * FROM category")->fetchAll();
// var_dump($categories);

if($_SERVER['REQUEST_METHOD'] === 'POST') 
{
// <script>alert("BANANA HACK")</script>
    // Initialize variables
    $titre = htmlspecialchars($_POST['titre']) ?? '';
    $categorie = htmlspecialchars($_POST['categorie']) ?? '';
    $difficulte = htmlspecialchars($_POST['difficulte']) ?? 'facile';
    $temps_preparation = htmlspecialchars($_POST['temps-preparation']) ?? '';
    $portions = htmlspecialchars($_POST['portions']) ?? 1;
    $description = htmlspecialchars($_POST['description']) ?? '';
    $ingredients = htmlspecialchars($_POST['ingredients']) ?? "";
    $instructions = htmlspecialchars($_POST['instructions']) ?? "";
    $slug = strtolower(trim(preg_replace("/[^A-Za-z0-9à-üÀ-Ü-]+/", '_', $titre)));

    $totalSameRecipes = $pdo->prepare("SELECT COUNT(*) FROM recipes WHERE slug LIKE ?");
    $totalSameRecipes->execute([$slug . '%']);

    $count = $totalSameRecipes->fetchColumn();
    
    $slug .= '_' . ($count + 1); // Append a number to make the slug unique
    
    // Handle file upload
    $photo_path = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "img/";
        $photo_path = $target_dir . basename($_FILES["photo"]["name"]);
        move_uploaded_file($_FILES["photo"]["tmp_name"], $photo_path);
    }
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        $error = "Invalid CSRF token.";
    }  else{
        // Insert recipe into database
        $stmt = $pdo->prepare("INSERT INTO recipes (slug, title, description, ingredients, instructions, cooking_time, number_persons, difficulty, category_id, photo, image_caption) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$slug, $titre, $description, $ingredients, $instructions, $temps_preparation, $portions, $difficulte, $categorie, $photo_path, 'Photo de la recette']);
    
        // Redirect to the recipe page
        header("Location: /recette.php?recette=" . urlencode($slug));
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./navbar.css">
    <link rel="stylesheet" href="./ajout-recette.css">
    <link rel="stylesheet" href="./footer.css">
    <title>Ajouter une Recette - Robots-Délices</title>
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
                <span>Ajouter une recette</span>
            </div>

            <!-- Hero Section -->
            <section class="add-recipe-hero">
                <h1>Partagez votre Recette</h1>
                <p>Ajoutez votre délicieuse recette à notre collection et inspirez d'autres passionnés de cuisine</p>
            </section>

            <!-- Form Container -->
            <div class="form-container">
                <form id="add-recipe-form" action="/ajout-recette.php" method="POST" enctype="multipart/form-data">
                    <div class="form-grid">
                        <!-- Informations de base -->
                        <div class="form-section">
                            <h3>Informations de base</h3>
                            
                            <div class="form-group">
                                <label for="titre">Titre de la recette</label>
                                <input type="text" id="titre" name="titre" required placeholder="Ex: Tarte aux pommes de grand-mère">
                            </div>

                            <div class="form-group">
                                <label for="categorie">Catégorie</label>
                                <select id="categorie" name="categorie" required>
                                    <option value="">Choisir une catégorie</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id']; ?>"><?= $category['category_logo'] . ' '. $category['category_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="difficulte">Difficulté</label>
                                <select id="difficulte" name="difficulte">
                                    <option value="facile">Facile</option>
                                    <option value="moyen">Moyen</option>
                                    <option value="difficile">Difficile</option>
                                </select>
                            </div>
                        </div>

                        <!-- Détails pratiques -->
                        <div class="form-section">
                            <h3>Détails pratiques</h3>
                            
                            <div class="form-group">
                                <label for="temps-preparation">Temps de préparation</label>
                                <input type="text" id="temps-preparation" name="temps-preparation" placeholder="Ex: 30 min">
                            </div>

                            <div class="form-group">
                                <label for="portions">Nombre de portions</label>
                                <input type="number" id="portions" name="portions" min="1" max="20" placeholder="Ex: 6">
                            </div>

                            <div class="form-group">
                                <label for="photo">Photo de la recette</label>
                                <input type="file" id="photo" name="photo" accept="image/*">
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="form-section full-width">
                            <h3>Description</h3>
                            <div class="form-group">
                                <label for="description">Description courte</label>
                                <textarea id="description" name="description" placeholder="Décrivez votre recette en quelques mots..."></textarea>
                            </div>
                        </div>

                        <!-- Ingrédients -->
                        <div class="form-section full-width">
                            <h3>Ingrédients</h3>
                            <div class="dynamic-section">
                                <div id="ingredients-container">
                                    <div class="ingredient-item">
                                        <input type="text" name="ingredients" placeholder="Séparez vos ingrédients d'une virgule. Ex: 500g de farine, 1kg de patate" required>
                                        <button type="button" class="btn-remove">✕</button>
                                    </div>
                                </div>
                                <button type="button" class="btn-add">+ Ajouter un ingrédient</button>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div class="form-section full-width">
                            <h3>Instructions</h3>
                            <div class="dynamic-section">
                                <div id="instructions-container">
                                    <div class="instruction-item">
                                        <textarea name="instructions" placeholder="Séparez chaque étape d'un saut à la ligne. Étape 1: Décrivez la première étape..." required></textarea>
                                        <button type="button" class="btn-remove">✕</button>
                                    </div>
                                </div>
                                <button type="button" class="btn-add">+ Ajouter une étape</button>
                            </div>
                        </div>
                    </div>
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Publier ma recette</button>
                        <a href="./index.php" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <p>© 2025 Robots-Délices. Tous droits réservés.</p>
    </footer>
</body>
</html>