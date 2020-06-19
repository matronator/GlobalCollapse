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
		$router[] = new Route('index.php', 'Front:Default:default', Route::ONE_WAY);

		/////////////////////// ADMIN ROUTES ///////////////////////
		$router[] = $adminRouter = new RouteList('Admin');
		$adminRouter[] = new Route('admin/<presenter>/<action>[/<id>]', 'Default:default'); //most general route

		/////////////////////// FRONT ROUTES ///////////////////////
		$router[] = $frontRouter = new RouteList('Front');
		$frontRouter[] = new Route('sitemap.xml', 'Sitemap:default');
		$frontRouter[] = new Route('sitemap', 'Sitemap:default');
		$frontRouter[] = new Route('[<locale=en en|ru|cs>/]news[/<page>[/<id>]]', 'Info:news');
		$frontRouter[] = new Route('[<locale=en en|ru|cs>/]news/<post>/<id>', 'Info:post');
		$frontRouter[] = new Route('[<locale=en en|ru|cs>/]copyright-notice', 'Info:copyright');
		$frontRouter[] = new Route('[<locale=en en|ru|cs>/]terms-of-use', 'Info:tos');
		$frontRouter[] = new Route('[<locale=en en|ru|cs>/]privacy-policy', 'Info:privacy');
		$frontRouter[] = new Route('[<locale=en en|ru|cs>/]rest', 'Default:rest');
		$frontRouter[] = new Route('[<locale=en en|ru|cs>/]training', 'Default:training');
		$frontRouter[] = new Route('[<locale=en en|ru|cs>/]darknet', 'City:darknet');
		$frontRouter[] = new Route('[<locale=en en|ru|cs>/]wastelands', 'City:wastelands');
		$frontRouter[] = new Route('[<locale=en en|ru|cs>/]buildings', 'Buildings:default');
		$frontRouter[] = new Route('[<locale=en en|ru|cs>/]player/detail/<user>', 'Player:detail');
		$frontRouter[] = new Route('[<locale=en en|ru|cs>/]assaults', 'Assaults:default');
		$frontRouter[] = new Route('[<locale=en en|ru|cs>/]assaults/detail/<user>', 'Assaults:detail');
		$frontRouter[] = new Route('[<locale=en en|ru|cs>/]assaults/assault/<match>', 'Assaults:assault');

		// APIs
		$frontRouter[] = new Route('api/job', 'Api:job');

		$frontRouter[] = new Route('[<locale=en en|ru|cs>/]<presenter>/<action>[/<id>]', 'Default:default'); //most general route

		return $router;
	}

}
