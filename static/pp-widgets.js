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
  var apiURLFlight = "https://prf.hn/click/camref:"+camref+"/adref:flight_deeplink/destination:http://www.bookingbuddy.com/en/partner/hero/?mode=air&source="+source_code+"&";
  var ip = localized_data.ip_address;

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
   * Setup airport search functionality
   */
  $(function(){
    // find inputs and dropdowns
    var $destination_dropdown = $('#pp-widgets-destination-suggestions');
    var $destination_input = $('#pp-widgets-destination');
    var $origin_dropdown = $('#pp-widgets-origin-suggestions');
    var $origin_input = $('#pp-widgets-origin');

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
     * Use API to search for airport names and codes
     * @param  {[type]} e         [description]
     * @param  {[type]} $input    [description]
     * @param  {[type]} $dropdown [description]
     */
    function search_airports(e, $input, $dropdown){
      var val = $input.val();
      var url = 'http://autocomplete.travelpayouts.com/places2?term=' + val + '&locale=en&types[]=airport';

      clear_dropdown_box($dropdown);
      if (!val) {
        close_dropdown_box($dropdown);
        return;
      }

      $.get(url, function(response, status){
        var airports = {};
        for (let i in response) {
          a = response[i]
          let str = a.name
          str += str.indexOf(a.city_name) >= 0 ? '' : ', ' + a.city_name;
          str += a.state_code ? ', ' + a.state_code : (
            ['GB'].indexOf(a.country_code) >= 0 ? ', ' + a.country_code : ', ' + a.country_name
              );
          str += ' (' + a.code + ')';
          airports[a.code] = str;
        }
        // console.log('----->', response, airports);
        open_dropdown_box($input, $dropdown, airports);
      });
    }

    /**
     * Shortcut to search for destinations
     * @param  {[type]} e [description]
     */
    function search_airports_destination (e) {
      search_airports(e, $destination_input, $destination_dropdown);
    }

    /**
     * Shortcut to search for origins
     * @param  {[type]} e [description]
     */
    function search_airports_origin (e) {
      search_airports(e, $origin_input, $origin_dropdown);
    }

    // Setup destinaton searh
    $destination_input.keyup( debounce(search_airports_destination, 250) );
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
    $origin_input.keyup( debounce(search_airports_origin, 250) );
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

    var $origin = $(this).find('input[name="origin"]');    
    var $destination = $(this).find('input[name="destination"]');
    $origin = $origin.length > 0 ? $($origin[0]) : null;
    $destination = $destination.length > 0 ? $($destination[0]) : null;
    // Validate these two fields
    $origin.removeClass('is-invalid');
    if (!$origin.val()){
      $origin.addClass('is-invalid');
      alert('Please enter a departing city or airport');
      return;
    }
    $destination.removeClass('is-invalid');
    if (!$destination.val()){
      $destination.addClass('is-invalid');
      alert('Please enter a destination city or airport');
      return;
    }

    // set rest to standard values
    //var url = [localized_data.url, 'wp-admin', "admin-post.php"].join('/');
    var data = $(this).serializeArray();
    data = data.reduce((obj, item) => {
      obj[item.name] = item.value;
      return obj;
      }, {})
    data['origin'] = $origin.attr('data-code') || $origin.val();
    data['destination'] = $destination.attr('data-code') || $destination.val();
    data['class'] = data['class'] || 'economy_coach';
    data['travelers'] = data['travelers'] || 2;
    data['date1'] = data['date1'] || (new Date()).toJSON().slice(0, 10);
    data['date2'] = data['date2'] || (new Date(date2Default)).toJSON().slice(0, 10);
    data['oneway'] = data['oneway'] === 'false' || data['oneway'] === 'true' ? data['oneway'] : 'false';
    data['nonstop'] = data['nonstop'] === 'false' || data['nonstop'] === 'true' ? data['nonstop'] : 'true';    

    //$(this).attr('method', 'post');
    //$(this).attr('action', url);
    // var queryString = $(this).serialize();
    // $(this).submit();
    var queryString = jQuery.param(data);
    window.open(apiURLFlight + queryString);
    /*
    $(this).find('input[type=text], select').each(function(){
      var name = $(this).attr('name');
      var val = $(this).val();

      $(this).removeClass('is-invalid');
      switch(name) {
        case 'origin':
          if (!val) {
            $(this).addClass('is-invalid');
            alert('Please enter a departing city or airport');
            return;
          }
          break;
        case 'destination':
          if (!val) {
            $(this).addClass('is-invalid');
            alert('Please enter a destination city or airport');
            return;
          }
          break;
      }
    });
    */
  });
});