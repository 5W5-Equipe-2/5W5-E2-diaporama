(function () {
  let diaporama = document.querySelector(".diaporama");
  let images = diaporama.querySelectorAll("img");
  let idx = 0;
  // Durée de l'intervalle par défaut (en millisecondes)
  let duration = diaporama_settings.interval_duration || 3000; // Durée de l'intervalle par défaut (en millisecondes)
  let diaporamaInterval; // Variable pour stocker l'intervalle

  /* On applique la classe CSS pour l'effet "Moody" */
  diaporama.classList.add("img-wrapper");

  //console.log("durée",duration);

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
  function updateIntervalDuration(newDuration) {
    duration = newDuration;
    clearInterval(diaporamaInterval); // Effacer l'intervalle précédent
    diaporamaInterval = setInterval(afficherImageSuivante, duration); // Créer un nouvel intervalle avec la nouvelle durée
  }

  // Démarrez un minuterie pour changer automatiquement d'image à intervalles réguliers
  diaporamaInterval = setInterval(afficherImageSuivante, duration);

  // Recherchez un élément qui permettra à l'utilisateur de régler la durée
  const durationInput = document.getElementById("interval-duration");
  if (durationInput) {
    durationInput.addEventListener("change", function () {
      const newDuration = parseInt(durationInput.value);
      updateIntervalDuration(newDuration);
    });
  }
})();
