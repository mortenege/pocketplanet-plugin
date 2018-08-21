<?php
/**
 * Author: Morten Ege Jensen <ege.morten@gmail.com>
 * License: May not be used without explicit consent from Author
 */
  if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

  // load iamge id from database
  $pp_widgets_background_image_id = get_option('pp_widgets_background_image_cars', 0);
  if (!$$pp_widgets_background_image_id) {
    $pp_widgets_background_image_id = get_option('pp_widgets_background_image', 0);
  }
  // get url for image_id
  $pp_widgets_image_url = wp_get_attachment_url( $pp_widgets_background_image_id );
  if ($pp_widgets_image_url) {
    $style_attr = "style=\"background: url({$pp_widgets_image_url}) no-repeat; background-size: cover;\"";
  } else {
    $style_attr = '';
  }
?>
<div id="pp-widgets-full-width-search" <?php echo $style_attr; ?>>
  <div style="width:100%;">
    <h1 class="pp-widgets-text">Search Rental Cars</h1>
    <form id="pp_widgets_form" data-search-type="car">
      <div class="pp-widgets-search-area">
        <div class="form-row">
          <div class="col-12 col-md-4">
            <div class="form-group" style="position: relative;">
              <label for="pp-widgets-destination">Pick Up Location</label>
              <input type="text" name="destination" id="pp-widgets-destination" class="form-control" placeholder="City or country destination" autocomplete="off" data-search-types="city,country"/>
              <div class="pp-widgets-suggestions" id="pp-widgets-destination-suggestions"></div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="form-group">
              <label for="pp-widgets-date1">Pick Up Date</label>
              <input type="text" name="date1" id="pp-widgets-date1" class="form-control" placeholder="yyyy-mm-dd" />
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="form-group">
              <label for="pp-widgets-date2">Drop Off Date</label>
              <input type="text" name="date2" id="pp-widgets-date2" class="form-control" placeholder="yyyy-mm-dd" />
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