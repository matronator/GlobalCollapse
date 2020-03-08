<?php

declare(strict_types = 1);

namespace App\FrontModule\Factories;

use \Kdyby\Translation\Translator;
use App\Model\UserRepository;
use App\Model\ContactFormRepository;


class AccountFormsFactory
{
	/** @var Translator */
	private $translator;

	/** @var UserRepository */
	private $userRepository;

	public function __construct(
		Translator $translator,
		UserRepository $userRepository
	)
	{
		$this->translator = $translator;
		$this->userRepository = $userRepository;
	}

	public function createRegistrationForm()
	{
		return new \AccountForms\RegistrationForm($this->translator, $this->userRepository);
	}

	public function createChangePasswordForm()
	{
		return new \AccountForms\ChangePasswordForm($this->translator, $this->userRepository);
	}

	public function createPasswordRecoveryForm()
	{
		return new \AccountForms\PasswordRecoveryForm($this->translator, $this->userRepository);
	}

	public function createSetNewPasswordForm()
	{
		return new \AccountForms\SetNewPasswordForm($this->translator, $this->userRepository);
	}

	public function createLogInForm()
	{
		return new \AccountForms\LogInForm($this->translator, $this->userRepository);
	}
}

class ContactFormFactory
{
	/** @var Translator */
	private $translator;

	/** @var ContactFormRepository */
	private $contactFormRepository;

	public function __construct(
		Translator $translator,
		ContactFormRepository $contactFormRepository
	)
	{
		$this->translator = $translator;
		$this->contactFormRepository = $contactFormRepository;
	}

	public function createContactForm()
	{
		return new \ContactForm($this->translator, $this->contactFormRepository);
	}
}