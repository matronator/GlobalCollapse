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
		$frontRouter[] = new Route('darknet', 'City:darknet');
		$frontRouter[] = new Route('waselands', 'City:wastelands');
		$frontRouter[] = new Route('<presenter>/<action>[/<htaccess>]', 'Default:default'); //most general route

		return $router;
	}

}
