<?php
/*
Plugin Name:  pocketplanet widgets
Plugin URI:   https://github.com/mortenege/pocketplanet-plugin
Description:  Custom Created widgets for pocketplanet.com
Version:      20181019
Author:       Morten Ege Jensen <ege.morten@gmail.com>
Author URI:   https://github.com/mortenege
License:      GPLv2 <https://www.gnu.org/licenses/gpl-2.0.html>
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class PPWidgets {
  public const VERSION = '201810192';

  public const COOKIE_NAME = 'pp_widgets';
  public const COOKIE_GUID_NAME = 'pp_widgets_guid';

  public static $config = [];

  public function __construct(){

    // init config
    self::initConfig();

    // Add City and Country Meta Boxes to POST
    add_action( 'add_meta_boxes', array(self::class, 'addCityCountryMetaBox' ));
    add_action( 'save_post', [self::class, 'savePostMeta']);

    // Add force_intent cookie ON/OFF rule
    add_action( 'init', [self::class, 'addForceIntentRewriteRule'] );

    // Set the visitor cookie
    add_action( 'init', [self::class, 'setCookie'] );
    // Load Smarterads script
    add_action( 'wp_head', [self::class, 'addSmarteradsScript'] );

    // Enqueue plugin scripts and styles
    add_action( 'wp_enqueue_scripts', [self::class, 'enqueueScripts'] );

    // admin
    add_action( 'admin_init', [self::class, 'initSettings'] );
    add_action( 'admin_menu', [self::class, 'addMenu'] );

    // Shortcodes
    add_shortcode( 'pp_widgets', [self::class, 'basicShortcode'] );
    add_shortcode( 'pp_widgets_rail', [self::class, 'adsRailShortcode'] );
    add_shortcode( 'pp_widgets_bottom', [self::class, 'adsBottomShortcode'] );

    // ajax calls
    add_action('wp_ajax_pp_widgets_widget_countries', [self::class, 'saveWidgetCountries']);

    add_action('wp_ajax_pp_widgets_force_intent_on', [self::class, 'setForceIntentCookieOn']);
    add_action('wp_ajax_nopriv_pp_widgets_force_intent_on', [self::class, 'setForceIntentCookieOn']);
    add_action('wp_ajax_pp_widgets_force_intent_off', [self::class, 'setForceIntentCookieOff']);
    add_action('wp_ajax_nopriv_pp_widgets_force_intent_off', [self::class, 'setForceIntentCookieOff']);
  }

  /**
   * Initialize Config Values
   */
  private static function initConfig(){
    self::$config = [
      'version' => self::VERSION,
      'camref' => get_option('pp_widgets_camref'),
      'source_code' => get_option('pp_widgets_source_code'),
      'intent_params' => array(
        'site' => 'POCKET_PLANET',
        'site_country' => 'ID',
        'site_language' => 'en',
        'site_currency' => 'USD',
        'publisher_user_id' => self::getGuid(),
      ),
      'force_intent' => isset($_COOKIE['force_intent']) ? ($_COOKIE['force_intent'] ? true : false) : false,
    ];
  }

  /**
   * Gets the GUID value from cookie, config or creates one
   * @return [type] [description]
   */
  public static function getGuid(){
    if (isset($_COOKIE[self::COOKIE_GUID_NAME])){
      return $_COOKIE[self::COOKIE_GUID_NAME];
    }

    if (self::$config['intent_params']['publisher_user_id']) {
      return self::$config['intent_params']['publisher_user_id'];
    }

    return substr(self::createGUID(), 1, -1);
  }

  /**
   * Create a GUID value
   * @return String GUID 
   */
  public static function createGUID(){
    mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
    $charid = strtoupper(md5(uniqid(mt_rand(), true)));
    $hyphen = chr(45);// "-"
    $uuid = chr(123)// "{"
      .substr($charid, 0, 8).$hyphen
      .substr($charid, 8, 4).$hyphen
      .substr($charid,12, 4).$hyphen
      .substr($charid,16, 4).$hyphen
      .substr($charid,20,12)
      .chr(125);// "}"
    return $uuid;
  }

  /**
   * Get visitor value from cookie
   * @return String|float Value between 0 and 1
   */
  public static function getCookieValue () {
    if (isset($_COOKIE[self::COOKIE_NAME])) {
      return $_COOKIE[self::COOKIE_NAME];
    }

    // calculate widget probability if not set
    if (!self::$config['widget_prob']) {
      self::$config['widget_prob'] = mt_rand(0, 100000) / 100000;
    }
    return self::$config['widget_prob'];
  }

  /**
   * AJAX call to update Widget Countries
   * @return [type] [description]
   */
  public static function saveWidgetCountries () {
    $widgets = $_POST['widgets'];
    if (!isset($widgets)) wp_send_json_error('Missing property', 400);
    
    $widgets = stripslashes($widgets);
    $widgets = json_decode($widgets);
    
    update_option('pp_widgets_widget_countries', $widgets);
    
    wp_send_json($widgets);
  }

  /**
   * Sets force_intent cookie to 1
   */
  public static function setForceIntentCookieOn () {
    setcookie(
      'force_intent',
      1,
      time() + (60 * 60 * 24), // 1 day
      '/'
    );
    ?>
    <p>Force Intent Cookie is On</p>
    <?php
    wp_die();
  }

  /**
   * Sets force_intent cookie to 0 / deletes cookie
   */
  public static function setForceIntentCookieOff () {
    setcookie(
      'force_intent',
      0,
      0,
      '/'
    );
    ?>
    <p>Force Intent Cookie is Off</p>
    <?php
    wp_die();
  }

  /**
   * Remember to 'flush' rewrite rules upon changes
   * >> settings->permalinks->save (without making changes)
   */
  public static function addForceIntentRewriteRule () {
    // Turn ON force_intent cookie
    add_rewrite_rule(
      '^force_intent_on',
      'wp-admin/admin-ajax.php?action=pp_widgets_force_intent_on',
      'top'
    );

    // Turn OFF force_intent cookie
    add_rewrite_rule(
      '^force_intent_off',
      'wp-admin/admin-ajax.php?action=pp_widgets_force_intent_off',
      'top'
    );
  }

  /**
   * HOOK to save POST meta
   * @param  Integer|String $post_id [description]
   */
  public static function savePostMeta ($post_id) {
    if (array_key_exists('pp_widgets_post_city', $_POST)) {
      update_post_meta(
        $post_id,
        'pp_widgets_post_city',
        $_POST['pp_widgets_post_city']
      );
    }

    if (array_key_exists('pp_widgets_post_country', $_POST)) {
      update_post_meta(
        $post_id,
        'pp_widgets_post_country',
        $_POST['pp_widgets_post_country']
      );
    }
  }

  /**
   * Adds Metabox to POST/PAGE edit page
   */
  public static function addCityCountryMetaBox () {
    add_meta_box(
      'pp_widgets_location_mb',
      'Set City and Country',
      [self::class, 'metaboxCityCountryHtml'],
      ['post', 'page'],
      'normal'
    );
  }

  /**
   * HTML to insert in Metabox
   * @param  WP_Post $post [description]
   */
  public static function metaboxCityCountryHtml ($post) {
    $city = get_post_meta($post->ID, 'pp_widgets_post_city', true);
    $country = get_post_meta($post->ID, 'pp_widgets_post_country', true);
    ?>
    <p>Enter the City and Country for use with Intent Media</p>
    <input type="text" name="pp_widgets_post_city" placeholder="City" value="<?= $city; ?>"/>
    <input type="text" name="pp_widgets_post_country" placeholder="Country" value="<?= $country; ?>" />
    <?php
  }

  /**
   * Enqueue necessary plugin scripts
   * @return [type] [description]
   */
  public static function enqueueScripts() {
    // bootstrap  
    wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css');
    wp_enqueue_script( 'bootstrap','https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js', array( 'jquery' ),'',true );

    // Flatpickr
    wp_enqueue_style( 'flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
    wp_enqueue_script( 'flatpickr','https://cdn.jsdelivr.net/npm/flatpickr', array(),'',true );

    // plugin style
    wp_enqueue_style(
      'pp_widgets',
      plugins_url('static/pp-widgets.css', __FILE__),
      array(),
      self::VERSION
    );

    // plugin script
    wp_register_script( 
      'pp_widgets',
      plugins_url('static/pp-widgets.js', __FILE__),
      array('jquery'),
      self::VERSION,
      true
    );
    
    // Create Localized data
    $cookie_val = self::getCookieValue();
    $force_intent = self::$config['force_intent'];
    $widgetCountries = get_option('pp_widgets_widget_countries', []);
    $is_template_page = get_page_template_slug() == 'templates/page-pocketplanet.php';
    $city = get_post_meta(get_the_ID(), 'pp_widgets_post_city', true);
    $country = get_post_meta(get_the_ID(), 'pp_widgets_post_country', true);
    $utm_source = isset($_GET['utm_source']) ? $_GET['utm_source'] : '';
    
    $lData = array(
      'url' => site_url(),
      'camref' => self::$config['camref'],
      'source_code' => self::$config['source_code'],
      'force_intent' => $force_intet,
      'disable_intent' => !$force_intent && get_option('pp_widgets_disable_intent', false),
      'disable_smartertravel' => get_option('pp_widgets_disable_smartertravel', false),
      'user_cookie_value' => $cookie_val,
      'intent_params' => self::$config['intent_params'],
      'widget_countries' => $widgetCountries,
      'is_template_page' => $is_template_page,
      'country' => $country,
      'city' => $city,
      'enable_backtabs' => get_option('pp_widgets_enable_backtabs', false),
      'utm_source' => $utm_source
    );

    wp_localize_script('pp_widgets', 'localized_data', $lData);
    wp_enqueue_script('pp_widgets');
  }

  /**
   * Settings Section HTML
   */
  public static function sectionHtmlCallback(){
    ?>
    <p><strong>Remember</strong> to purge the cache when updating these values. Otherwise they will not be reflected on the frontend.</p>
    <?php
  }

  /**
   * Loads the 'upload background image' field
   */
  public static function backgroundImageHtmlCallback(){
    // WP function to add ability to use Media MOdal
    wp_enqueue_media();
    // Register plugin script
    wp_register_script(
      'pp_widgets_admin',
      plugins_url('static/pp-widgets-admin.js', __FILE__),
      array('jquery'),
      null,
      true
    );
    wp_enqueue_script('pp_widgets_admin');

    $image_id = get_option('pp_widgets_background_image', 0);
    $image_url = wp_get_attachment_url( $image_id );
    ?>
    <div class='image-preview-wrapper'>
      <img id='image-preview' src='<?php echo $image_url; ?>' width='100' height='100' style='max-height: 100px; width: 100px;'>
    </div>
    <input id="upload_image_button" type="button" class="button" value="<?php _e( 'Upload image' ); ?>" />
    <input type='hidden' name='pp_widgets_background_image' id='image_attachment_id' value=<?= $image_id; ?>>
    <?php
  }

  /**
   * Setting HTML: source_code
   */
  public static function sourceCodeHtmlCallback(){
    $value = get_option('pp_widgets_source_code');
    ?>
    <input type="text" name="pp_widgets_source_code" placeholder="" value="<?= $value; ?>" />
    <?php
  }

  /**
   * Setting HTML: camref
   */
  public static function camrefHtmlCallback(){
    $value = get_option('pp_widgets_camref');
    ?>
    <input type="text" name="pp_widgets_camref" placeholder="" value="<?= $value; ?>" />
    <?php
  }

  /**
   * Setting HTML: disable intent checkbox
   */
  public static function disableIntentHtmlCallback () {
    $value = get_option('pp_widgets_disable_intent', false);
    ?>
    <input type="checkbox" name="pp_widgets_disable_intent" value="1" <?php checked($value); ?> />
    <small>This will override any specific country setting</small>
    <?php 
  }

  /**
   * Setting HTML: disable smartertravel checkbox
   */
  public static function disableSmartertravelHtmlCallback () {
    $value = get_option('pp_widgets_disable_smartertravel', false);
    ?>
    <input type="checkbox" name="pp_widgets_disable_smartertravel" value="1" <?php checked($value); ?> />
    <small>This will override any specific country setting</small>
    <?php 
  }

  /**
   * Setting HTML: disable smartertravel checkbox
   */
  public static function enableBacktabHtmlCallback () {
    $value = get_option('pp_widgets_enable_backtabs', false);
    ?>
    <input type="checkbox" name="pp_widgets_enable_backtabs" value="1" <?php checked($value); ?> />
    <?php 
  }

  /**
   * Defines settings and their respective settings sections and fields
   */
  public static function initSettings() {
    
    add_settings_section(
      'pp_widgets_section_1',
      'Settings for pocketplanet widgets',
      [self::class, 'sectionHtmlCallback'],
      'pp_widgets');

    // add background image setting
    register_setting( 'pp_widgets', 'pp_widgets_background_image' );
    add_settings_field(
      'pp_widgets_background_image',
      'Set a fallback image to full width search pane',
      [self::class, 'backgroundImageHtmlCallback'],
      'pp_widgets',
      'pp_widgets_section_1'
    );

    // add camref setting
    register_setting( 'pp_widgets', 'pp_widgets_camref' );
    add_settings_field(
      'pp_widgets_camref',
      'SmarterAds Camref',
      [self::class, 'camrefHtmlCallback'],
      'pp_widgets',
      'pp_widgets_section_1'
    );

    // add source_code setting
    register_setting( 'pp_widgets', 'pp_widgets_source_code' );
    add_settings_field(
      'pp_widgets_source_code',
      'SmarterAds Source Code',
      [self::class, 'sourceCodeHtmlCallback'],
      'pp_widgets',
      'pp_widgets_section_1'
    );

    register_setting( 'pp_widgets', 'pp_widgets_disable_intent' );
    add_settings_field(
      'pp_widgets_disable_intent',
      'Disable Intent Media completely',
      [self::class, 'DisableIntentHtmlCallback'],
      'pp_widgets',
      'pp_widgets_section_1'
    );

    register_setting( 'pp_widgets', 'pp_widgets_disable_smartertravel' );
    add_settings_field(
      'pp_widgets_disable_smartertravel',
      'Disable Smarter Travel completely',
      [self::class, 'disableSmartertravelHtmlCallback'],
      'pp_widgets',
      'pp_widgets_section_1'
    );

    register_setting( 'pp_widgets', 'pp_widgets_enable_backtabs' );
    add_settings_field(
      'pp_widgets_enable_backtabs',
      'Enable "Backtab"',
      [self::class, 'enableBacktabHtmlCallback'],
      'pp_widgets',
      'pp_widgets_section_1'
    );
  }

  /**
   * Create the settings page (basically just wordpress stuff)
   */
  public static function settingsPageHtmlCallback() {
    ?>
    <div class="wrap">
      <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
      <form action="options.php" method="post">
      <?php
        settings_fields( 'pp_widgets' );
        do_settings_sections( 'pp_widgets' );
        submit_button( 'Save Settings' );
      ?>
      </form>
    </div>

    <?php
    $filename = '/templates/country-control.php';
    include dirname(__FILE__) . $filename;
  }

  /**
   * Add a submenu on the 'Settings' tab for our PP widgets settings
   */
  public static function addMenu() {
    add_submenu_page(
      'options-general.php',
      'PP Widgets',
      'PP Widgets',
      'manage_options',
      'pp_widgets',
      [self::class, 'settingsPageHtmlCallback']
    );
  }

  /**
   * Shortcode: Load the full width search from an external source
   */
  public static function basicShortcode($atts = [], $content = '', $tag = ''){
     // normalize attribute keys, lowercase
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
    // override default attributes with user attributes
    $parsed_atts = shortcode_atts([
      'type' => 'flights',
    ], $atts, $tag);

    $type = $parsed_atts['type'];
    $visible_type = in_array($type, ['flights', 'hotels', 'cars', 'cruises']) ? $type : 'flights';
    
    $filename = '/templates/full-width-search-all.php';
    ob_start();
    require_once(dirname(__FILE__) . $filename);
    return ob_get_clean();   
  }

  /**
   * Shortcode for Ad Rail vertical
   * @return String HTML
   */
  public static function adsRailShortcode () {
    return '<div id="pp-widgets-ad-rail"></div>';
  }

  /**
   * Shortcode for Ad Bottom vertical
   * @return String HTML
   */
  public static function adsBottomShortcode () {
    return '<div id="pp-widgets-ad-bottom"></div>';
  }

  /**
   * Sets the visitor widget probability cookie and visitor GUID cookie
   */
  public static function setCookie () {
    if (!isset($_COOKIE[self::COOKIE_NAME])){
      // get or create cookie value
      $value = self::getCookieValue();
      // set cookie
      setcookie(
        self::COOKIE_NAME,
        $value,
        time() + (60 * 10), // 10 minutes
        '/'
      );
    }

    if (!isset($_COOKIE[self::COOKIE_GUID_NAME])){
      // Create a GUID
      self::$config['intent_params']['publisher_user_id'] = substr(self::createGUID(), 1, -1);
      // set cookie
      setcookie(
        self::COOKIE_GUID_NAME,
        self::$config['intent_params']['publisher_user_id'],
        time() + (60 * 60 * 24 * 30), // 30 days
        '/'
      ); 
    }
  }

  /**
   * Add SmarterTravel Script in Header
   */
  public static function addSmarteradsScript () {
      ?>
  <script>
  !function(e,r,t){function n(e){var r,t=document.createElement("script");t.src="//p.smarter-js.com"+e,t.async=!0,(r=document.getElementsByTagName("script")[0]).parentNode.insertBefore(t,r)}var i,a=["/ext/partner/universal-integration/universal-integration-hosted.min.js"];(i=e.smarter=e.smarter||function(e){if("register"===e){i.API_KEY=arguments&&arguments[1]?arguments[1]:null;for(var r=0;r<a.length;r++)n(a[r])}else i._queue.push(arguments)})._init||(e.SmarterTravelNetworkNS="smarter",i.BOOTSTRAP_VERSION="2.2.0",i._init=!0,i._queue=[],i("register","tC3iwejhT2m8JNEKtbA6CA"))}(window);
  </script>
      <?php
  }
}

include "template-injector.php";
include "meta-boxes.php";

new PPWidgets();