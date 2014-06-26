<?php

namespace Langosh\component;

use Nette\Application\UI\Control;

/**
 * Ancestor for all controls
 * @author lang
 */
class BaseControl extends Control {

	private $templatePath;

	public function getTemplatePath() {
		return $this->templatePath;
	}

	public function setTemplatePath($templatePath) {
		$this->templatePath = $templatePath;
	}

	public function flashError($message) {
		return parent::flashMessage($message, 'error');
	}

	public function flashSuccess($message) {
		return parent::flashMessage($message, 'success');
	}

}

