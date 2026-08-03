<?php

declare(strict_types=1);

namespace StoreStackAttributeFeesForWooCommerce;

defined('ABSPATH') || exit;


class Migrations
{
    public static string $table_name = 'ssaffw_attribute_fees';
    private static string $latest_migration = '1';

    public static function migrate()
    {
        $applied_migration = get_option('ssaffw_attribute_fees_db_applied_migration', '0');

        // If the DB is already up to date, return.
        if (version_compare($applied_migration, self::$latest_migration, '>=')) {
            return;
        }

        // Define migrations in chronological order: 'version' => 'method_name'
        $migrations = [
            '1' => 'migrate_1',
            // '2' => 'migrate_2',
        ];

        foreach ($migrations as $version => $method) {
            // Only run migrations that are newer than the currently applied version
            if (version_compare($applied_migration, (string)$version, '<')) {
                if (method_exists(self::class, $method)) {
                    // Execute the migration
                    self::$method();

                    // Update the option step-by-step in case a later migration fails
                    update_option('ssaffw_attribute_fees_db_applied_migration', $version);
                }
            }
        }
    }

    /**
     * Migration 1: Initial table creation and foreign keys.
     */
    private static function migrate_1()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . self::$table_name;

        $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `product_id` BIGINT(20) UNSIGNED NOT NULL,
            `attribute_id` BIGINT(20) UNSIGNED NOT NULL,            
            `term_id` BIGINT(20) UNSIGNED NOT NULL,
            `fee_type` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,            
            `fee` DECIMAL(14,2) DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uq_product_attribute_option` (`product_id`, `attribute_id`, `term_id`),
            FOREIGN KEY (`product_id`) REFERENCES {$wpdb->prefix}posts(`ID`) ON DELETE CASCADE,
            FOREIGN KEY (`attribute_id`) REFERENCES {$wpdb->prefix}woocommerce_attribute_taxonomies(`attribute_id`) ON DELETE CASCADE,
            FOREIGN KEY (`term_id`) REFERENCES {$wpdb->prefix}terms(`term_id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $wpdb->query($sql);
    }

    /**
     * Future Migration Example
     * Safely add a new column in the future.
     */
    // private static function migrate_2()
    // {
    //     global $wpdb;
    //     $table_name = $wpdb->prefix . self::$table_name;

    //     // Include WordPress upgrade functions so we can use maybe_add_column()
    //     require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    //     // maybe_add_column() safely checks if 'status' exists before trying to add it
    //     $create_ddl = "ALTER TABLE {$table_name} ADD COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'active';";
    //     maybe_add_column($table_name, 'status', $create_ddl);
    // }
}
