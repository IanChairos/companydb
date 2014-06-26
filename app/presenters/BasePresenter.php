<?php

/**
 * Ancestor for all application presenters
 * @author Jan Svatoš <svatosja@gmail.com>
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter {
	
	public function flashError($message) {
		return parent::flashMessage($message, 'error');
	}
	
	public function flashSuccess($message) {
		return parent::flashMessage($message, 'success');
	}
	
}
