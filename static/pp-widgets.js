/**
 * Author: Morten Ege Jensen <ege.morten@gmail.com>
 * license: May not be used without explicit consent of Author
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
    
    $.get('http://travelpayouts.com/whereami?locale=en&ip='+ip, function(response, status){
      var json = response
      var loc = json.name + ', ' + json.country_name + ' (' + json.iata + ')';
      $origin.val(loc);
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
    function open_dropdown_box($input, $dropdown, results) {
      $dropdown.empty();
      // fill dropdown with data
      for (let key in results) {
        let $el = $('<div></div>').text(results[key]);
        $el.attr('data-code', key);
        $el.on('click', function(e){
          $input.val($(this).text());
          $input.attr('data-code', $(this).attr('data-code'));
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

      // get searcg term
      var val = $input.val();
      // get types of search
      var types = $input.attr('data-search-types') || 'country,city,airport';
      types = types.split(',').map(function(type){
        return 'types[]=' + type.trim();
      }).join('&');
      // generate url
      var url = 'http://autocomplete.travelpayouts.com/places2?term=' + val + '&locale=en&' + types;

      clear_dropdown_box($dropdown);
      if (!val) {
        close_dropdown_box($dropdown);
        return;
      }

      $.get(url, function(response, status){
        var results = {};
        
        var type = types.indexOf('airport') >= 0 ? 'airport' : 'city';
        for (let i in response) {
          a = response[i]
          let str = parse_api_result(a, type);
          results[a.code] = str;
        }
        // console.log('----->', response, results);
        open_dropdown_box($input, $dropdown, results);
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

    var queryString = jQuery.param(data);
    var url = buildAPIUrl(camref, source_code, search_type, queryString);
    window.open(url);
  });
});