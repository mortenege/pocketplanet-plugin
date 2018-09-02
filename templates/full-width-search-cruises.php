<?php
/*
Author:       Morten Ege Jensen <ege.morten@gmail.com>
Author URI:   https://github.com/mortenege
License:      GPLv2 <https://www.gnu.org/licenses/gpl-2.0.html>
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// load iamge id from database
// $pp_widgets_background_image_id = get_option('pp_widgets_background_image_cars', 0);
$pp_widgets_background_image_id = get_post_meta(get_the_ID(), 'pp_widgets_background_image', true);
if (!$pp_widgets_background_image_id) {
  $pp_widgets_background_image_id = get_option('pp_widgets_background_image', 0);
}
// get url for image_id
$pp_widgets_image_url = wp_get_attachment_url( $pp_widgets_background_image_id );
if ($pp_widgets_image_url) {
  $style_attr = "style=\"background-image: url({$pp_widgets_image_url});\"";
} else {
  $style_attr = '';
}
?>
<div id="pp-widgets-full-width-search" <?php echo $style_attr; ?>>
  <div style="width:100%;">
    <h1 class="pp-widgets-text">Search Cruises</h1>
    <form id="pp_widgets_form" data-search-type="cruise">
      <div class="pp-widgets-search-area">
        <div class="form-row">
          <div class="col-12 col-md-3">
            <div class="form-group">
              <label for="pp-widgets-cruise-destination">Destination</label>
              <select name="cruise_destination" id="pp-widgets-cruise-destination" class="form-control" >
                <option value="100000003" selected>Any Destination</option>
                <option value="100000001">Africa</option>
                <option value="100000002">Alaska</option>
                <option value="100000004">Antarctica</option>
                <option value="100000005">Asia</option>
                <option value="100000006">Australia</option>
                <option value="100000007">Bahamas</option>
                <option value="100000008">Bermuda</option>
                <option value="100000009">Canada/New England</option>
                <option value="100000010">Caribbean</option>
                <option value="100000011">Caribbean East</option>
                <option value="100000012">Caribbean South</option>
                <option value="100000013">Caribbean West</option>
                <option value="100000014">Central America</option>
                <option value="100000015">China</option>
                <option value="100000016">Cruise to No Where</option>
                <option value="100000017">Europe</option>
                <option value="100000018">Europe E. Mediterranean</option>
                <option value="100000019">Europe Northern</option>
                <option value="100000020">Europe W. Mediterranean</option>
                <option value="100000021">Galapagos</option>
                <option value="100000022">Hawaii</option>
                <option value="100000023">Mediterranean</option>
                <option value="100000024">Mexico</option>
                <option value="100000025">Middle East</option>
                <option value="100000026">Pacific Coastal</option>
                <option value="100000027">Panama Canal</option>
                <option value="100000028">South America
                <option value="100000029">South Pacific</option>
                <option value="100000030">Tahiti</option>
                <option value="100000031">Transatlantic</option>
                <option value="100000032">Transpacific</option>
                <option value="100000033">U.S. River</option>
                <option value="100000034">United States</option>
                <option value="100000035">World Cruise</option>
              </select>
            </div>
          </div>
          <div class="col-12 col-md-3">
            <div class="form-group">
              <label for="pp-widgets-cruise-line">Cruise Line</label>
              <select name="cruise_line" id="pp-widgets-cruise-line" class="form-control" >
                <option value="Any Cruise Line">Any Cruise Line</option>
                <option value="Carnival">Carnival</option>
                <option value="Celebrity">Celebrity</option>
                <option value="Costa Cruises">Costa Cruises</option>
                <option value="Crystal Cruises">Crystal Cruises</option>
                <option value="Cunard">Cunard</option>
                <option value="Disney">Disney</option>
                <option value="Holland America">Holland America</option>
                <option value="Norwegian">Norwegian</option>
                <option value="Oceania">Oceania</option>
                <option value="Orient">Orient</option>
                <option value="Princess">Princess</option>
                <option value="Radisson Seven Seas">Radisson Seven Seas</option>
                <option value="Royal Caribbean">Royal Caribbean</option>
                <option value="Seabourn">Seabourn</option>
                <option value="Silversea">Silversea</option>
                <option value="Windstar">Windstar</option>
              </select>
            </div>
          </div>
          <div class="col-6 col-md-2">
            <div class="form-group">
              <label for="pp-widgets-cruise-month">Departure</label>
              <select name="cruise_month" id="pp-widgets-cruise-month" class="form-control">
                <?php
                  $now = date('Y-m');
                  for ($i=0;$i<12;$i++){
                    $short = date('Y-m', strtotime("{$date} +{$i} months"));
                    $long = date('M Y', strtotime("{$date} +{$i} months"));
                  ?>
                  <option value="<?= $short; ?>" <?= ($i === 0 ? 'selected' : ''); ?>><?= $long; ?></option>
                  <?php
                  }
                ?>
              </select>
            </div>
          </div>
          <div class="col-6 col-md-2">
            <div class="form-group">
              <label for="pp-widgets-cruise-length">Length</label>
              <select name="cruise_length" id="pp-widgets-cruise-length" class="form-control">
                <option selected>Any Length</option>
                <option>3-6 Nights</option>
                <option>7-9 Nights</option>
                <option>10-14 Nights</option>
              </select>
            </div>
          </div>
          <div class="col-6 col-md-2">
            <div class="form-group">
              <label>&nbsp;</label>
              <input type="submit" name="submit" value="Search" class="form-control"/>
            </div>
          </div>
          <div class="clearfix"></div>
        </div>
        <div>&nbsp;</div>
      </div>
    </form>
  </div>
</div>