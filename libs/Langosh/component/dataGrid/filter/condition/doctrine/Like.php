<?php

namespace Langosh\component\dataGrid\filter\condition\doctrine;

use Doctrine\ORM\QueryBuilder;

/**
 * Like - doctrine filter condition
 * @author Jan Svatoš <svatosja@gmail.com>
 */
class Like extends Condition {

	public function apply(QueryBuilder $queryBuilder, $alias) {
		if (!$this->getValue())
			return;
		$key = $this->getUniqueQueryKey();
		$whereExpr = $queryBuilder->expr()->like($alias . '.' . $this->getQueryIdentifier(), $key);
		$queryBuilder->andWhere($whereExpr)->setParameter($key, $this->getQueryValue());
	}

	protected function getQueryValue() {
		return $this->getValue();
	}

}

