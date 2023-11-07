<?php
/*
/*
  * Plugin name: Diaporama 5W5
  * Description: Cette extension diaporama permet d'afficher les images de la classe Média
  * Version: 1.0
  * Author: Noémie da Silva, Victor Desjardins, Vincent Gélinas, Vincent Hum, Dac Anne Nguyen
  * Author URI: https://github.com/5W5-Equipe-2
  */


   
  function mon_enqueue_css_js() {
    $version_css = filemtime(plugin_dir_path( __FILE__ ) . "style.css");
     $version_js = filemtime(plugin_dir_path(__FILE__) . "js/diaporama.js");
 
     wp_enqueue_style(   '5w5_plugin_diaporama_css',
     plugin_dir_url(__FILE__) . "style.css",
     array(),
     $version_css);
 
    wp_enqueue_script(  '5w5_plugin_diaporama_js',
    plugin_dir_url(__FILE__) ."js/diaporama.js",
    array(),
    $version_js,
    true); //permet d'ajouter le JS à la fin de la page
   }
 
   add_action('wp_enqueue_scripts', 'mon_enqueue_css_js');

  /******************************************************************************** 
  * MODE ADMINISTRATEUR
  */
  function mon_diaporama_settings_page() {
    add_menu_page('Diaporama Settings', 'Diaporama', 'manage_options', 'mon-diaporama-settings', 'mon_diaporama_settings_page_content');
}


function mon_diaporama_settings_page_content() {
  // Vérifiez les autorisations de l'utilisateur
  if (!current_user_can('manage_options')) {
      return;
  }

  // Enregistrez les paramètres si le formulaire est soumis
  if (isset($_POST['mon_diaporama_submit'])) {
      update_option('mon_diaporama_theme', $_POST['mon_diaporama_theme']);
      echo '<div class="updated"><p>Thème mis à jour.</p></div>';
  }

  // Affichez le formulaire de configuration du diaporama
  $diaporama_theme = get_option('mon_diaporama_theme', 'light');
  ?>
  <div class="wrap">
      <h2>Paramètrez le diaporama</h2>
      <form method="post" action="">
        <h3>Choississez l'alignement des images</h3>
          <label for="mon_diaporama_theme">Alignement latéral (x) :</label>
          <select name="mon_diaporama_theme" id="mon_diaporama_theme">
              <option value="left" <?php selected($diaporama_theme, 'left'); ?>>Gauche</option>
              <option value="50" <?php selected($diaporama_theme, '50'); ?>>Centre</option>
              <option value="right" <?php selected($diaporama_theme, 'right'); ?>>Droite</option>
          </select>
          <label for="mon_diaporama_theme">Alignement vertical (y) :</label>
          <select name="mon_diaporama_theme" id="mon_diaporama_theme">
              <option value="top" <?php selected($diaporama_theme, 'top'); ?>>Haut</option>
              <option value="50" <?php selected($diaporama_theme, '50'); ?>>Centre</option>
              <option value="bottom" <?php selected($diaporama_theme, 'bottom'); ?>>Bas</option>
          </select>
          <input type="submit" name="mon_diaporama_submit" class="button-primary" value="Enregistrer">
      </form>
  </div>
  <?php
}

add_action('admin_menu', 'mon_diaporama_settings_page');


  /******************************************************************************** 
  * EXTENSION
  */





  ?>

