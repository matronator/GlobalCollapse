<?php

declare(strict_types=1);

// Uncomment this line if you must temporarily take down your site for maintenance.
// require '.maintenance.php';

// if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
// 	if ($_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' && isset($_SERVER['SERVER_PORT']) && in_array($_SERVER['SERVER_PORT'], [80, 82])) { // https over proxy
// 		$_SERVER['HTTPS'] = 'On';
// 		$_SERVER['SERVER_PORT'] = 443;

// 	} elseif ($_SERVER['HTTP_X_FORWARDED_PROTO'] === 'http' && isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 80) { // http over proxy
// 		$_SERVER['HTTPS'] = 'Off';
// 		$_SERVER['SERVER_PORT'] = 80;
// 	}
// }

// absolute filesystem path to the web root
define('WWW_DIR', __DIR__);

// absolute filesystem path to the application root
define('APP_DIR', WWW_DIR . '/../app');

// absolute filesystem path to the libraries
define('LIBS_DIR', WWW_DIR . '/../libs');

// absolute filesystem path to the temporary files
define('TEMP_DIR', WWW_DIR . '/../temp');

// absolute filesystem path to the temporary files
define('LOG_DIR', WWW_DIR . '/../log');

// absolute filesystem path to the application root
define('LANG_DIR', WWW_DIR . '/../lang');

// absolute filesystem path to the application root
define('CONF_DIR', WWW_DIR . '/../app/config/');

require __DIR__ . '/../vendor/autoload.php';
App\Bootstrap::boot()
	->createContainer()
	->getByType(Nette\Application\Application::class)
	->run();
