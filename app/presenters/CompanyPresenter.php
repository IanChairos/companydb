<?php

use components\company\EditControl;
use components\filter\CompanyFilter;
use Langosh\component\dataGrid\DataGrid;
use Langosh\component\dataGrid\paginator\StandardPaginator;
use Nette\Application\BadRequestException;
use model\database\entity\Company;

/**
 * CompanyPresenter
 * @persistent companyList
 * @author Jan Svatoš <svatosja@gmail.com>
 */
class CompanyPresenter extends BasePresenter {
	
	/** @var Company */
	private $company;
	
	public function actionList() {
	}
	
	public function createComponentCompanyList() {
		/* @var $dao \model\database\dao\CompanyDAO */
		$dao		= $this->getService('companyDAO');
		/* @var $dataSource \model\database\dataSource\DoctrineDataSource */
		$dataSource = $dao->getDataSource();
		$dataGrid	= new DataGrid($this, 'companyList', $dataSource);
		$dataGrid->setTemplatePath(COMPONENTS_DIR.'/dataGrid/CompanyList.latte');
		$filter		= new CompanyFilter($dataGrid, 'filter');
		$filter->setTemplatePath(COMPONENTS_DIR.'/filter/CompanyFilter.latte');
		$pageSizes	= array( 1=>1,2=>2,5=>5,10=>10,20=>20 );
		$paginator	= new StandardPaginator($dataGrid, 'paginator', $pageSizes);
		$paginator->setDisplayed(10);
		$paginator->setDefaultPageSize(10);
		
		$presenter = $this;
		$dataGrid->addAction('contacts', 'Contacts', function($item) use ($presenter,$dataSource) {
			$presenter->redirect('Contact:companyList',array('company'=>$dataSource->identify($item)));
		},array('icon'=>'icon-user'));
		
		$dataGrid->addAction('edit', 'Edit', function($item) use ($presenter,$dataSource) {
			$presenter->redirect('Company:edit',array('id'=>$dataSource->identify($item)));
		},array('icon'=>'icon-wrench'));
		
		$deleteActionSetup = array(
			'icon'=>'icon-white icon-remove',
			'class'=>'btn btn-small btn-danger modal-confirm',
			'attributes' => array(
				'data-modal-message'=>'By deleting a company you will also delete all contacts assigned to this company. Do you want to proceed?'
			)
		);
		$dataGrid->addAction('remove', 'Delete', function($item) use ($presenter) {
			$presenter->deleteCompany($item);
		},$deleteActionSetup);
		
		return $dataGrid;
	}
	
	/**
	 * @param Company $company
	 */
	public function deleteCompany(Company $company) {
		/* @var $dao \model\database\dao\CompanyDAO */
		$dao = $this->getService('companyDAO');
		try{
			$dao->deleteCompany($company);
			$this->flashSuccess('Company deleted');
		}catch( PDOException $e ) {
			$this->flashError('Error while deleting: '.$e->getMessage());
		}
		$this->redirect('this');
	}

	public function createComponentEditControl() {
		$control = new EditControl($this,'editControl',$this->company);
		return $control;
	}
	
	public function actionNew() {
		$this->setView('edit');
	}
	
	public function actionEdit($id) {
		$this->loadCompany($id);
	}
	
	/**
	 * Loads company and sets it into template
	 * @param int $id
	 * @throws BadRequestException
	 */
	public function loadCompany($id) {
		if( $this->company )
			return;
		
		/* @var $dao \model\database\dao\CompanyDAO */
		$dao = $this->getService('companyDAO');
		$company = $dao->getById($id);
		
		if( !$company ) {
			$this->flashError('Company ['.$id.'] not found');
			$this->redirect('Company:list');
		}
		
		$this->template->company = $company;
		$this->company = $company;
	}
	
}

