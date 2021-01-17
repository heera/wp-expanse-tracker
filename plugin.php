<?php defined('ABSPATH') or die;

/*
Plugin Name: Alpha
Description: An accounting plugin to manage expenses.
Version: 1.0.0
Author: Sheikh Heera
Author URI: https://heera.it
Plugin URI: https://heera.it
License: GPLv2 or later
Text Domain: alpha
Domain Path: /language
*/

require __DIR__.'/vendor/autoload.php';

call_user_func(function($bootstrap) {
    $bootstrap(__FILE__);
}, require(__DIR__.'/boot/app.php'));
