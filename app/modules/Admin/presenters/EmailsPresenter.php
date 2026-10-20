<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\Components\DataTable\DataTable;
use App\Components\DataTable\DataTableFactory;
use App\Model\Entity\Item;
use App\Model\ItemsRepository;
use App\Model\UserRepository;
use DateTime;
use ImageStorage;
use Latte\Engine;
use Nette,
	Nette\Application\UI\Form,
	App\Model;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Nette\Mail\SmtpMailer;
use Nette\Utils\Html;
use Tracy\Debugger;

final class EmailsPresenter extends BasePresenter
{
	private ImageStorage $imageStorage;

	/** @var UserRepository */
	private $userRepository;

	private array $mailSettings;

	public function __construct(array $mailSettings, ImageStorage $imageStorage, UserRepository $userRepository)
	{
		$this->imageStorage = $imageStorage;
		$this->userRepository = $userRepository;
		$this->mailSettings = $mailSettings;
	}

	protected function startup()
	{
		parent::startup();
	}

	/*********************** RENDER VIEWS ***********************/

	public function renderDefault()
	{
		$this->template->uploadDir = ItemsRepository::IMAGES_UPLOAD_DIR;
		$this->template->imagesDir = ItemsRepository::IMAGES_DIR;
	}

	public function renderEdit(?int $id = null)
	{
		$form = $this['itemForm'];
		$this->template->id = $id;
		if ($id) {
			$item = $this->itemsRepository->get($id);
			if (!$item) {
				$this->flashMessage('Entry not found!', 'danger');
				$this->redirect('default');
			}
			$this->template->item = $item;
			$form->setDefaults($item->toArray());
		}
		$this->template->uploadDir = ItemsRepository::IMAGES_UPLOAD_DIR;
		$this->template->imagesDir = ItemsRepository::IMAGES_DIR;
	}

	public function actionDelete(int $id)
	{
		$item = $this->itemsRepository->get($id);
		if (!$item) {
			$this->flashMessage('Entry not found!');
		} else {
			$this->flashMessage('Entry deleted!');
			$this->itemsRepository->get($id)->delete();
		}
		$this->redirect('default');
	}

	protected function createComponentEmailForm()
    {
        $form = new Form;

        $form->addText('subject', 'Subject')
            ->setHtmlAttribute('class', 'uk-input')
            ->setRequired('Please enter subject.');

        $form->addTextArea('content', 'Content')
			->setHtmlAttribute('class', 'ckeditor uk-textarea');

        $form->addSubmit('save', 'Save');
        $form->onSuccess[] = [$this, 'emailFormSucceeded'];
        return $form;
    }

	public function emailFormSucceeded(Form $form, $values)
	{
		$latte = new Engine;
		$params = [
			'subject' => $values->subject,
			'content' => $values->content,
		];

		$users = $this->userRepository->findAll()->fetchAll();
		$mailer = new SmtpMailer(
			host: $this->mailSettings['host'],
			username: $this->mailSettings['username'],
			password: $this->mailSettings['password'],
			port: 465,
		);

		$sent = 0;
		$batchSize = 50;

		$mail = new Message;
		$mail->setFrom('no-reply@global-collapse.com', 'Global Collapse: Pandemic')
			->setSubject($values->subject)
			->setHtmlBody($latte->renderToString(__DIR__ . '/../templates/_emailTemplate.latte', $params));

		$success = true;

		foreach ($users as $user) {
			$mail->addTo($user->email);

			if ($sent % $batchSize === 0) {
				if ($sent <= 0) {
					$sent++;
					continue;
				}
				try {
					$mailer->send($mail);
				} catch (\Exception $e) {
					$success = false;
					Debugger::log($e);
				}
				$mail = new Message;
				$mail->setFrom('no-reply@global-collapse.com', 'Global Collapse: Pandemic')
					->setSubject($values->subject)
					->setHtmlBody($latte->renderToString(__DIR__ . '/../templates/_emailTemplate.latte', $params));
			}

			$sent++;
		}

		if ($success === false) {
			$this->flashMessage('Emails not sent!', 'danger');
			$this->redirect('default');
		}

		$this->flashMessage('Emails sent!', 'success');
		$this->redirect('default');
	}
}
