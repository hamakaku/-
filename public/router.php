<?php

/**
 * Router for PHP's built-in development server.
 * Existing files are served directly; all other requests are handled by FuelPHP.
 */

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__.str_replace('/', DIRECTORY_SEPARATOR, $path);

if ($path !== '/' && is_file($file))
{
	return false;
}

require __DIR__.DIRECTORY_SEPARATOR.'index.php';
