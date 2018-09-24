<script>

<?php if (get_page_template_slug() != 'templates/page-pocketplanet.php'): ?>
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

(function(){
  window.IntentIsBlocked = null;
  adBlockDetector({
    onIsBlocked: function(){
      window.IntentIsBlocked = true;
    },
    onNotBlocked: function(){
      window.IntentIsBlocked = false;
    }
  })
}());
<?php endif;?>

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

<?php if (!$data['disable_intent'] && !is_front_page()): ?>
window.IntentMediaProperties = {  
  site_name: 'POCKET_PLANET',
  site_country: 'ID',
  site_language: 'en',
  site_currency: 'USD',
  page_id: 'content.general',
  /*generic*/
  travel_date_start: '<?= $data['date1']; ?>',
  travel_date_end: '<?= $data['date2']; ?>',
  travelers: '2',
   
  <?php if ($data['city']): ?>
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
  var url = "https://compare.pocketplanet.com/javascripts/v1/p/alt_core.js";
  // var url = '//a.cdn.intentmedia.net/javascripts/v1/intent_media_core.js';
  script.src = url;
  script.async = true;
  document.getElementsByTagName("head")[0].appendChild(script);
}());
<?php endif; ?>
</script>