<?php

namespace Alpha\App\Hooks\Handlers;

use Alpha\App\Database\DBMigrator;
use Alpha\App\Database\DBSeeder;

class ActivationHandler
{
    public function handle($network_wide = false)
    {
        DBMigrator::run($network_wide);
        DBSeeder::run();
    }
}
