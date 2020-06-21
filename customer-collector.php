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

$customerFactory = new \ET\CustomerCollector\Repository\CustomerFactory();
$customerRepository = new \ET\CustomerCollector\Repository\CustomerRepository($customerFactory);
$template = (new \ET\CustomerCollector\Template\Loader())->get();

$hooks = [
    (new \ET\CustomerCollector\Hooks\PostTypeDefinition()),
    (new \ET\CustomerCollector\Hooks\CustomFieldsMetaBox($template, $customerRepository, $customerFactory)),
    (new \ET\CustomerCollector\Hooks\Resources()),
    (new \ET\CustomerCollector\Hooks\Shortcode($template)),
    (new \ET\CustomerCollector\Hooks\SaveCustomerAjax($customerRepository, $customerFactory))
];

foreach ($hooks as $hook) {
    $hook->registerHooks();
}