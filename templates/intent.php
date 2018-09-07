<button id="intentBtn">Click</button>
<script>
window.IntentMediaProperties = { 
  site_name: 'POCKET_PLANET', 
  page_id: 'flight.home', 
  site_country: 'ID', 
  site_language: 'en', 
  site_currency: 'USD', 
};
(function() {
  var script = document.createElement("script");
  var url = '//a.cdn.intentmedia.net/javascripts/v1/intent_media_core.js';
  script.src = url;
  script.async = true;
  document.getElementsByTagName("head")[0].appendChild(script);
}());
/*
window.IntentMediaProperties = { 
  site_name: 'POCKET_PLANET', 
  page_id: 'flight.home', 
  site_country: 'ID', 
  site_language: 'en', 
  site_currency: 'USD', 
}; 

function load() {
  var script = document.createElement("script");
  var url = 'https://a.cdn.intentmedia.net/javascripts/v1/intent_media_core.js';
  script.src = url;
  script.async = true;
  document.getElementsByTagName("head")[0].appendChild(script);
}

function load2(){
  var head = document.getElementsByTagName('head')[0];
  var script = document.createElement('script');
  script.type = 'text/javascript';

  // var url = "<?= plugins_url('/static/intent-core.js', dirname(__FILE__)); ?>"
  var url = '//a.cdn.intentmedia.net/javascripts/v1/intent_media_core.js';
  script.src = url;
  head.appendChild(script);
}
*/
jQuery(document).ready(function($){
  console.log('ready');
  /*
  var script = $('<script>');
  var url = 'https://a.cdn.intentmedia.net/javascripts/v1/intent_media_core.js';
  script.attr('src', url);
  script.attr('async', true);
  $('head').append(script);
  */
  $('#intentBtn').on('click', function(){
    console.log('bacon');
    IntentMediaProperties.travelers = "2";
    IntentMediaProperties.travel_date_start = "2018-09-09";
    IntentMediaProperties.travel_date_end = "2018-10-09";
    IntentMediaProperties.flight_origin = "Bali";
    IntentMediaProperties.flight_destination = "Bangkok";
    IntentMediaProperties.trip_type = "one_way";

    if(window.IntentMedia && IntentMedia.trigger) {
      IntentMedia.trigger("open_exit_unit");
    }
  });
});
</script>