const favoris = document.querySelectorAll(".bouton-favoris");
favoris.forEach((bouton) => {
  bouton.addEventListener("click", function (e) {
    e.preventDefault(); // Prevent default action of the link
    const recetteId = this.getAttribute("data-id");
    const data = new FormData();
    data.append("recette_id", recetteId);
    data.append("action", "toggle_favorites");

    fetch("api-favoris.php", {
      method: "POST",
      body: data,
      headers: {
        "X-Requested-With": "XMLHttpRequest", // Indicate that this is an AJAX request
        Accept: "application/json", // Expect JSON response
      },
    })
      .then((response) => response.json())
      .then((data) => {
        // console.log(data);
        const spanText = this.querySelector("span");
        switch (data.status) {
          case "added":
            this.innerHTML = "❤️"; // Change icon to filled heart
            break;
          case "removed":
            this.innerHTML = "🤍"; // Change icon to empty heart
            break;
          case "error":
            alert("Erreur lors de l'ajout aux favoris.");
            break;
          case "not_logged_in":
            alert("Veuillez vous connecter pour ajouter aux favoris.");
            break;
          default:
            console.error("Statut inconnu:", data.status);
            alert("Une erreur inconnue est survenue.");
        }
        if (spanText) {
          this.append(spanText); // Re-append the span if it exists
        }
      })
      .catch((error) => console.error("Erreur:", error));
  });
});
