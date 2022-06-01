<?php

declare(strict_types=1);

namespace App\BaseModule\Presenters;

use Nette\Http\Request;
use Nette\Http\Url;
use Nette\Application\UI\Presenter;
use Nette\Localization\ITranslator;

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

	/** @var ITranslator @inject */
	public $translator;


	protected function startup()
	{
		parent::startup();

        $this->localeList = ['en', 'ru', 'cs'];
        $this->localeArrayMap = [
            'en' => 'English',
            'ru' => 'Russian',
            'cs' => 'Čeština'
        ];
        $this->locale = $this->getParameter('locale');
    }

    /**
     * Shortcut translation method
     * @param string $key
     * @param array $args
     * @return string
     */
    public function translate($key, $args = [])
    {
        return $this->translator->translate($key, $args);
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
