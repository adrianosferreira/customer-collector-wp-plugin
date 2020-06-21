<?php

/**
 * Plugin Name: Customer Collector
 * Plugin URI:
 * Description: A plugin for collecting customer information
 * Author: Adriano Ferreira
 * Author URI: https://github.com/adrianosferreira/
 * Version: 1.0.0
 */

if ( ! defined('ABSPATH')) {
    exit;
}

define('CUSTOMER_COLLECTOR_PATH', __DIR__);
define('CUSTOMER_COLLECTOR_URL', untrailingslashit(plugin_dir_url(__FILE__)));
define('CUSTOMER_COLLECTOR_TEXTDOMAIN', 'customer-collector');

require_once CUSTOMER_COLLECTOR_PATH . '/vendor/autoload.php';

$hooks = [
    (new \ET\CustomerCollector\Hooks\PostTypeDefinition()),
    (new \ET\CustomerCollector\Hooks\CustomFieldsMetaBox((new \ET\CustomerCollector\Template\Loader())->get(),
        new \ET\CustomerCollector\Repository\CustomerRepository(new \ET\CustomerCollector\Repository\CustomerFactory()))),
    (new \ET\CustomerCollector\Hooks\Resources()),
];

foreach ($hooks as $hook) {
    $hook->registerHooks();
}