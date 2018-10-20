/*
Author:       Morten Ege Jensen <ege.morten@gmail.com>
Author URI:   https://github.com/mortenege
License:      GPLv2 <https://www.gnu.org/licenses/gpl-2.0.html>
*/

var queryParam = window.location.search;
queryParam = queryParam.indexOf('?') === 0 ? queryParam.slice(1) : queryParam;
var urlParams = new URLSearchParams(queryParam);
var MODE_DEBUG = urlParams.get('debug');
MODE_DEBUG = MODE_DEBUG === 'true' ? true : false;
if (MODE_DEBUG) {
  console.log('Debug mode: ON');
}

function setOneway(oneway) {
  let el = document.getElementById('pp-widgets-date2');
  if (!el) return;
  el.disabled = oneway;
  
  el.classList.remove('disabled');
  if (el.disabled) {
    el.value = '';
    el.classList.add('disabled');
  }
}

function mergeObjects (o1, o2) {
  let objs = [o1, o2];
  let result =  objs.reduce(function (r, o) {
      Object.keys(o).forEach(function (k) {
          r[k] = o[k];
      });
      return r;
  }, {});
  return result;
}

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

var date1Default = new Date((new Date()).getTime() + 60*60*24*30*1000);
var date2Default = new Date((date1Default).getTime() + 60*60*24*7*1000); // + 7 days

// Check whether an input is a function
function isFn(uhhFn) { return typeof uhhFn === 'function'; }

// adblockers will intercept requests containing 'ad', some others block certain domains
// either way - if we can fetch an image from our CDN, we can get scripts & make requests
function intentMediaCDN() { 
  return window.location.protocol + '//a.cdn.intentmedia.net/images/ad.png'; 
}

function invisibleImageFrom(srcUrl) {
  // positioned off screen to top and left will not show scroll bars
  var invisible = 'position:absolute;left:-100px;top:-100px;height:1px;width:1px;pointer-events:none;';
  var img = document.createElement('img');
  img.setAttribute('src', srcUrl);
  img.setAttribute('style', invisible);
  return img;
}

function cleanup(adBlockedImg, timeout) {
  if (timeout) { clearTimeout(timeout); }
  if (adBlockedImg.parentElement && adBlockedImg.parentElement.contains(adBlockedImg)) {
      adBlockedImg.parentElement.removeChild(adBlockedImg);
  }
}

/*
 *
 * interface:
 *
 * adBlockDetector({
 *     onIsBlocked: doSomethingSpecial, // optional callback function
 *     onNotBlocked: doSomethingNormal, // optional callback function
 *     onTimedout: handleError, // optional callback function
 *     timeout: 1500 // optional integer
 * });
 *
 * */
function adBlockDetector(opts) {
  opts = opts || {};

  // img onsuccess / onerror fns are fired on all browsers w/ adBlockers
  // scripts, iframes & requests don't always signal when they are blocked
  var adBlockedImg = invisibleImageFrom(intentMediaCDN());

  var timeoutTimer = setTimeout(() => { // something went wrong - remove the img from DOM
      if (isFn(opts.onTimedout)) { opts.onTimedout(); }
      cleanup(adBlockedImg);
  }, opts.timeout || 1500);

  if (isFn(opts.onIsBlocked)) {
      adBlockedImg.onerror = () => { // img not loaded, our cdn is intercepted by adBlockers
          opts.onIsBlocked();
          cleanup(adBlockedImg, timeoutTimer);
      };
  }

  if (isFn(opts.onNotBlocked)) {
      adBlockedImg.onload = () => { // img loaded, we can request resources from our cdn
          opts.onNotBlocked();
          cleanup(adBlockedImg, timeoutTimer);
      };
  }

  document.body.appendChild(adBlockedImg);
}

/**
 * Check whether IntentMedia is Blocked by an adBlocker
 * @return {[type]} [description]
 */
