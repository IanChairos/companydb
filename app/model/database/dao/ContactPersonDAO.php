<?php
namespace model\database\dao;

use Doctrine\ORM\QueryBuilder;
use model\database\datasource\DoctrineDataSource;
use model\database\entity\ContactPerson;
use model\database\entity\Company;

/**
 * ContactPersonDAO
 * @author lang
 */
class ContactPersonDAO extends BaseDAO {
	
	const ENTITY_NAME = 'model\database\entity\ContactPerson';
	
	/**
	 * DataSource for company contacts
	 * @param int|Company $company
	 * @return DoctrineDataSource
	 */
	public function getCompanyListDataSource($company) {
		$qb = new QueryBuilder($this->getEntityManager());
		$qb->select('p')->from(self::ENTITY_NAME, 'p')->andWhere($qb->expr()->eq('p.company', $company));
		$ds = new DoctrineDataSource($qb,'p',callback($this,'identify'));
		
		return $ds;
	}
	
	/**
	 * DataSource for all contacts
	 * @return DoctrineDataSource
	 */
	public function getListDataSource() {
		$qb = new QueryBuilder($this->getEntityManager());
		$qb->select('p')->from(self::ENTITY_NAME, 'p');
		$ds = new DoctrineDataSource($qb,'p',callback($this,'identify'));
		
		return $ds;
	}
	
	/**
	 * @param int $id
	 * @throws DAOException
	 */
	public function delete($id) {
		$contactPerson = $this->getById($id);
		if( !$contactPerson )
			throw DAOException::notFound($id, 'contact person');
		
		return $this->deleteContact($contactPerson);
	}
	
	/**
	 * @param \model\database\entity\ContactPerson $contactPerson
	 * @throws \PDOException
	 */
	public function deleteContact(ContactPerson $contactPerson) {
		$this->getEntityManager()->remove($contactPerson);
		$this->getEntityManager()->flush();
	}

	/**
	 * @param int $id
	 * @return ContactPerson|NULL
	 */
	public function getById($id) {
		$id = (int)$id;
		$dql = 'SELECT p FROM model\database\entity\ContactPerson p WHERE p.id = :id';
		return $this->getEntityManager()->createQuery($dql)->setParameter(':id', $id)->getOneOrNullResult();
	}
	
	/**
	 * @see \model\database\dataSource\IDataSource::identify()
	 * @param \model\database\entity\ContactPerson $item
	 * @return int
	 * @throws \InvalidArgumentException
	 */
	public function identify( $item ) {
		if( !($item instanceof ContactPerson) )
			throw \InvalidArgumentException('Expected instance of type ['.self::ENTITY_NAME.'] ');
		return $item->getId();
	}
	
}

