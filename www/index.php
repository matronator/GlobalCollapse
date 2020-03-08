<?php

declare(strict_types=1);

// Uncomment this line if you must temporarily take down your site for maintenance.
// require '.maintenance.php';

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
