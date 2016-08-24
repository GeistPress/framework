<?php
/*
Plugin Name: GeistPress framework
Plugin URI:
Description: A framework for WordPress developers.
Version: 0.1.0
Author: Pascal Kleindienst
Author URI: https://www.pascalkleindienst.de/
License: GPLv3
*/
require_once __DIR__ . '/vendor/autoload.php';

/*----------------------------------------------------*/
// The directory separator.
/*----------------------------------------------------*/
defined('DS') ? DS : define('DS', DIRECTORY_SEPARATOR);

/*----------------------------------------------------*/
// Geistpress textdomain.
/*----------------------------------------------------*/
defined('GEISTPRESS_TEXTDOMAIN') ? GEISTPRESS_TEXTDOMAIN : define('GEISTPRESS_TEXTDOMAIN', 'geistpress-framework');

/*----------------------------------------------------*/
// Storage path.
/*----------------------------------------------------*/
defined('GEISTPRESS_STORAGE') ? GEISTPRESS_STORAGE : define('GEISTPRESS_STORAGE', WP_CONTENT_DIR.DS.'storage');

/*----------------------------------------------------*/
// Bootstrap App
/*----------------------------------------------------*/
$app = new \GeistPress\Foundation\Application();

/*----------------------------------------------------*/
// Make App available globally
/*----------------------------------------------------*/
$GLOBALS['geistpress'] = $app;
