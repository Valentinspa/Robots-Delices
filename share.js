// ==== SYSTÈME DE PARTAGE ====
// Ce fichier gère le partage de recettes sur les réseaux sociaux ou par lien

// 1. RECHERCHE DU BOUTON DE PARTAGE
// Cherche un bouton avec la classe "share-btn" sur la page
const btn = document.querySelector(".share-btn");

// 2. VÉRIFICATION DE L'EXISTENCE DU BOUTON
// On vérifie que le bouton existe avant d'ajouter des événements
if (btn) {
  // 3. AJOUT DE L'ÉCOUTEUR DE CLIC
  // Le partage doit être déclenché par une action utilisateur (clic)
  btn.addEventListener("click", async (e) => {
    // Empêche le comportement par défaut du lien
    e.preventDefault();
    
    // 4. PRÉPARATION DES DONNÉES À PARTAGER
    // Crée un objet avec les informations de la page à partager
    const shareData = {
      title: document.title, // Le titre de la page (affiché dans l'onglet)
      url: window.location.href, // L'URL complète de la page actuelle
    };
    
    // 5. TENTATIVE DE PARTAGE NATIF
    try {
      // Utilise l'API Web Share (disponible sur mobile principalement)
      // Cette API ouvre le menu de partage du téléphone/navigateur
      await navigator.share(shareData);
    } catch (err) {
      // 6. SOLUTION DE REPLI : COPIE DANS LE PRESSE-PAPIERS
      // Si le partage natif ne fonctionne pas (PC ou navigateur non compatible)
      
      // Copie l'URL dans le presse-papiers de l'utilisateur
      navigator.clipboard.writeText(shareData.url);
      
      // 7. CRÉATION D'UN MESSAGE DE CONFIRMATION
      // Crée un élément HTML pour afficher un message
      const span = document.createElement("span");
      span.textContent = "Lien copié dans le presse-papiers !";
      
      // 8. POSITIONNEMENT DU MESSAGE
      // Place le message à l'endroit où l'utilisateur a cliqué
      span.style.position = "fixed"; // Position fixe par rapport à la fenêtre
      span.style.top = e.clientY + "px"; // Position Y du clic
      span.style.left = e.clientX + "px"; // Position X du clic
      
      // 9. STYLE DU MESSAGE
      span.style.backgroundColor = "#4CAF50"; // Fond vert
      span.style.color = "white"; // Texte blanc
      span.style.padding = "10px"; // Espacement intérieur
      span.style.borderRadius = "5px"; // Coins arrondis
      
      // 10. AFFICHAGE DU MESSAGE
      // Ajoute le message à la page
      document.body.appendChild(span);
      
      // 11. SUPPRESSION AUTOMATIQUE DU MESSAGE
      // Programme la suppression du message après 3 secondes
      setTimeout(() => {
        span.remove();
      }, 3000); // 3000 millisecondes = 3 secondes
    }
  });
}
