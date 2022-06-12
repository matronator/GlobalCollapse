<?php

declare(strict_types=1);

namespace App\BaseModule\Presenters;

use Nette\Http\Request;
use Nette\Http\Url;
use Nette\Application\UI\Presenter;
use Nette\Localization\Translator;

class BasePresenter extends Presenter
{

	/** @persistent */
	public $locale;

	/** @var array */
    public $localeList;

	/** @var array */
	public $localeArrayMap;

	/** @var string */
	public $defaultLocale;

	/** @var Translator @inject */
	public $translator;


	protected function startup()
	{
		parent::startup();

        $this->localeList = ['en', 'ru', 'cs'];
        $this->localeArrayMap = [
            'en' => 'English',
            'ru' => 'Русский',
            'cs' => 'Čeština'
        ];
        $this->locale = $this->getParameter('locale') ?? 'en';
    }

    /**
     * Shortcut translation method
     * @return string
	 * @param mixed $message
	 * @param mixed ...$parameters
	 */
	public function translate($message, ...$parameters): string
    {
        return $this->translator->translate($message, $parameters);
    }

	// protected function verifyRecaptcha($recaptcha_token)
    // {
    //     $post_data = http_build_query(
    //         [
    //             'secret' => 'SECRET_KEY_HERE',
    //             'response' => $recaptcha_token,
    //             'remoteip' => $_SERVER['REMOTE_ADDR']
    //         ]
    //     );
    //     $opts = [
    //         'http' =>
    //         [
    //             'method'  => 'POST',
    //             'header'  => 'Content-type: application/x-www-form-urlencoded',
    //             'content' => $post_data
    //         ]
    //     ];
    //     $context  = stream_context_create($opts);
    //     $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
    //     $result = json_decode($response);

    //     return $result;
    // }

    public function getURL()
	{
		$httpRequest = $this->getHttpRequest();
		$rawUrl = $httpRequest->getUrl();
		return new Url($rawUrl);
	}

}
