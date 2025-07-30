// ==== SYSTÈME DE FAVORIS ====
// Ce fichier gère l'ajout et la suppression de recettes dans les favoris

// 1. SÉLECTION DES BOUTONS
// Récupère tous les boutons avec la classe "bouton-favoris" sur la page
const favoris = document.querySelectorAll(".bouton-favoris");

// 2. AJOUT D'UN ÉCOUTEUR D'ÉVÉNEMENT À CHAQUE BOUTON
// Pour chaque bouton favoris trouvé, on ajoute un écouteur de clic
favoris.forEach((bouton) => {
  bouton.addEventListener("click", function (e) {
    // Empêche le comportement par défaut du lien (ne redirige pas)
    e.preventDefault();
    
    // 3. RÉCUPÉRATION DES DONNÉES
    // Récupère l'ID de la recette depuis l'attribut "data-id" du bouton
    const recetteId = this.getAttribute("data-id");
    
    // Crée un objet FormData pour envoyer les données au serveur
    const data = new FormData();
    data.append("recette_id", recetteId); // Ajoute l'ID de la recette
    data.append("action", "toggle_favorites"); // Indique qu'on veut basculer les favoris

    // 4. ENVOI DE LA REQUÊTE AU SERVEUR
    // Utilise fetch() pour envoyer une requête AJAX au fichier PHP
    fetch("api-favoris.php", {
      method: "POST", // Méthode HTTP POST pour envoyer des données
      body: data, // Les données à envoyer
      headers: {
        // Indique au serveur que c'est une requête AJAX
        "X-Requested-With": "XMLHttpRequest",
        // Indique qu'on attend une réponse JSON
        Accept: "application/json",
      },
    })
      // 5. TRAITEMENT DE LA RÉPONSE
      // Convertit la réponse en JSON
      .then((response) => response.json())
      .then((data) => {
        // Récupère le texte à l'intérieur du bouton (si il y en a un)
        const spanText = this.querySelector("span");
        
        // 6. MISE À JOUR DE L'INTERFACE selon la réponse du serveur
        switch (data.status) {
          case "added":
            // Si la recette a été ajoutée aux favoris : cœur plein
            this.innerHTML = "❤️";
            break;
          case "removed":
            // Si la recette a été retirée des favoris : cœur vide
            this.innerHTML = "🤍";
            break;
          case "error":
            // En cas d'erreur générale
            alert("Erreur lors de l'ajout aux favoris.");
            break;
          case "not_logged_in":
            // Si l'utilisateur n'est pas connecté
            alert("Veuillez vous connecter pour ajouter aux favoris.");
            break;
          default:
            // Pour tout autre cas non prévu
            console.error("Statut inconnu:", data.status);
            alert("Une erreur inconnue est survenue.");
        }
        
        // 7. RESTAURATION DU TEXTE
        // Si il y avait du texte dans le bouton, on le remet
        if (spanText) {
          this.append(spanText);
        }
      })
      // 8. GESTION DES ERREURS DE CONNEXION
      // Si la requête échoue complètement (problème réseau, serveur indisponible...)
      .catch((error) => console.error("Erreur:", error));
  });
});
