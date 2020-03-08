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

	/** @var string */
	public $defaultLocale;

	/** @var ITranslator @inject */
	public $translator;


	protected function startup()
	{
		parent::startup();

		foreach($this->translator->getAvailableLocales() as $lang){
			$this->localeList[] = substr($lang, 0, 2);
		}

		$this->defaultLocale = $this->translator->getDefaultLocale();
	}

	protected function verifyRecaptcha($recaptcha_token)
    {
        $post_data = http_build_query(
            [
                'secret' => 'SECRET_KEY_HERE',
                'response' => $recaptcha_token,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            ]
        );
        $opts = [
            'http' =>
            [
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $post_data
            ]
        ];
        $context  = stream_context_create($opts);
        $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
        $result = json_decode($response);
        
        return $result;
    }

    public function getURL()
	{
		$httpRequest = $this->getHttpRequest();
		$rawUrl = $httpRequest->getUrl();
		return new Url($rawUrl);
	}

}
