<?php

namespace Alpha\App\Database\Migrations;

class LedgerMigrator
{
    static $tableName = 'alpha_ledgers';

    public static function migrate()
    {
        global $wpdb;

        $charsetCollate = $wpdb->get_charset_collate();

        $table = $wpdb->prefix . static::$tableName;

        $parentTable = $wpdb->prefix . 'alpha_accounts';

        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
            $sql = "CREATE TABLE $table (
                `id` BIGINT(20) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
                `account_id` BIGINT(20) UNSIGNED NOT NULL,
                `name` VARCHAR(100) NULL,
                `note` TEXT NULL,
                `created_at` TIMESTAMP NULL,
                `updated_at` TIMESTAMP NULL,
                FOREIGN KEY (account_id)
                REFERENCES $parentTable (id)
                ON DELETE CASCADE
            ) $charsetCollate;";
            dbDelta($sql);
        }
    }
}
