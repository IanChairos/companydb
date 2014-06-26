<?php

namespace Langosh\component\dataGrid\filter\condition\doctrine;

use Doctrine\ORM\QueryBuilder;

/**
 * In - doctrine filter condition
 * @author Jan Svatoš <svatosja@gmail.com>
 */
class In extends Condition {

	public function __construct($queryIdentifier, array $values) {
		parent::__construct($queryIdentifier, $values);
	}

	public function apply(QueryBuilder $queryBuilder, $alias) {
		if (!$this->getValue())
			return;
		$whereExpr = $queryBuilder->expr()->in($alias . '.' . $this->getQueryIdentifier(), $this->getValue());
		$queryBuilder->andWhere($whereExpr);
	}

}

