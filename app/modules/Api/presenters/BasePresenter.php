<?php

declare(strict_types=1);

namespace App\ApiModule\Presenters;

use App\ApiModule\Responses\BaseResponse;
use App\ApiModule\Responses\Enums\ResponseMessage;
use App\ApiModule\Responses\Enums\ResponseStatus;
use App\ApiModule\Responses\ResponseFactory;
use App\Model;
use App\Services\PaymentService;
use DateTime;
use Nette\Database\Table\ActiveRow;
use stdClass;

class BasePresenter extends \App\BaseModule\Presenters\BasePresenter
{
	public $userPrefs;

	/** @var PaymentService @inject */
	public PaymentService $paymentService;

	/** @var ResponseFactory @inject */
	public ResponseFactory $responseFactory;

	/** @var Model\UserRepository */
	protected $userRepository;

	/** @var Model\UnlockablesRepository */
	protected $unlockablesRepository;

    /** @var Model\StatisticsRepository */
    protected $statisticsRepository;

	public function injectRepository(
		Model\UserRepository $userRepository,
		Model\UnlockablesRepository $unlockablesRepository,
        Model\StatisticsRepository $statisticsRepository
	)
	{
		$this->userRepository = $userRepository;
		$this->unlockablesRepository = $unlockablesRepository;
        $this->statisticsRepository = $statisticsRepository;
	}

	protected function beforeRender()
	{
		$this->template->urlAbsolutePath = $this->getURL()->hostUrl;
		$this->template->urlFullDomain = $this->getURL()->host;

		$this->getHttpResponse()->setContentType('application/json', 'utf-8');

		if (!$this->user->isLoggedIn()) {
			$this->sendError(ResponseMessage::NOT_LOGGED_IN);
			$this->redirect(':Front:Signup:default');
		}

		if (!$this->isAllowed('read')) {
			$this->sendError(ResponseMessage::NOT_PERMITTED);
		}
	}

	protected function sendError(ResponseMessage $message): never
	{
		$this->getHttpResponse()->setCode(400);
		$this->sendJson($this->responseFactory->create(ResponseStatus::ERROR, $message, [
			'href' => $this->link('Account:login'),
		]));
	}

	protected function sendApiResponse(array $response)
	{
		$this->getHttpResponse()->setCode(200);
		$this->sendJson($response);
	}

	public function isAllowed($privilege, $resource = null)
	{
		$resource = $resource ? $resource : $this->name;
		return $this->user->isAllowed($resource, $privilege);
	}

	/**
	 * Check if time between `$date` and `$currentDate` is larger than `$targetTime` (defaults to 3600 or 1 hour). Returns the remaining time to `$targetTime` or 0.
	 * @return integer
	 * @param DateTime $date
	 * @param DateTime $currentDate
	 * @param integer $targetTime
	 */
	protected static function checkDates(DateTime $date, DateTime $currentDate, int $targetTime = 3600): int
	{
		$diff = abs($date->getTimestamp() - $currentDate->getTimestamp());
		if ($diff >= $targetTime) {
			return 0;
		} else {
			return $targetTime - $diff;
		}
	}
}
