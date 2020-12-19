<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\Model,
	Nette,
	Nette\Security,
	Nette\Application\UI\Form,
	Nette\Mail\Message,
	Nette\Utils\Validators,
	Nette\Utils\DateTime;

final class UserPresenter extends BasePresenter
{
	/** @var Model\UserRepository */
	private $userModel;
	private $authenticator;

	/**
	 * @var \Nette\Mail\IMailer
	 * @inject
	 */
	public $mailer;

	public function __construct(Model\UserRepository $users, Model\Authenticator $authenticator)
	{
		$this->userModel = $users;
		$this->authenticator = $authenticator;
	}

    protected function startup()
    {
        parent::startup();
    }


	public function renderDefault()
	{
		$this->template->data = $this->userModel->findAll();
		$this->template->roles = $this->userModel->roles;
	}

	public function renderEdit(int $id = null)
	{
		$form = $this['userForm'];
		$this->template->id = $id;
		if ($id) {
			$user = $this->userModel->findAll()->get($id);
			if (!$user) {
				$this->error('Entry not found!');
			}
			$form->setDefaults($user);
		}
	}

	/*********************** ACTIONS ***********************/

	public function actionDelete(int $id)
	{
		$user = $this->userModel->findAll()->get($id);
		if (!$user) {
			$this->flashMessage('Entry not found!');
		}else{
			$this->flashMessage('Entry deleted!');
			$this->userModel->findAll()->get($id)->delete();
		}
		$this->redirect('default');
	}

	/*********************** COMPONENT FACTORIES ***********************/

	/**
	 * Edit form factory.
	 * @return Form
	 */
	protected function createComponentUserForm()
	{
		$form = new Form;

		$renderer = $form->getRenderer();
		$renderer->wrappers['controls']['container'] = 'div class="container"';
		$renderer->wrappers['pair']['container'] = 'div class="input"';
		$renderer->wrappers['label']['container'] = null;
		$renderer->wrappers['control']['container'] = null;

		//přihlašovací údaje
		$form->addGroup('Login information')->setOption('container', 'fieldset');
        $form->addText('email', 'E-mail')
                ->setRequired('Please enter an e-mail address.');
        $form->addPassword('password', 'Password');
        $form->addPassword('passwordVerify', 'Password again')
			    ->addRule(Form::EQUAL, 'Passwords do not match', $form['password'])
                ->setRequired(true);
        $form->addSelect('group', 'Group', array(
					            'u' => 'User',
					            'a' => 'Admin',
					        )
		        		);

        // osobní údaje
        $form->addGroup('Personal information (optional)')->setOption('container', 'fieldset');
        $form->addText('firstname', 'Name');
        $form->addText('lastname', 'Surname');

		$form->addSubmit('save', 'Submit');

        $form->onSuccess[] = [$this, 'userFormSucceeded'];

		return $form;
	}


	public function userFormSucceeded(Form $form, $values)
	{
		$id = (int) $this->getParameter('id');

		if ($id) {
			// edit user
            if($values->password!=$values->passwordVerify){
                $form->addError('Passwords do not match.');
            }
            else if($values->password=='' && $values->passwordVerify==''){
                unset($values->password);
                unset($values->passwordVerify);
                $this->userModel->findAll()->get($id)->update($values);
                $this->flashMessage('Update successful.');
            }
            else if($values->password==$values->passwordVerify){
                $values->password = Security\Passwords::hash($values->password);
                unset($values->passwordVerify);

                $this->userModel->findAll()->get($id)->update($values);
                $this->flashMessage('Update successful.');
            }

			$this->redirect('edit', $id);
		} else {
			//add user
			if($values->password!=$values->passwordVerify){
				$form->addError('Passwords do not match.');
			}else{
				$values->password = Security\Passwords::hash($values->password);
				unset($values->passwordVerify);
				$values->registration = new DateTime();

				$this->userModel->findAll()->insert($values);
				$this->flashMessage('Record successfully crated.');
			}

			$this->redirect('default');
		}
	}

}
