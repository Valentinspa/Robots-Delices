const btn = document.querySelector(".share-btn");

if (btn) {
  // Share must be triggered by "user activation"
  btn.addEventListener("click", async (e) => {
    e.preventDefault(); // Prevent default action of the link
    const shareData = {
      title: document.title,
      url: window.location.href,
    };
    try {
      await navigator.share(shareData);
    } catch (err) {
      navigator.clipboard.writeText(shareData.url);
      const span = document.createElement("span");
      span.textContent = "Lien copié dans le presse-papiers !";
      span.style.position = "fixed";
      span.style.top = e.clientY + "px";
      span.style.left = e.clientX + "px";
      span.style.backgroundColor = "#4CAF50";
      span.style.color = "white";
      span.style.padding = "10px";
      span.style.borderRadius = "5px";
      document.body.appendChild(span);
      setTimeout(() => {
        span.remove(); // Remove the message after 3 seconds
      }, 3000);
    }
  });
}
