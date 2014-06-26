<?php

namespace Langosh\component\dataGrid\filter\condition\doctrine;

use Doctrine\ORM\QueryBuilder;

/**
 * Doctrine filter condition
 * @author Jan Svatoš <svatosja@gmail.com>
 */
abstract class Condition {
	
	/** @var string */
	private $queryIdentifier;
	
	/** @var mixed */
	private $value;
	
	public function __construct($queryIdentifier,$value) {
		$this->queryIdentifier = $queryIdentifier;
		$this->value = $value;
	}
	
	/**
	 * @return string
	 */
	public function getQueryIdentifier() {
		return $this->queryIdentifier;
	}
	
	/**
	 * @return mixed
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * @param QueryBuilder $queryBuilder
	 * @param string alias
	 */
	abstract public function apply(QueryBuilder $queryBuilder,$alias);
	
	protected function getUniqueQueryKey() {
		return ':'.uniqid('QK');
	}
	
}

