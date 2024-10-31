=== Paps shipping for WooCommerce plugin ===
Contributors: kiamet MAVOUNGOU
Tags: paps, paps-api, livraison, woocommerce, woocommerce shipping, paps shipping
Requires PHP: 7.0
Requires at least: 5.9
Tested up to: 6.0
Stable tag: trunk
Stable tag: 3.0.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Paps integration for WooCommerce allows you to effortlessly and efficiently manage your parcel delivery requests directly through your WordPress admin.

== Description ==

Important, please update the plugin if you are still using version 1.4 Many improvements have been introduced and allow you to benefit from the latest updates to our API.

By using this extension, you give yourself the possibility to receive and support delivery requests on your platform or online shop. By integrating our extension, you not only reduce all the stress related to the delivery of your parcels (status of each race) and the costs that this can cause by having a single rate for all races in the locations located in the same city.

Plugin Features:

- The status of each delivery in the orders page.
- Personalized notes for the courier who handles the pickup.
- Status of the delivery directly to the user with a tracking link.
- Possibility for the admin to expressly pick up the errands.
- Automatic calculation of the delivery rate.
- Possibility to set a flat rate for all deliveries.
- Send email to admin when delivery request fails.
- Etc.

Available soon:

- Possibility for the user to choose himself a Standard delivery (Scheduled) or in Relay Point (in SENEGAL).
- more


== Setup ==

Requis:

- An API Security Key which you can obtain by visiting [swagger](https://paps-api.papslogistics.com/docs/#/Auth/AuthController_login) and clicking the "Get Key" button on the home page.

-Valid email address (the address where your API security key was sent)

- Address of your warehouse where your packages are located (Can be your company address). You can put the address of our Paps warehouse if you have chosen to store your products with us.

Optional:
- A secret signature (only if you want to use Webhooks) 

== Support ==

If you have a question, please send an email to dev@paps-app.com

== Frequently Asked Questions ==

Visit the official documentation on [docs](https://paps.readme.io/reference/getting-started-with-your-api)

== Screenshots ==
1. Admin status.
2. Settings.
3. Settings.
4. Settings.
5. Settings.

== Installation ==

= Minimum Requirements =

* WooCommerce 5.9 or later
* WordPress 6.0 or later

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t need to leave your web browser. To do an automatic install of WooCommerce, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type “WooCommerce Paps Integration” and click Search Plugins. Once you’ve found the plugin you can view details about it such as the the point release, rating and description. Most importantly of course, you can install it by simply clicking “Install Now”.

= Manual installation =

The manual installation method involves downloading the plugin and uploading it to your webserver via your favourite FTP application. The WordPress codex contains [instructions on how to do this here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

== Changelog ==

=3.0.4=
possibility to choose the type of tasks

=3.0.3=
 Action to the Recalculate shipping taxes after have updated shipping address

=3.0=
Release and upgrade API

=2.0=
Fixing the standard price problem, compatibility with other extensions.

== Upgrade Notice ==

Please update the plugin to version 2.0 to take advantage of the latest improvements: automatic support of Standard (scheduled) races, possibility to set a flat rate for all races, delivery method automatically supported with Paps Standard, automatic calculation of delivery rates, real time update of a race on the Admin and user area and more.

1. Activate or deactivate express shopping in the settings (Woocommerce > Settings > Shipping > Paps)
2. Go to the delivery method settings and choose Paps Standard
3. Optionally, if you wish, set a flat rate for all deliveries. Note that in this case, the normal delivery costs deducted from the race will be charged to you.
4. Enjoy 😎
