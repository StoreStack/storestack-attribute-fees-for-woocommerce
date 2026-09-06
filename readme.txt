=== StoreStack Attribute Fees for WooCommerce ===
Contributors: tiagosartor3, storestack
Tags: woocommerce, product attributes, extra fees, variable products, pricing
Requires at least: 6.7
Tested up to: 7.1
Requires PHP: 8.2
Stable tag: 1.1.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Add fees to product attributes and change final product price based on user-selected attributes and options.

== Description ==

StoreStack Attribute Fees for WooCommerce allows WooCommerce store owners to easily assign extra fees to variable products based on the specific attributes selected by the customer.

Whether you charge extra for custom materials, special colors, premium finishes, or specific dimensions, this plugin gives you complete control over attribute pricing directly within the product edit screen.

### Features
* **Flexible Fee Types**: Support for flat fees, percentage fees, and compound percentage fees for each attribute option.
* **Native WooCommerce Integration**: Dynamic fee calculations update in real-time on variable product pages, cart, mini-cart, and checkout.
* **Built-in Translations**: Includes full support for English (`en_US`) and Brazilian Portuguese (`pt_BR`).

### Integrations

Works seamlessly with the following plugins:

* **[WooCommerce](https://wordpress.org/plugins/woocommerce)**
* **[StoreStack Attribute Swatches for WooCommerce](https://wordpress.org/plugins/storestack-attribute-swatches-for-woocommerce)**

== Need Help or Have Feedback? ==

Before leaving a negative review, please consider reaching out to us first! If you encounter any bugs, unexpected behavior, or have ideas for new features and improvements, please open a support request or report an issue on our GitHub repository. We will gladly work with you to resolve any problems.

== Contributing & GitHub Repository ==

We welcome and appreciate contributions from the community! Whether you want to fix a bug, improve documentation, or propose a new feature, feel free to fork our repository, submit pull requests, or open issues on GitHub:

[GitHub Repository](https://github.com/StoreStack/storestack-attribute-fees-for-woocommerce)

Your feedback and code contributions help make this plugin better for everyone.

== Installation ==

### Getting Started 

1. **Install and Activate**:
   - Download the `.zip` file from WordPress.org and install via **WordPress Admin > Plugins > Add New > Upload Plugin**.
   - Click **Activate** to enable the plugin.

2. **Configure Attributes on a Variable Product**:
   - Navigate to **Products > All Products** and edit an existing **Variable Product** (or create a new one).
   - Under the **Attributes** tab in the *Product Data* panel, assign your desired global or custom attributes (e.g., *Color*, *Size*, *Material*).
   - Save and update the product before proceeding to the next step.

3. **Set Attribute Fees**:
   - Click the new **Attribute Fees** tab in the *Product Data* panel.
   - For each attribute option, enter a fee value and select the appropriate **Fee Type**:
     * **Flat**: Adds a fixed currency amount (e.g. `+$10.00`).
     * **Percentage**: Adds a percentage fee calculated from the base variation price (e.g. `+15%`).
     * **Compound Percentage**: Calculates the percentage fee based on the running price including prior fees.
   - *Tip: Use the **Change All** dropdown and button to update all fee types in an attribute block at once.*

4. **Save and Test on Frontend**:
   - Click **Update** to save your product.
   - Visit the product page on your store. When customers select variation options, the fees will be displayed next to the option labels and prices will update live for the product, cart items, mini-cart, and checkout.
   - *On some hosting providers, it may be needed to delete the server-level cache in order for the changes to be reflected on the frontend.*

== Frequently Asked Questions ==

= What fee types are supported? =
The plugin supports flat fees ($), percentage fees (%), and compound percentage fees.

= I've set the fees for the product but I can't see the changes =
After setting or updating the fees, it may be needed to delete the server-level cache in order for the changes to be reflected on the frontend.

= Does this plugin support WooCommerce High-Performance Order Storage (HPOS)? =
Yes! The plugin declares full compatibility with WooCommerce HPOS (Custom Order Tables).

= What happens to my data when I uninstall the plugin? =
When deleting the plugin from WordPress Admin, it performs a clean uninstall, which deletes any custom database table, plugin options, and cache.

== Screenshots ==

1. Assign attributes to an existing variable product.
2. Set fees for each attribute option.
3. Frontend demo with the Storefront theme.

== Changelog ==

= 1.1.0 - 2026/09/07 =
* Updated the minimum supported versions to PHP 8.2, WordPress 6.7, and WooCommerce 10.0.
* Improved adherence to WordPress Coding Standards and strengthened type declarations to enhance performance, reliability, and maintainability.

= 1.0.2 - 2026/08/16 =
* Refactored attribute fee handling.
* Improved code consistency for price calculation.
* Fixed fee type terminology.
* Updated README.

= 1.0.1 - 2026/08/03 =
* Updated README.

= 1.0.0 - 2026/08/02 =
* Initial release.
