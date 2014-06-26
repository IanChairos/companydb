<?php

namespace Langosh\component\dataGrid\filter\condition\doctrine;

use Doctrine\ORM\QueryBuilder;

/**
 * Equals - doctrine filter condition
 * @author Jan Svatoš <svatosja@gmail.com>
 */
class Equals extends Condition {

	public function apply(QueryBuilder $queryBuilder, $alias) {
		if (!$this->getValue())
			return;
		$key = $this->getUniqueQueryKey();
		$whereExpr = $queryBuilder->expr()->eq($alias . '.' . $this->getQueryIdentifier(), $key);
		$queryBuilder->andWhere($whereExpr)->setParameter($key, $this->getValue());
	}

}

