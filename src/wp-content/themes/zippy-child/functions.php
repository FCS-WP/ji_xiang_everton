<?php
/*
 * Define Variables
 */
if (!defined('THEME_DIR'))
    define('THEME_DIR', get_template_directory());
if (!defined('THEME_URL'))
    define('THEME_URL', get_template_directory_uri());

if (!defined('BILLING_DATE'))
    define('BILLING_DATE', '_billing_date');
if (!defined('BILLING_TIME'))
    define('BILLING_TIME', '_billing_time');
if (!defined('BILLING_OUTLET_ADDRESS'))
    define('BILLING_OUTLET_ADDRESS', '_billing_outlet_address');
if (!defined('BILLING_OUTLET'))
    define('BILLING_OUTLET', '_billing_outlet');
if (!defined('BILLING_METHOD'))
    define('BILLING_METHOD', '_billing_method_shipping');

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
/*
 * Include framework files
 */
foreach (glob(THEME_DIR . '-child' . "/includes/*.php") as $file_name) {
    require_once($file_name);
}
