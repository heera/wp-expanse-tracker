<?php

namespace Alpha\App\Database\Migrations;

class EntryMigrator
{
    static $tableName = 'alpha_entries';

    public static function migrate()
    {
        global $wpdb;

        $charsetCollate = $wpdb->get_charset_collate();

        $table = $wpdb->prefix . static::$tableName;

        $parentTable = $wpdb->prefix . 'alpha_ledgers';

        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
            $sql = "CREATE TABLE $table (
                `id` BIGINT(20) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
                `ledger_id` BIGINT(20) UNSIGNED NOT NULL,
                `title` VARCHAR(100) NOT NULL,
                `amount` DECIMAL(10, 2),
                `note` TEXT NULL,
                `created_at` TIMESTAMP NULL,
                `updated_at` TIMESTAMP NULL,
                FOREIGN KEY (ledger_id)
                REFERENCES $parentTable (id)
                ON DELETE CASCADE
            ) $charsetCollate;";
            dbDelta($sql);
        }
    }
}
