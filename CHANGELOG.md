CHANGELOG
=========

* 1.24.2

  * HK: Coming soon page reactivated

* 1.24.1

  * Removed HK coming soon page
  * INTVOLO-460: Coming soon page AE/SG/CA
  * GTM fix: New cookie lib, API changed

* 1.24.0

  * SGFD-18923: Error handling after sucessful purchase
  * SGFD-18589: Api cart quantity calculation bug
  * INTVOLO-436: HK/AU time picker design changes
  * INTVOLO-86: City pages Facebook preview image
  * INTVOLO-294: Rebranding to Suppertime look
  * INTVOLO-409: Escape slash in phone number
  * INTVOLO-483: Url with parameter to noindex
  * INTVOLO-318: JS error for Mac on Chrome
  * INTVOLO-468: Multi language fixes
  * INTVOLO-476: Prefilling checkout
  * INTVOLO-309: Home-page-App-Promo
  * INTVOLO-471: Geocoding fixes

* 1.23.8
  * Fixed AU timezone

* 1.23.7
  * INTVOLO-419 HK: Wrong currency is shown on menu page

* 1.23.6
  * INTVOLO-416: Reverting most of turbolinks visit locale fix

* 1.23.5
  * INTVOLO-421 Wrong error page is shown when reloading order success page after delting cache and cookies

* 1.23.3
  * INTVOLO-415 added cache breaker for FOS routing JS

* 1.23.2
  * INTVOLO-416 turbolinks visit locale fix

* 1.23.1
  * INTVOLO-314: Geo-coding improvement - Fixing short-locale names
  * INTVOLO-415 fos js routing fix
  * SGFD-19149 Added AE and SG

* 1.23.0
  * INTVOLO-89 multilanguage
  * INTVOLO-314 Multilanguage Improvement / Router and Browser Lang Detection
  * INTVOLO-412: Full Address geocoding without postal-code

* 1.22.2
  * INTVOLO-407 - replacing capitalized R with r with geo search results

* 1.22.1
  * Removed fully geocoded addresses for ES, added it for HK

* 1.22.0
  * INTVOLO-404 logging errors improvements
  * INTVOLO-285: Allow fully geocoded addresses

* 1.21.1-1.21.2

  * Disable SSL verification on API call

* 1.21.0
  * INTVOLO-174: API Cache

* 1.20.3
  * INTVOLO-398 fixed address saved twice

* 1.20.2
  * INTVOLO-104: Profile page validation - fixed checkout form

* 1.20.1
  * SGFD-18036 CTA GTM click - rebase problems fix

* 1.20.0
  * INTVOLO-310: Index city and vendor pages
  * INTVOLO-307 newrelic / slack error logging

* 1.19.0
  * INTVOLO-2 CTA GTM click

* 1.18.0
  * INTVOLO-104: Profile page validation
  * INTVOLO-115: Change the foodora fonts to paid version

* 1.17.4

  * Coming soon <> health check page fix

* 1.17.3

  * INTVOLO-X: Timezone hotfix

* 1.17.1 / 1.17.2

  * INTVOLO-313: Fix no vendor found page
  * INTVOLO-85: Swapped SEO descr. city/vendor listing

* 1.17.0

  * SGFD-17614: CSS font sizes refactor on all pages
  * SGFD-18775: Paypal pending/error order handling
  * SGFD-17759: IE | Input field description is missing after opening home page
  * SGFD-18830: IE | Scroll to top not working in Menu and Checkout pages
  * SGFD-18286: IE | Delivery instructions field - placeholder is used as content
  * SGFD-15763: SEO | URL canonical & noindex
  * SGFD-15789: SEO | Metadata
  * INTVOLO-82: Vendor code and CMS redirection
  * INTVOLO-189: Simple load balancer check
  * INTVOLO-288: Login without cookie

* 1.16.2

  * Deployment: hardcoded list of countries

* 1.16.1

  * INTVOLO-75: HK blank page fix

* 1.16.0
  * INTVOLO-303 Checkout - Missing Amex icon
  * INTVOLO-296 hide add new cards for COD

* 1.51.4
  * SGFD-19122 fixed xss address fields issue

* 1.51.3
  * INTVOLO-65 SGFD-18306 moved address data from the header to post param

* 1.15.1
  * SGFD-19122 fixed xss address fields issue & SGFD-17989 fixed special characters in addresses
  * SGFD-18918 temporary disabled polling ajax request in the tracking page
  * SGFD-19076 Spain / change avg. delivery time on home page to 30 (fixed in 1.12.3)

* 1.15.0
  * SGFD-18902: IE / PLZ placeholder styling #454 
  * SGFD-18891: IE 9 / Error Pages #
  * SGFD-17924: IE 11, 10 - Overlap of cart icon with scrolling bar #443 
  * SGFD-18718: checkout cart item - disable edit #427 

