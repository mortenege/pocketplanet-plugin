/**
 * Author: Morten Ege Jensen <ege.morten@gmail.com>
 * license: May not be used without explicit consent of Author
 */
jQuery(document).ready(function($){
  var camref = localized_data.camref;
  var source_code = localized_data.source_code;
  var date2Default = (new Date()).getTime() + 60*60*24*7*1000; // seven days from now
  // var apiurlFlight = "https://prf.hn/click/camref:{camref}/adref:flight_deeplink/destination:http://www.bookingbuddy.com/en/partner/tabs/?mode=air&source={source_code}&origin={origin_airport}&destination={destination_searched}&date1={departure_date}&date2={return_date}&travelers={number_of_travelers}&oneway={false_or_true}&nonstop={true_or_false}";
  var apiURLFlight = "https://prf.hn/click/camref:"+camref+"/adref:flight_deeplink/destination:http://www.bookingbuddy.com/en/partner/hero/?mode=air&source="+source_code+"&";
  // init flatpickr
  $('#pp-widgets-date1').flatpickr({
    defaultDate: 'today',
  });
  $('#pp-widgets-date2').flatpickr({
    defaultDate: date2Default,
  });

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