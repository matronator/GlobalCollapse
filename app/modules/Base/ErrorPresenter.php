<?php

declare(strict_types=1);

namespace App\BaseModule\Presenters;

use Nette;
use Nette\Application\Responses;
use Nette\Http;
use Tracy\ILogger;


class ErrorPresenter implements Nette\Application\IPresenter
{
	use Nette\SmartObject;

	/** @var ILogger */
	private $logger;


	public function __construct(ILogger $logger)
	{
		$this->logger = $logger;
	}


	public function run(Nette\Application\Request $request): Nette\Application\IResponse
	{
		$exception = $request->getParameter('exception');

		// 404
		if ($exception instanceof Nette\Application\BadRequestException) {
			[$module, , $sep] = Nette\Application\Helpers::splitName($request->getPresenterName());
			$module = $module ? $module : 'Front';
			$sep = $sep ? $sep : ':';
			return new Responses\ForwardResponse($request->setPresenterName($module . $sep . 'Error4xx'));
		}
		

		// 500
		$this->logger->log($exception, ILogger::EXCEPTION);
		return new Responses\CallbackResponse(function (Http\IRequest $httpRequest, Http\IResponse $httpResponse): void {
			if (preg_match('#^text/html(?:;|$)#', (string) $httpResponse->getHeader('Content-Type'))) {
				require __DIR__ . '/templates/Error/500.phtml';
			}
		});
	}

}
