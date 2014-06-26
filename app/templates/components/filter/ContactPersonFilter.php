<?php

namespace components\filter;

use Langosh\component\dataGrid\filter\Filter;
use Langosh\component\dataGrid\filter\condition\doctrine\LikeWrapped;
use model\database\dataSource\IDataSource;
use Nette\Application\UI\Form;

/**
 * ContactPersonFilter
 * @author Jan Svatoš <svatosja@gmail.com>
 */
class ContactPersonFilter extends Filter {
	
	/** @var array @persistent */
	public $conditions = array();
	
	/**
	 * @param \model\database\dataSource\IDataSource $dataSource
	 */
	protected function applyFilter(IDataSource $dataSource) {
		$conditions = $this->buildConditions();
		$dataSource->filter($conditions);
	}
	
	private function buildConditions() {
		$conditions = array();
		foreach($this->conditions as $key => $value ) {
			if( $key == 'lastname') {
				$conditions[] = new LikeWrapped('lastname', $value);
			}
		}
		return $conditions;
	}

	public function createComponentForm() {
		$form = new Form($this, self::FORM_NAME);
		$form->addText('lastname','Last name:');
		$form->addSubmit('submit', 'Apply');
		$form->addSubmit('reset', 'Reset');
		$form->onSuccess[] = callback( $this, 'formSubmit' );
		
		return $form;
	}
	
	/**
	 * Form processing
	 * @param \Nette\Application\UI\Form $form
	 */
	public function formSubmit(Form $form) {
		$values = $form->getValues();
		
		if( $this->getForm()->getComponent('reset')->isSubmittedBy() ) {
			$this->reset();
			$this->redirect('this');
		}
		
		if( $values->lastname ) {
			$this->conditions['lastname'] = $values->lastname;
		}else{
			unset($this->conditions['lastname']);
		}
		$this->redirect('this');
	}
	
	/**
	 * Resets filter conditions and form values
	 */
	private function reset() {
		$this->conditions = array();
		$this->setFormByConditions();
	}
	
	private function setFormByConditions(){
		$values = $this->conditions;
		$this->getForm()->setValues($values,TRUE);
	}
	
	public function render() {
		$this->template->conditions = $this->conditions;
		$this->setFormByConditions();
		parent::render();
	}
	
}

