<?php

namespace Alpha\App\Database;

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

use Alpha\App\Database\Migrations\AccountMigrator;
use Alpha\App\Database\Migrations\LedgerMigrator;
use Alpha\App\Database\Migrations\EntryMigrator;

class DBMigrator
{
    protected static $migrators = [
        AccountMigrator::class,
        LedgerMigrator::class,
        EntryMigrator::class
    ];

    public static function run($network_wide = false)
    {
        global $wpdb;

        if ($network_wide) {
            // Retrieve all site IDs from this network (WordPress >= 4.6 provides easy to use functions for that).
            if (function_exists('get_sites') && function_exists('get_current_network_id')) {
                $site_ids = get_sites(array(
                    'fields' => 'ids',
                    'network_id' => get_current_network_id()
                ));
            } else {
                $site_ids = $wpdb->get_col(
                    "SELECT blog_id FROM $wpdb->blogs WHERE site_id = $wpdb->siteid;"
                );
            }
            // Install the plugin for all these sites.
            foreach ($site_ids as $site_id) {
                switch_to_blog($site_id);
                self::migrate();
                restore_current_blog();
            }
        } else {
            self::migrate();
        }
    }

    public static function migrate()
    {
        $config = require(__DIR__ . '/../config/database.php');

        $namespace = $config['namespace'];

        foreach (static::$migrators as $class) {
            $class::migrate();
        }
    }
}
