<?php

namespace model\database\entity;

use model\database\entity\Company;

/**
 * Description of ContactPerson
 *
 * @author lang
 */
class ContactPerson {

	/** @var int */
	private $id;
	
	/** @var string */
	private $firstname;
	
	/** @var string */
	private $lastname;
	
	/** @var string */
	private $email;
	
	/** @var string */
	private $phone;
	
	/** @var string */
	private $jobName;
	
	/** @var \DateTime */
	private $created;
	
	/** @var \DateTime */
	private $updated;

	/** @var Company */
	private $company;
	
	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param type $id
	 * @return \ContactPerson
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getFirstname() {
		return $this->firstname;
	}

	/**
	 * @param string $firstname
	 * @return \model\database\entity\ContactPerson
	 */
	public function setFirstname($firstname) {
		$this->firstname = $firstname;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLastname() {
		return $this->lastname;
	}

	/**
	 * @param string $lastname
	 * @return \model\database\entity\ContactPerson
	 */
	public function setLastname($lastname) {
		$this->lastname = $lastname;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}
	
	/**
	 * @param string $lastname
	 * @return \model\database\entity\ContactPerson
	 */
	public function setEmail($email) {
		$this->email = $email;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPhone() {
		return $this->phone;
	}

	/**
	 * @param string $lastname
	 * @return \model\database\entity\ContactPerson
	 */
	public function setPhone($phone) {
		$this->phone = $phone;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getJobName() {
		return $this->jobName;
	}

	/**
	 * @param string $lastname
	 * @return \model\database\entity\ContactPerson
	 */
	public function setJobName($jobName) {
		$this->jobName = $jobName;
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
	 * @return \model\database\entity\ContactPerson
	 */
	public function setCreated(\DateTime $created) {
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
	 * @return \model\database\entity\ContactPerson
	 */
	public function setUpdated(\DateTime $updated) {
		$this->updated = $updated;
		return $this;
	}

	/**
	 * @return Company
	 */
	public function getCompany() {
		return $this->company;
	}

	/**
	 * @param Company $company
	 * @return \model\database\entity\ContactPerson
	 */
	public function setCompany($company) {
		$this->company = $company;
		return $this;
	}

}

