<?php
/*
Plugin Name:  pocketplanet widgets
Plugin URI:   https://github.com/mortenege/pocketplanet-plugin
Description:  Custom Created widgets for pocketplanet.com
Version:      20180902
Author:       Morten Ege Jensen <ege.morten@gmail.com>
Author URI:   https://github.com/mortenege
License:      GPLv2 <https://www.gnu.org/licenses/gpl-2.0.html>
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// set version number (for cache busting)
$pp_widgets_version = '20180902';
$pp_widgets_config = [
  'version' => $pp_widgets_version,
  'camref' => get_option('pp_widgets_camref'),
  'source_code' => get_option('pp_widgets_source_code')
];

// set cookie names
$PP_WIDGETS_COOKIE_NAME = 'pp_widgets';

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

function pp_widgets_prob_widget1_html_callback () {
  $value = get_option('pp_widgets_prob_widget1', 0.5);
  ?>
  <input type="number" name="pp_widgets_prob_widget1" placeholder="" value="<?= $value; ?>" min="0" max="1" step="0.01"/>
  <small>Value between 0 and 1. Lower for SmarterAds, higher for Intent</small>
  <?php 
}

function pp_widgets_prob_widget2_html_callback () {
  $value = get_option('pp_widgets_prob_widget2', 0.5);
  ?>
  <input type="number" name="pp_widgets_prob_widget2" placeholder="" value="<?= $value; ?>" min="0" max="1" step="0.01"/>
  <small>Value between 0 and 1. Lower for SmarterAds, higher for Intent</small>
  <?php 
}

function pp_widgets_prob_widget3_html_callback () {
  $value = get_option('pp_widgets_prob_widget3', 0.5);
  ?>
  <input type="number" name="pp_widgets_prob_widget3" placeholder="" value="<?= $value; ?>" min="0" max="1" step="0.01"/>
  <small>Value between 0 and 1. Lower for Intent, higher for SmarterAds</small>
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
    'Set a fallback image to full width search pane',
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

  // add probability for widget 1 setting
  register_setting( 'pp_widgets', 'pp_widgets_prob_widget1' );
  add_settings_field(
    'pp_widgets_prob_widget1',
    'Ad Share for Search Widget',
    'pp_widgets_prob_widget1_html_callback',
    'pp_widgets',
    'pp_widgets_section_1'
  );

  // add probability for widget 1 setting
  register_setting( 'pp_widgets', 'pp_widgets_prob_widget2' );
  add_settings_field(
    'pp_widgets_prob_widget2',
    'Ad share for Rail Widget',
    'pp_widgets_prob_widget2_html_callback',
    'pp_widgets',
    'pp_widgets_section_1'
  );

  // add probability for widget 1 setting
  register_setting( 'pp_widgets', 'pp_widgets_prob_widget3' );
  add_settings_field(
    'pp_widgets_prob_widget3',
    'Ad share for Bottom Widget',
    'pp_widgets_prob_widget3_html_callback',
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
  $type = in_array($type, ['flights', 'hotels', 'cars', 'cruises']) ? $type : null;
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

/**
 * Append smarter travel shortcode to each post
 * @return [type] [description]
 */
function pp_widgets_add_smarterads () {
  global $post;
  if( ! $post instanceof WP_Post ) return;
  if ( 'post' !== $post->post_type ) return;
  echo do_shortcode("[pp_widgets_smarterads destination='{$post->post_title}']");
}
add_filter( 'wp_footer', 'pp_widgets_add_smarterads' );

/**
 * SmarterTravel Short code script
 * @param  array  $atts    [description]
 * @param  string $content [description]
 * @param  string $tag     [description]
 * @return string          [description]
 */
function pp_widgets_smarterads_shortcode($atts = [], $content = '', $tag = ''){
   // normalize attribute keys, lowercase
  $atts = array_change_key_case((array)$atts, CASE_LOWER);
  // override default attributes with user attributes
  $parsed_atts = shortcode_atts([
    'type' => 'hotel',
    'destination' => 'Bangkok',
    // 'origin' => 'Bali',
    'date1' => date('Y-m-d'),
    'date2' => date('Y-m-d', strtotime("+3 days"))
  ], $atts, $tag);

  $type = $parsed_atts['type'];
  $type = in_array($type, ['hotel', 'air', 'car', 'vacation', 'cruise']) ? $type : 'hotel';
  $data['type'] = $type;
  $data['date1'] = $parsed_atts['date1'];
  $data['date2'] = $parsed_atts['date2'];
  $data['origin'] = $parsed_atts['origin'];
  $data['destination'] = $parsed_atts['destination'];

  $filename = '/templates/ads.php';
  ob_start();
  include dirname(__FILE__) . $filename;
  return ob_get_clean();   
}
add_shortcode('pp_widgets_ads', 'pp_widgets_smarterads_shortcode');

