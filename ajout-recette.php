<?php
// Page d'ajout de recette - Permet aux utilisateurs connectés d'ajouter une nouvelle recette
// Inclut validation des données, upload d'image et protection CSRF

session_start(); // Démarrage de la session
require_once 'connexionBDD.php'; // Connexion à la base de données
require_once "csrf.php"; // Protection contre les attaques CSRF

// Récupération de toutes les catégories disponibles pour le formulaire
$categories = $pdo->query("SELECT * FROM category")->fetchAll();
// var_dump($categories); // Ligne de debug commentée

// Traitement du formulaire d'ajout de recette (méthode POST)
if($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    // Récupération et sécurisation des données du formulaire
    // htmlspecialchars() protège contre les attaques XSS
    // ?? '' définit une valeur par défaut si le champ est vide
    $titre = htmlspecialchars($_POST['titre']) ?? ''; // Titre de la recette
    $categorie = htmlspecialchars($_POST['categorie']) ?? ''; // ID de catégorie
    $difficulte = htmlspecialchars($_POST['difficulte']) ?? 'facile'; // Niveau de difficulté
    $temps_preparation = htmlspecialchars($_POST['temps-preparation']) ?? ''; // Temps en minutes
    $portions = htmlspecialchars($_POST['portions']) ?? 1; // Nombre de portions
    $description = htmlspecialchars($_POST['description']) ?? ''; // Description courte
    $ingredients = htmlspecialchars($_POST['ingredients']) ?? ""; // Liste des ingrédients
    $instructions = htmlspecialchars($_POST['instructions']) ?? ""; // Étapes de préparation
    
    // Génération du slug (URL-friendly) à partir du titre
    // Convertit en minuscules et remplace caractères spéciaux par _
    $slug = strtolower(trim(preg_replace("/[^A-Za-z0-9à-üÀ-Ü-]+/", '_', $titre)));

    // Vérification de l'unicité du slug et génération d'un suffixe si nécessaire
    // Compte combien de recettes ont un slug similaire
    $totalSameRecipes = $pdo->prepare("SELECT COUNT(*) FROM recipes WHERE slug LIKE ?");
    $totalSameRecipes->execute([$slug . '%']); // Recherche slugs commençant par notre slug
    
    $count = $totalSameRecipes->fetchColumn(); // Nombre de slugs similaires trouvés
    
    // Ajoute un numéro pour rendre le slug unique (ex: tarte-pommes_2)
    $slug .= '_' . ($count + 1);
    
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