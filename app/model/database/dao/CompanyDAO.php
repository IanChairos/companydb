<?php

namespace model\database\dao;

use Doctrine\ORM\QueryBuilder;
use model\database\datasource\DoctrineDataSource;
use model\database\entity\Company;

/**
 * CompanyDAO - Company manipulation|database access
 * @author lang
 */
class CompanyDAO extends BaseDAO {
	
	const ENTITY_NAME = 'model\database\entity\Company';
	
	/**
	 * @return DoctrineDataSource
	 */
	public function getDataSource() {
		$qb = new QueryBuilder($this->getEntityManager());
		$qb->select('c')->from(self::ENTITY_NAME, 'c');
		$ds = new DoctrineDataSource($qb,'c',callback($this,'identify'));
		
		return $ds;
	}
	
	public function getById($id) {
		$id = (int)$id;
		$dql = 'SELECT c FROM model\database\entity\Company c WHERE c.id = :id';
		return $this->getEntityManager()->createQuery($dql)->setParameter(':id', $id)->getOneOrNullResult();
	}
	
	/**
	 * @param \model\database\entity\Company $company
	 * @throws \PDOException
	 */
	public function deleteCompany(Company $company) {
		$this->getEntityManager()->remove($company);
		$this->getEntityManager()->flush();
	}
	
	/**
	 * @return array
	 */
	public function getSelectboxList() {
		$dql = 'SELECT c FROM model\database\entity\Company c ORDER BY c.name';
		$companies = $this->getEntityManager()->createQuery($dql)->getResult();
		$list = array();
		foreach( $companies as $company ) {
			$list[$company->getId()] = ucfirst($company->getName()).' ('.$company->getIc().')';
		}
		return $list;
	}
	
	/**
	 * @see \model\database\dataSource\IDataSource::identify()
	 * @param \model\database\entity\Company $item
	 * @return int
	 * @throws \InvalidArgumentException
	 */
	public function identify( $item ) {
		if( !($item instanceof Company) )
			throw \InvalidArgumentException('Expected instance of type ['.self::ENTITY_NAME.'] ');
		return $item->getId();
	}
	
	/**
	 * Whether the table `company` exists in DB
	 * @return boolean
	 */
	public function tableExists() {
		$sql = "SELECT * FROM `company`";
		$statement = $this->getEntityManager()->getConnection()->prepare($sql);
		try{
			$statement->execute();
			return TRUE;
		}catch( \PDOException $e ){
			return FALSE;
		}
	}
	
}

