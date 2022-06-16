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
			// EN
			->addRoute('[<locale=en en|ru>/]news[/<page>[/<id>]]', 'Info:news')
			->addRoute('[<locale=en en|ru>/]news/<post>/<id>', 'Info:post')
			->addRoute('[<locale=en en|ru>/]copyright-notice', 'Info:copyright')
			->addRoute('[<locale=en en|ru>/]terms-of-use', 'Info:tos')
			->addRoute('[<locale=en en|ru>/]privacy-policy', 'Info:privacy')
			->addRoute('[<locale=en en|ru>/]earn-reward', 'Info:voting')
			->addRoute('[<locale=en en|ru>/]reset-password/<hash>', 'Recover:default')
			->addRoute('[<locale=en en|ru>/]unlockables', 'Default:unlockables')
			->addRoute('[<locale=en en|ru>/]rest', 'Default:rest')
			->addRoute('[<locale=en en|ru>/]training', 'Default:training')
			->addRoute('[<locale=en en|ru|cs>/]darknet', 'City:darknet')
			->addRoute('[<locale=en en|ru>/]wastelands', 'City:wastelands')
			->addRoute('[<locale=en en|ru>/]lands', 'Buildings:lands')
			->addRoute('[<locale=en en|ru>/]buildings', 'Buildings:default')
			->addRoute('[<locale=en en|ru>/]player/detail/<user>', 'Player:detail')
			->addRoute('[<locale=en en|ru>/]assaults', 'Assaults:default')
			->addRoute('[<locale=en en|ru>/]assaults/detail/<user>', 'Assaults:detail')
			->addRoute('[<locale=en en|ru>/]assaults/assault/<match>', 'Assaults:assault')

			// CZ
			->addRoute('[<locale=cs cs>/]novinky[/<page>[/<id>]]', 'Info:news')
			->addRoute('[<locale=cs cs>/]novinky/<post>/<id>', 'Info:post')
			->addRoute('[<locale=cs cs>/]vlastnicka-prava', 'Info:copyright')
			->addRoute('[<locale=cs cs>/]podminky-pouziti', 'Info:tos')
			->addRoute('[<locale=cs cs>/]ochrana-soukromi', 'Info:privacy')
			->addRoute('[<locale=cs cs>/]ziskej-odmenu', 'Info:voting')
			->addRoute('[<locale=cs cs>/]obnova-hesla/<hash>', 'Recover:default')
			->addRoute('[<locale=cs cs>/]odmeny', 'Default:unlockables')
			->addRoute('[<locale=cs cs>/]odpocinek', 'Default:rest')
			->addRoute('[<locale=cs cs>/]trenink', 'Default:training')
			->addRoute('[<locale=cs cs>/]pustina', 'City:wastelands')
			->addRoute('[<locale=cs cs>/]pozemky', 'Buildings:lands')
			->addRoute('[<locale=cs cs>/]budovy', 'Buildings:default')
			->addRoute('[<locale=cs cs>/]hrac/detail/<user>', 'Player:detail')
			->addRoute('[<locale=cs cs>/]prepadeni', 'Assaults:default')
			->addRoute('[<locale=cs cs>/]prepadeni/detail/<user>', 'Assaults:detail')
			->addRoute('[<locale=cs cs>/]prepadeni/souboj/<match>', 'Assaults:assault')

			->addRoute('[<locale=en en|ru|cs>/]<presenter>/<action>[/<id>]', 'Default:default') //most general route

		// APIs
		->addRoute('api/job', 'Api:job')

		->addRoute('[<locale=en en|ru|cs>/]<presenter>/<action>[/<id>]', 'Default:default'); //most general route

		return $router;
	}

}
