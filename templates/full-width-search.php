<?php
/**
 * Author: Morten Ege Jensen <ege.morten@gmail.com>
 * License: May not be used without explicit consent from Author
 */
  if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

  // load iamge id from database
  $pp_widgets_background_image_id = get_option('pp_widgets_background_image', 0);
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
    <h1 class="pp-widgets-text">Search Flights</h1>
    <form id="pp_widgets_form">
      <div class="pp-widgets-search-area">
        <div class="form-check form-check-inline">
          <label class="pp-widgets-radio" for="pp-widgets-radio-1">
            <input type="radio" name="oneway" id="pp-widgets-radio-1" value="false" checked>
            <span></span>
            <p>Return</p>
          </label>
        </div>
        <div class="form-check form-check-inline">
          <label class="pp-widgets-radio" for="pp-widgets-radio-2">
            <input type="radio" name="oneway" id="pp-widgets-radio-2" value="true">
            <span></span>
            <p>One way</p>
          </label>
        </div>
        <div class="pp-widgets-row">
          <div class="pp-widgets-col pp-widgets-col-lg">
            <div class="form-group" style="position: relative;">
              <label for="pp-widgets-origin">Origin</label>
              <input type="text" name="origin" id="pp-widgets-origin" class="form-control" placeholder="City or Airport Code" autocomplete="off" />
              <div class="pp-widgets-suggestions" id="pp-widgets-origin-suggestions"></div>
            </div>
          </div>
          <div class="pp-widgets-col pp-widgets-col-lg">
            <div class="form-group" style="position: relative;">
              <label for="pp-widgets-destination">Destination</label>
              <input type="text" name="destination" id="pp-widgets-destination" class="form-control" placeholder="City or Airport code" autocomplete="off" />
              <div class="pp-widgets-suggestions" id="pp-widgets-destination-suggestions"></div>
            </div>
          </div>
          <div class="pp-widgets-col pp-widgets-col-md">
            <div class="form-group">
              <label for="pp-widgets-date1">Departing</label>
              <input type="text" name="date1" id="pp-widgets-date1" class="form-control" placeholder="yyyy-mm-dd" />
            </div>
          </div>
          <div class="pp-widgets-col pp-widgets-col-md">
            <div class="form-group">
              <label for="pp-widgets-date2">Arriving</label>
              <input type="text" name="date2" id="pp-widgets-date2" class="form-control" placeholder="yyyy-mm-dd" />
            </div>
          </div>
          <div class="pp-widgets-col pp-widgets-col-sm">
            <div class="form-group">
              <label for="pp-widgets-travelers">Travelers</label>
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
          <div class="pp-widgets-col pp-widgets-col-sm">
            <div class="form-group">
              <label for="pp-widgets-class">Class</label>
              <select name="class" class="form-control" id="pp-widgets-class">
                <option value="economy_coach" selected>Economy</option>
                <option value="business">Business</option>
                <option value="first">First</option>
              </select>
            </div>
          </div>
          <div class="pp-widgets-col pp-widgets-col-sm">
            <div class="form-group">
              <label>&nbsp;</label>
              <input type="submit" name="submit" value="Search" class="form-control"/>
            </div>
          </div>
          <div class="clearfix"></div>
        </div>
      </div>
      <input type="hidden" name="nonstop" value="true" />
      <!--<input type="hidden" name="action" value="pp_widgets_form" />-->
    </form>
  </div>
</div>