<?php

namespace model\database\entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of Company
 * @author lang
 */
class Company {
	
	private $id;
	private $name;
	private $ic;
	private $email;
	private $address;
	private $employeeCount;
	private $created;
	private $updated;
	private $contactPersons;
	
	public function __construct() {
		$this->contactPersons = new ArrayCollection();
	}
	
	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return \model\database\entity\Company
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return \model\database\entity\Company
	 */
	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getIc() {
		return $this->ic;
	}

	/**
	 * @param string $ic
	 * @return \model\database\entity\Company
	 */
	public function setIc($ic) {
		$this->ic = $ic;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @param string $email
	 * @return \model\database\entity\Company
	 */
	public function setEmail($email) {
		$this->email = $email;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAddress() {
		return $this->address;
	}

	/**
	 * @param string $address
	 * @return \model\database\entity\Company
	 */
	public function setAddress($address) {
		$this->address = $address;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getEmployeeCount() {
		return $this->employeeCount;
	}

	/**
	 * @param int $employeeCount
	 * @return \model\database\entity\Company
	 */
	public function setEmployeeCount($employeeCount) {
		$this->employeeCount = $employeeCount;
		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreated() {
		return $this->created;
	}

	/**
	 * @param \DateTime $created
	 * @return \model\database\entity\Company
	 */
	public function setCreated($created) {
		$this->created = $created;
		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getUpdated() {
		return $this->updated;
	}

	/**
	 * @param \DateTime $updated
	 * @return \model\database\entity\Company
	 */
	public function setUpdated($updated) {
		$this->updated = $updated;
		return $this;
	}

	/**
	 * @return \Doctrine\ORM\PersistentCollection
	 */
	public function getContactPersons() {
		return $this->contactPersons;
	}

	/**
	 * @param array|ArrayCollection $contactPersons
	 */
	public function setContactPersons($contactPersons) {
		$this->contactPersons = $contactPersons;
	}

}

