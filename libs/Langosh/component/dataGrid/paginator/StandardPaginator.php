<?php

namespace Langosh\component\dataGrid\paginator;

use Langosh\component\BaseControl;
use Nette\Application\UI\Form;
use Langosh\component\dataGrid\DataGrid;
use model\database\dataSource\IDataSource;
use Nette\Utils\Paginator;

/**
 * StandardPaginator
 *  - common visual pagination control for DataGrid
 * @@author Jan Svatoš <svatosja@gmail.com>
 */
class StandardPaginator extends BaseControl {
	
	const FORM_NAME = 'form';
	
	/** @var int @persistent */
	public $page = 1;
	
	/** @var int @persistent */
	public $pageSize;
	
	/** @var array */
	private $pageSizes = array();
	
	/** @var int */
	private $defaultPageSize = 1;
	
	/** @var Paginator */
	private $paginator;
	
	/** @var int */
	private $displayed = 5;
	
	public function __construct(DataGrid $dataGrid, $name, array $pageSizes) {
		parent::__construct($dataGrid,$name);
		$this->pageSizes = $pageSizes;
		$dataGrid->setPaginator($this);
	}
	
	/**
	 * @return DataGrid
	 */
	private function getDataGrid() {
		return $this->getParent();
	}
	
	
	public function setDefaultPageSize($defaultPageSize) {
		$this->defaultPageSize = (int)$defaultPageSize;
	}

	/**
	 * Sets max number of displayed pages
	 * @param int $displayed
	 */
	public function setDisplayed($displayed) {
		$this->displayed = $displayed > 0 ? (int)$displayed : 1;
	}

	/**
	 * Returns current page number
	 * @return int
	 */
	public function getPage() {
		return $this->page;
	}
	
	/**
	 * Returns current page size
	 * @return int
	 */
	private function getPageSize() {
		return $this->pageSize ?: $this->defaultPageSize;
	}
	
	public function createComponentForm() {
		$form = new Form($this,self::FORM_NAME);
		$form->addSelect('pageSize', 'Page size:', $this->pageSizes);
		$form->addSubmit('submit','apply');
		$form->onSuccess[] = callback( $this,'formSubmit' );
		
		return $form;
	}
	
	/**
	 * Paginator form processing
	 * - changes page size and resets page to 1
	 * @param \Nette\Application\UI\Form $form
	 */
	public function formSubmit( Form $form ) {
		$values = $form->getValues();
		if( $values->pageSize ) {
			$this->pageSize = $values->pageSize;
			$this->page = 1;
		}
		$this->redirect('this');
	}
	
	/**
	 * @return Paginator 
	 */
	public function getPaginator() {
		if( $this->paginator === NULL ) {
			$paginator = new Paginator();
			$paginator->setBase(1);
			$paginator->setItemCount( $this->getDataGrid()->getCount() );
			$paginator->setItemsPerPage( $this->getPageSize() );
			$paginator->setPage($this->page);
			$this->paginator = $paginator;
		}
		
		return $this->paginator;
	}
	
	/**
	 * Applies pagination on datasource
	 * @param \model\database\dataSource\IDataSource $dataSource
	 */
	public function apply(IDataSource $dataSource) {
		$offset = $this->getPaginator()->getOffset();
		$limit = $this->getPaginator()->getItemsPerPage();
		$dataSource->limit($limit, $offset);
	}
	
	/**
	 * Returns array of page numbers to display
	 * @return array
	 */
	private function getPages() {
		$pages = array();
		$displayed = $this->displayed-1;
		
		if($this->page>ceil($displayed/2)) {
			$end = min($this->getLastPage(),$this->page+ceil($displayed/2));
			$start = max($this->getPaginator()->getBase(), ceil($end-$displayed));
		}else{
			$start = $this->getPaginator()->getBase();
			$end = min($this->getLastPage(),$displayed+1);
		}
		
		for($i=$start;$i<=$end;$i++){
			$pages[$i] = $i;
		}
		
		// only current page -> hide
		if( count($pages)==1 )
			return array();
		
		return $pages;
	}
	
	/**
	 * Returns last page
	 * @return int
	 */
	public function getLastPage() {
		return $this->getPaginator()->getLastPage();
	}
	
	/**
	 * Signal for changing page
	 * @param int $page
	 */
	public function handlePaginate($page) {
		$this->page = $page;
	}
	
	public function render() {
		// page overflow restriction
		$this->page = min($this->getLastPage(),$this->page);
		$this->template->pages = $this->getPages();
		$this->setFormByParams();
		$templatePath = $this->getTemplatePath() ?: __DIR__. '/StandardPaginatorDefault.latte';
		$this->template->setFile( $templatePath );
		$this->template->render();
	}
	
	private function setFormByParams() {
		$form = $this->getComponent(self::FORM_NAME);
		$form->setValues(array('pageSize'=>$this->getPageSize()),TRUE);
	}
	
}
