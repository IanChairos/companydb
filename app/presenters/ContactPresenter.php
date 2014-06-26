<?php

use components\filter\ContactPersonFilter;
use components\contactPerson\EditControl;
use Langosh\component\dataGrid\DataGrid;
use Langosh\component\dataGrid\paginator\StandardPaginator;
use model\database\entity\ContactPerson;
use model\database\datasource\DoctrineDataSource;

/**
 * ContactPresenter
 * @author Jan Svatoš <svatosja@gmail.com>
 */
class ContactPresenter extends \BasePresenter {
	
	/** @var int */
	private $companyId;
	
	/** @var ContactPerson */
	private $contactPerson;
	
	/**
	 * List all availible contacts
	 */
	public function actionList() {}
	
	/**
	 * List company contacts
	 */
	public function actionCompanyList($company) {
		$this->loadCompany($company);
	}
	
	/**
	 * Loads company and sets it in template
	 * @param int $id Company.ID
	 */
	private function loadCompany($id) {
		/* @var $dao model\database\dao\CompanyDAO */
		$dao = $this->getService('companyDAO');
		$companyEntity = $dao->getById($id);
		if( !$companyEntity ) {
			$this->flashError('Unknown company ['.$id.']');
			$this->redirect('Contact:list');
		}
		$this->companyId = (int)$id;
		$this->template->company = $companyEntity;
	}

	public function createComponentContactList() {
		/* @var $dao model\database\dao\ContactPersonDAO */
		$dao		= $this->getService('contactPersonDAO');
		$dataSource = $dao->getListDataSource();
		
		return $this->createContactList($dataSource,'contactList','/dataGrid/ContactPersonList.latte');
	}
	
	public function createComponentCompanyContactList() {
		/* @var $dao model\database\dao\ContactPersonDAO */
		$dao		= $this->getService('contactPersonDAO');
		$dataSource = $dao->getCompanyListDataSource($this->companyId);
		
		return $this->createContactList($dataSource,'companyContactList','/dataGrid/CompanyContactPersonList.latte');
	}
	
	private function createContactList(DoctrineDataSource $dataSource, $name,$templatePath) {
		$dataGrid	= new DataGrid($this, $name, $dataSource);
		$dataGrid->setTemplatePath(COMPONENTS_DIR.$templatePath);
		$filter		= new ContactPersonFilter($dataGrid, 'filter');
		$filter->setTemplatePath(COMPONENTS_DIR.'/filter/ContactPersonFilter.latte');
		$pageSizes	= array( 1=>1,2=>2,5=>5,10=>10,20=>20,50=>50,100=>100 );
		$paginator	= new StandardPaginator($dataGrid, 'paginator', $pageSizes);
		$paginator->setDisplayed(10);
		$paginator->setDefaultPageSize(10);
		
		$presenter = $this;
		$dataGrid->addAction('edit', 'Edit', function($item) use ($presenter,$dataSource) {
			$presenter->redirect('Contact:edit',array('id'=>$dataSource->identify($item)));
		},array('icon'=>'icon-wrench'));
		
		$deleteActionSetup = array(
			'icon'=>'icon-white icon-remove',
			'class'=>'btn btn-small btn-danger modal-confirm',
			'attributes' => array(
				'data-modal-title'=>'Are you sure?',
				'data-modal-message'=>'Do you really want to delete this contact?'
			)
		);
		$dataGrid->addAction('remove', 'Delete', function($item) use ($presenter) {
			$presenter->deleteContact($item);
		},$deleteActionSetup);
		
		return $dataGrid;
	}
	
	/**
	 * @param \model\database\entity\ContactPerson $contact
	 */
	public function deleteContact(ContactPerson $contact) {
		/* @var $dao \model\database\dao\ContactPersonDAO */
		$dao = $this->getService('contactPersonDAO');
		try{
			$dao->deleteContact($contact);
			$this->flashSuccess('Contact person deleted');
		}catch( PDOException $e ) {
			$this->flashError('Error while deleting: '.$e->getMessage());
		}
		$this->redirect('this');
	}
	
	public function createComponentEditControl() {
		$control = new EditControl($this,'editControl',$this->contactPerson,$this->companyId);
		return $control;
	}
	
	/**
	 * Create new contact
	 *  - optionally for specific company
	 * @param int $company Company.ID
	 */
	public function actionNew($company) {
		if( $company )
			$this->loadCompany($company);
		$this->setView('edit');
	}
	
	/**
	 * Edit contact
	 * @param int $id ContactPerson.ID
	 */
	public function actionEdit($id) {
		$this->loadContact($id);
	}
	
	/**
	 * Loads contact and sets it in template
	 * @param int $id
	 * @throws \Nette\Application\BadRequestException
	 */
	public function loadContact($id) {
		if( $this->contactPerson )
			return;
		
		/* @var $dao model\database\dao\ContactPersonDAO */
		$dao = $this->getService('contactPersonDAO');
		$contact = $dao->getById($id);
		
		// @TODO jina reakce misto 404
		
		if( !$contact )
			throw new \Nette\Application\BadRequestException('Contact person ['.$id.'] not found');
		
		$this->template->contact = $contact;
		$this->contactPerson = $contact;
	}
	
}