(function(){
  window.IntentIsBlocked = null;
  if (!localized_data.is_template_page) return;

  adBlockDetector({
    onIsBlocked: function(){
      window.IntentIsBlocked = true;
      if (MODE_DEBUG) {
        console.log('Intent is BLOCKED');
      }
    },
    onNotBlocked: function(){
      window.IntentIsBlocked = false;
      if (MODE_DEBUG) {
        console.log('Intent is NOT BLOCKED');
      }
    }
  })
}());

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

/**
 * Standard Date formatting
 * @param  {String} date Any parsable date string
 * @return {String}      Date on format YYYY-MM-DD
 */
function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [year, month, day].join('-');
}

function shouldShowSmarter (placement) {
  if (['w1', 'w2', 'w3', 'w4', 'w5'].indexOf(placement) < 0) return false;
  if (!window.country_name) return false;
  let country = window.country_name.toLowerCase();
  let widgets;
  let user_value = localized_data.user_cookie_value;
  // find country in list
  for (let code in localized_data.widget_countries) {
    let o = localized_data.widget_countries[code]
    if (o.name.toLowerCase() !== country) continue;
    
    // convert to numbers
    widgets = {
      w1: +o.w1,
      w2: +o.w2,
      w3: +o.w3,
      w4: +o.w4,
      w5: +o.w5
    }
    break;
  }
  
  // console.log('Widget Countries', placement, country, user_value, widgets)
  if (!widgets) return false;
  return user_value < widgets[placement];
}

