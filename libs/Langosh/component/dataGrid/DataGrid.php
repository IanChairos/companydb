<?php

namespace Langosh\component\dataGrid;

use Langosh\component\BaseControl;
use Langosh\component\dataGrid\filter\Filter;
use Nette\Application\UI\PresenterComponent;
use model\database\dataSource\IDataSource;
use Langosh\component\dataGrid\paginator\StandardPaginator;

/**
 * DataGrid
 *  - component for displaying data loaded thru IDataSource
 *  - supports paginator
 *  - supports filter
 * @author Jan Svatoš <svatosja@gmail.com>
 */
class DataGrid extends BaseControl {

	/** @var IDataSource */
	protected $dataSource;

	/** @var int Total count of items */
	protected $count;

	/** @var array */
	protected $data;

	/** @var StandardPaginator */
	protected $paginator;

	/** @var FilterForm */
	protected $filter;

	/** @var array */
	private $actions = array();

	/**
	 * @param \Nette\Application\UI\PresenterComponent $parent
	 * @param string $name
	 * @param \model\database\dataSource\IDataSource $dataSource
	 */
	public function __construct(PresenterComponent $parent, $name, IDataSource $dataSource) {
		parent::__construct($parent, $name);
		$this->dataSource = $dataSource;
	}

	/**
	 * @param \Langosh\component\dataGrid\paginator\StandardPaginator $paginator
	 * @return \Langosh\component\dataGrid\DataGrid
	 */
	public function setPaginator(StandardPaginator $paginator = NULL) {
		$this->paginator = $paginator;
		return $this;
	}

	/**
	 * @param \Langosh\component\dataGrid\filter\Filter $filter
	 * @return \Langosh\component\dataGrid\DataGrid
	 */
	public function setFilter(Filter $filter = NULL) {
		$this->filter = $filter;
		return $this;
	}

	/**
	 * Adds an item action
	 * @param string $name Action name
	 * @param string $label Display name
	 * @param callable $callback
	 * @param array $options Optional options
	 */
	public function addAction($name, $label, $callback, array $options = array()) {
		$setup = array('name' => $name, 'label' => $label, 'callback' => $callback);
		$this->actions[$name] = array_merge($setup, $options);
	}

	private function getActionCallback($name) {
		if (!isset($this->actions[$name]))
			return NULL;

		return $this->actions[$name]['callback'];
	}

	/**
	 * Returns array of possible item actions
	 * @return array
	 */
	private function getActions() {
		return $this->actions;
	}

	private function getItemById($id) {
		foreach ($this->getData() as $item) {
			if ($this->dataSource->identify($item) == $id)
				return $item;
		}
		return NULL;
	}

	/**
	 * Signal for triggering item action
	 * @param string $action Action name
	 * @param int $id Target item ID
	 * @throws \Nette\Application\AbortException
	 */
	public function handleAction($action, $id) {
		$item = $this->getItemById($id);
		if (!$item) {
			$this->flashError('Cannot perform [' . $action . '] action, record [' . $id . '] not found');
			$this->redirect('this');
		}
		$action = $this->getActionCallback($action);
		if (is_callable($action)) {
			$action($item);
		}
	}

	/**
	 * Returns total count of data.
	 * @return int
	 */
	public function getCount() {
		if ($this->count === NULL) {
			if ($this->filter) {
				$this->filter->apply($this->dataSource);
			}
			$this->count = $this->dataSource->getCount();
		}
		return $this->count;
	}

	/**
	 * Fetches data.
	 * @return array
	 */
	public function getData() {
		if ($this->data === NULL) {
			if ($this->filter)
				$this->filter->apply($this->dataSource);

			if ($this->paginator)
				$this->paginator->apply($this->dataSource);

			$this->data = $this->dataSource->getData();
		}

		return $this->data;
	}

	public function render() {
		$templatePath = $this->getTemplatePath() ? : __DIR__ . '/DataGridDefault.latte';
		$this->template->setFile($templatePath);
		$this->template->data = $this->getData();
		$this->template->actions = $this->getActions();
		$this->template->render();
	}

	/**
	 * Renders assigned filter
	 */
	public function renderFilter() {
		if (!$this->filter)
			return;
		return $this->filter->render();
	}

	/**
	 * Renders assigned paginator
	 */
	public function renderPaginator() {
		if (!$this->paginator)
			return;
		return $this->paginator->render();
	}

	/**
	 * Renders an action
	 *  - used for quick default render
	 * @param int $itemId
	 * @param array actionSetup
	 */
	public function renderAction($itemId, array $actionSetup) {
		$el = \Nette\Utils\Html::el('a');
		$attributes = isset($actionSetup['attributes']) ? $actionSetup['attributes'] : array();
		$attributes['class'] = isset($actionSetup['class']) ? $actionSetup['class'] : 'btn btn-small';
		$attributes['href'] = $this->link('action!', array('action' => $actionSetup['name'], 'id' => $itemId));
		$inner = $actionSetup['label'];
		$el->addAttributes($attributes);

		if (isset($actionSetup['icon']) && $actionSetup['icon']) {
			$icon = \Nette\Utils\Html::el('i');
			$icon->addAttributes(array('class' => $actionSetup['icon']));
			$inner = $icon . ' ' . $inner;
		}
		$el->add($inner);
		echo $el;
	}

}