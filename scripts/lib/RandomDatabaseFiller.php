<?php

use Nette\Utils\Strings;
use Nette\DI\IContainer;
use model\database\entity\Company;
use model\database\entity\ContactPerson;
use Doctrine\ORM\EntityManager;
use Nette\IOException;

/**
 * RandomDatabaseFiller
 *  - fills the database with random generated data
 *  - supports table creation
 *  - supports cleaning tables
 * @author Jan Svatoš <svatosja@gmail.com>
 */
class RandomDatabaseFiller {

	/** @var IContainer */
	private $context;
	
	/** @var EntityManager */
	private $entityManager;
	
	/** @var int */
	private $companies;
	
	/** @var int */
	private $perCompany;
	
	/**
	 * @param \Nette\DI\IContainer $context
	 * @param int $companies
	 * @param int $maxContactsPerCompany
	 */
	public function __construct(IContainer $context, $companies = 100, $maxContactsPerCompany = 100) {
		$this->context = $context;
		$this->entityManager = $context->getService('entityManager');
		$this->companies = (int)$companies;
		$this->perCompany = (int)$maxContactsPerCompany;
	}
	
	/**
	 * Magic calling
	 */
	public function __invoke() {
		$this->emptyTables();
		$this->generate();
	}
	
	public function createTables() {
		$filepath = __DIR__.'/InitDatabaseSchema.sql';
		$sql = @file_get_contents($filepath);
		if( $sql === FALSE )
			throw new IOException('Error reading file ['.$filepath.']');
		
		return $this->entityManager->getConnection()->executeQuery($sql)->errorCode();
	}

	public function emptyTables() {
		return $this->entityManager->getConnection()->exec('TRUNCATE TABLE `company`; TRUNCATE TABLE `contact_person`;');
	}
	
	private function generate() {
		$count = mt_rand($this->companies/2, $this->companies);
		$companies = array();
		for($i=0;$i<$count;$i++) {
			$company = $this->generateCompany();
			$this->entityManager->persist($company);
			$this->entityManager->flush();
			$this->generateContacts($company);
			$this->entityManager->flush();
			$this->entityManager->clear();
			unset($company);
		}
		
		return $companies;
	}

	/**
	 * @return \model\database\entity\Company
	 */
	public function generateCompany() {
		$company = new Company();
		$company->setIc( $this->generateIc() );
		$company->setName( $this->generateCompanyName() );
		$company->setAddress( $this->generateAddress() );
		$company->setEmail( $this->generateEmail() );
		$company->setEmployeeCount( mt_rand(0, 1500) );
		$company->setCreated( new \DateTime() );
		$company->setUpdated( new \DateTime() );
		
		return $company;
	}

	private function generateIc() {
		return mt_rand(1,9).Strings::random(11, '0-9');
	}
	
	private function generateCompanyName() {
		$companyNames = array(
			'Best Global Brands','McDonald','Louis Vuitton','Heinz','Colgate','Gillette','Wrigley','Dave Packard','Bill Hewlett','Kiichiro Toyoda','Tiffany','JKM','Bayerische Motoren Werke','International Business Machines','Hennes & Mauritz','Hongkong Banking Corporation','Shanghai Banking Corporation','Systeme Anwendungen','Produkte der Datenverarbeitung','Microprocessor Software','Integrated Electronics ','Infya','Gtiroll','Blineeya','Meebeemojo','Infeliblog','Infechnorati','Seceejax','Yahoogami','Meebolishare','Tiveliorb','Sectirati','Gtix'
		);
		return $companyNames[mt_rand(0, count($companyNames) - 1)];
	}
	
	private function generateAddress() {
		$streets = array(
			'živá', 'hloupá', 'chytrá', 'petýrková', 'holá', 'stříbrná', 'zlatá', 'skořicová', 'kulatá', 'bramborová',
			'arkalická', 'mezinárodní', 'zadní', 'přední', 'dušní', 'stříbrného', 'cihlová'
		);
		$streetNumber = mt_rand(1, 2000);
		$miscNumber = mt_rand(1, 100) > 50 ? '/' . mt_rand(10, 30) : '';
		$psc = mt_rand(100, 150) . ' 00';

		return ucfirst($streets[mt_rand(0, count($streets) - 1)]) . ' ' . $streetNumber . $miscNumber . ', ' . $psc;
	}

	private function generateEmail() {
		$name = Strings::random(mt_rand(3, 10), 'a-z');
		$domain = Strings::random(mt_rand(3, 10), 'a-z');
		$type = Strings::random(mt_rand(2, 3), 'czomeunetgov');
		return $name . '@' . $domain . '.' . $type;
	}
	
	public function generateContacts(Company $company) {
		$count = mt_rand(0, $this->perCompany);
		for($i=0;$i<$count;$i++) {
			$contact = $this->generateContact();
//			$this->entityManager->getUnitOfWork()->addToIdentityMap($company);
			$contact->setCompany($company);
			$this->entityManager->persist($contact);
		}
//		$this->entityManager->flush();
//		$this->entityManager->clear();
	}

	/**
	 * @return \model\database\entity\ContactPerson
	 */
	private function generateContact() {
		$contact = new ContactPerson();
		$male = mt_rand(0, 100) > 50;
		if ($male) {
			$contact->setFirstname( $this->generateMaleFirstname() );
			$contact->setLastname( $this->generateMaleLastname() );
		} else {
			$contact->setFirstname( $this->generateFemaleFirstname() );
			$contact->setLastname( $this->generateFemaleLastname() );
		}
		$contact->setPhone( $this->generatePhone() );
		$contact->setEmail( $this->generateEmail() );
		$contact->setJobName( $this->generateJob() );
		$contact->setCreated( new \DateTime() );
		$contact->setUpdated( new \DateTime() );
		
		return $contact;
	}

	private function generateMaleFirstname() {
		$maleNames = array(
			'jan', 'petr', 'pavel', 'radek', 'jindřich', 'karel', 'daniel', 'marek', 'martin'
		);
		return ucfirst($maleNames[mt_rand(0, count($maleNames) - 1)]);
	}

	private function generateMaleLastname() {
		$maleSurnames = array(
			'grimm', 'hloupý', 'chytrý', 'petýrek', 'holý', 'stříbrný', 'zlatý', 'skořicový', 'kule', 'brambor'
		);
		return ucfirst($maleSurnames[mt_rand(0, count($maleSurnames) - 1)]);
	}

	private function generateFemaleFirstname() {
		$femaleNames = array(
			'jana', 'petra', 'pavla', 'radka', 'jitka', 'kateřina', 'dana', 'martina', 'nikola'
		);
		return ucfirst($femaleNames[mt_rand(0, count($femaleNames) - 1)]);
	}

	private function generateFemaleLastname() {
		$femaleSurnames = array(
			'grimmová', 'hloupá', 'chytrá', 'petýrková', 'holá', 'stříbrná', 'zlatá', 'skořicová', 'kulatá', 'bramborová'
		);
		return ucfirst($femaleSurnames[mt_rand(0, count($femaleSurnames) - 1)]);
	}

	private function generateJob() {
		$jobs = array(
			'designer', 'developer', 'H&R', 'management', 'marketing', 'business relations', 'copywriter', 'assistant'
		);
		return ucfirst($jobs[mt_rand(0, count($jobs) - 1)]);
	}
	
	private function generatePhone() {
		$prefix = mt_rand(0, 100) > 50 ? '+42'.mt_rand(0, 9).' ' : '';
		$phone = Strings::random(3, '1-9').' '.Strings::random(3, '0-9').' '.Strings::random(3, '0-9');
		return $prefix.$phone;
	}

}

