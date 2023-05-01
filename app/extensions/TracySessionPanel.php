<?php

declare(strict_types=1);

namespace App\Extensions;

use Nette\Utils\Helpers;
use Tracy\IBarPanel;

class TracySessionPanel implements IBarPanel
{
	public function getTab()
	{
		return Helpers::capture(function () {
			require __DIR__ . '/templates/TracySessionPanel.tab.phtml';
		});
	}

	public function getPanel()
	{
		return Helpers::capture(function () {
			require __DIR__ . '/templates/TracySessionPanel.panel.phtml';
		});
	}
}
