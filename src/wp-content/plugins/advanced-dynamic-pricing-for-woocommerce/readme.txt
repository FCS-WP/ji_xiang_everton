=== Advanced Dynamic Pricing for WooCommerce ===
Contributors: algolplus
Donate link: https://paypal.me/ipprokaev/0usd
Tags: woocommerce, dynamic pricing, discount, pricing rule, bulk discount
Requires PHP: 7.1
Requires at least: 4.8
Tested up to: 6.8
Stable tag: 4.9.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

All discount types - flexible and feature rich plugin.

== Description ==

This plugin helps you  quickly set discounts and pricing rules for your WooCommerce store.

Set up any kind of discount or dynamic pricing you like, and activate/deactivate rules as needed.

Configure fixed dollar amount adjustments, percentage adjustments, or set fixed price for the product or group of products.

Also supports role-based prices & bulk pricing. **Bulk tables can be designed with Customizer.** You should setup bulk rule for category/product at first and enable "Show Bulk Table" at tab "Settings".

= Some Examples  =

* Category-level discounts - discount products and provide free shipping
* Buy 4(or more) items on Friday and get 20% off
* Buy product X and get product Y for free - immediately added and visible in cart
* Buy a package -  discount it (each item separately), and also get a free product
* Apply bulk discount for selected items, available only to wholesale buyers
* Give a 10% discount to all "Accessories"(Category) if a product X is present in the cart

