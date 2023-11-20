<?php
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
        'desaturation' => get_option('diaporama_desaturation', 80),
        'image_positions' => array_reduce(get_posts(array('category_name' => 'media', 'posts_per_page' => -1)), function ($acc, $article) {
            $article_id = $article->ID;
            $acc[$article_id] = get_option('diaporama_image_position' . $article_id, 'center center');
            return $acc;
        }, array())
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

    $position_centre = 'center center';

    // Enregistrez les paramètres si le formulaire est soumis
    if (isset($_POST['mon_diaporama_submit'])) {
        $interval_duree = isset($_POST['interval-duree']) ? intval($_POST['interval-duree']) : $interval_duree;
        $desaturation = isset($_POST['desaturation']) ? intval($_POST['desaturation']) : $desaturation;

        foreach (get_posts(array('category_name' => 'media', 'posts_per_page' => -1)) as $article) {
            $article_id = $article->ID;
            $position_image = isset($_POST['image-position'][$article_id]) ? sanitize_text_field($_POST['image-position'][$article_id]) : $position_centre;
            update_option('diaporama_image_position' . $article_id, $position_image);
        }

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

    <h3>Positionnement des images pour les petits écrans</h3>
    <h4>Choisissez si les images s'allignent à gauche, au centre ou à droite</h4>
    <?php
    // Récupérer les articles de la catégorie 'media'
    $args = array(
      'category_name' => 'media',
      'posts_per_page' => -1, // Récupérer tous les articles de la catégorie
    );
    $image = get_posts($args);

    ?>
    <!--     Tableau des images du diaporama -->
    <table id="the-list">
      <tr>
        <th>Image</th>
        <th>Gauche</th>
        <th>Centre</th>
        <th>Droite</th>
      </tr>
      <?php
      // Boucle à travers les articles
      foreach ($image as $article) : setup_postdata($article);
        // Récupérer l'URL de l'image thumbnail
        $thumbnail = get_the_post_thumbnail_url($article->ID, 'thumbnail');
        // Vérifier si l'URL de l'image existe
        if ($thumbnail) {
      ?>
          <tr>
            <td><img class="media-icon" src="<?php echo $thumbnail; ?>" alt="<?php the_title(); ?>"></td>
            <td>
              <input type="radio" name="image-position[<?php echo $article->ID; ?>]" value="left" <?php checked(get_option('diaporama_image_position' . $article->ID), 'left'); ?>>
            </td>
            <td>
              <input type="radio" name="image-position[<?php echo $article->ID; ?>]" value="center" <?php checked(get_option('diaporama_image_position' . $article->ID, 'center'), 'center'); ?>>
            </td>
            <td>
              <input type="radio" name="image-position[<?php echo $article->ID; ?>]" value="right" <?php checked(get_option('diaporama_image_position' . $article->ID), 'right'); ?>>
            </td>
          </tr>
      <?php
        }
      endforeach;
      // Réinitialiser les données de post globales
      wp_reset_postdata();
      ?>
    </table>

    <br>
    <input type="submit" name="mon_diaporama_submit" value="Enregistrer" class="button-primary" />
  </form>

  <?php
  // Enregistrez les paramètres si le formulaire est soumis
  if (isset($_POST['mon_diaporama_submit'])) {
    foreach ($image as $article) {
      $article_id = $article->ID;
      $position_image = isset($_POST['image-position'][$article_id]) ? sanitize_text_field($_POST['image-position'][$article_id]) : $position_centre;
      update_option('diaporama_image_position' . $article_id, $position_image);
    }
  }
  ?>

<?php
  // Récupérez les valeurs enregistrées dans les options du formulaire
  $duree_sauvee = get_option('diaporama_interval_duree', $interval_duree_defaut);
  $desaturation_sauvee = get_option('diaporama_desaturation', $desaturation_defaut);

  // Ajoutez la durée dans une balise script
  echo "<script>
  var sauverDuree = " . esc_attr($duree_sauvee) . "; 
  var sauverDesaturation = " . esc_attr($desaturation_sauvee) . ";
</script>";

  // Récupérez les options de position pour chaque image
  $image = get_posts(array('category_name' => 'media', 'posts_per_page' => -1));

  // Ajoutez les options de position dans une balise script
  echo "<script>";
  foreach ($image as $article) {
    $article_id = $article->ID;
    $position_sauvee = get_option('diaporama_image_position' . $article_id, $position_centre);
    echo "var sauverPosition" . $article_id . " = '" . esc_attr($position_sauvee) . "'; ";
  }
  echo "</script>";
}
add_action('admin_menu', 'mon_diaporama_settings_page');
?>