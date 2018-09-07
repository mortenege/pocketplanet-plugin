<script>
jQuery(document).ready(function ($) {
smarter('reset');

// Configure search data and custom tracking variables:
smarter('context', { name: 'placement', handler: function() {
    // TODO: Update values to match page state and remove any unnecessary fields
    return {
        phgAdrefId: 'adref12345',       // Domain, marketing source, or other tracking value
        adVertical: 'hotel',              // One of 'air', 'hotel', 'car', 'vacation'
        locale: 'en_US',                // ISO standard locale code
        currency: 'USD',                // 3-character currency code
        date1: '<?= $data['date1']; ?>',            // Departure date in 'YYYY-MM-DD' format
        date2: '<?= $data['date2']; ?>',            // Return date in 'YYYY-MM-DD' format
        destinationName: '<?= $data['destination']; ?>',  // Destination city, state, and/or country
        originName: '<?= $data['origin']; ?>',     // Origin city, state, and/or country
        numAdults: 2,                   // Number of adults
        flightType: 'roundtrip',        // One of 'roundtrip', or 'oneway'
        flightServiceClass: 'economyCoach', // One of 'economyCoach', 'business', or 'firstClass'
        numRooms: 1,                    // Number of rooms
        time1: '7',                     // Time for date1. Integer from 0 to 23 or one of “anytime”, “morning”, “noon”, or “evening”
        time2: '16',                    // Time for date2. Integer from 0 to 23 or one of “anytime”, “morning”, “noon”, or “evening”
        // The below custom tracking variables can pass additional information. These are optional can be omitted if unused.
        customTrackingVar1: 'custom1',
        customTrackingVar2: 'custom2',
        customTrackingVar3: 'custom3',
        customTrackingVar4: 'custom4',
        customTrackingVar5: 'custom5'
    }
} });

// Load integration:
smarter('load');
});
</script>