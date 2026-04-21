=== WooCommerce - Bitrix24 CRM - Integration ===
Contributors: https://github.com/drabodan
Tags: bitrix24, bitrix24 leads, business leads, woocommerce, woocommerce bitrix24, order, integration, lead finder, lead management, lead scraper, leads, marketing leads, sales leads, crm

== Description ==

The main task of this plugin is a send your WooCommerce orders directly to your Bitrix24 account.

= Features =

* Integrate your `WooCommerce` orders with Bitrix24 CRM;
* Integrate your `WooCommerce` customers with Bitrix24 CRM;
* Works with any edition of Bitrix24 CRM (except for the free plan, since from January 1, 2021, Bitrix24 changes the conditions);
* You can choice that your want to generate - lead, deal + contact or deal + contact + company;
* Creation of the deal, occurs together with the creation / binding of the contact and the company. (if their fields are filled);
* Creation of notifications in Bitrix24 CRM when adding a lead and deal;
* Fields are loaded from the CRM (including custom fields);
* Sending data about the products in order to the lead and the deal;
* Supports for sending order status changes;
* Supports for getting order status changes from CRM (via Outbound webhook);
* Support for deleting an order on the site when deleting a lead / deal in the CRM (via Outbound webhook);
* Multiple deal pipeline support;
* Bulk order sending capability;
* Sending in two modes: immediately upon checkout / status change, or with a slight delay via `WP Cron`;
* Supports use `utm` params from the `URL`;
* Supports for sending `GA Client ID`;
* Supports for sending `roistat_visit` cookie;
* Supports for sending `_ym_uid` cookie;
* Supports for sending `_fbp` cookie;
* Supports for sending `_fbc` cookie;
* Supports for sending order coupon list;
* Supports for sending vendor name `Dokan`;
* Supports for sending vendor name `WC Marketplace`;
* Supports for sending voucher code `WooCommerce - PDF Vouchers`;
* Supports for sending `Order notes`;

== Installation ==

1. Extract `woocommerce-bitrix24-integration.zip` and upload it to your `WordPress` plugin directory
(usually /wp-content/plugins ), or upload the zip file directly from the WordPress plugins page.
Once completed, visit your plugins page.
2. Be sure `WooCommerce` Plugin is enabled.
3. Activate the plugin through the `Plugins` menu in WordPress.
4. Go to your `Bitrix24` -> `Applications` -> `Web hooks`.
5. Click `ADD WEB HOOK`. Choose `Inbound web hook`.
6. Check `CRM` and `Chat and Notifications (im)`. Click the button `SAVE`.
7. Copy value from `REST call example URL` without `profile/`.
8. Go to the `WooCommerce` -> `Bitrix24`.
9. Insert in the field `Inbound web hook` copied value and click `Check webhook`.
10. Configure the type of lead, status, and fields.
11. Save settings.
