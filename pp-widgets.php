<?php
/*
Plugin Name: pocketplanet widgets
Plugin URI:  
Description: Custom Created widgets for pocketplanet
Version:     20180817
Author:      Morten Ege Jensen <ege.morten@gmail.com>
Author URI:  
License:     May not be used without the explicit consent of the Author.
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// set version number (for cache busting)
$pp_widgets_version = '201808204';
$pp_widgets_config = [
  'version' => $pp_widgets_version,
  'camref' => get_option('pp_widgets_camref'),
  'source_code' => get_option('pp_widgets_source_code'),
];

/**
 * Get IP address of user
 * other APIs:
 * https://stackoverflow.com/questions/391979/how-to-get-clients-ip-address-using-javascript
 * @return string ip address
 */
function pp_widgets_get_ip_address() {
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip=$_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
    $ip=$_SERVER['REMOTE_ADDR'];
  }
  return $ip;
}

/**
 * Empty Wordpress section HTML
 */
function pp_widgets_section_html_callback(){}

/**
 * Loads the 'upload background image' field
 */
function pp_widgets_background_image_html_callback(){
  wp_enqueue_media();
  wp_register_script( 'pp_widgets_admin', plugins_url('static/pp-widgets-admin.js', __FILE__), array('jquery'), null,true );
  
  $image_id = get_option('pp_widgets_background_image', 0);
  // wp_localize_script( 'pp_widgets_admin', 'localized_data', array('image_id' => 88) );
  wp_enqueue_script('pp_widgets_admin');
  $image_url = wp_get_attachment_url( $image_id );
  ?>
  <div class='image-preview-wrapper'>
    <img id='image-preview' src='<?php echo $image_url; ?>' width='100' height='100' style='max-height: 100px; width: 100px;'>
  </div>
  <input id="upload_image_button" type="button" class="button" value="<?php _e( 'Upload image' ); ?>" />
  <input type='hidden' name='pp_widgets_background_image' id='image_attachment_id' value=<?= $image_id; ?>>
  <?php
}

function pp_widgets_source_code_html_callback(){
  $value = get_option('pp_widgets_source_code');
  ?>
  <input type="text" name="pp_widgets_source_code" placeholder="" value="<?= $value; ?>" />
  <?php
}

function pp_widgets_camref_html_callback(){
  $value = get_option('pp_widgets_camref');
  ?>
  <input type="text" name="pp_widgets_camref" placeholder="" value="<?= $value; ?>" />
  <?php
}

/**
 * Defines settings and their respective settings sections and fields
 */
function pp_widgets_settings_init() {
  
  add_settings_section(
    'pp_widgets_section_1',
    'Settings for pocketplanet widgets',
    'pp_widgets_section_html_callback',
    'pp_widgets');

  // add background image setting
  register_setting( 'pp_widgets', 'pp_widgets_background_image' );
  add_settings_field(
    'pp_widgets_background_image',
    'Add a background image to full width search',
    'pp_widgets_background_image_html_callback',
    'pp_widgets',
    'pp_widgets_section_1'
  );

  // add camref setting
  register_setting( 'pp_widgets', 'pp_widgets_camref' );
  add_settings_field(
    'pp_widgets_camref',
    'SmarterAds Camref',
    'pp_widgets_camref_html_callback',
    'pp_widgets',
    'pp_widgets_section_1'
  );

  // add source_code setting
  register_setting( 'pp_widgets', 'pp_widgets_source_code' );
  add_settings_field(
    'pp_widgets_source_code',
    'SmarterAds Source Code',
    'pp_widgets_source_code_html_callback',
    'pp_widgets',
    'pp_widgets_section_1'
  );
}
add_action( 'admin_init', 'pp_widgets_settings_init' );

/**
 * Create the settings page (basically just wordpress stuff)
 */
