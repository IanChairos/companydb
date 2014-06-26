<?php

namespace model\database\datasource;

use DoctrineExtensions\Paginate\Paginate;
use Doctrine\ORM\QueryBuilder;
use Langosh\component\dataGrid\filter\condition\doctrine\Condition;
use Nette\Callback;

/**
 * DoctrineDataSource - utilizes dotrine query builder, access to DB
 * @author lang
 */
class DoctrineDataSource implements IDataSource {

	/** @var int Item count */
	private $count = NULL;

	/** @var QueryBuilder */
	private $queryBuilder;
	
	/** @var callable Callback to uniquely identify item, that came from this DS */
	private $identifyCallback;
	
	/** @var string Root entity alias */
	private $alias;
	
	public function __construct(QueryBuilder $queryBuilder, $alias, Callback $identifyCallback) {
		$this->queryBuilder = $queryBuilder;
		$this->identifyCallback = $identifyCallback;
		$this->alias = $alias;
	}

	public function getData() {
		$result = $this->queryBuilder->getQuery()->getResult();
		return $result;
	}

	public function getCount() {
		if ($this->count === null) {
			$query = $this->queryBuilder->getQuery();
			$this->count = Paginate::getTotalQueryResults($query);
		}
		return $this->count;
	}

	public function getTotalDataCount() {
		return $this->getDataCount();
	}

	public function filter(array $conditions) {
		foreach( $conditions as $condition ) {
			if( !($condition instanceof Condition) ) {
				trigger_error('Expected instance of [\Langosh\component\dataGrid\filter\condition\doctrine\Condition]', E_USER_WARNING);
				continue;
			}
			$condition->apply($this->queryBuilder,$this->alias);
		}
	}

	public function limit($limit, $offset = NULL) {
		if ($limit) {
			$this->queryBuilder->setMaxResults($limit);
			if ($offset)
				$this->queryBuilder->setFirstResult($offset);
		}
	}
	
	public function identify( $item ) {
		return $this->identifyCallback->invokeArgs(array($item));
	}

}

