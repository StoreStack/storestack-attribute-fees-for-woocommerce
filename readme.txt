=== StoreStack Attribute Fees for WooCommerce ===
Contributors: tiagosartor3, storestack
Tags: woocommerce, product attributes, extra fees, variable products, pricing
Requires at least: 6.2
Tested up to: 7.0
Requires PHP: 8.1
Stable tag: 1.0.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Add fees to product attributes and change final product price based on user-selected attributes and options.

== Description ==

StoreStack Attribute Fees for WooCommerce allows WooCommerce store owners to easily assign extra fees to variable products based on the specific attributes selected by the customer.

Whether you charge extra for custom materials, special colors, premium finishes, or specific dimensions, this plugin gives you complete control over attribute pricing directly within the product edit screen.

### Features
* **Flexible Fee Types**: Support for Flat fees, Percentage fees, and Percentage Compounded fees per attribute option.
* **Native WooCommerce Integration**: Dynamic fee calculations updated live in product variation dropdowns, cart, mini-cart, and checkout.
* **Built-in Translations**: Includes full support for English (`en_US`) and Brazilian Portuguese (`pt_BR`).

== Need Help or Have Feedback? ==

Before leaving a negative review, please consider reaching out to us first! If you encounter any bugs, unexpected behavior, or have ideas for new features and improvements, please open a support request or report an issue. We will gladly work with you to resolve any problems.

== Contributing & GitHub Repository ==

We welcome and appreciate contributions from the community! Whether you want to fix a bug, improve documentation, or propose a new feature, feel free to fork our repository, submit pull requests, or open issues on GitHub:

[GitHub Repository](https://github.com/StoreStack/storestack-attribute-fees-for-woocommerce)

Your feedback and code contributions help make this plugin better for everyone!

== Installation ==

### Getting Started 

1. **Install and Activate**:
   - Download the .zip from WordPress.org and install via **WordPress Admin > Plugins > Add New > Upload Plugin**.
   - Click **Activate** to enable the plugin.

2. **Configure Attributes on a Variable Product**:
   - Navigate to **Products > All Products** and edit an existing **Variable Product** (or create a new one).
   - Under the **Attributes** tab in the *Product Data* panel, assign your desired global or custom attributes (e.g., *Color*, *Size*, *Material*).
   - Save and update the product before proceeding to the next step.

3. **Set Attribute Fees**:
   - Click the new **Attribute Fees** tab in the *Product Data* panel.
   - For each attribute option, enter a fee value and select the appropriate **Fee Type**:
     * **Flat**: Adds a fixed dollar/currency amount (e.g. `+$10.00`).
     * **Percentage**: Adds a percentage fee calculated from the base variation price (e.g. `+15%`).
     * **Percentage Compounded**: Calculates the percentage fee based on the running price including prior fees.
   - *(Tip: Use the **Change All** dropdown and button to update all fee types in an attribute block at once!)*

4. **Save and Test on Frontend**:
   - Click **Update** to save your product.
   - Visit the product page on your store. When customers select variation options, the fees will be shown automatically in dropdown labels, the product price, cart items, mini-cart, and checkout.
   - In some hosting providers, it may be needed to delete the server-level cache in order for the changes to be reflected on the frontend.

== Frequently Asked Questions ==

= What fee types are supported? =
The plugin supports Flat fees ($), Percentage fees (%), and Percentage Compounded fees.

= I've set the fees for the product but I can't see the changes =
After setting or updating the fees, it may be needed to delete the server-level cache in order for the changes to be reflected on the frontend.

= Does this plugin support WooCommerce High-Performance Order Storage (HPOS)? =
Yes! The plugin fully declares compatibility with WooCommerce Custom Order Tables / HPOS.

= What happens to my data when I uninstall the plugin? =
When deleting the plugin from WordPress Admin, it performs a clean uninstall, which deletes any custom database table, plugin options, and cache.

== Changelog ==

= 1.0.0 - 2026/08/02 =
* Initial release.