function pp_widgets_settings_page_html_callback() {
  ?>
  <div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <!--
    <div>
      <form action="<?php echo admin_url('admin-post.php'); ?>" method="post" enctype="multipart/form-data">
        <div>
          <label>Upload airports file <small>must be in the form of [iata, name, city, country, lat, lng]</small></label>
        </div>
        <input type="file" name="airports" class="form-control"/>
        <input type="hidden" name="action" value="pp_widgets_upload_airports" />
        <input type="submit" name="submit" value="Upload" class="button button-primary" />
      </form>
    </div>
  -->
    <form action="options.php" method="post">
    <?php
      settings_fields( 'pp_widgets' );
      do_settings_sections( 'pp_widgets' );
      submit_button( 'Save Settings' );
    ?>
    </form>
  </div>
  <?php
}

/**
 * Add a submenu on the 'Settings' tab for our PP widgets settings
 */
function pp_widgets_add_menu() {
  add_submenu_page('options-general.php', 'PP Widgets', 'PP Widgets', 'manage_options', 'pp_widgets', 'pp_widgets_settings_page_html_callback');
}
add_action('admin_menu', 'pp_widgets_add_menu');

/**
 * Shortcode: Load the full width search from an external source
 */
function pp_widgets_basic_shortcode($atts = [], $content = '', $tag = ''){
   // normalize attribute keys, lowercase
  $atts = array_change_key_case((array)$atts, CASE_LOWER);
  // override default attributes with user attributes
  $parsed_atts = shortcode_atts([
    'type' => 'flights',
  ], $atts, $tag);

  $type = $parsed_atts['type'];
  $type = in_array($type, ['flights', 'hotels', 'cars']) ? $type : null;
  if (!$type) return '';
  $filename = '/templates/full-width-search-' . $type . '.php';
  ob_start();
  require_once(dirname(__FILE__) . $filename);
  return ob_get_clean();   
  // return '<h2>Hello, World</h2>';
}
add_shortcode( 'pp_widgets', 'pp_widgets_basic_shortcode');

/**
 * enqueue the scripts needed to show and use the widgets
 */
function pp_widgets_scripts() {
  global $pp_widgets_config;
  // bootstrap  
  wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css');
  wp_enqueue_script( 'bootstrap','https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js', array( 'jquery' ),'',true );
  // Flatpickr
  wp_enqueue_style('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
  wp_enqueue_script( 'flatpickr','https://cdn.jsdelivr.net/npm/flatpickr', array(),'',true );

  // select2
  // wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css');
  // wp_enqueue_script( 'select2','https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js', array('jquery'),'',true );

  // pp-widgets
  wp_enqueue_style('pp_widgets', plugins_url('static/pp-widgets.css', __FILE__), array(), $pp_widgets_config['version']);
  wp_register_script( 'pp_widgets', plugins_url('static/pp-widgets.js', __FILE__), array('jquery'), $pp_widgets_config['version'], true );
  // Localize script
  $lData = array(
    'url' => site_url(),
    'ip_address' => pp_widgets_get_ip_address(),
    'camref' => $pp_widgets_config['camref'],
    'source_code' => $pp_widgets_config['source_code'],
  );
  
  wp_localize_script('pp_widgets', 'localized_data', $lData);
  wp_enqueue_script('pp_widgets');
}
add_action( 'wp_enqueue_scripts', 'pp_widgets_scripts' );

/*
function pp_widgets_upload_airports(){
  if (!isset($_FILES['airports'])) {
    die('Missing airports file');
  }

  if ($_FILES["airports"]["error"] > 0) {
      die("Error: " . $_FILES["airports"]["error"]);
  }
  
  $csvFile = file($_FILES['airports']['tmp_name']);
  $data = [];
  foreach ($csvFile as $line) {
      $data[] = str_getcsv($line);
  }
  var_dump(array_slice($data, 0, 10));
}
add_action('admin_post_pp_widgets_upload_airports', 'pp_widgets_upload_airports');
*/

//add_action( 'wp_ajax_my_action', 'my_action' );
//add_action( 'wp_ajax_nopriv_my_action', 'my_action' );

include "template-injector.php";
include "meta-boxes.php";