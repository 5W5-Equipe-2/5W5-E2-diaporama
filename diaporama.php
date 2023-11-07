<?php
/*
/*
  * Plugin name: Diaporama 5W5
  * Description: Cette extension diaporama permet d'afficher les images de la classe Média et de contrôler certains paramètres d'affichage 
  * Version: 1.0
  * Author: Noémie da Silva, Victor Desjardins, Vincent Gélinas, Vincent Hum, Dac Anne Nguyen
  * Author URI: https://github.com/5W5-Equipe-2
  */

function mon_enqueue_dia_css_js()
{
  $version_css = filemtime(plugin_dir_path(__FILE__) . "style.css");
  $version_js = filemtime(plugin_dir_path(__FILE__) . "js/diaporama.js");

  wp_enqueue_style(
    '5w5_plugin_diaporama_css',
    plugin_dir_url(__FILE__) . "style.css",
    array(),
    $version_css
  );

  wp_enqueue_script(
    '5w5_plugin_diaporama_js',
    plugin_dir_url(__FILE__) . "js/diaporama.js",
    array(),
    $version_js,
    true
  ); 

  wp_localize_script('5w5_plugin_diaporama_js', 'diaporama_settings', array(
    'interval_duration' => get_option('diaporama_interval_duration', 3000)
  ));
}

add_action('wp_enqueue_scripts', 'mon_enqueue_dia_css_js');

/******************************************************************************** 
 * MODE ADMINISTRATEUR
 */
function mon_diaporama_settings_page()
{
  add_menu_page('Diaporama Settings', 'Diaporama', 'manage_options', 'mon-diaporama-settings', 'mon_diaporama_settings_page_content');
}


function mon_diaporama_settings_page_content()
{
  // Vérifiez les autorisations de l'utilisateur
  if (!current_user_can('manage_options')) {
    return;
  }

  // Enregistrez les paramètres si le formulaire est soumis
  if (isset($_POST['mon_diaporama_submit'])) {
    // Vérifiez si le formulaire a été soumis
    $interval_duration = intval($_POST['interval-duration']);
    update_option('diaporama_interval_duration', $interval_duration); // Enregistrez la durée dans les options de l'extension
  }

  // Affichez le formulaire de configuration du diaporama
  $diaporama_theme = get_option('mon_diaporama_theme');
?>

  <h2>Paramètrez le diaporama</h2>
  <div>
    <h3>Choississez la durée d'affichage des images</h3>
    <h4>La durée actuelle d'affichage des images est de <?php echo isset($interval_duration) ? $interval_duration : 3000; ?> ms.</h4>
    <form id="diaporama-settings-form" method="post">
      <label for="interval-duration">Nouvelle durée, en millisecondes :</label>
      <input type="number" id="interval-duration" name="interval-duration" min="100" />
      
      <input type="submit" name="mon_diaporama_submit" value="Enregistrer" />
    </form>
  </div>


<?php
  // Récupérez la durée enregistrée dans les options de l'extension
  $saved_duration = get_option('diaporama_interval_duration', 3000);

  // Ajoutez la durée dans une balise script
  echo "<script>var savedDuration = $saved_duration;</script>";
}
add_action('admin_menu', 'mon_diaporama_settings_page');
?>