/*
Author:       Morten Ege Jensen <ege.morten@gmail.com>
Author URI:   https://github.com/mortenege
License:      GPLv2 <https://www.gnu.org/licenses/gpl-2.0.html>
*/

/**
 * Standard debounce function
 */
function debounce(func, wait, immediate) {
  var timeout;
  return function() {
    var context = this, args = arguments;
    var later = function() {
      timeout = null;
      if (!immediate) func.apply(context, args);
    };
    var callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
    if (callNow) func.apply(context, args);
  };
};

jQuery(document).ready(function($){
  // load params
  var camref = localized_data.camref;
  var source_code = localized_data.source_code;
  var date2Default = (new Date()).getTime() + 60*60*24*7*1000; // seven days from now
  var ip = localized_data.ip_address;

  /**
   * Build an API url from all segments
   * @param  {[type]} camref      [description]
   * @param  {[type]} source_code [description]
   * @param  {[type]} type        [description]
   * @param  {[type]} queryString [description]
   * @return {[type]}             [description]
   */
  function buildAPIUrl (camref, source_code, type, queryString) {
    var mode = type === 'flight' ? 'air' : type;
    return "https://prf.hn/click/camref:"+camref+"/adref:" + type + "_deeplink/destination:http://www.bookingbuddy.com/en/partner/hero/?mode=" + mode + "&source=" + source_code + "&" + queryString;
  }

  // init flatpickr
  $('#pp-widgets-date1').flatpickr({
    defaultDate: 'today',
  });
  $('#pp-widgets-date2').flatpickr({
    defaultDate: date2Default,
  });

  /**
   * Prefill 'origin' airport if not filled
   */
  $(function(){
    var $origin = $('#pp-widgets-origin').first();
    if (!$origin || $origin.val()) return;
    
    $.get('https://travelpayouts.com/whereami?locale=en&ip='+ip, function(response, status){
      var json = response
      var loc = json.name + ', ' + json.country_name + ' (' + json.iata + ')';
      $origin.val(loc);
      $origin.attr('data-code', json.iata);
      $origin.attr('data-country-name', json.country_name);
      $origin.attr('data-name', json.name);
    });
  })

  /**
   * Setup search functionality
   */
  $(function(){
    // find inputs and dropdowns
    var $destination_input = $('#pp-widgets-destination');
    var $origin_input = $('#pp-widgets-origin');
    var $destination_dropdown = $('#pp-widgets-destination-suggestions');
    var $origin_dropdown = $('#pp-widgets-origin-suggestions');

    /**
     * A click handler added to document to close all open dropdowns
     */
    var document_click_handler = function (e) {    
      close_dropdown_box($destination_dropdown);
      close_dropdown_box($origin_dropdown);
    };

    /**
     * Close a dropdown (but keep content)
     * @param  {[type]} $dropdown [description]
     */
    function close_dropdown_box($dropdown){
      $dropdown.hide();

      // unbind "close" handler
      $(document).off("click", document_click_handler);
    }

    /**
     * Clear content of dropdown
     * @param  {[type]} $dropdown [description]
     */
    function clear_dropdown_box($dropdown){
      $dropdown.empty();
    }

    /**
     * Open a dropdown and fill with rows
     * @param  {[type]} $input    [description]
     * @param  {[type]} $dropdown [description]
     * @param  {Array|Object} results   [description]
     */
    function open_dropdown_box($input, $dropdown, results, type = 'airport') {
      $dropdown.empty();
      // fill dropdown with data
      for (let i in results) {
        let loc = results[i]
        let str = parse_api_result(loc, type);
        let $el = $('<div></div>').text(str);
        $el.attr('data-code', loc.code);
        $el.attr('data-country-code', loc.country_code);
        $el.attr('data-country-name', loc.country_name);
        $el.attr('data-state-code', loc.state_code);
        $el.attr('data-city-name', loc.city_name || loc.name);
        $el.attr('data-type', loc.type);
        $el.attr('data-name', loc.name);
        

        $el.on('click', function(e){
          $input.val($(this).text());
          $input.attr('data-code', $(this).attr('data-code'));
          $input.attr('data-country-name', $(this).attr('data-country-name'));
          $input.attr('data-country-code', $(this).attr('data-country-code'));
          $input.attr('data-state-code', $(this).attr('data-state-code'));
          $input.attr('data-city-name', $(this).attr('data-city-name'));
          $input.attr('data-type', $(this).attr('data-type'));
          $input.attr('data-name', $(this).attr('data-name'));
          close_dropdown_box($dropdown);
          e.stopPropagation();
        });
        $dropdown.append($el);
      }

      if (Object.keys(results).length === 0) {
        $dropdown.append($('<span></span>').text('No results found'));
      }
      // show dropdown
      $dropdown.show();

      // attach "close" handler
      $(document).off("click", document_click_handler);
      $(document).on("click", document_click_handler);
    }

    /**
     * Shorthand to parse the incoming object
     * @param  {object} a    [description]
     * @param  {string} type [description]
     * @return {string}      [description]
     */
    function parse_api_result (a, type) {
      var str = '';
      if (a.type == "airport") {
        str = a.name;
        str += str.indexOf(a.city_name) >= 0 ? '' : ', ' + a.city_name;
        str += a.state_code ? ', ' + a.state_code : (
          ['GB'].indexOf(a.country_code) >= 0 ? ', ' + a.country_code : ', ' + a.country_name
            );
        str += ' (' + a.code + ')';
      } else if (a.type == "city" && type == 'airport') {
        str = a.main_airport_name || a.name;
        str += str.indexOf(a.name) >= 0 ? '' : ', ' + a.name;
        str += a.state_code ? ', ' + a.state_code : (
          ['GB'].indexOf(a.country_code) >= 0 ? ', ' + a.country_code : ', ' + a.country_name
            );
        str += ' (' + a.code + ')';
      } else if (a.type == 'city'){
        str = a.name;
        str += a.state_code ? ', ' + a.state_code : '';
        str += ', ' + a.country_name;
      } else {
        str = a.name;
      }
      return str;
    }

    /**
     * Use API to search for cities, countries and airports
     * @param  {[type]} e         [description]
     */
    function search_api(e){
      // get input and dropdown elements
      var $input = $(this);
      var $dropdown = $input.closest('div.form-group').find('.pp-widgets-suggestions');
      // clear infrmation
      $input.removeAttr('data-code');
      $input.removeAttr('data-country-name');
      $input.removeAttr('data-city-name');
      $input.removeAttr('data-country-code');
      $input.removeAttr('data-state-code');
      $input.removeAttr('data-type');
      $input.removeAttr('data-name');

      // get searcg term
      var val = $input.val();
      // get types of search
      var types = $input.attr('data-search-types') || 'city,airport';
      types = types.split(',').map(function(type){
        return 'types[]=' + type.trim();
      }).join('&');
      // generate url
      var url = 'https://autocomplete.travelpayouts.com/places2?term=' + val + '&locale=en&' + types;

      clear_dropdown_box($dropdown);
      if (!val) {
        close_dropdown_box($dropdown);
        return;
      }

      $.get(url, function(response, status){
        var type = types.indexOf('airport') >= 0 ? 'airport' : 'city';
        open_dropdown_box($input, $dropdown, response, type);
      });
    }

    // Setup destinaton searh
    $destination_input.keyup( debounce(search_api, 250) );
    $destination_input.on('click', function(e){
      e.stopPropagation();
    });
    $destination_input.focus(function(e){
      $origin_dropdown.hide();
      $destination_dropdown.show();
      // attach "close" handler
      $(document).on("click", document_click_handler);
    });

    // setup origin search
    $origin_input.keyup( debounce(search_api, 250) );
    $origin_input.on('click', function(e){
      e.stopPropagation();
    });
    $origin_input.focus(function(e){
      $origin_dropdown.show();
      $destination_dropdown.hide();
      // attach "close" handler
      $(document).on("click", document_click_handler);
    });
  });

  /**
   * Validate and Submit form
   */
  $('#pp_widgets_form').on('submit', function(event){
    event.preventDefault();
    var search_type = $(this).attr('data-search-type');
    
    if (search_type === 'flight') {
      var $origin = $(this).find('input[name="origin"]');
      $origin = $origin.length > 0 ? $($origin[0]) : null;
      $origin.removeClass('is-invalid');
      if (!$origin.val()){
        $origin.addClass('is-invalid');
        alert('Please enter a departing city or airport');
        return;
      }
    }

    // destination is there for all search types
    var $destination = $(this).find('input[name="destination"]');
    $destination = $destination.length > 0 ? $($destination[0]) : null;
    if ($destination) {
      $destination.removeClass('is-invalid');
      if (!$destination.val()){
        $destination.addClass('is-invalid');
        alert('Please enter a destination');
        return;
      }
    }
    // set rest to standard values
    var data = $(this).serializeArray();
    data = data.reduce((obj, item) => {
      obj[item.name] = item.value;
      return obj;
      }, {})

    if (search_type !== 'cruise') {
      data['destination'] = $destination.val();
      data['travelers'] = data['travelers'] || 2;
      data['date1'] = data['date1'] || (new Date()).toJSON().slice(0, 10);
      data['date2'] = data['date2'] || (new Date(date2Default)).toJSON().slice(0, 10);
    }
    
    if (search_type === 'flight') {
      data['origin'] = $origin.attr('data-code') || $origin.val();
      data['class'] = data['class'] || 'economy_coach';
      data['oneway'] = data['oneway'] === 'false' || data['oneway'] === 'true' ? data['oneway'] : 'false';
      data['nonstop'] = data['nonstop'] === 'false' || data['nonstop'] === 'true' ? data['nonstop'] : 'true';    
    } else if (search_type === 'hotel') {
      data['rooms'] = data['rooms'] || 1;
    }

    /*
     * for INTENT
     */
    if (localized_data.widget1 == 1 && search_type !== 'cruise') {
      // Hack to get first value in suggestboxes
      if ($origin) {
        let $origin_dropdown = $origin.closest('div.form-group').find('.pp-widgets-suggestions');
        let $firstOrigin = $origin_dropdown.find('[data-code]').first();
        $firstOrigin = $firstOrigin.length > 0 ? $($firstOrigin) : null;
      }
      let $destination_dropdown = $destination.closest('div.form-group').find('.pp-widgets-suggestions');
      let $firstDestination = $destination_dropdown.find('[data-code]').first();
      $firstDestination = $firstDestination.length > 0 ? $($firstDestination) : null;

      let data2 = localized_data.intent_params; 
      data2['cache_buster'] = new Date().getTime();
      data2['travelers'] = data['travelers'];
      data2['travel_date_start'] = data['date1'].replace(/-/g, '');
      data2['travel_date_end'] = data['date2'].replace(/-/g, '');
      data2['privacy_policy_link'] = 'https://pocketplanet.com/privacy-policy/';

      if (search_type === 'flight') {
        data2['ad_unit_id'] = 'ppl_sca_flt_hom_xu_api';
        data2['page_id'] = 'flight.home';
        data2['product_category'] = 'FLIGHTS';
        data2['trip_type'] = data['oneway'] ? 'oneway' : 'roundtrip';
        data2['flight_origin'] = $origin.attr('data-code') || 
          ($firstOrigin ? $firstOrigin.attr('data-code') : data['origin']);
        data2['flight_destination'] = $destination.attr('data-code') || 
          ($firstDestination ? $firstDestination.attr('data-code') : $data['destination']);
      }

      if (search_type === 'hotel') {
        data2['ad_unit_id'] = 'ppl_sca_hot_hom_xu_api';
        data2['page_id'] = 'hotel.home';
        data2['product_category'] = 'HOTELS';
        data2['hotel_rooms'] = data['rooms'];
        let destination_arr = data['destination'].split(',')[0];
        let destination_city = destination_arr[0];
        let destination_country = destination_arr.length > 1 ? destination_arr[1] : '';
        data2['hotel_city_name'] = $destination.attr('data-city-name') || 
          ($firstDestination ? $firstDestination.attr('data-city-name') : destination_city);
        data2['hotel_country_code'] = $destination.attr('data-country-code') || 
          ($firstDestination ? $firstDestination.attr('data-country-code') : destination_country);
        data2['hotel_state_code'] = '';
        if (data2['hotel_country_code'] == 'US') {
          let state_code = $destination.attr('data-state-code') || 
            ($firstDestination ? $firstDestination.attr('data-state-code') : null);
          if (state_code) {
            data2['hotel_state_code'] = state_code;
          }
        }
      }

      if (search_type === 'car') {
        data2['ad_unit_id'] = 'ppl_sca_car_hom_xu_api';
        data2['page_id'] = 'car,home';
        data2['product_category'] = 'CARS';
        data2['car_pickup_time'] = '1200';
        data2['car_dropoff_time'] = '1000';

        let destination_arr = data['destination'].split(',')[0];
        let destination_city = destination_arr[0];
        let destination_country = destination_arr.length > 1 ? destination_arr[1] : '';

        data2['car_pickup_city'] = $destination.attr('data-city-name') || 
          ($firstDestination ? $firstDestination.attr('data-city-name') : destination_city);
        data2['car_pickup_country'] = $destination.attr('data-country-code') || 
          ($firstDestination ? $firstDestination.attr('data-country-code') : destination_country);
        data2['car_pickup_state'] = ''
        if (data2['car_pickup_country'] == 'US') {
          let state_code = $destination.attr('data-state-code') || 
            ($firstDestination ? $firstDestination.attr('data-state-code') : null);
          if (state_code) {
            data2['car_pickup_state'] = state_code;
          }
        }
        data2['car_dropoff_city'] = data2['car_pickup_city'];
        data2['car_dropoff_country'] = data2['car_pickup_country'];
        data2['car_dropoff_state'] = data2['car_pickup_state'];
      }

      var queryString = jQuery.param(data2);
      if (window.IntentIsBlocked === false || window.IntentIsBlocked === null) {
        console.log('NOT BLOCKED');
        var url = "https://a.intentmedia.net/api/sca/v1/exit_units?" + queryString;
      } else {
        console.log('BLOCKED');
        var url = "https://compare.pocketplanet.com/api/sca/v1/exit_units?alt_svc=Y&" + queryString;
      }
      var win = window.open();
      $.get(url, function(response){
        if (response && 'url' in response) {
          win.location.href = response.url + "&nolimit=true&popsOver=true";
        } else {
          win.close();
        }
      });
      return;
    }

    var queryString = jQuery.param(data);
    var url = buildAPIUrl(camref, source_code, search_type, queryString);
    window.open(url);
  });
});