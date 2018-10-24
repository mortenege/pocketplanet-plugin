<?php 
// define('WP_USE_THEMES', false);
// require('../../../wp-load.php');

// var_dump($_GET);
$provider = isset($_GET['provider']) ? $_GET['provider'] : 'intentmedia';
unset($_GET['provider']);
$ad_block = isset($_GET['ad_block']) ? ($_GET['ad_block'] === 'true' ? true : false) : false;
unset($_GET['ad_block']);

if ($provider === 'intentmedia') {
  if (!$ad_block) {
    $url = "https://a.intentmedia.net/api/sca/v1/exit_units?";
  } else {
    $url = "https://compare.pocketplanet.com/api/sca/v1/exit_units?alt_svc=Y&";
  }
} else {
  
  $type = isset($_GET['search_type']) ? $_GET['search_type'] : 'flight';
  $mode = $type === 'flight' ? 'air' : $type;
  $camref = isset($_GET['camref']) ? $_GET['camref'] : '';
  $source_code = isset($_GET['source_code']) ? $_GET['source_code'] : '';
  // $mode = in_array($mode, ['air', 'hotel', 'car', 'cruise']) ? $mode : 'air';
  
  $url = "https://prf.hn/click/camref:{$camref}/adref:{$type}_deeplink/destination:http://www.bookingbuddy.com/en/partner/hero/?mode={$mode}&source={$source_code}&";
}

$url = $url . http_build_query($_GET);

$base_url = dirname($_SERVER['PHP_SELF']) . '/static/';
$img1_url = $base_url . 'pocketplanet-loadingicon-gif.gif';
$img2_url = $base_url . 'pp-white.png';

?>

<!DOCTYPE html>
<html>
<head>
  <title>Redirecting</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montseratt" />
  <style type="text/css">
    html, body {
      width: 100%;
      height: 100%;
      margin: 0;
      padding: 0;
    }

    .container {
      width: 100%;
      height: 100%;
      /* background-color: #fafafb; 
      color: #1d1d1b;  */
      background-color: rgb(25, 91, 247);
      color: white;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .centered {
      text-align: center;
      max-width: 400px;
    }

    .text {
      font-size: 35px;
      font-family: "Montseratt", arial, sans serif;
      font-weight: normal;
    }

    .logo {
      width: 100%;
      max-width: 400px;
      display: block;
      margin: 0 auto;
    }
    .loader {
      height: 100px;
    }
  </style>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <!-- Taboola Pixel Code -->
  <script type='text/javascript'>
    window._tfa = window._tfa || [];
    window._tfa.push({notify: 'event', name: 'page_view', id: 1124491});
    !function (t, f, a, x) {
           if (!document.getElementById(x)) {
              t.async = 1;t.src = a;t.id=x;f.parentNode.insertBefore(t, f);
           }
    }(document.createElement('script'),
    document.getElementsByTagName('script')[0],
    '//cdn.taboola.com/libtrc/unip/1124491/tfa.js',
    'tb_tfa_script');
  </script>
  <noscript>
    <img src='//trc.taboola.com/1124491/log/3/unip?en=page_view'
        width='0' height='0' style='display:none'/>
  </noscript>
  <!-- End of Taboola Pixel Code -->
</head>
<body>
<div class="container">
  <div class="centered">
    <div class="loader">
      <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="lds-bars"><rect ng-attr-x="{{config.x1}}" y="30" ng-attr-width="{{config.width}}" height="40" fill="#ffffff" x="15" width="10"><animate attributeName="opacity" calcMode="spline" values="1;0.2;1" keyTimes="0;0.5;1" dur="1" keySplines="0.5 0 0.5 1;0.5 0 0.5 1" begin="-0.6s" repeatCount="indefinite"></animate></rect><rect ng-attr-x="{{config.x2}}" y="30" ng-attr-width="{{config.width}}" height="40" fill="#ffff" x="35" width="10"><animate attributeName="opacity" calcMode="spline" values="1;0.2;1" keyTimes="0;0.5;1" dur="1" keySplines="0.5 0 0.5 1;0.5 0 0.5 1" begin="-0.4s" repeatCount="indefinite"></animate></rect><rect ng-attr-x="{{config.x3}}" y="30" ng-attr-width="{{config.width}}" height="40" fill="#ffffff" x="55" width="10"><animate attributeName="opacity" calcMode="spline" values="1;0.2;1" keyTimes="0;0.5;1" dur="1" keySplines="0.5 0 0.5 1;0.5 0 0.5 1" begin="-0.2s" repeatCount="indefinite"></animate></rect><rect ng-attr-x="{{config.x4}}" y="30" ng-attr-width="{{config.width}}" height="40" fill="#ffffff" x="75" width="10"><animate attributeName="opacity" calcMode="spline" values="1;0.2;1" keyTimes="0;0.5;1" dur="1" keySplines="0.5 0 0.5 1;0.5 0 0.5 1" begin="0s" repeatCount="indefinite"></animate></rect></svg>
    </div>
    <img src="<?= $img2_url ?>" class="logo"/>
    <h3 class="text">We are finding you the best prices</h3>
  </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($){
  var url = '<?= $url ?>';
  var provider = '<?= $provider ?>';

  if (provider !== 'intentmedia') {
    setTimeout(function(){
      window.location.href = url
    },2000);
  } else {
    $.get(url, function(response){
      if (response && 'url' in response) {
        let url = response.url + "&nolimit=true&popsOver=true";
        setTimeout(function(){
          window.location.href = url
        },1500);
      } else {
        console.error(url, response)
      }
    });
  }
});
</script>

</body>
</html>

