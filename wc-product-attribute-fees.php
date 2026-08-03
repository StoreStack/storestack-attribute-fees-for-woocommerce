<?php

/**
 * Plugin Name:        StoreStack Attribute Fees for WooCommerce
 * Plugin URI:         https://github.com/StoreStack/storestack-attribute-fees-for-woocommerce
 * Description:        Add fees to product attributes and change final product price based on user-selected attributes and options.
 * Version:            1.0.0
 * Author:             StoreStack
 * Author URI:         https://github.com/StoreStack
 * License:            GPLv3 or later
 * License URI:        https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:        storestack-attribute-fees-for-woocommerce
 * Requires at least:  6.2
 * Tested up to:       7.0
 * Requires Plugins:   woocommerce
 * WC tested up to:    10.3
 * Requires PHP:       8.1
 */

declare(strict_types=1);

namespace StoreStackAttributeFeesForWooCommerce;

defined('ABSPATH') || exit;


class Loader
{
    private static ?self $instance = null;

    public function __construct()
    {
        add_action('before_woocommerce_init', [$this, 'declare_wc_support']);
        add_action('plugins_loaded', [$this, 'init']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_scripts']);
    }

    public static function run()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function define_constants()
    {
        define('SSAFFW_PLUGIN_FILE', __FILE__);
        define('SSAFFW_PLUGIN_PATH', plugin_dir_path(__FILE__));
        define('SSAFFW_PLUGIN_URL', plugin_dir_url(__FILE__));
        define('SSAFFW_PLUGIN_VERSION', '1.0.0');
    }

    private function load_classes()
    {
        $includes_dir = SSAFFW_PLUGIN_PATH . 'includes/';

        require_once $includes_dir . 'class-migrations.php';
        new Migrations();

        // Load enum before AttributeFees class
        require_once $includes_dir . 'enum-fee-type.php';

        require_once $includes_dir . 'class-attribute-fees.php';
        new AttributeFees();
    }

    public function init()
    {
        $this->define_constants();
        $this->load_classes();

        $installed_version = get_option('ssaffw_attribute_fees_plugin_version');

        if ($installed_version !== SSAFFW_PLUGIN_VERSION) {
            update_option('ssaffw_attribute_fees_plugin_version', SSAFFW_PLUGIN_VERSION);
        }
    }


    public function enqueue_admin_scripts()
    {
        if (!wp_doing_ajax()) {
            wp_enqueue_style('storestack-attribute-fees-for-woocommerce-admin', SSAFFW_PLUGIN_URL . 'assets/css/admin.css', array(), SSAFFW_PLUGIN_VERSION);
            wp_enqueue_script('storestack-attribute-fees-for-woocommerce-admin', SSAFFW_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), SSAFFW_PLUGIN_VERSION, true);
        }
    }

    public function enqueue_frontend_scripts()
    {
        wp_enqueue_script('storestack-attribute-fees-for-woocommerce', SSAFFW_PLUGIN_URL . 'assets/js/frontend.js', array('jquery', 'wc-accounting', 'wc-add-to-cart-variation'), SSAFFW_PLUGIN_VERSION, true);

        wp_localize_script('storestack-attribute-fees-for-woocommerce', 'currency_params', array(
            'currency_symbol'    => esc_attr(get_woocommerce_currency_symbol()),
            'decimal_precision'  => absint(wc_get_price_decimals()),
            'thousand_separator' => esc_attr(wc_get_price_thousand_separator()),
            'decimal_separator'  => esc_attr(wc_get_price_decimal_separator()),
            'price_format'       => esc_attr(str_replace(array('%1$s', '%2$s'), array('%s', '%v'), get_woocommerce_price_format()))
        ));
    }

    public function declare_wc_support()
    {
        if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
        }
    }
}


/**
 * Run the plugin
 */
Loader::run();
