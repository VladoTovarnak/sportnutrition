<?php
/**
 * Basic Cake functionality.
 *
 * Handles loading of core files needed on every request
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
if (!defined('PHP5')) {
	define('PHP5', (PHP_VERSION >= 5));
}
if (!defined('E_DEPRECATED')) {
	define('E_DEPRECATED', 8192);
}

$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
if (!strpos($actual_link, '/admin/') !== false) {
    die('www stránky v rekonstrukci');
}


error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

require CORE_PATH . 'cake' . DS . 'basics.php';
$TIME_START = getMicrotime();
require CORE_PATH . 'cake' . DS . 'config' . DS . 'paths.php';
require LIBS . 'object.php';
require LIBS . 'inflector.php';
require LIBS . 'configure.php';
require LIBS . 'set.php';
require LIBS . 'cache.php';
Configure::getInstance();
require CAKE . 'dispatcher.php';
