<?php
/**
 * Uninstall file for StoreStack Attribute Fees for WooCommerce.
 *
 * @package StoreStackAttributeFeesForWooCommerce
 */

declare(strict_types=1);

namespace StoreStackAttributeFeesForWooCommerce;

defined( 'WP_UNINSTALL_PLUGIN' ) || exit; // Prevent direct access and ensure this runs only on uninstall.

/**
 * Perform a clean uninstallation of the plugin.
 * Removes custom database tables, options, and clears cache.
 */
global $wpdb;

// 1. Drop custom database table.
$table_name = $wpdb->prefix . Migrations::$table_name;
$query      = $wpdb->prepare(
	'DROP TABLE IF EXISTS %i',
	$table_name
);
$wpdb->query( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

// 2. Clean up plugin options.
delete_option( 'ssaffw_attribute_fees_remove_data' );
delete_option( 'ssaffw_attribute_fees_plugin_version' );
delete_option( 'ssaffw_attribute_fees_db_applied_migration' );

// Remove any additional options matching ssaffw_%.
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'ssaffw_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

// 3. Clear cache.
if ( function_exists( 'wp_cache_flush' ) ) {
	wp_cache_flush();
}
