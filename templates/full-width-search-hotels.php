<?php
/*
Author:       Morten Ege Jensen <ege.morten@gmail.com>
Author URI:   https://github.com/mortenege
License:      GPLv2 <https://www.gnu.org/licenses/gpl-2.0.html>
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// load iamge id from database
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
    <h1 class="pp-widgets-text">Search Hotels</h1>
    <form id="pp_widgets_form" data-search-type="hotel">
      <div class="pp-widgets-search-area">
        <div class="form-row">
          <div class="col-12 col-md-4">
            <div class="form-group" style="position: relative;">
              <label for="pp-widgets-destination">Destination</label>
              <input type="text" name="destination" id="pp-widgets-destination" class="form-control" placeholder="City or country destination" autocomplete="off" data-search-types="city"/>
              <div class="pp-widgets-suggestions" id="pp-widgets-destination-suggestions"></div>
            </div>
          </div>
          <div class="col-6 col-sm-3 col-md-2">
            <div class="form-group">
              <label for="pp-widgets-date1">Check In</label>
              <input type="text" name="date1" id="pp-widgets-date1" class="form-control" placeholder="yyyy-mm-dd" />
            </div>
          </div>
          <div class="col-6 col-sm-3 col-md-2">
            <div class="form-group">
              <label for="pp-widgets-date2">Check Out</label>
              <input type="text" name="date2" id="pp-widgets-date2" class="form-control" placeholder="yyyy-mm-dd" />
            </div>
          </div>
          <div class="col-6 col-sm-3 col-md-1">
            <div class="form-group">
              <label for="pp-widgets-travelers">Guests</label>
              <select name="travelers" class="form-control" id="pp-widgets-travelers">
                <option>1</option>
                <option selected>2</option>
                <option>3</option>
                <option>4</option>
                <option>5</option>
                <option>6</option>
              </select>
            </div>
          </div>
          <div class="col-6 col-sm-3 col-md-1">
            <div class="form-group">
              <label for="pp-widgets-class">Rooms</label>
              <select name="rooms" class="form-control" id="pp-widgets-class">
                <option selected>1</option>
                <option>2</option>
                <option>3</option>
              </select>
            </div>
          </div>
          <div class="col-6 col-sm-3 col-md-2">
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