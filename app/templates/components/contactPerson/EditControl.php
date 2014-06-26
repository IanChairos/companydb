<?php

namespace components\contactPerson;

use Langosh\component\BaseControl;
use model\database\entity\ContactPerson;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\InvalidStateException;

/**
 * Contact person control - create,edit,delete
 * @author Jan Svatoš <svatosja@gmail.com>
 */
class EditControl extends BaseControl {

	const FORM_NAME = 'form';

	/** @var ContactPerson */
	private $contactPerson;

	/** @var Nette\DI\IContainer */
	private $context;

	/** @var int */
	private $companyId;

	/**
	 * @param \Nette\Application\UI\Presenter $parent
	 * @param string $name
	 * @param \model\database\entity\ContactPerson $contactPerson
	 * @param int $companyId
	 */
	public function __construct(Presenter $parent, $name, ContactPerson $contactPerson = NULL, $companyId = NULL) {
		parent::__construct($parent, $name);
		$this->contactPerson = $contactPerson;
		$this->context = $parent->context;
		$this->companyId = (int) $companyId;
	}

	public function createComponentForm() {
		$form = new Form($this, self::FORM_NAME);

		$form->addText('firstname', 'First name')
				->setRequired('%label is required')
				->addRule(Form::MAX_LENGTH, 'Maximum length (%d) exceeded', 32);

		$form->addText('lastname', 'Last name')
				->setRequired('%label is required')
				->addRule(Form::MAX_LENGTH, 'Maximum length (%d) exceeded', 32);

		if ($this->companyId) {
			// we are adding contact to certain company, lock this company in form through disabled selectbox and hidden field
			$form->addSelect('company_select', 'Company', $this->getCompanyList())->setValue($this->companyId)->setDisabled();
			$form->addHidden('company', $this->companyId);
		} else {
			$form->addSelect('company', 'Company', $this->getCompanyList());
		}

		$form->addText('email', 'E-mail')
				->setRequired('%label is required')
				->addRule(Form::EMAIL, '%label has wrong format');

		$form->addText('phone', 'Phone')
				->setRequired('%label is required')
				->addRule(Form::PATTERN, 'Use [+42x ]xxx xxx xxx', '^(\+42[\d]\s)?(\d{3,3}\s){2,2}(\d{3,3})$');

		$form->addText('job_name', 'Job name')
				->setRequired('%label is required')
				->addRule(Form::MAX_LENGTH, 'Maximum length (%d) exceeded', 32);

		$form->addSubmit('save', 'Save');
		$form->addSubmit('delete', 'Delete');

		$form->onSuccess[] = callback($this, 'formSubmit');

		return $form;
	}

	/**
	 * Returns company list for selectbox
	 * @return array
	 */
	private function getCompanyList() {
		/* @var $dao \model\database\dao\CompanyDAO */
		$dao = $this->context->getService('companyDAO');
		$list = $dao->getSelectboxList();
		return $list;
	}

	/**
	 * Form processing
	 * @param \Nette\Application\UI\Form $form
	 */
	public function formSubmit(Form $form) {
		$values = $form->getValues();

		if ($this->contactPerson instanceof ContactPerson) {
			// Update contact
			$contact = $this->contactPerson;

			if ($form->getComponent('delete')->isSubmittedBy()) {
				// Delete contact
				try {
					$companyId = $contact->getCompany()->getId();
					$this->deleteContact($contact);
					$this->getParent()->flashSuccess('Contact deleted');
					$this->getParent()->redirect('Contact:companyList', array('company' => $companyId));
				} catch (\PDOException $e) {
					$this->flashError('Error while deleting : ' . $e->getMessage());
					return;
				}
			}
			$successMessage = 'Contact saved';
		} else {
			// Create new contact
			$contact = new ContactPerson();
			$contact->setCreated(new \DateTime());
			$successMessage = 'Contact created';
		}

		$contact
				->setFirstname(ucfirst($values->firstname))
				->setLastname(ucfirst($values->lastname))
				->setEmail($values->email)
				->setPhone($values->phone)
				->setJobName(ucfirst($values->job_name))
				->setUpdated(new \DateTime());

		try {
			$company = $this->getCompany($values->company);
			$contact->setCompany($company);

			$entityManager = $this->context->getService('entityManager');
			$entityManager->persist($contact);
			$entityManager->flush();

			$this->flashSuccess($successMessage);
			$this->getParent()->redirect('Contact:edit', array('id' => $contact->getId()));
		} catch (InvalidStateException $e) {
			$this->flashError('Error while saving : [' . get_class($e) . '] ' . $e->getMessage());
		} catch (\PDOException $e) {
			$this->flashError('Error while saving : [' . get_class($e) . '] ' . $e->getMessage());
		}
	}

	/**
	 * Deletes contact from database
	 * @param \model\database\entity\ContactPerson $contact
	 * @throws \PDOException
	 */
	private function deleteContact(ContactPerson $contact) {
		/* @var $dao \model\database\dao\ContactPersonDAO */
		$dao = $this->context->getService('contactPersonDAO');
		$dao->deleteContact($contact);
	}

	/**
	 * Fetches company from database
	 * @param int $id
	 * @return \model\database\entity\Company
	 * @throws InvalidStateException
	 */
	private function getCompany($id) {
		/* @var $dao \model\database\dao\CompanyDAO */
		$dao = $this->context->getService('companyDAO');
		$company = $dao->getById($id);
		if (!$company)
			throw new InvalidStateException('Unknown company [' . $id . ']');

		return $company;
	}

	private function setFormByContact() {
		if (!($this->contactPerson instanceof ContactPerson)) {
			return;
		}

		$form = $this->getComponent(self::FORM_NAME);
		$values = array(
			'firstname' => $this->contactPerson->getFirstname(),
			'lastname' => $this->contactPerson->getLastname(),
			'company' => $this->contactPerson->getCompany()->getId(),
			'email' => $this->contactPerson->getEmail(),
			'phone' => $this->contactPerson->getPhone(),
			'job_name' => $this->contactPerson->getJobName(),
		);
		$form->setValues($values, TRUE);
	}

	public function render() {
		$this->setFormByContact();
		$this->template->contact = $this->contactPerson;
		$this->template->setFile(__DIR__ . '/EditControl.latte');
		$this->template->render();
	}

}

