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
    'interval_duree' => get_option('diaporama_interval_duree', 3000),
    'desaturation' => get_option('diaporama_desaturation', 80)
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

  
  // Obtenez les valeurs actuelles des options de l'extension
  $interval_duree_defaut = 3000;
  $interval_duree = get_option('diaporama_interval_duree', $interval_duree_defaut);
  $desaturation_defaut = 80;
  $desaturation = get_option('diaporama_desaturation', $desaturation_defaut);

  // Enregistrez les paramètres si le formulaire est soumis
  if (isset($_POST['mon_diaporama_submit'])) {
    // Vérifiez si le formulaire a été soumis
    $interval_duree = isset($_POST['interval-duree']) ? intval($_POST['interval-duree']) : $interval_duree;
    $desaturation = isset($_POST['desaturation']) ? intval($_POST['desaturation']) : $desaturation;

    update_option('diaporama_interval_duree', $interval_duree);
    update_option('diaporama_desaturation', $desaturation);
  }

  // Affichez le formulaire de configuration du diaporama
  $diaporama_theme = get_option('mon_diaporama_theme');

?>

  <h2>Paramètrez le diaporama</h2>
  <form id="diaporama-settings-form" method="post">
    <div>
      <h3>Durée d'affichage des images</h3>
      <label for="interval-duree">en ms</label>
      <input type="number" id="interval-duree" name="interval-duree" min="100" max="600000" value="<?php echo $interval_duree; ?>" />
      <h4>(Affichage actuel : <?php echo isset($interval_duree) ? ($interval_duree / 1000) : 3; ?> sec)</h4>
    </div>
    <div>
      <h3>Force du filtre noir et blanc</h3>
      <label for="desaturation">en %</label>
      <input type="number" id="desaturation" name="desaturation" min="0" max="100" value="<?php echo isset($desaturation) ? ($desaturation) : $desaturation_defaut; ?>" />
      <h4>(Saturation : <?php echo isset($desaturation) ? (100 - $desaturation) : 20; ?>%)</h4>
    </div>
    <br>
    <input type="submit" name="mon_diaporama_submit" value="Enregistrer" class="button-primary" />
  </form>

<?php
  // Récupérez les valeurs enregistrées dans les options du formulaire
  $duree_sauvee = get_option('diaporama_interval_duree', $interval_duree_defaut);
  $desaturation_sauvee = get_option('diaporama_desaturation', $desaturation_defaut);

  // Ajoutez la durée dans une balise script
  echo "<script>var sauverDuree = " . esc_attr($duree_sauvee) . "; var sauverDesaturation = " . esc_attr($desaturation_sauvee) . ";</script>";
}
add_action('admin_menu', 'mon_diaporama_settings_page');
?>