<script>
<?php if (!$data['disable_smartertravel']): ?>
jQuery(document).ready(function ($) {
  // Load smarter ads
  smarter('reset');
  smarter('config', {
    loadPlacements: [
      'inlineB', // Inline Banner (horizontal)
      'inlineM', // Inline Banner for Mobile
      'inlineR', // Inline Rail (vertical)
      'leaveBehind',
      <?php if ($show_smarter_overlays === true): ?>
      'mobileOverlay',
      'overlay',
      <?php endif; ?>
      'widgetNoCheckboxes'
    ]
  });
  smarter('context', {
    name: 'placement',
    handler: function() {
      return {
        phgAdrefId: '<?= $data['camref']; ?>',
        adVertical: 'hotel',              // One of 'air', 'hotel', 'car', 'vacation'
        locale: 'en_US',                // ISO standard locale code
        currency: 'USD',                // 3-character currency code
        date1: '<?= $data['date1']; ?>',            // Departure date in 'YYYY-MM-DD' format
        date2: '<?= $data['date2']; ?>',            // Return date in 'YYYY-MM-DD' format
        destinationName: '<?= $data['destination']; ?>',  // Destination city, state, and/or country
        // originName: '<?= $data['origin']; ?>',     // Origin city, state, and/or country
        numAdults: 2,                   // Number of adults
        // flightType: 'roundtrip',        // One of 'roundtrip', or 'oneway'
        // flightServiceClass: 'economyCoach', // One of 'economyCoach', 'business', or 'firstClass'
        numRooms: 1,                    // Number of rooms
        time1: 'anytime',                     // Time for date1. Integer from 0 to 23 or one of “anytime”, “morning”, “noon”, or “evening”
        time2: 'anytime',                    // Time for date2. Integer from 0 to 23 or one of “anytime”, “morning”, “noon”, or “evening”
      }
    }
  });
  smarter('load');
});
<?php endif; ?>

<?php if (!$data['disable_intent']): ?>
window.IntentMediaProperties = {  
  site_name: 'POCKET_PLANET',
  site_country: 'ID',
  site_language: 'en',
  site_currency: 'USD',
   
  <?php if ($data['city']): ?>
  page_id: 'content.general',
  /*generic*/
  travel_date_start: '<?= $data['date1']; ?>',
  travel_date_end: '<?= $data['date2']; ?>',
  travelers: '2',
  hotel_city: '<?= $data['city']; ?>',
  <?php endif; ?>
  <?php if ($data['city'] && $data['country']): ?>
  hotel_country: '<?= $data['country']; ?>',
  <?php endif; ?>
  
  <?php if (!$data['force_intent'] && $show_intent_overlays === false): ?> 
  show_inactivity_overlays: "N",
  show_mouseleave_overlays: "N",
  show_mobile_ribbon: "N",
  show_ribbon: "N"
  <?php endif; ?>
};

(function() {
  var script = document.createElement("script");
  var url = '//a.cdn.intentmedia.net/javascripts/v1/intent_media_core.js';
  script.src = url;
  script.async = true;
  document.getElementsByTagName("head")[0].appendChild(script);
}());
<?php endif; ?>
</script>