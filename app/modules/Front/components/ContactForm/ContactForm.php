<?php

declare(strict_types=1);

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Nette\Utils\DateTime;
use \Contributte\Translation\Translator;
use App\Model\ContactFormRepository;

class ContactForm extends Control {

    private $translator;
    private $contactFormRepository;
    public $onSuccess;
    public $onError;

    public function __construct(
        Translator $translator,
        ContactFormRepository $contactFormRepository
    )
    {
        $this->translator = $translator;
        $this->contactFormRepository = $contactFormRepository;
    }

    public function render($params = [])
    {
        $this->template->setParameters($params);
        $this->template->setFile(__DIR__ . '/ContactForm.latte');
        $this->template->render();
    }

    public function createComponentForm()
    {
        $gdprConsent = $this->presenter->getHttpRequest()->getUrl()->getBasePath() . 'downloads/gdpr_file.pdf';

        $form = new Form;

        $form->addText('fullname', $this->translate('form.contact.fullname'))
            ->setAttribute('placeholder', $this->translate('form.contact.fullname'))
            ->setRequired($this->translate('form.general.required', ['requiredItem' => '"%label"']));

        $form->addText('phone', $this->translate('form.contact.phone'))
            ->setAttribute('type', 'tel')
            ->setAttribute('placeholder', $this->translate('form.contact.phone'));

        $form->addEmail('email', $this->translate('form.contact.email'))
            ->setAttribute('placeholder', $this->translate('form.contact.email'))
            ->addRule(Form::EMAIL, $this->translate('form.contact.invalidMail'))
            ->setRequired($this->translate('form.general.required', ['requiredItem' => '"%label"']));

        $form->addTextarea('text', $this->translate('form.contact.text'))
            ->setAttribute('placeholder', $this->translate('form.contact.text'))
            ->setRequired($this->translate('form.general.required', ['requiredItem' => '"%label"']));

        $form->addCheckbox('consent', $this->translate('form.contact.consent', ['link' => $gdprConsent]))
            ->setAttribute('class', 'checkbox')
            ->setRequired($this->translate('form.general.requiredConsent'));

        $form->addCheckbox('consentNewsletter', $this->translate('form.contact.consentNewsletter', ['link' => $gdprConsent]))
            ->setAttribute('class', 'checkbox')
            ->setRequired($this->translate('form.general.requiredConsent'));

        $form->addHidden('recaptcha_token');

        $form->addSubmit('send', $this->translate('form.contact.send'));

        $form->onSuccess[] = [$this, 'processForm'];
        return $form;
    }

    public function processForm(Form $form, $values)
    {
        $recaptcha = $this->verifyRecaptcha($values->recaptcha_token);

        if ($recaptcha->success && $recaptcha->score > 0.49){

            $mailer = new SendmailMailer;
            $mail = new Message;
            $latte = new Latte\Engine;

            $mail->setFrom('info@mail.yo');
            $mail->addReplyTo($values->email);
            $mail->setSubject('Subject');
            $mail->setHtmlBody($latte->renderToString(__DIR__ . '/ContactFormMail.latte', ['values' => $values]));
            $mail->addTo('target@mail.hu');

            try {
                $this->model->findAll()->insert([
                    'data'=>json_encode($values),
                    'datetime'=> new \DateTime(),
                    'ip'=>$_SERVER["REMOTE_ADDR"]
                ]);
                $mailer->send($mail);
                $this->presenter->flashMessage($this->translate('form.contact.success'), 'success');
            } catch (\Exception $e) {
                $this->presenter->flashMessage($this->translate('form.contact.failed'), 'error');
                Debugger::log(new \Exception($e->getMessage()));
            }
            $this->onSuccess($form);
        }else{
            $this->presenter->flashMessage($this->translate('form.contact.errorYouAreRobot'), 'error');
            $this->onError($form);
        }
    }
}
