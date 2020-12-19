<?php

declare(strict_types=1);

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use App\Model\UserRepository;
use Nette\Application\BadRequestException;

class DatetimeForm extends Control {

    private $userRepository;
    public $onSuccess;
    public $onError;

    public function __construct(
        UserRepository $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }

    public function render($params = [])
    {
        $this->template->setParameters($params);
        $this->template->setFile(__DIR__ . '/DatetimeForm.latte');
        $this->template->render();
    }

    public function createComponentForm()
    {
        $allzones = Timezones::TIME_ZONES;
        $timezones = array_slice($allzones, 1, count($allzones) - 2, true);
        $userSettings = $this->userRepository->getSettings($this->presenter->user->getIdentity()->id)->fetch();
        $form = new Form;
        $form->addSelect('timezone', 'Choose your timezone', array_combine(array_keys($timezones), array_column($timezones, 'label')))
            ->setDefaultValue($userSettings->timezone)
            ->setHtmlAttribute('class', 'uk-select')
            ->setHtmlId('timezones')
            ->setHtmlAttribute('placeholder', 'Select a timezone')
            ->setRequired();
        $form->addCheckbox('dst', ' Auto-detect summer time?')
            ->setDefaultValue($userSettings->dst)
            ->setHtmlAttribute('class', 'uk-checkbox')
            ->setHtmlId('summertime');
        $form->addSubmit('save', 'Update');

        $form->onSuccess[] = [$this, 'processForm'];
        return $form;
    }

    public function processForm(Form $form)
    {
        $values = $form->getValues();
        if (is_numeric($values->timezone) && ($values->timezone <= 14 && $values->timezone >= -12)) {
            $this->onSuccess($form);
        } else {
            $this->onError($form);
        }
        $this->redirect('this');
    }
}
