<?php

namespace Langosh\component\dataGrid\filter;

use Langosh\component\BaseControl;
use Nette\Application\UI\Form;
use model\database\dataSource\IDataSource;
use Langosh\component\dataGrid\DataGrid;

/**
 * Filter for DataGrid
 * @author Jan Svatoš <svatosja@gmail.com>
 */
abstract class Filter extends BaseControl {

	const FORM_NAME = 'form';

	/** @var bool IFF true, this filter has already been applied to a IDataSource */
	private $applied = FALSE;
	
	/**
	 * @param \Langosh\component\dataGrid\DataGrid $dataGrid
	 * @param string $name
	 */
	public function __construct(DataGrid $dataGrid, $name) {
		parent::__construct($dataGrid, $name);
		$dataGrid->setFilter($this);
	}

	/**
	 * Applies filtering to IDataSource
	 * @param \model\database\dataSource\IDataSource $dataSource
	 * @param bool $forced IFF true, forces re-apply
	 */
	public function apply(IDataSource $dataSource,$forced = FALSE){
		if( $this->applied && !$forced )
			return;
		$this->applyFilter($dataSource);
		$this->applied = TRUE;
	}
	
	abstract protected function applyFilter(IDataSource $dataSource);
	
	abstract public function createComponentForm();

	/**
	 * @return Form
	 */
	public function getForm() {
		return $this->getComponent(self::FORM_NAME);
	}
	
	public function render() {
		$templatePath = $this->getTemplatePath() ?: __DIR__ . '/FilterFormDefault.latte';
		$this->template->setFile($templatePath);
		$this->template->render();
	}

}

