document.addEventListener("DOMContentLoaded", function () {
  let diaporama = document.querySelector(".diaporama");
  let images = diaporama.querySelectorAll("img");
  let idx = 0;
  let diaporamaInterval;

  /******************************************************************************
  Les variables modifiables dans le tableau de bord de l'extension, dans WP
 ********************************************************************************/
  let duree = diaporama_settings.interval_duree || 1000; // Durée de l'intervalle par défaut (en millisecondes)
  let desaturation = diaporama_settings.desaturation || 80; // % de désaturation
  let contraste = diaporama_settings.contraste || 100; // % de contraste
  let luminosite = diaporama_settings.luminosite || 100; // % de contraste
  let positionImage = diaporama_settings.image_positions; // Objet avec les ID des images et leur positionnement CSS (object-position)

  /******************************************************************************
  Le programme
 ********************************************************************************/

  /* On enlève la classe CSS pour masquer l'image (avant que le JS s'applique) */
  diaporama.classList.remove("masquer-image");

  /* On applique la classe CSS pour les effets sur les images */
  diaporama.classList.add("img-wrapper");

  // On applique le filtre de désaturation des images avec la valeur choisie par l'utilisateur
  // On applique le filtre de contrasten des images avec la valeur choisie par l'utilisateur
  diaporama.style.filter = `grayscale(${desaturation}%) contrast(${contraste}%) brightness(${luminosite}%)`;

  /* On boucle à travers les images pour appliquer les positions (objetc-position) CSS
  choisies par l'utilisateur */
  images.forEach((image) => {
    // Obtenir l'ID de l'image à partir de l'attribut "data-image-id" (template-part categorie-media.php)
    let imageId = image.getAttribute("data-image-id");

    // Vérifier si l'ID de l'image existe dans positionImage
    if (positionImage.hasOwnProperty(imageId)) {
      // Appliquer la position CSS à l'image
      image.style.objectPosition = positionImage[imageId];
    } else {
      console.warn(
        `Aucune position CSS trouvée pour l'image avec l'ID ${imageId}`
      );
    }
  });

  /* On démarre une minuterie pour changer automatiquement d'image à intervalles réguliers
  avec la durée choisie par l'utilisateur */
  diaporamaInterval = setInterval(afficherImageSuivante, duree);

  /**
   * Affiche l'image suivante dans le diaporama
   *
   * @function
   * @name afficherImageSuivante
   * @description Cette fonction masque toutes les images du diaporama, passe à l'image suivante
   * (ou revient à la première) et affiche l'image suivante avec une animation.
   *
   * @param {NodeList} images - La liste des éléments d'image du diaporama.
   * @param {number} idx - L'indice de l'image actuellement affichée.
   *
   * @returns {void}
   */
  function afficherImageSuivante() {
    // Masquer toutes les images
    images.forEach((image) => {
      image.style.display = "none";
      image.classList.remove("fondu");
    });

    // Passer à l'image suivante (ou revenir à la première si nécessaire)
    idx = (idx + 1) % images.length;

    // Affichez l'image suivante
    images[idx].style.display = "flex";
    images[idx].classList.add("fondu");
  }
});
