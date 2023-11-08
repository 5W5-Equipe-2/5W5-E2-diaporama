document.addEventListener("DOMContentLoaded", function() {
  let diaporama = document.querySelector(".diaporama");
  let images = diaporama.querySelectorAll("img");
  let idx = 0;

  let duree = diaporama_settings.interval_duree || 3000; // Durée de l'intervalle par défaut (en millisecondes)
  let diaporamaInterval; // Variable pour stocker l'instance du setIntervalle

  let desaturation = diaporama_settings.desaturation || 80; // % de désaturation

  console.log("desaturation",desaturation);
  console.log("durée",duree);

  /* On applique la classe CSS pour l'effet "Moody" */
  diaporama.classList.add("img-wrapper");

  //console.log("durée",duree);

  // Fonction pour afficher l'image suivante et faire rouler le diaporama
  function afficherImageSuivante() {
    // Masquez l'image actuelle avec animation
    images[idx].classList.remove("fondu");
    images[idx].style.display = "none";

    idx = (idx + 1) % images.length; // Passez à l'image suivante (ou revenez à la première si nécessaire)

    // Affichez l'image suivante avec animation
    images[idx].style.display = "flex";
    images[idx].classList.add("fondu");
  }

  // Fonction pour mettre à jour la durée de l'intervalle
  function majDureeInterval(newDuree) {
    duree = newDuree;
    clearInterval(diaporamaInterval); // Effacer l'intervalle précédent
    diaporamaInterval = setInterval(afficherImageSuivante, duree); // Créer un nouvel intervalle avec la nouvelle durée
  }

  // Démarrez un minuterie pour changer automatiquement d'image à intervalles réguliers
  diaporamaInterval = setInterval(afficherImageSuivante, duree);

  // Recherchez un élément qui permettra à l'utilisateur de régler la durée
  const dureeInput = document.getElementById("interval-duree");
  if (dureeInput) {
    dureeInput.addEventListener("change", function () {
      const newDuree = parseInt(dureeInput.value);
      majDureeInterval(newDuree);
    });
  }
})();