* 1.14.0
  * SGFD-17488 Checkout / Show Only Deliverable Addresses
  * SGDF-18580 Cash On Delivery
  * SGFD-18306 Checkout / Usability Improvement 2: Address management
  * SGFD-18841 Checkout / anonymous-registered user use the same UI

* 1.13.0
  * SGFD-18925 skip coming soon page with cookie

* 1.12.3
  * SGFD-19076 Spain / change avg. delivery time on home page to 30

* 1.12.2
  * SGFD-19026: Checkout / redirect instead of error page 

* 1.12.1
  * SGFD-16740: Forgot Password
  * SGFD-18781: HOTFIX 1.11.1 / 404 Page / JS not loaded -> no shadow action (2nd part)

* 1.11.4
  * SGFD-19003: GTM cart calculation error handling

* 1.11.2.1
  * Hot fix GTM issue in the tracking page

* 1.11.2

  * SGFD-18538: hide input field when zip code is present in url
  * SGFD-18827: register promise on every page
  * SGFD-18827: added Promise polyfill to bower
  * SGFD-18826: IE10, 9 / Javascript error on restaurant page
  * SGFD-18802: Checkout / Change personal information no possible
  * SGFD-18781: error pages js fix
  * SGFD-18705: Prevent scroll bars disappearance on Mac

* 1.11.1

  * SGFD-18538: zip code fix

* 1.11.0

  * SGFD-18742: jshint update
  * SGFD-18618: Checkout disable button on new address
  * SGFD-18705: Prevent scroll bars disappearance on Mac
  * SGFD-18538: Zip query param
  * SGFD-15756: SEO city redirection
  * SGFD-18549: Address form improvements
  * SGFD-18042: GTM Page load on restaurant detail page
  * SGFD-18041: GTM clicks on restaurants
  * SGFD-18040: GTM Additional restaurant impressions
  * SGFD-18038: GTM virtualPageView vendors
  * SGFD-18035: GTM pageload new

* 1.10.2-1.10.3

  * Release to kill the cache

* 1.10.1

  * SGFD-18629: Checkout edit information link binding
  * SGFD-18519: Fixing mobile country validation

* 1.10.0

  * SGFD-18442: Checkout Select first payment method
  * SGFD-18152: Area Change, logo not reappearing after scrolling up
  * SGFD-18388: Cache schedule
  * SGFD-18372: Success Page Add placeholder
  * SGFD-18445: Checkout visual Improvment
  * SGFD-18299: Profile Refactor SASS to BEM
  * SGFD-18320: Error pages Refactor SASS to BEM
  * SGFD-18319: Static pages Refactor SASS to BEM
  * SGFD-18174: Menu Refactor SASS to BEM
  * SGFD-17992: Checkout save address
  * SGFD-17818: Checkout button
  * SGFD-17361: Profile change password
  * SGFD-18380: Time picker format adjustement
  * SGFD-18153: Registration validations

* 1.9.5

  * SGFD-18478 Hide Special instructions

* 1.9.4

  * Fixed AT timezone

* 1.9.2

  *  SGFD-18388: Vendor listing opening time fix (reverted)

* 1.9.1

  * SGFD-18211 Coming soon page fix
  * SGFD-18388: Vendor listing opening time fix (edited)
  * Edited timezones

* 1.9.0

  * SGFD-18074: Home Refactor SASS to BEM
  * SGFD-18116: Restaurants Refactor SASS to BEM
  * SGFD-17695: Logo Change Sweden
  * SGFD-18332: virtualPageView dataLayer at success page
  * SGFD-18270: User can procede with geocomplete result without city
  * SGFD-18211: Coming soon page

* 1.8.3

  * SGFD-18261: Placeholder special instructions

* 1.8.2

  * SGFD-18206: XSS special instructions
  * SGFD-17535: Safari ipad fix
  * SGFD-18180: Adding the referrer to the data layer

* 1.8.0-1.8.1

  * SGFD-17535: timetable mobile layout fix
  * SGFD-18143: Home / Style fixes - plate knife fork
  * SGFD-18027: Hero-Banner images are not loaded
  * SGFD-18090: Special instructions duplicate fix
  * SGFD-17753: cart model refactor + unit tests
  * SGFD-17527: Mobile / Name in header gets doubled

* 1.7.6

  * SGFD-18180 GA to vanilla

* 1.7.5

  * SGFD-17861: Homepage / France / 29 minutes avg. delivery time (reverted)

* 1.7.4

  * SGFD-18067: mobile cart fix

* 1.7.3

  * SGFD-17892: Topping Overlay (ellipsis fix)

* 1.7.2

  * SGFD-17942: Legal name in SE and NO (fix)
  * SGFD-17892: Topping Overlay / 2 Improvements (fix)
  * SGFD-18032: GA check & checkout double tags

* 1.7.1

  * SGFD-18006: Postalcode validation

