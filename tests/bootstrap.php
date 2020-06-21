<?php

define('CUSTOMER_COLLECTOR_URL', 'http://site.com');
define('CUSTOMER_COLLECTOR_TEXTDOMAIN', 'customer-collector');
define('CUSTOMER_COLLECTOR_PATH', __DIR__ . '/../');

require_once  'vendor/autoload.php';

WP_Mock::setUsePatchwork( false );
WP_Mock::bootstrap();