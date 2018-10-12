<?php
/*
Author:       Morten Ege Jensen <ege.morten@gmail.com>
Author URI:   https://github.com/mortenege
License:      GPLv2 <https://www.gnu.org/licenses/gpl-2.0.html>
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// load iamge id from database
$pp_widgets_background_image_id = get_option('pp_widgets_background_image', 0);
// get url for image_id
$pp_widgets_image_url = wp_get_attachment_url( $pp_widgets_background_image_id );
if ($pp_widgets_image_url) {
  $style_attr = "style=\"background-image: url({$pp_widgets_image_url});\"";
} else {
  $style_attr = '';
}
?>

<div id="pp-widgets-full-width-search" <?php echo $style_attr; ?>>
  <div id="pp-widgets-full-width-search--switcher">
    <div class="switch-tab-button selected" data-tab="flights">
      <img src="https://d31st11mn3cu1v.cloudfront.net/images/home/menu_icon_flight.png"/>
      <span>Flights</span>
    </div>
    <div class="switch-tab-button" data-tab="hotels">
      <img src="https://d31st11mn3cu1v.cloudfront.net/images/home/menu_icon_hotels.png"/>
      <span>Hotels</span>
    </div>
    <div class="switch-tab-button" data-tab="cruises" >
      <img src="https://d31st11mn3cu1v.cloudfront.net/images/home/menu_icon_cruises.png"/>
      <span>Cruises</span>
    </div>
    <div class="switch-tab-button" data-tab="cars">
      <img src="https://d31st11mn3cu1v.cloudfront.net/images/home/menu_icon_car.png"/>
      <span>Cars</span>
    </div>

    <div style="clear:left;"></div>
  </div>

  <div style="display:block;" class="full-width-tab" data-tab="flights">
    <h1 class="pp-widgets-text">Search Flights</h1>
    <form id="pp_widgets_form" data-search-type="flight" data-page-id="flight.home" data-ad-unit-id="ppl_sca_flt_hom_xu_api">
      <div class="pp-widgets-search-area">
        <div class="form-check form-check-inline">
          <label class="pp-widgets-radio" for="pp-widgets-radio-1">
            <input type="radio" name="oneway" id="pp-widgets-radio-1" value="false" checked onclick="setOneway(false)">
            <span></span>
            <p>Return</p>
          </label>
        </div>
        <div class="form-check form-check-inline">
          <label class="pp-widgets-radio" for="pp-widgets-radio-2">
            <input type="radio" name="oneway" id="pp-widgets-radio-2" value="true" onclick="setOneway(true)">
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
              <label for="pp-widgets-date2">Returning</label>
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
        <div>&nbsp;</div>
      </div>
      <input type="hidden" name="nonstop" value="true" />
      <!--<input type="hidden" name="action" value="pp_widgets_form" />-->
    </form>
  </div>

  <div class="full-width-tab" data-tab="hotels">
    <h1 class="pp-widgets-text">Search Hotels</h1>
    <form id="pp_widgets_form" data-search-type="hotel" data-page-id="hotel.home" data-ad-unit-id="ppl_sca_hot_hom_xu_api">
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

  <div class="full-width-tab" data-tab="cars">
    <h1 class="pp-widgets-text">Search Rental Cars</h1>
    <form id="pp_widgets_form" data-search-type="car" data-page-id="car.home" data-ad-unit-id="ppl_sca_car_hom_xu_api">
      <div class="pp-widgets-search-area">
        <div class="form-row">
          <div class="col-12 col-md-4">
            <div class="form-group" style="position: relative;">
              <label for="pp-widgets-destination">Pick Up Location</label>
              <input type="text" name="destination" id="pp-widgets-destination" class="form-control" placeholder="City or country destination" autocomplete="off" data-search-types="city"/>
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

  <div class="full-width-tab" data-tab="cruises">
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

<script>
jQuery(document).ready(function($){
  $('.switch-tab-button').click(function(e){

    $('.switch-tab-button.selected').removeClass('selected');
    $(this).addClass('selected');

    let type = $(this).data('tab');
    let tabs = $('.full-width-tab');
    // clear
    tabs.each(function(index, el){
      let tab = $(el);
      let type = $(tab).data('tab');
      tab.css({display: 'none'});
    })

    // show tab
    $('.full-width-tab[data-tab="' + type + '"]').css({display: 'block'});
  });
});
</script>