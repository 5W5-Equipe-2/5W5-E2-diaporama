(function () {
  let diaporama = document.querySelector(".diaporama");
  let images = diaporama.querySelectorAll("img");
  let idx = Math.floor(Math.random() * images.length);
  let diaporamaInterval; // Variable pour stocker l'instance du setIntervalle

  //Les variables modifiables dans le tableau de bord de l'extension, dans WP
  let duree = diaporama_settings.interval_duree || 1000; // Durée de l'intervalle par défaut (en millisecondes)
  let desaturation = diaporama_settings.desaturation || 80; // % de désaturation

  console.log("desaturation", desaturation);
  console.log("durée", duree);
  console.log("idx", idx);

  const afficherImage = (idx) => {
    diaporama.classList.remove("masquer-image");
    /* On applique les classes CSS pour les effets sur les images */
    diaporama.classList.add("img-wrapper");
    // On appliquez le filtre de desaturation des images avec la valeur choisit par l'utilisateur
    diaporama.style.filter = `grayscale(${desaturation}%)`;

    // Affichez l'image spécifique à l'index passé en paramètre
    images[idx].style.display = "flex";

     // Démarrez un minuterie pour changer automatiquement d'image à intervalles réguliers
  diaporamaInterval = setInterval(afficherImageSuivante, duree);
};

afficherImage(idx); 


 // Démarrez un minuterie pour changer automatiquement d'image à intervalles réguliers
 diaporamaInterval = setInterval(afficherImageSuivante, duree);

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

  // Recherchez les éléments que l'utilisateur peut changer
  const dureeInput = document.getElementById("interval-duree");
  if (dureeInput) {
    dureeInput.addEventListener("change", function () {
      const newDuree = parseInt(dureeInput.value);
      majDureeInterval(newDuree);
    });
  }

  const desaturationInput = document.getElementById("desaturation");
  if (desaturationInput) {
    desaturationInput.addEventListener("change", function () {
      const newDesaturation = parseInt(desaturationInput.value);
      majDesaturation(newDesaturation);
    });
  }
})();
