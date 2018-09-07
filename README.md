# Pocket Planet Custom Search Widget

## Instructions

In Wordpress

 * Under "settings" - "PP Widgets" add Camref and Source Code from SmarterAds
 * Add a fallback image (optional)
 * Under "Pages" - *Select a Page*, in the MetaBox "Page Attributes" Change "Template" to "PP Widgets"
 * On the same page in the MetaBox "... PP Widget ..." choose which search template to use and add a background image

 For *Ad Share* functionality: each user that visits the site gets attributed a random number between 0 and 1. This is used in conjunction with the "Ad Share Widget"-values. If the user value is lower than the selected Ad Share value then SmarterAds is chosen. Otherwise Intent is chosen.

 > To only use intent set the value to 1 and to only use SmarterAds set the value to 0.

### **Intent** possible reasons for errors

> remember to refresh http://config.intentmedia.net/forceAds every 10 minutes

 * Error 500
  * Check date format has to be YYYYMMDD
 * Just spinning  
  * Check GUID
  * Check timestamp
 * Blank screen
  * Check city string (It will fail if the city does not exist or if there are any commas or other)
 * Random empty search box
  * Randomly whenever it feels like it