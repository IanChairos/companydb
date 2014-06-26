<?php
namespace model\database\dao;

use Doctrine\ORM\EntityManager;

/**
 * BaseDAO
 * @author Jan Svatoš <svatosja@gmail.com>
 */
abstract class BaseDAO {
	
	private $entityManager;
	
	public function __construct(EntityManager $entityManager){
		$this->entityManager = $entityManager;
	}
	
	/**
	 *
	 * @return EntityManager
	 */
	protected function getEntityManager() {
		return $this->entityManager;
	}

}

