<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;
		$router->addRoute('index.php', 'Front:Default:default', Route::ONE_WAY);

		/////////////////////// ADMIN ROUTES ///////////////////////
		$router->withModule('Admin')
			->addRoute('admin/<presenter>/<action>[/<id>]', 'Default:default'); //most general route

		/////////////////////// FRONT ROUTES ///////////////////////
		$router->withModule('Front')
			->addRoute('sitemap.xml', 'Sitemap:default')
			->addRoute('sitemap', 'Sitemap:default')
			->addRoute('[<locale=en en|ru|cs>/]news[/<page>[/<id>]]', 'Info:news')
			->addRoute('[<locale=en en|ru|cs>/]news/<post>/<id>', 'Info:post')
			->addRoute('[<locale=en en|ru|cs>/]copyright-notice', 'Info:copyright')
			->addRoute('[<locale=en en|ru|cs>/]terms-of-use', 'Info:tos')
			->addRoute('[<locale=en en|ru|cs>/]privacy-policy', 'Info:privacy')
			->addRoute('[<locale=en en|ru|cs>/]reset-password/<hash>', 'Recover:default')
			->addRoute('[<locale=en en|ru|cs>/]rest', 'Default:rest')
			->addRoute('[<locale=en en|ru|cs>/]training', 'Default:training')
			->addRoute('[<locale=en en|ru|cs>/]darknet', 'City:darknet')
			->addRoute('[<locale=en en|ru|cs>/]wastelands', 'City:wastelands')
			->addRoute('[<locale=en en|ru|cs>/]lands', 'Buildings:lands')
			->addRoute('[<locale=en en|ru|cs>/]buildings', 'Buildings:default')
			->addRoute('[<locale=en en|ru|cs>/]player/detail/<user>', 'Player:detail')
			->addRoute('[<locale=en en|ru|cs>/]assaults', 'Assaults:default')
			->addRoute('[<locale=en en|ru|cs>/]assaults/detail/<user>', 'Assaults:detail')
			->addRoute('[<locale=en en|ru|cs>/]assaults/assault/<match>', 'Assaults:assault')
			->addRoute('[<locale=en en|ru|cs>/]<presenter>/<action>[/<id>]', 'Default:default') //most general route

		// APIs
		->addRoute('api/job', 'Api:job')

		->addRoute('[<locale=en en|ru|cs>/]<presenter>/<action>[/<id>]', 'Default:default'); //most general route

		return $router;
	}

}
