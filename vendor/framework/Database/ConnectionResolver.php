<?php

namespace Alpha\Framework\Database;

use Alpha\Framework\Foundation\App;
use Alpha\Framework\Database\ConnectionResolverInterface;

class ConnectionResolver implements ConnectionResolverInterface {

    /**
     * Get a database connection instance.
     *
     * @param  string $name
     *
     * @return \Illuminate\Database\Connection
     */
    public function connection( $name = null ) {
        return App::getInstance('db');
    }

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection() {
        // TODO: Implement getDefaultConnection() method.
    }

    /**
     * Set the default connection name.
     *
     * @param  string $name
     *
     * @return void
     */
    public function setDefaultConnection( $name ) {
        // TODO: Implement setDefaultConnection() method.
    }
}