/**
 * Add SmarterTravel Script in Header
 */
function pp_widgets_add_smarterads_script () {
    ?>
<script>
!function(e,r,t){function n(e){var r,t=document.createElement("script");t.src="//p.smarter-js.com"+e,t.async=!0,(r=document.getElementsByTagName("script")[0]).parentNode.insertBefore(t,r)}var i,a=["/ext/partner/universal-integration/universal-integration-hosted.min.js"];(i=e.smarter=e.smarter||function(e){if("register"===e){i.API_KEY=arguments&&arguments[1]?arguments[1]:null;for(var r=0;r<a.length;r++)n(a[r])}else i._queue.push(arguments)})._init||(e.SmarterTravelNetworkNS="smarter",i.BOOTSTRAP_VERSION="2.2.0",i._init=!0,i._queue=[],i("register","tC3iwejhT2m8JNEKtbA6CA"))}(window);
</script>
    <?php
}
add_action('wp_head', 'pp_widgets_add_smarterads_script');

include "template-injector.php";
include "meta-boxes.php";


add_shortcode('ege_intent', 'ege_intent_shortcode');
function ege_intent_shortcode () {
  ?>
<!--<div id="IntentMediaSlimIntercard"></div>-->
<!--<div id="IntentMediaFooter"></div>-->
<!--
<div id="IntentMediaIntercard"></div>
<div id="IntentMediaRail"></div>-->
<script type="text/javascript">
window.IntentMediaProperties = {  
site_name: 'POCKET_PLANET',
page_id: 'content.general',
site_country: 'ID',
site_language: 'en',
site_currency: 'USD',
/*generic*/
travel_date_start: '20180909',
travel_date_end: '20180910',
travelers: '2',
 
/* Hotel search parameters */
hotel_airport_code: 'BKK',
//hotel_city: '{{HOTEL_CITY_GOES_HERE}}',
//hotel_country: '{{HOTEL_COUNTRY_GOES_HERE}}', 

};
(function() {
  var script = document.createElement("script");
  var url = '//a.cdn.intentmedia.net/javascripts/v1/intent_media_core.js';
  script.src = url;
  script.async = true;
  document.getElementsByTagName("head")[0].appendChild(script);
}());

document.addEventListener('click', fireIntent);
function fireIntent(e){
  console.log('Clicked...', IntentMediaProperties)
  e.preventDefault();

  if(window.IntentMedia && IntentMedia.trigger) {
    console.log('Triggered...', IntentMedia);
    IntentMedia.trigger("open_exit_unit");
  }

}  
</script>
  <?php
}

add_shortcode('pp_widgets_rail', 'pp_widgets_rail_shortcode');
function pp_widgets_rail_shortcode () {
  $val = pp_widgets_get_cookie_value('widget2');
  if ($val <= get_option('pp_widgets_prob_widget2', 0.5)) {
    return '<div id="IntentMediaRail"></div>';  
  } else {
    return '<div id="smartertravel_inline_r"></div>';
  }
}

add_shortcode('pp_widgets_bottom', 'pp_widgets_bottom_shortcode');
function pp_widgets_bottom_shortcode () {
  $val = pp_widgets_get_cookie_value('widget3');
  if ($val <= get_option('pp_widgets_prob_widget3', 0.5)) {
    return '<div id="IntentMediaIntercard"></div>';  
  } else {
    return '<div id="smartertravel_inline_b"></div>';
  }
}

add_action('init', 'pp_widgets_set_cookie');
function pp_widgets_set_cookie () {
  global $PP_WIDGETS_COOKIE_NAME;
  
  if (!isset($_COOKIE[$PP_WIDGETS_COOKIE_NAME])){
    global $pp_widgets_config;
    // calculate widget probability if cookie is not set
    

    // set cookie
    
  }
  $pp_widgets_config['widget_prob'] = mt_rand(0, 100000) / 100000;
  setcookie(
      $PP_WIDGETS_COOKIE_NAME,
      $pp_widgets_config['widget_prob'],
      time() + (60 * 10), // 10 minutes
      '/'
    );
}

function pp_widgets_get_cookie_value ($name) {
  global $PP_WIDGETS_COOKIE_NAME;
  global $pp_widgets_config;
  
  if (isset($_COOKIE[$PP_WIDGETS_COOKIE_NAME])) {
    return $_COOKIE[$PP_WIDGETS_COOKIE_NAME];
  }
  return $pp_widgets_config['widget_prob'];
}

function pp_widgets_check_probability($probability=0.5, $length=10000) {
  $test = mt_rand(1, $length);
  return $test <= $probability * $length;
}