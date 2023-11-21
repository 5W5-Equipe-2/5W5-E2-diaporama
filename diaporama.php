<?php
/*
 * Plugin name: Diaporama 5W5
 * Description: Cette extension diaporama permet de boucler dans les images de la classe Média 
 * et de contrôler certains paramètres d'affichage choisis par l'utilisateur
 * Version: 1.0
 * Author: Noémie da Silva, Victor Desjardins, Vincent Gélinas, Vincent Hum, Dac Anne Nguyen
 * Author URI: https://github.com/5W5-Equipe-2
 */

 /**
 * Enregistre les fichiers CSS et JavaScript pour le diaporama.
 *
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
      'contraste' => get_option('diaporama_contraste', 100),
      'luminosite' => get_option('diaporama_luminosite', 100),

      'image_positions' => array_reduce(get_posts(array('category_name' => 'media', 'posts_per_page' => -1)), 
      function ($acc, $article) {
          $article_id = $article->ID;
  
          // Obtenir l'ID de l'image en vedette
          $image_id = get_post_thumbnail_id($article_id);
  
          // Vérifier si l'image en vedette existe
          if ($image_id) {
              // Récupérer la position de l'image en vedette
              $position = get_option('diaporama_image_position' . $image_id, 'center center');
  
              // Ajouter l'entrée à l'accumulateur
              $acc[$image_id] = $position;
          }
  
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
    // Vérifier les autorisations de l'utilisateur
    if (!current_user_can('manage_options')) {
        return;
    }

    // Obtenir les valeurs actuelles des options de l'extension
    $interval_duree_defaut = 3000;
    $interval_duree = get_option('diaporama_interval_duree', $interval_duree_defaut);
    $desaturation_defaut = 80;
    $desaturation = get_option('diaporama_desaturation', $desaturation_defaut);
    $contraste_defaut = 100;
    $contraste = get_option('diaporama_contraste', $contraste_defaut);
    $luminosite_defaut = 100;
    $luminosite = get_option('diaporama_luminosite', $luminosite_defaut);
    $position_centre = 'center center';

    // Enregistrez les paramètres si le formulaire est soumis
    if (isset($_POST['mon_diaporama_submit'])) {
        $interval_duree = isset($_POST['interval-duree']) ? intval($_POST['interval-duree']) : $interval_duree;
        $desaturation = isset($_POST['desaturation']) ? intval($_POST['desaturation']) : $desaturation;
        $contraste = isset($_POST['contraste']) ? intval($_POST['contraste']) : $contraste;
        $luminosite = isset($_POST['luminosite']) ? intval($_POST['luminosite']) : $luminosite;

        foreach (get_posts(array('category_name' => 'media', 'posts_per_page' => -1)) as $article) {
            $article_id = $article->ID;
            $position_image = isset($_POST['image-position'][$article_id]) ? sanitize_text_field($_POST['image-position'][$article_id]) : $position_centre;
            update_option('diaporama_image_position' . $article_id, $position_image);
        }

        update_option('diaporama_interval_duree', $interval_duree);
        update_option('diaporama_desaturation', $desaturation);
        update_option('diaporama_contraste', $contraste);
        update_option('diaporama_luminosite', $luminosite);
    }

    // Afficher le formulaire de configuration du diaporama
    $diaporama_theme = get_option('mon_diaporama_theme');
?>

  <h2>Paramètrez le diaporama et l'esthétique des images</h2>
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
    <div>
      <h3>Contraste des images</h3>
      <label for="contraste">en %</label>
      <input type="number" id="contraste" name="contraste" min="0" max="1000" value="<?php echo isset($contraste) ? ($contraste) : $contraste_defaut; ?>" />
    </div>
    <div>
      <h3>Luminosité des images</h3>
      <label for="luminosite">en %</label>
      <input type="number" id="luminosite" name="luminosite" min="0" max="1000" value="<?php echo isset($luminosite) ? ($luminosite) : $luminosite_defaut; ?>" />
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
      // Boucler à travers les articles
      foreach ($image as $article) : setup_postdata($article);
        // Récupérer l'URL de l'image thumbnail
        $thumbnail = get_the_post_thumbnail_url($article->ID, 'thumbnail');
        // Vérifier si l'URL de l'image existe
        if ($thumbnail) {
      ?>
          <tr>
            <td><img class="media-icon" src="<?php echo $thumbnail; ?>" alt="<?php the_title(); ?>"></td>
            <td>
              <input type="radio" name="image-position[<?php echo $article->ID; ?>]" value="left center" <?php checked(get_option('diaporama_image_position' . $article->ID), 'left center'); ?>>
            </td>
            <td>
              <input type="radio" name="image-position[<?php echo $article->ID; ?>]" value="center center" <?php checked(get_option('diaporama_image_position' . $article->ID, 'center center'), 'center center'); ?>>
            </td>
            <td>
              <input type="radio" name="image-position[<?php echo $article->ID; ?>]" value="right center" <?php checked(get_option('diaporama_image_position' . $article->ID), 'right center'); ?>>
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
  // Enregistrer les paramètres si le formulaire est soumis
  if (isset($_POST['mon_diaporama_submit'])) {
    $interval_duree = isset($_POST['interval-duree']) ? intval($_POST['interval-duree']) : $interval_duree;
    $desaturation = isset($_POST['desaturation']) ? intval($_POST['desaturation']) : $desaturation;
    $contraste = isset($_POST['contraste']) ? intval($_POST['contraste']) : $contraste;
    $luminosite = isset($_POST['luminosite']) ? intval($_POST['luminosite']) : $luminosite;

    foreach (get_posts(array('category_name' => 'media', 'posts_per_page' => -1)) as $article) {
        $article_id = $article->ID;

        // Obtenir l'ID de l'image en vedette
        $image_id = get_post_thumbnail_id($article_id);

        // Vérifier si l'image en vedette existe
        if ($image_id) {
            // Obtenir la position de l'image à partir du formulaire
            $position_image = isset($_POST['image-position'][$article_id]) ? sanitize_text_field($_POST['image-position'][$article_id]) : $position_centre;

            // Enregistrer la position de l'image en vedette
            update_option('diaporama_image_position' . $image_id, $position_image);
        }
    }

    update_option('diaporama_interval_duree', $interval_duree);
    update_option('diaporama_desaturation', $desaturation);
    update_option('diaporama_contraste', $contraste);
    update_option('diaporama_luminosite', $luminosite);
}
}
add_action('admin_menu', 'mon_diaporama_settings_page');
?>