* 1.7.0

  * SGFD-17861: Homepage / France / 29 minutes avg. delivery time
  * SGFD-17942: Legal name hidden if empty
  * SGFD-17837: Animation fixes
  * SGFD-17765: Clean cart on logout
  * SGFD-17635: Fix hardcoded GTM country
  * SGFD-17986: GTM GA Fixes (conversions, instances)
  * SGFD-17892: Topping overlay improvements
  * SGFD-17860: Restaurant special note
  * SGFD-17709: Ideal payment
  * SGFD-17866: Footer / Cms links should be a CMS block
  * SGFD-17280: Special instructions
  * SGFD-17242: Special closing, race condition fix, opening/closing hours fix

* 1.6.1

  * SGFD-17203 city redirects fix (muenchen, koeln, duesseldorf)

* 1.6.0
  * SGFD-17615 Profile dropdown styling
  * SGFD-17760 IE - Input field description is shifted down a bit
  * SGFD-17133 IE Registration button cut off
  * SGFD-17787 animations on home page are loaded on page:load
  * SGFD-17862 Menu Page / Label in the postal code field

* 1.5.1

  * SGFD-17837 - intl fix

* 1.5.0

  * SGFD-17725 Error pages - reduce font size
  * SGFD-17529 bootstrap.js cleanup

* 1.4.11

  * SGFD-17933: change continue to save for logged user on checkout contact form

* 1.4.10

  * SGFD-17837: Intl fix ( backported from 1.5.1 )

* 1.4.9

  * SGFD-17933: Checkout address bug (fix)

* 1.4.8

  * SGFD-17933: Checkout address bug

* 1.4.7

  * SGFD-17932: Checkout delivery validation bug

* 1.4.6

  * SGFD-17850: Cannot Deliver to Postal Code (fix)

* 1.4.5

  * SGFD-17850: Cannot Deliver to Postal Code (fix)

* 1.4.4

  * SGFD-17850: Cannot Deliver to Postal Code

* 1.4.3

  * SGFD-17242 Changed opening/closing hours calculation

* 1.4.2

  * SGFD-17424: Closing in X minutes - fix

* 1.4.1

  * SGFD-17242: Special days - offset fix

* 1.4.0

  * SGFD-17596 GTM QA Fixes

* 1.3.0 (2015-07-07)

  * SGFD-17698 Additional changes requested for Spain Rebranding

* 1.2.0

  * SGFD-17603 asset version for thumbor images is the deploy timestamp
  * SGFD-17376 rich description
  * SGFD-17370 iPAD / Cart superimposed with the menu page
  * SGFD-17515 Tooltips-script-refactor
  * SGFD-17339: IOS improvements
  * SGFD-17565: Remove Cart horizontal scroll bar on Windows system

* 1.1.0

  * SGFD-17550 Fixed XSS in address form
  * SGFD-17520 logo-fix
  * SGFD-17187 profile page returns 403 for anonymous users

* 1.0.33

  * SGFD-17525 duplicated GTM tags fix (2)

* 1.0.32

  * SGFD-17525 Duplicated GTM tags fix

* 1.0.31

  * SGFD-17468: set country specific css variations
  * SGFD-17282 - tooltip on home screen is shown on top for small screens

* 1.0.30

  * SGFD-15802 City page / SEO box
  * SGFD-17281 Menu Page / Black layer if no postal code
  * SGFD-17438 - toppings javascript cleanup

* 1.0.29

  * SGFD-16165 removed FoodpandaWebTranslateItBundle, added our implementation
  * SGFD-17312 - undelegate events on close toppings menu
  * SGFD-17292 - payment method is highlighted after selection
  * SGFD-17120 - continue button is enabled after back from payment selection

* 1.0.28.7 (2015-07-06)

  * SGFD-17242 Special days ( + vendor listing fix )

* 1.0.28.6

  * SGFD-17242 Special days

* 1.0.28.5

  * SGFD-17520 Spain Branding: Social footer icons to CMS
  * SGFD-17550 XSS sanitize

* 1.0.28 (2015-06-30)

  * SGFD-17240 Menu Page / Special design for hero image
  * SGFD-17283 Newsletter subscription and Opt-out CC persistence

* 1.0.27 (2015-06-30)

 * SGFD-17045 Remove the COMEBACK voucher from success page

* 1.0.26

 * SGFD-17159 Checkout - reduce "add address" link margin top
 * SGFD-15119 Menu page / SEO box

* 1.0.25

 * SGFD-17366 Geocoding checkout addresses
 * SGFD-16817 Header Js optimization
 * SGFD-17215 added validation for lat/lng in DeliverabilityService::isDeliverableLocation()
 * SGFD-17235 title repetition removed

* 1.0.24

 * SGFD-17154 Customer vouchers

* 1.0.23

 * SGFD-17277 Timepicker sorting
 * SGFD-16685 Error message in cart
 * SGFD-17224 border restored

* 1.0.22

 * SGFD-17193 profile - line height refined in address box and add new link removed
 * SGFD-17196 HP - best restaurant logos distance increased
 * SGFD-17147 checkout - drop down font size reduced
 * SGFD-17068 - Payment selection fix
 * SGFD-17144 menu page - first title padding top reduced
 
