<?php

namespace components\company;

use Langosh\component\BaseControl;
use model\database\entity\Company;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

/**
 * Company control - create,edit,delete
 * @author Jan Svatoš <svatosja@gmail.com>
 */
class EditControl extends BaseControl {

	const FORM_NAME = 'form';

	/** @var Company */
	private $company;

	/** @var Nette\DI\IContainer */
	private $context;

	/**
	 * @param \Nette\Application\UI\Presenter $parent
	 * @param string $name
	 * @param \model\database\entity\Company $company
	 */
	public function __construct(Presenter $parent, $name, Company $company = NULL) {
		parent::__construct($parent, $name);
		$this->company = $company;
		$this->context = $parent->context;
	}

	public function createComponentForm() {
		$form = new Form($this, self::FORM_NAME);

		$form->addText('ic', 'IČ')
				->setRequired('%label is required')
				->addRule(Form::MAX_LENGTH, 'Maximum length (%d) exceeded', 32)
				->addRule(Form::INTEGER, 'Whole number expected');

		$form->addText('name', 'Name')
				->setRequired('%label is required')
				->addRule(Form::MAX_LENGTH, 'Maximum length (%d) exceeded', 32);

		$form->addText('email', 'E-mail')
				->setRequired('%label is required')
				->addRule(Form::MAX_LENGTH, 'Maximum length (%d) exceeded', 64)
				->addRule(Form::EMAIL, '%label has wrong format');

		$form->addText('address', 'Address')
				->setRequired('%label is required')
				->addRule(Form::MAX_LENGTH, 'Maximum length (%d) exceeded', 128);

		$form->addText('employeeCount', '# of Emplyoees')
				->setRequired('%label is required')
				->addRule(Form::INTEGER, 'Whole number expected');

		$form->addSubmit('save', 'Save');
		$form->addSubmit('delete', 'Delete');

		$form->onSuccess[] = callback($this, 'formSubmit');

		return $form;
	}

	/**
	 * Form processing
	 * @param \Nette\Application\UI\Form $form
	 */
	public function formSubmit(Form $form) {
		$values = $form->getValues();

		if ($this->company instanceof Company) {
			// Update company
			$company = $this->company;

			if ($form->getComponent('delete')->isSubmittedBy()) {
				// Delete company
				try {
					$this->deleteCompany($company);
					$this->getParent()->flashSuccess('Company deleted');
					$this->getParent()->redirect('Company:list');
				} catch (\PDOException $e) {
					$this->flashError('Error while deleting : [' . get_class($e) . '] ' . $e->getMessage());
					return;
				}
			}
			$successMessage = 'Company saved';
		} else {
			// Create new company
			$company = new Company();
			$company->setCreated(new \DateTime());
			$successMessage = 'Company created';
		}
		$company
				->setIc($values->ic)
				->setName(ucfirst($values->name))
				->setEmail($values->email)
				->setAddress($values->address)
				->setEmployeeCount($values->employeeCount)
				->setUpdated(new \DateTime());

		try {
			$entityManager = $this->context->getService('entityManager');
			$entityManager->persist($company);
			$entityManager->flush();

			$this->company = $company;
			$this->flashSuccess($successMessage);
			$this->getParent()->redirect('Company:edit', array('id' => $this->company->getId()));
		} catch (\PDOException $e) {
			$this->flashError('Error while saving : [' . get_class($e) . '] ' . $e->getMessage());
		}
	}

	/**
	 * Deletes company from database
	 * @param \model\database\entity\Company $company
	 * @throws \PDOException
	 */
	private function deleteCompany(Company $company) {
		/* @var $dao \model\database\dao\CompanyDAO */
		$dao = $this->context->getService('companyDAO');
		$dao->deleteCompany($company);
	}

	private function setFormByCompany() {
		if (!($this->company instanceof Company))
			return;

		$form = $this->getComponent(self::FORM_NAME);
		$values = array(
			'ic' => $this->company->getIc(),
			'name' => $this->company->getName(),
			'email' => $this->company->getEmail(),
			'address' => $this->company->getAddress(),
			'employeeCount' => $this->company->getEmployeeCount()
		);
		$form->setValues($values, TRUE);
	}

	public function render() {
		$this->setFormByCompany();
		$this->template->company = $this->company;
		$this->template->setFile(__DIR__ . '/EditControl.latte');
		$this->template->render();
	}

}