jQuery(document).ready(function($){

/* START LOAD ADS FUNCTION */
function loadAds (params) {
  if (MODE_DEBUG) {
    console.log('loadAds params', params);
  }

  let show_smarter_overlays = params.show_smarter_overlays === undefined ? false : params.show_smarter_overlays;
  let show_intent_overlays = params.show_intent_overlays === undefined ? false : params.show_intent_overlays;
  let force_intent = params.force_intent || false;
  let disable_intent = params.disable_intent || false;
  let disable_smartertravel = force_intent || params.disable_smartertravel || false;
  let is_template_page = params.is_template_page !== undefined ? params.is_template_page : true;

  // Load correct widgets
  let w2_id = params.w2 ? 'smartertravel_inline_r' : 'IntentMediaRail';
  let w3_id = params.w3 ? 'smartertravel_inline_b' : 'IntentMediaIntercard';
  $('#pp-widgets-ad-rail').attr('id', w2_id);
  $('#pp-widgets-ad-bottom').attr('id', w3_id);

  if (MODE_DEBUG) {
    console.log('Ad widget Rail', w2_id);
    console.log('Ad widget Bottom', w3_id);
    console.log('Overlays', show_smarter_overlays ? 'SmarterTravel': '', show_intent_overlays ? 'IntentMedia' : '');
  }

  // Create Dates
  let date1 = formatDate(date1Default.toString());
  let date2 = formatDate(date2Default.toString());

  // set locations
  let originCity = params.origin_city || null;
  let originCountry = params.origin_country || null;
  let destinationCity = params.destination_city || originCity;
  let destinationCountry = params.destination_country || originCountry;

  // Load SmarterTravel Ads
  if (!force_intent && !disable_smartertravel) {
    if (MODE_DEBUG) {
      console.log('Loading SmarterTravel');
    }
    // which SmarterTravel placements to load
    let loadPlacements = ['inlineB','inlineM','inlineR','leaveBehind','widgetNoCheckboxes'];
    if (show_smarter_overlays) {
      loadPlacements.push('mobileOverlay');
      loadPlacements.push('overlay');
    }

    // Load SmarterTravel
    smarter('reset');
    smarter('config', {
      loadPlacements: loadPlacements
    });
    smarter('context', {
      name: 'placement',
      handler: function() {
        return {
          phgAdrefId: params.camref,
          adVertical: 'hotel',            // One of 'air', 'hotel', 'car', 'vacation'
          locale: 'en_US',                // ISO standard locale code
          currency: 'USD',                // 3-character currency code
          date1: date1,                   // Departure date in 'YYYY-MM-DD' format
          date2: date2,                   // Return date in 'YYYY-MM-DD' format
          destinationName: destinationCity,  // Destination city, state, and/or country
          originName: originCity,         // Origin city, state, and/or country
          numAdults: 2,                   // Number of adults
          // flightType: 'roundtrip',     // One of 'roundtrip', or 'oneway'
          // flightServiceClass: 'economyCoach', // One of 'economyCoach', 'business', or 'firstClass'
          numRooms: 1,                    // Number of rooms
          time1: 'anytime',               // Time for date1. Integer from 0 to 23 or one of “anytime”, “morning”, “noon”, or “evening”
          time2: 'anytime',               // Time for date2. Integer from 0 to 23 or one of “anytime”, “morning”, “noon”, or “evening”
        }
      }
    });
    smarter('load');
  }

  // Load Intent Media Ads
  if (!is_template_page && (force_intent || !disable_intent)) {
    if (MODE_DEBUG) {
      console.log('Loading Intent');
    }

    window.IntentMediaProperties = {  
      site_name: 'POCKET_PLANET',
      site_country: 'ID',
      site_language: 'en',
      site_currency: 'USD',
      page_id: 'content.general',
      /*generic*/
      travel_date_start: date1,
      travel_date_end: date2,
      travelers: '2',

      hotel_city: destinationCity || originCity || undefined,
      hotel_country: destinationCountry || originCountry || undefined,

      // Overlays
      show_inactivity_overlays: !force_intent && !show_intent_overlays ? "N" : "Y",
      show_mouseleave_overlays: !force_intent && !show_intent_overlays ? "N" : "Y",
      show_mobile_ribbon: !force_intent && !show_intent_overlays ? "N" : "Y",
      show_ribbon: !force_intent && !show_intent_overlays ? "N" : "Y"
    };

    (function() {
      var script = document.createElement("script");
      var url = "https://compare.pocketplanet.com/javascripts/v1/p/alt_core.js";
      // var url = '//a.cdn.intentmedia.net/javascripts/v1/intent_media_core.js';
      script.src = url;
      script.async = true;
      document.getElementsByTagName("head")[0].appendChild(script);
    }());
  }
}
/* END LOAD ADS FUNCTION */

/* START CLASS */
class PPWidgetSearch {
  constructor (originInput, destinationInput) {
    this.$originInput = $(originInput);
    this.$destinationInput = $(destinationInput);

    // Setup destination searh
    this.$destinationInput.keyup( debounce(this.searchApi.bind(this), 250) );
    this.$destinationInput.on('click', function(e){
      e.stopPropagation();
    });
    this.$destinationInput.focus((e) => { // Use arrow function to keep refernce to 'this'
      this.getDropdown(this.$originInput).hide();
      this.getDropdown(this.$destinationInput).show();

      // attach "close" handler
      $(document).on("click", this.documentClickHandler.bind(this));
    });

    this.$originInput.keyup( debounce(this.searchApi.bind(this), 250) );
    this.$originInput.on('click', function(e){
      e.stopPropagation();
    });
    this.$originInput.focus((e) => {  // Use arrow function to keep refernce to 'this'
      this.getDropdown(this.$destinationInput).hide();
      this.getDropdown(this.$originInput).show();

      // attach "close" handler
      $(document).on("click", this.documentClickHandler.bind(this));
    });
  }

  fillOriginInput (city, country, iata) {
    if (!this.$originInput || this.$originInput.val()) return;

    let loc = city + ', ' + country + ' (' + iata + ')';
    this.$originInput.val(loc);
    this.$originInput.attr('data-code', iata);
    this.$originInput.attr('data-country-name', country);
    this.$originInput.attr('data-name', city);
  }

  getDropdown ($input) {
    return $input.closest('div.form-group').find('.pp-widgets-suggestions');
  }

  /**
   * A click handler added to document to close all open dropdowns
   */
  documentClickHandler () {
    this.closeDropdown(this.getDropdown(this.$destinationInput));
    this.closeDropdown(this.getDropdown(this.$originInput));
  }

  /**
   * Close a dropdown (but keep content)
   * @param  {[type]} $dropdown [description]
   */
  closeDropdown($dropdown){
    $dropdown.hide();

    // unbind "close" handler
    $(document).off("click", this.documentClickHandler.bind(this));
  }


  /**
   * Clear content of dropdown
   * @param  {[type]} $dropdown [description]
   */
  clearDropdown($dropdown){
    $dropdown.empty();
  }

  /**
   * Shorthand to parse the incoming object
   * @param  {object} a    [description]
   * @param  {string} type [description]
   * @return {string}      [description]
   */
  parseApiResult (a, type) {
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
   * Open a dropdown and fill with rows
   * @param  {[type]} $input    [description]
   * @param  {Array|Object} results   [description]
   */
  openDropdown($input, results, type = 'airport') {
    let vm = this;
    let $dropdown = this.getDropdown($input);
    $dropdown.empty();
    
    // fill dropdown with data
    for (let i in results) {
      let loc = results[i]
      let str = this.parseApiResult(loc, type);
      let $el = $('<div></div>').text(str);
      $el.attr('data-code', loc.code);
      $el.attr('data-country-code', loc.country_code);
      $el.attr('data-country-name', loc.country_name);
      $el.attr('data-state-code', loc.state_code);
      $el.attr('data-city-name', loc.city_name || loc.name);
      $el.attr('data-type', loc.type);
      $el.attr('data-name', loc.name);

      // Add click listener
      $el.on('click', function(e) {
        $input.val($(this).text());
        $input.attr('data-code', $(this).attr('data-code'));
        $input.attr('data-country-name', $(this).attr('data-country-name'));
        $input.attr('data-country-code', $(this).attr('data-country-code'));
        $input.attr('data-state-code', $(this).attr('data-state-code'));
        $input.attr('data-city-name', $(this).attr('data-city-name'));
        $input.attr('data-type', $(this).attr('data-type'));
        $input.attr('data-name', $(this).attr('data-name'));
        
        vm.closeDropdown($dropdown);
        
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
    $(document).off("click", this.documentClickHandler.bind(this));
    $(document).on("click", this.documentClickHandler.bind(this));
  }

  /**
   * Use API to search for cities, countries and airports
   * @param  {[type]} e         [description]
   */
  searchApi (e) {
    // get input and dropdown elements
    let $input = $(e.target);
    let $dropdown = this.getDropdown($input);
    
    // clear infrmation
    $input.removeAttr('data-code');
    $input.removeAttr('data-country-name');
    $input.removeAttr('data-city-name');
    $input.removeAttr('data-country-code');
    $input.removeAttr('data-state-code');
    $input.removeAttr('data-type');
    $input.removeAttr('data-name');

    // get searcg term
    let val = $input.val();

    // get types of search
    let types = $input.attr('data-search-types') || 'city,airport';
    types = types.split(',').map(function(type){
      return 'types[]=' + type.trim();
    }).join('&');
    
    // generate url
    let url = 'https://autocomplete.travelpayouts.com/places2?term=' + val + '&locale=en&' + types;

    this.clearDropdown($dropdown);
    if (!val) {
      this.closeDropdown($dropdown);
      return;
    }

    let vm = this;
    $.get(url, function(response, status){
      let type = types.indexOf('airport') >= 0 ? 'airport' : 'city';
      vm.openDropdown($input, response, type);
    });
  }

  genIntentBase (travelers, date1, date2) {
    let data = localized_data.intent_params; 
    data['cache_buster'] = new Date().getTime();
    data['travelers'] = travelers || 2;
    data['travel_date_start'] = date1.replace(/-/g, '');
    data['travel_date_end'] = date2.replace(/-/g, '');
    data['privacy_policy_link'] = 'https://pocketplanet.com/privacy-policy/';
    return data
  }

  genIntentFlight (oneway, origin, destination) {
    let data = {}
    data['ad_unit_id'] = 'ppl_sca_flt_hom_xu_api';
    data['page_id'] = 'flight.home';
    data['product_category'] = 'FLIGHTS';
    data['trip_type'] = oneway === 'true' ? 'oneway' : 'roundtrip';
    data['flight_origin'] = origin;
    data['flight_destination'] = destination;
    return data
  }

  genIntentHotel (rooms, city, country, state) {
    let data = {}
    data['ad_unit_id'] = 'ppl_sca_hot_hom_xu_api';
    data['page_id'] = 'hotel.home';
    data['product_category'] = 'HOTELS';
    data['hotel_rooms'] = rooms;
    data['hotel_city_name'] = city;
    data['hotel_country_code'] = country;
    data['hotel_state_code'] = '';
    if (data['hotel_country_code'] == 'US' && state) {
      data['hotel_state_code'] = state;
    }
    return data;
  }

  genIntentCar (city, country, state) {
    let data = {}
    data['ad_unit_id'] = 'ppl_sca_car_hom_xu_api';
    data['page_id'] = 'car.home';
    data['product_category'] = 'CARS';
    data['car_pickup_time'] = '1200';
    data['car_dropoff_time'] = '1000';

    data['car_pickup_city'] = city;
    data['car_pickup_country'] = country;
    data['car_pickup_state'] = ''
    if (data['car_pickup_country'] == 'US' && state) {
      data['car_pickup_state'] = state;
    }
    data['car_dropoff_city'] = data['car_pickup_city'];
    data['car_dropoff_country'] = data['car_pickup_country'];
    data['car_dropoff_state'] = data['car_pickup_state'];

    return data;
  }

  openIntentUrl (win, data, callback) {
    let queryString = jQuery.param(data);
    let url;
    if (window.IntentIsBlocked === false || window.IntentIsBlocked === null) {
      url = "https://a.intentmedia.net/api/sca/v1/exit_units?" + queryString;
    } else {
      url = "https://compare.pocketplanet.com/api/sca/v1/exit_units?alt_svc=Y&" + queryString;
    }

    var vm = this
    $.get(url, function(response){
      if (response && 'url' in response) {
        let url = response.url + "&nolimit=true&popsOver=true";
        win.location.href = url
        
        // Callback
        if (typeof callback === 'function') {
          callback.apply(vm);
        }
        if (MODE_DEBUG) {
          console.log('IntentAds XU url', url);
        }
      } else {
        console.error(url, response)
        win.close();
      }
    });
  }

  openPassthroughUrl (win, provider, data) {
    let d = {
      provider: provider,
      ad_block: window.IntentIsBlocked
    }

    d = mergeObjects(d, data);
    let queryString = jQuery.param(d);
    win.location.href = localized_data.home_url + '/passthrough?provider=' + provider + '&' + queryString
  }

  openSmarterUrl (win, camref, source_code, search_type, data, callback) {
    let queryString = jQuery.param(data);
    let url = buildAPIUrl(camref, source_code, search_type, queryString);
    win.location.href = url;

    // Callback
    if (typeof callback === 'function') {
      callback.apply(this);
    }
    if (MODE_DEBUG) {
      console.log('SmarterAds XU url', url);
    }  
  }

  submit (form, data, params) {
    let $origin = $(form).find('.pp-widgets-origin').first();
    let $destination = $(form).find('.pp-widgets-destination').first();
    let search_type = params.search_type || 'hotel';
    let camref = params.camref;
    let source_code = params.source_code;
    let utm_source = params.utm_source;

    // Validate origin input
    if (search_type === 'flight') {
      $origin.removeClass('is-invalid');
      if (!$origin.val()){
        $origin.addClass('is-invalid');
        alert('Please enter a departing city or airport');
        return;
      }
    }

    // Validate destination input
    if (search_type !== 'cruise') {
      $destination.removeClass('is-invalid');
      if (!$destination.val()){
        $destination.addClass('is-invalid');
        alert('Please enter a destination');
        return;
      }
    }

    // Open window as early as possible
    let win = window.open();

    // Turn serialized data into associated
    data = data.reduce((obj, item) => {
      obj[item.name] = item.value;
      return obj;
      }, {})

    if (search_type !== 'cruise') {
      data['travelers'] = data['travelers'] || 2;
      data['date1'] = data['date1'] || (new Date()).toJSON().slice(0, 10);
      data['date2'] = data['date2'] || (new Date(date2Default)).toJSON().slice(0, 10);

      // Normalize dates to not cause breaking APIs
      let d0, d1, d2;
      d0 = new Date();
      d1 = new Date(data['date1']);
      d2 = new Date(data['date2']);
      data['date1'] = d1 < d0 ? d0.toJSON().slice(0, 10) : data['date1'];
      d1 = new Date(data['date1']);
      data['date2'] = d2 < d1 ? new Date(d1.getTime() + 60*60*24*7*1000) : d2;
      data['date2'] = data['date2'].toJSON()  .slice(0, 10);
    }

    // Copy for redirect page
    let dataSmarterRedirect = mergeObjects({}, data);
    
    if (search_type === 'flight') {
      data['origin'] = $origin.attr('data-code') || $origin.val();
      data['class'] = data['class'] || 'economy_coach';
      data['oneway'] = data['oneway'] === 'false' || data['oneway'] === 'true' ? data['oneway'] : 'false';
      data['nonstop'] = data['nonstop'] === 'false' || data['nonstop'] === 'true' ? data['nonstop'] : 'true';    

      dataSmarterRedirect['rooms'] = 1;
    } else if (search_type === 'hotel') {
      data['rooms'] = data['rooms'] || 1;

      dataSmarterRedirect['oneway'] = 'true';
      dataSmarterRedirect['nonstop'] = 'true';
      dataSmarterRedirect['class'] = 'economy_coach';
      dataSmarterRedirect['origin'] = window.userLocation.iata || 'LAX';
    }

    /*
     * for INTENT
     */
    
    // Hack to get first value in suggestboxes
    let $firstOrigin, $firstDestination;
    if ($origin) {
      let $origin_dropdown = this.getDropdown($origin);
      $firstOrigin = $origin_dropdown.find('[data-code]').first();
      $firstOrigin = $firstOrigin.length > 0 ? $($firstOrigin) : null;
    }
    let $destination_dropdown = this.getDropdown($destination);
    $firstDestination = $destination_dropdown.find('[data-code]').first();
    $firstDestination = $firstDestination.length > 0 ? $($firstDestination) : null;

    // Generate Airport code, or city
    let origin_code = '';
    if ($origin) {
      origin_code = $origin.attr('data-code') || ($firstOrigin ? $firstOrigin.attr('data-code') : data['origin']);
    }
    let destination_code = $destination.attr('data-code') || ($firstDestination ? $firstDestination.attr('data-code') : $data['destination']);
    // Generate Destination city & country
    let destination_arr = data['destination'].split(',')
    let destination_city = destination_arr[0];
    let destination_country = destination_arr.length > 1 ? destination_arr[1] : '';
    destination_city = $destination.attr('data-city-name') || ($firstDestination ? $firstDestination.attr('data-city-name') : destination_city);
    destination_country = $destination.attr('data-country-code') || ($firstDestination ? $firstDestination.attr('data-country-code') : destination_country);
    let state_code = $destination.attr('data-state-code') || ($firstDestination ? $firstDestination.attr('data-state-code') : null);
    
    // Generate the base for intent
    let dataIntent = this.genIntentBase(data['travellers'], data['date1'], data['date2']);
    dataIntent['site_reporting_value_01'] = utm_source;
    let dataRedirect = mergeObjects({}, dataIntent);

    if (search_type === 'flight') {
      dataIntent = mergeObjects(dataIntent, this.genIntentFlight(data['oneway'], origin_code, destination_code))
      dataRedirect = mergeObjects(dataRedirect, this.genIntentHotel(1, destination_city, destination_country, state_code));
      dataRedirect['page_id'] = 'flight.home.hotxs';
      dataRedirect['ad_unit_id'] = 'ppl_sca_flt_hot_xs_hom_xu_api';
    } else if (search_type === 'hotel') {
      dataIntent = mergeObjects(dataIntent, this.genIntentHotel(data['rooms'], destination_city, destination_country, state_code))
      let iata = window.userLocation.iata;
      dataRedirect = mergeObjects(dataRedirect, this.genIntentFlight('roundtrip', iata, destination_code))
    } else if (search_type === 'car') {
      dataIntent = mergeObjects(dataIntent, this.genIntentCar(destination_city, destination_country, state_code))
    }

    // setup special ad unit id and page id for redirect
    // TODO

    let provider, obj1;
    if ((localized_data.force_intent || !shouldShowSmarter('w1')) && search_type !== 'cruise') {
      provider = 'intentmedia';
      obj1 = dataIntent
    } else {
      provider = 'smartertravel';
      obj1 = mergeObjects({
        camref: camref,
        source_code: source_code,
        search_type: search_type
      }, data);
    }

    this.openPassthroughUrl (win, provider, obj1);
    return;

    // Open for INTENT
    if ((localized_data.force_intent || !shouldShowSmarter('w1')) && search_type !== 'cruise') {
      this.openIntentUrl(win, dataIntent, function(){
        if (['flight', 'hotel'].indexOf(search_type) >= 0 && localized_data.enable_backtabs) {
          if (shouldShowSmarter('w5')) {
            let opposite = search_type === 'flight' ? 'hotel' : 'flight';
            this.openSmarterUrl(window, camref, source_code, opposite, dataSmarterRedirect);
          } else {
            this.openIntentUrl(window, dataRedirect);    
          }
        }
      });
      return;
    }

    // OR... Open for SMARTER
    this.openSmarterUrl(win, camref, source_code, search_type, data, function(){
      if (['flight', 'hotel'].indexOf(search_type) >= 0 && localized_data.enable_backtabs) {
        if (shouldShowSmarter('w5')) {
          let opposite = search_type === 'flight' ? 'hotel' : 'flight';
          this.openSmarterUrl(window, camref, source_code, opposite, dataSmarterRedirect);
        } else {
          this.openIntentUrl(window, dataRedirect);    
        }
      }
    });
    
  }
}
/* END CLASS */

  // Tab
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

  // init flatpickr
  $('.pp-widgets-date1').flatpickr({
    defaultDate: date1Default,
  });
  $('.pp-widgets-date2').flatpickr({
    defaultDate: date2Default,
  });

  // Init Search Widget
  const searchWidget = new PPWidgetSearch('.pp-widgets-origin', '.pp-widgets-destination');

  // Load user location
  $.get('https://travelpayouts.com/whereami?locale=en', function(response, status){
    if (MODE_DEBUG) {
      console.log('IP Geolocation', response);
    }
    // Set global variables
    window.userLocation = response
    window.country_name = response.country_name;

    // Fill Origin Input box
    searchWidget.fillOriginInput(response.name, response.country_name, response.iata);

    // load params
    let w4 = shouldShowSmarter('w4');
    let show_smarter_overlays = w4 || localized_data.is_template_page;
    let show_intent_overlays = !show_smarter_overlays;

    // Load adds now that we know the country
    loadAds({
      user_value: localized_data.user_cookie_value,
      camref: localized_data.camref,
      source_code: localized_data.source_code,
      show_smarter_overlays: show_smarter_overlays,
      show_intent_overlays: show_intent_overlays,
      disable_intent: localized_data.disable_intent,
      disable_smartertravel: localized_data.disable_smartertravel,
      force_intent: localized_data.force_intent,
      origin_city: response.name,
      origin_country: response.country_name,
      origin_iata: response.iata,
      destination_city: localized_data.city,
      destination_country: localized_data.country,
      w2: shouldShowSmarter('w2'),
      w3: shouldShowSmarter('w3'),
      w4: w4,
      is_template_page: localized_data.is_template_page,
    });
  });

  // This code is necessary so that SmarterTravel doesn't open a pop up first
  $('.pp_widgets_form').find('[type=submit]').click(function(e){
    e.preventDefault();
    $(this).closest('.pp_widgets_form').submit();
  });

  // Submit form
  $('.pp_widgets_form').on('submit', function(event){
    event.preventDefault();
    
    searchWidget.submit(this, $(this).serializeArray(), {
      search_type: $(this).attr('data-search-type'),
      camref: localized_data.camref,
      source_code: localized_data.source_code,
      utm_source: localized_data.utm_source
    });

  });
});