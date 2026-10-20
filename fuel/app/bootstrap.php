<?php
/**
 * Fuel is a fast, lightweight, community driven PHP 5.4+ framework.
 *
 * @package    Fuel
 * @version    1.9-dev
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010-2026 Fuel Development Team
 * @link       https://fuelphp.com
 */

// Bootstrap the framework - THIS LINE NEEDS TO BE FIRST!
require COREPATH.'bootstrap.php';

// Add framework overload classes here
\Autoloader::add_classes(array(
	// Example: 'View' => APPPATH.'classes/myview.php',
	'MyRules' => APPPATH.'classes/extensions/myrules.php',
));

// Register the autoloader
\Autoloader::register();

/**
 * PHP 8 以降は @ で抑制したエラーでも error_reporting() が 0 にならないため、
 * FuelPHP のエラーハンドラが例外にしてしまう（例: Twig のキャッシュ書き込み時の @chmod()）。
 * 抑制されたエラーは FuelPHP のハンドラに渡さずに無視する。
 */
$fuel_error_handler = set_error_handler(function ($severity, $message, $filepath, $line) use (&$fuel_error_handler)
{
	if ( ! (error_reporting() & $severity))
	{
		return true;
	}

	return call_user_func($fuel_error_handler, $severity, $message, $filepath, $line);
});

/**
 * Your environment.  Can be set to any of the following:
 *
 * Fuel::DEVELOPMENT
 * Fuel::TEST
 * Fuel::STAGING
 * Fuel::PRODUCTION
 */
Fuel::$env = Arr::get($_SERVER, 'FUEL_ENV', Arr::get($_ENV, 'FUEL_ENV', getenv('FUEL_ENV') ?: Fuel::DEVELOPMENT));

// Initialize the framework with the config file.
\Fuel::init('config.php');