Check more examples [on our website](https://docs.algolplus.com/algol_pricing/sample-discount/).

= One pricing rule can  =
* Filter cart items by products, categories, tags or custom fields
* Modify price for each product separately
* Or set total price for whole set
* Apply cart discounts and fees
* Add free products on fly
* Use tables to get bulk rates
* Validate conditions for cart items, user roles or dates
* Track limits (only "max usage" supported currently)

= Interface settings =
* Show/hide original prices
* Show/hide badge "On Sale"
* Show/hide bulk discount table on the product page
* Set rule for  products which already on sale
* Add shortcodes to display discounted or BOGO products at separate pages
* and much more ...

[Pro version](https://algolplus.com/plugins/downloads/advanced-dynamic-pricing-woocommerce-pro/?currency=USD) can [adjust product price onfly](https://docs.algolplus.com/algol_pricing/advanced-features-in-action/), adds **exclusive rules, extra conditions and a lot of settings(which allow you to configure different layouts and scenarios).

Have an idea or feature request?
Please create a topic in the "Support" section with any ideas or suggestions for new features.

== Installation ==

= Automatic Installation =
Go to Wordpress dashboard, click  Plugins / Add New  , type 'Advanced Dynamic Pricing for WooCommerce' and hit Enter.
Install and activate plugin, visit WooCommerce > Pricing Rules.

= Manual Installation =
[Please, visit the link and follow the instructions](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation)

== Frequently Asked Questions ==

= How can I increase prices in my shop? =
You should setup negative discount.
= The plugin slows down my site a lot. Sometimes the cart page just freezes. =
It seems your websitе calls external API to do shipping calculations.
Please, visit >WooCommerce>Pricing Rules>Settings>Calculation, mark "Disable shipping calculation" and check speed.
= Free product can't be added to the cart. I see message "Sorry, this product cannot be purchased." =
WooCommerce verifies product before adding to the cart. So this product must be published, in stock and has price defined.
= How can I hide original prices? =
It's a PRO feature. You should turn off option "Show striked prices" at tab Settings, for category and product pages.
= I don't see "For sale" badge for variable products =
It's a PRO feature. You should turn on option "Calculate 'On Sale' badge for variable products" at tab Settings, section Calculations.
= Is it compatible with WPML? WOOCS ? =
Yes.
= Compatibility with my theme/plugin =
Free and pro versions use same core, so you can test it using free version. [Please, visit the link to see detailed reply](https://docs.algolplus.com/algol_pricing/common-faq/)
= How to allow customer to select free product =
You should create package rule and set zero price for free product. [Please, check 2nd example](https://docs.algolplus.com/algol_pricing/bogo-discount-help/)
= How to customize bulk tables or row "amount saved" =
You should copy necessary file from folder “BaseVersion/templates” to folder “advanced-dynamic-pricing-for-woocommerce” (create it in active theme)
= The rules are not applied to orders if I use button "Add order" (>WooCommerce>Orders) =
This form adds new order directly to the database. But all pricing plugins work with cart items. Use our plugin [Phone Orders](https://wordpress.org/plugins/phone-orders-for-woocommerce/) to add backend orders.
= I can't change quantity or delete item from cart =
It's a conflict with another plugin which modifies cart items too. You should turn on debugbar and send us report/json file. [Read short guide.](https://docs.algolplus.com/algol_pricing/debug/)
= I marked checkbox "Add products to cart at normal cost and add coupon...", but I don't see any coupons in the cart =
You should visit >WooCommerce>Settings and mark "Enable the use of coupon codes".
= I need custom cart condition =
You should be PHP programmer to do it. [Please, review sample addon and adapt it for your needs](https://docs.algolplus.com/algol_pricing/program-custom-condition/)
= I don't see my question   =
[Please, review full FAQ](https://docs.algolplus.com/algol_pricing/common-faq/)

== Screenshots ==
1. List of pricing rules
2. Rule type selector
3. Discount 5%, up to 10 euro
4. The rule was applied to the cart
5. Settings page


== Changelog ==

= 4.9.5 - 2025-04-09 =
* Fixed critical bug - caching plugins were throwing fatal error when running on PHP 8.1+

= 4.9.4 - 2025-04-08 =
* Fixed CSRF vulnerability
* Added new discount type "Buy 3 for X"
* Updated section Tools>Import rules (new option, fixed some bugs)
* Fixed bug - excessive memory usage, plugin uses  wp_cache_* functions now
* Fixed bug - blocks cart displayed with fatal error if cart contained "Sold individually" item
* Fixed bug - some warnings for filters by product brands( native for WooCommerce products)
* Updated compatibility with "WPC Composite Products for WooCommerce"
* Updated compatibility with "Woocommerce Product Bundles"
* Updated compatibility with "TM Extra product options"
* Updated compatibility with "Klarna On-Site Messaging for WooCommerce"

= 4.9.3 - 2025-02-04 =
* Importer generates meaningful names for created rules
* Fixed bug - can't edit the pages in some visual page builders
* Fixed bug - missed value 0% for tag {{percentage_saved}}
* Updated compatibility with "Yoast SEO"(PHP warnings)

= 4.9.2 - 2025-01-15 =
* Added "Percentage discount" to section "Cart adjustments"
* Removed complex discounts in section "Cart adjustments" (existing rules will work)
* Fixed bug - block-based cart removed gifts (in some cases)
* Fixed bug - "on sale" badge was not displayed if regular price = 1000 and sale price = 900
* Fixed bug - shortcode [adp_products_on_sale] ignored the rule's date conditions
* Fixed bug - variable subscription shown fatal error in the cart
* Fixed bug - rules importer(CSV) used same discount for all roles in "role-based" import
* Fixed bug - rules backup tool ignored fields "Start Date" and "End Date"
* Updated compatibility with "Yoast SEO"
* Updated compatibility with "WPC Product Bundles for WooCommerce"
* Updated (multi-currency) compatibility with "WooCommerce Payments"

= 4.9.1 - 2024-12-11 =
* Fixed XSS vulnerability
* Added "Exclude products" selectors to section "Filter by products"
* Added button "Reset to default settings" (>Settings>System), highlight the changed options
* Optimized calculations for variable products
* WooCommerce shortcode [products on_sale="true"] displays products modified by [Product Only rules](https://docs.algolplus.com/algol_pricing/product-only-type-rule/)
* Bulk table hides footer if there's no bulk table message in the rule
* Fixed bug - shipping cost was not added to  total , if our rules were NOT applied  and option "disable shipping calculation" was active
* Fixed bug - bulk mode "Based on variation" handled different simple products as ONE variation
* Fixed bug - wrong prices in bulk table for the products with WC sale price
* Fixed bug - cart conditions "Subtotal ..." ignored discounts added by other rules
* Fixed bug - shortcode [adp_products_on_sale] shown products with increased cost (negative discount)
* Fixed bug - incorrectly updated bulk rules when use >Tools>Update bulk ranges (CSV)
* Fixed bug - WooCommerce Subscriptions product ignored "signup fee" (in some cases)
* Fixed bug - an infinite loop if "WooCommerce Prices By User Role" plugin was active
* Fixed bug - CURCY shown wrong prices after switching currency in the cart
* Fixed bug - prices were doubled if the bundled product was created using the "Woocommerce Product Bundles" plugin
* Fixed bug - "YITH WooCommerce Advanced Product Options Premium" break layout for our pages
* Added compatibility with "WPC Composite Products for WooCommerce"
* Added compatibility with HeyLight Payment System
* Updated compatibility with "WPC Product Bundles for WooCommerce"
* Updated (multi-currency) compatibility with "WooCommerce Payments"
* Fixed PHP 8.4 warnings

= 4.9.0 - 2024-10-14 =
* Increased speed of generating store/category pages for shops having a lot of variations
* Added option "Use aproximate price range if product has [10] variations or more"
* Added position "After subtotal" for amount saved in the cart/checkout
* Amount saved  supported for block-based cart/checkout
* Fixed bug - "out of memory" error when WooCommerce Subscriptions was active
* Fixed bug - "Replace price with lowest bulk price"  worked incorrectly when sale price < price of our discount
* Fixed bug - "Replace the price with the lowest price of variations" tag  {{regular_price_striked}} shown the striked price even there was no discount
* Fixed bug - mini-cart shown the striked price after deleting all products from the cart
* Fixed bug - CURCY(WooCommerce Multi Currency Premium by VillaTheme) can't change currency in the cart
* Fixed bug - Divi theme builder failed to start
* Fixed bug - cart condition "Order count" didn't ignore draft orders created by block-based checkout
* Updated compatibility with "FOX - Currency Switcher Professional"
* Updated compatibility with "WPC Product Bundles for WooCommerce"
* Updated compatibility with " WooCommerce Extra Product Options"
* Updated compatibility with WoodMart theme

= 4.8.3 - 2024-08-21 =
* Fixed bug - wrong coupon amount for the gift (block-based cart)
* Fixed bug - can't add the free product if this product had stock qty = 1
* Fixed bug - wrong price range displayed for variable products if option "Add products to cart at normal cost and show discount as a coupon" was active
* Fixed bug - option "Replace price with lowest bulk price" incorrectly displayed WooCommerce sale price
* Fixed bug - sale prices was not set for variable products in product feed
* Fixed bug - REST api returned wrong sale price for product if option "Show On Sale badge for Simple product if price was modified" was inactive
* Updated compatibility with "WooCommerce Composite Products"
* Updated compatibility with "WPC Product Bundles for WooCommerce"

= 4.8.2 - 2024-06-26 =
* Fully support Block-Based Checkout
* The option "Disable shipping calculation" is OFF, by default
* Added option "Individual use" WC coupon suppress coupons added by rules" (>Settings>Coupons)
* Fixed bug - bulk table was not displayed if final range is less than qty in the cart
* Fixed bug - bulk table was not displayed if product sale price is lower than the bulk price
* Fixed bug - wrong order total if "Don't recalculate cart on page load" enabled
* Updated compatibility with "Woo Product Bundles", item subtotal was incorrect
* Updated compatibility with "WooCommerce Mix and Match Products", item subtotal was incorrect
* Fixed non-reported bugs, detected by PHPStan

= 4.8.1 - 2024-06-05 =
* Fixed bug - option "Add products to cart at normal cost" added coupon with 0 amount if product has sale price
* Fixed bug - option "Combine multiple fixed discounts" added coupon with 0 amount
* Fixed bug - option "Don't recalculate cart on page load" worked incorrectly for products which have non-empty sale price
* Fixed bug - option "Don't recalculate cart on page load" displayed an error if cart discount applied
* Fixed bug - php warnings for undefined variables in StructuredData.php
* Updated compatibility with "YITH WooCommerce Gift Cards"

= 4.8.0 - 2024-05-29 =
* Added option "Don't recalculate cart on page load" (>Settings>Calculation, default OFF)
* Added option “Force displaying variation price“ (>Settings>Product Page, default OFF)
* Added option "Apply pricing rules while doing REST API" (>Settings>System, default ON)
* The option "Disable shipping calculation" is ON, by default
* The option "Show unmodified price if product discounts added as coupon" is ON, by default
* Optimized rules import (CSV) - merges products with similiar discounts to one rule
* Fixed bug - bulk table missed if 1st bulk range didn't starts at "1" and product has sale price
* Fixed bug - google markup depended on option "Round up totals"
* Fixed bug - fatal error in REST API if our coupon was applied to the order
* Fixed bug - fatal error (division by zero) if variation has zero price
* Fixed bug - product filters didn't support attributes with ":" in name
* Fixed bug - Grouped product was not excluded by product filters
* Fixed bug - "Individual use only" WC coupon applied together with our coupons
* Added compatibility with "WooCommerce Chained Products", by StoreApps
* Added compatibility with "Free Gift Coupons", by Backcourt Development
* Added compatibility with "Yoast SEO", by Team Yoast
* Updated compatibility with "WPC Product Bundles for WooCommerce"
* Updated compatibility with "WC Fields Factory"
* Updated compatibility with "Klarna On-Site Messaging for WooCommerce"
* Updated compatibility with "YITH WooCommerce Gift Cards"
* Updated compatibility with "Shoptimizer" theme

= 4.7.2 - 2024-04-17 =
* Fixed bug - the cart displayed regular price for onsale items
* Fixed bug - button "Update onsale list" ignored product filters by "Attibutes" and by custom taxonomies
* Fixed bug - product filters applied only once for pack of items, due caching
* Updated compatibility with "Woo Product Bundles", main product had empty price

= 4.7.1 - 2024-04-09 =
* Fixed bug - fatal error in the cart for the bundled products
* Fixed bug - fatal error in the cart, if variable product added itself (not variation!)
* Fixed bug - fatal error in REST API, since WooCommerce version 8.7
* Fixed bug - product filter didn't work with custom taxonomy
* Fixed bug - bulk table was hidden at product page (only for mode "After matching condition")
* Fixed bug - option"Replace price with lowest bulk price" didn't work for mode "After matching condition"
* Fixed bug - cache recalculation (for "Product Only" rules) freeezed if shop had a lot of products
* Fixed bug - some phrases can not be translated
* Updated compatibility with "WPC Product Bundles", bundled products had zero or negative price in the cart

= 4.7.0 - 2024-03-25 =
* Speed up our plugin a bit
* UI tweak - allow to set same dates in rules header
* Bug fixed - column "Discounted price" had the same price in bulk table
* Bug fixed - shipping cost ignored in the cart if the rules were not applied and mode "Disable shipping calculation" active
* Updated compatibility with "YayCurrency", fatal error for new version
* Updated compatibility with "Woo Product Bundles", fatal error for priced individually bundles

= 4.6.2 - 2024-02-26 =
* Warning! Now field "To Date" (in rule header)  is LAST date for the rule
* Bug fixed -  fatal PHP error in ContainerCompatibilityManager.php, line 22
* Bug fixed -  bulk discount didn't work correctly for  "Product only" rules
* Bug fixed -  wrong notice that this plugin is not compatible with HPOS mode
* Bug fixed -  all sections were cleared if user pressed Enter in any field of the rule
* Bug fixed -  impossible to translate text above bulk table

= 4.6.1 - 2024-02-05 =
* Bug fixed -  PHP fatal error "undefined constant ATTR_TEMP"
* Bug fixed -  incorrect calculations if a volume discount or cart adjustment discount was added as a coupon
* Bug fixed -  same prices for all ranges in the bulk table if the discount amount was added as a coupon
* Bug fixed -  prices were doubled if options were added to the product using the “Woocommerce Product Add-Ons” plugin
* Bug fixed -  prices were doubled if the bundled product was created using the "Woocommerce Product Bundles" plugin
* Bug fixed -  PHP error for role-based discounts (rare case)
* Bug fixed -  "Products Only" rules were not cached
* Bug fixed -  same rule was being applied multiple times to the same cart item when an order was created using the Phone Orders plugin
* Bug fixed -  some phrases could not be translated

= 4.6.0 - 2024-01-16 =
* Fully recoded compatibility with the popular product bundle plugins
* Reduced size of section "exclude products" (inside product filters)
* Deleted unused option "Apply pricing rules while doing API request"
* Bug fixed - fatal PHP error for wrongly formatted date in cart condition "Date"
* Bug fixed - incorrect bulk table for default variation
* Added compatibility with "Klarna On-Site Messaging for WooCommerce" plugin, by krokedil
* Added compatibility with "WC Fields Factory", by Saravana Kumar K
* Updated compatibility with "TM Extra product options"
* Updated compatibility with "Yith WooCommerce gift cards"

= 4.5.4 - 2023-12-04 =
* Speed up generation of bulk table, at product page
* Bug fixed - wrong coupon amount for mode "Don't modify price/add as coupon"
* Bug fixed - conflict between option "Override cents" and rule checkbox "Don't modify price/add as coupon"
* Bug fixed - PHP8.2 deprecation warnings
* Updated compatibility with "YITH WooCommerce Gift Cards"

= 4.5.3 - 2023-10-30 =
* Bug fixed - can't view order in the backend , if WooCommerce Payments (version 6.6.0) is active
* Bug fixed - "individual use only" didn't work properly for WC coupons
* Bug fixed - can't deactivate WooCommerce Subscription plugin
* Added compatibility with "PPOM for WooCommerce", by Themeisle
* Updated compatibility with "WPML"
* Dev - added hook "adp_is_tax_exempt_processor_active", use it only if your custom code manages tax exemption

= 4.5.2 - 2023-10-10 =
* Minor UI tweaks at tab "Rules"
* Bug fixed - field "Sale Price" was overwritten when user used >Products>All Products>Export
* Bug fixed - zero "Amount Saved" at "Thank-you" page
* Updated compatibility with "WPML"
* Updated compatibility with "YITH WooCommerce Product Add-Ons"
* Dev - we pass raw $data_rows to template bulk-table.php (to simplify custom templates)

= 4.5.1 - 2023-09-11 =
* Critical bug fixed - it was impossible to save settings in section >Settings>Cart
* Bug fixed - incorrect display of the long names of the rules
* Bug fixed - incorrect display of "Add rule" button
* Bug fixed - PHP8 deprecation warnings
* Added compatibility with "Quote for WooCommerce", by WPExperts.io

= 4.5.0 - 2023-08-29 =
We are happy to announce the restyled version of our plugin! Advanced Dynamic Pricing is more user-friendly with this update

* Updated "Rules" tab (rules color, cache control buttons moved, "Add rule" button moved)
* Show discount type selection each time a rule is created
* The field "Max Amount" renamed to "Limit discount to amount" to avoid confusion
* Show an icon for each section of the rules
* Show the most used cart conditions in the Cart Conditions section
* Added search on the "Settings" tab
* Align sections vertically in the Settings and Tools tabs.
* The "Amount Saved" option has been moved from the Customizer to the cart/order settings
* Added 'Read-only quantity' option to free products settings
* Merged import/export settings into one "Backup" section inside the "Tools" tab
* Restyled sections in the "Help" tab

= 4.4.3 - 2023-07-31 =
* Bug fixed - multiple attributes worked incorrectly inside product filter
* Bug fixed - WooCommerce coupons caused "500 error" during checkout
* Bug fixed - conflict(empty cart) with Product Feed PRO for WooCommerce, by AdTribes.io
* Bug fixed - conflict(empty discount) with Points and Rewards for WooCommerce, by WPSwings
* Bug fixed - fee removed if user edited order using plugin "Phone Orders PRO"
* Bug fixed - section Limits didn't work when the rule applied as coupon
* Bug fixed - single quote soubled in discount name , section "Cart Adjustment"
* Bug fixed - missed order stats if applied coupons were merged
* Added compatibility with "Points and Rewards for WooCommerce", by WP Swings

= 4.4.2 - 2023-06-21 =
* Bug fixed - free shipping ignored if it was added by WooCommerce сoupon
* Bug fixed - import CSV failed if field "To range" was empty for bulk rule
* Bug fixed - deprecation notices in PHP 8.1
* Added compatibility with "Variation Swatches for WooCommerce", by Emran Ahmed
* Added (multi-currency) compatibility with "WooCommerce Payments", by WooCommerce
* Updated compatibility with "Acowebs Custom Product Addons", to support version 5.x

= 4.4.1 - 2023-05-25 =
* Critical bug fixed - discount doubled for WooCommerce сoupons
* Updated code for [adp_products_on_sale] and [adp_products_bogo] shortcodes

= 4.4.0 - 2023-05-22 =
* "Products" - the default value for the new filter (section "Filter by products")
* Bug fixed - the "Free shipping" rule was not applied to the created order
* Bug fixed - variant name does not show attributes if this variant has 3+ attributes
* Bug fixed - exported rules were skipping "Cart setup" section
* Bug fixed - divide-by-zero error for products with zero price inside a bundle
* Bug fixed - fatal PHP error in Processor.php file, line 357 (only for rules with free products)
* Added compatibility with "YayCurrency - WooCommerce Multi-Currency Switcher", by YayCommerce
* Updated compatibility with "Additional product options and add-ons for WooCommerce"
* Updated compatibility with "Acowebs Custom Product Addons", fixed some php warnings
* Updated compatibility with "WPML", added hook "adp_translate_rules"

= 4.3.2 - 2023-04-19 =
* internal, not published

= 4.3.1 - 2023-04-04 =
* Added selector "When the striked price should be shown" to section >Settings>Product Price. [More details](https://docs.algolplus.com/algol_pricing/when-the-striked-price-should-be-shown/)
* Added/updated sections in Customizer
* Bug fixed - wrong "Amount Saved" displayed if option "Override the cents on the calculated price" was active
* Bug fixed - WooCommerce REST API failed with error 500 in ShippingController.php
* Bug fixed - impossible to hide fixed price for bulk table, in mode "Display ranges as headers"
* Bug fixed - PHP fatal error "undefined function wc_get_notices()"
* Added compatibility with "Mix and Match Products", by Backcourt Development
* Added compatibility with "MyRewards - Loyalty Points and Rewards for WooCommerce", by Long Watch Studio
* Updated compatibility with "WooCommerce Price Based on Country", "Woocommerce Custom Product Addons" and Shoptimizer theme

= 4.3.0 - 2023-01-24 =
* Support High-Performance order storage (COT)
* Bug fixed - option "Override the cents on the calculated price" didn't work at product page
* Bug fixed - spliited items(same product!) should be next to each other
* Bug fixed - WPC Product Bundles were ignored in the conditions
* Added compatibility with "YITH WooCommerce Product Add-Ons", by YITH
* Added compatibility with "YITH WooCommerce Product Bundles", by YITH
* Updated compatibility with "Aelia Currency Switcher"
* Updated compatibility with [Phone Orders](https://wordpress.org/plugins/phone-orders-for-woocommerce/)
* Rewrite compatibility with Polylang and WPML plugins
* Support mode "Display ranges as headers" for shortcode [adp_category_bulk_rules_table]
