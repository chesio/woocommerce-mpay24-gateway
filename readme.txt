=== WooCommerce mPAY24 Gateway ===

Contributors: datenwerk, chesio
Tags: woocommerce, gateway, mpay24
Requires at least: 3.5
Tested up to: 4.5.4
Stable tag: 2.0.0
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Add mPAY24 Payment Gateway to WooCommerce Plugin.

== Description ==

= Features =

* Uses mPAY24 PHP files (version 2016-10-19), follows mPAY24 SOAP Specification (version 2014-09-12)
* Using the mPAY24 payment page to integrate in website
* Switching between DEV and PROD mPAY24 environment with SOAP logins
* Supports all payment methods of mPAY24
* Languages available for Wordpress admin: EN, DE
* Languages available for payment page: BG, CS, DA, DE, FI, EN, ES, EL, FR, HR, HU, IT, JA, NL, NO, PL, PT, RO, RU, SK, SL, SR, SV, TR, UK, ZH
* Supported currency in payment page: EUR
* no mPAY24 proSafe support
* CSS styling for payment page
* Setting .htaccess credentials in mPAY24 confirmation url

= mPAY24 =

If there are newer mPAY24 PHP Files you can replace the files in the folder `MPay24`.
If you want to extend the functionality you have to edit `class-wc-mpay24-shop.php`

== Installation ==

= Requirements =

* PHP >= 5 (mPAY24 API)
* cURL (mPAY24 API)
* DOM (mPAY24 API)
* Mcrypt (mPAY24 API)
* [WooCommerce Plugin](https://wordpress.org/plugins/woocommerce/) v2.0 or higher

= Manual installation =

1. Upload `woocommerce-gateway-mpay24` to the `/wp-content/plugins/` directory
2. Activate the plugin through the `Plugins` menu in WordPress
3. Configure your mPAY24 settings under `WooCommerce Settings -> Checkout -> mPAY24`

== Frequently Asked Questions ==

= Which payment methods does the plugin support? =

The plugin supports all payment methods available with mPAY24. It pulls the payment methods from your mPAY24 merchant account and shows all on the payment page.

= How can I change the styling of the payment page? =

Since version 1.3 there are styling options on the gateway settings page. Basic CSS knowledge is required for this. Some basic changes on the default mPAY24 stylings are implemented as default values. One rule for styling: You are not allowed to send markup with urls to external (not mPAY24 hosted files) files (e.g. background images).

= I get an permission error for writing in mPAY24 folder? =

If you enable logging in the gateway settings then the curl operations will write a `curllog.log` in the MPay24 folder of the plugin. Please make this file or the whole folder writable for the webserver.

= How to get the current MDXI.xsd from mPAY24? =

If a transaction is done, mPAY24 sometimes sends a newer version of the MDXI.xsd file which is located in the MPay24 folder. Make sure that this file can be written by the webserver.

= The transaction is successful in payment page but in woocommerce the order status is cancelled. What's wrong? =

Maybe you have an .htaccess protection (HTTP authentication) on your wordpress site. That's why mPAY24 cannot access the success url to send the right transaction status. Removing HTTP authentication should solve the problem. Since version 1.4 you can put your HTTP authentication credentials in the gateway settings so that mPAY24 can send the right transaction status.

== Screenshots ==

1. Settings under WooCommerce payment tab
2. mPAY24 payment page

== Changelog ==

= 2.0.0 - 2016-10-20 =
* Tweak - Update mPAY24 PHP API to v.2.0.0 (recent master). Include missing abstract methods in WC_MPAY24_Shop class, but leave them unimplemented.
* Tweak - Add logs directory for logging of curl responses when mPAY24 API runs in debug mode. This is a work-around for https://github.com/mPAY24/mpay24_php_api/issues/6.
* Fix - TID check added to prevent PHP undefined key notices.
* Fix - Prefer wc_get_order() instead of new WC_Order() to retrieve orders.
* Fix - Fix return value of WC_Gateway_MPAY24::process_payment() method.
* Fix - Do not use deprecated method wpdb::supports_collation().
* Other - Minor codebase fixes and improvements.
* Other - Minor readme.txt file updates.
* Other - Drop MPay24/doc directory, documentation can be found online.

= 1.5.1 - 2014-11-28 =

* New Feature: added new languages for payment page DA, FI, SV, NO, UK, EL. If option "auto with WPML" is set, "NO" is mapped to "nb".

= 1.5 - 2014-11-28 =

* Bugfix: change translation domain for "Proceed to mPAY24" to "mpay24"
* Enhancement: changed default payment page language to "EN"
* New Feature: set automatically payment page language to current site language, if available on mPAY24, else use "EN".
"ZH" is mapped to "zh-hans". "PT" is mapped to "pt-pt". WPML plugin required for this feature!

= 1.4.2 - 2014-10-13 =

* Bugfix: add new DB field profile_id to enable successful transactions for merchants with profile_id
* Bugfix: complete ENUM values for DB field language with all available payment page languages

= 1.4.1 - 2014-10-08 =

* Enhancement: TID contains now order->id, order->billing_last_name and order->billing_first_name with totally maximum 32 characters (limitation of mPAY24)
* Enhancement: update help text about log directory
* add new plugin banner and icon (WP 4.0 Plugin Installer)

= 1.4 - 2014-08-04 =

* Bugfix: wc_add_notice not WC 2.0 compatible
* Bugfix: welcome message not WC 2.0 compatible
* Bugfix: transaction secret not saved correctly in database
* Enhancement: Fixed order status for "SOFORT"-Überweisung (thanks to [mmtomm](http://profiles.wordpress.org/mmtomm/) for donation)
* Enhancement: add var "order_button_text" with default text "Proceed to mPAY24" for the checkout submit button if gateway is selected, change text with filter "woocommerce_mpay24_button_text"
* New Feature: possibility to set .htaccess credentials in mPAY24 confirmation url in gateway settings

= 1.3 - 2014-06-03 =

* Bugfix: Fatal error - Call to undefined method ListPaymentMethodsResponse::getExternalStatus()
* Bugfix: Creating new transaction entries when failed order is payed
* Enhancement: Replace deprecated $woocommerce->logger() with new WC_Logger() (deprecated in WC 2.1)
* Enhancement: Use responsive payment page template (if mPAY24 set a logo for your payment page you have to call the mPAY24 support to set it also in the mobile template)
* Enhancement: Update language files
* Enhancement: Update FAQ and screenshots
* New Feature: Enable CSS styling of payment page in gateway settings

= 1.2 - 2013-11-12 =

* Enhancement: Update to current mPAY24 specification
* Enhancement: Support of new languages for payment page (ES, IT, CS, HR, SK, SL, SR, RO, RU, PL, PT, TR, ZH, JA)

= 1.1 - 2013-04-12 =

* Enhancement: Update to WooCommerce 2.x, plugin not backward compatible to WooCommerce version < 2.0
* Enhancement: Update mPAY24 API to current version
* Enhancement: Add some documentation for styling options of the mPAY24 payment page

= 1.0 - 2012-11-21 =

* Initial Release

== Upgrade Notice ==