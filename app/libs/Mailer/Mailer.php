<?php
declare(strict_types=1);

namespace App\Libs\Mailer;

use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use \Contributte\Translation\Translator;
use Nette\Application\LinkGenerator;

class Mailer {

    private $config;
    private $translator;
    private $linkGenerator;

    public function __construct(
        array $config,
        Translator $translator,
        LinkGenerator $linkGenerator
    )
    {
        $this->config = $config;
        $this->translator = $translator;
        $this->linkGenerator = $linkGenerator;
    }

    public function sendRegistrationEmail(array $values)
    {
        //Install LinkGenerator to latte so we can use {link} and n:href in email template
        $latte = new \Latte\Engine;
        \Nette\Bridges\ApplicationLatte\UIMacros::install($latte->getCompiler());
        $latte->addProvider('uiControl', $this->linkGenerator);

        // Create email
        $mail = new Message;
        $mail->setFrom($this->config['email'])
        ->addTo($values['email'])
        ->setSubject($this->translator->translate('m.email.registration.subject'))
        ->setHtmlBody($latte->renderToString(__DIR__ . '/emailTemplateRegistration.latte', ['values' => $values]));

        // dump('success die, trying to send mail');
        // dump($mail);
        // die();

        // Send mail
        $mailer = new SendmailMailer;
        $mailer->send($mail);
    }

    public function sendPasswordRecoveryEmail(array $values)
    {
        //Install LinkGenerator to latte so we can use {link} and n:href in email template
        $latte = new \Latte\Engine;
        \Nette\Bridges\ApplicationLatte\UIMacros::install($latte->getCompiler());
        $latte->addProvider('uiControl', $this->linkGenerator);

        // Create email
        $mail = new Message;
        $mail->setFrom($this->config['email'])
        ->addTo($values['email'])
        ->setSubject($this->translator->translate('m.email.recovery.subject'))
        ->setHtmlBody($latte->renderToString(__DIR__ . '/emailTemplateRecoverPassword.latte', ['values' => $values]));

        // dump('success die, trying to send mail');
        // dump($mail->body);
        // die();

        // Send mail
        $mailer = new SendmailMailer;
        $mailer->send($mail);
    }
}
