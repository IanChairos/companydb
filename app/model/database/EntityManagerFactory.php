<?php

namespace model\database;

use \Doctrine\ORM\Configuration;
use \Doctrine\ORM\EntityManager;
use \Doctrine\ORM\Mapping\Driver\XmlDriver;

/**
 * EntityManagerFactory
 * @author Jan Svatoš <svatosja@gmail.com>
 */
class EntityManagerFactory extends EntityManager {

	/**
	 * @param string $driver
	 * @param string $host
	 * @param string $db
	 * @param string $username
	 * @param string $password
	 * @param bool $productionMode
	 * @return \Doctrine\ORM\EntityManager
	 */
	static public function createManager($driver, $host, $db, $username, $password, $productionMode = FALSE) {
		
		$config = new Configuration;
		$config->setMetadataDriverImpl(new XmlDriver(ENTITY_DIR . '/mapping'));
		$config->setProxyDir(ENTITY_DIR . '/proxy');
		$config->setProxyNamespace('Proxy');
		
//		if ($productionMode)
//			$config->setAutoGenerateProxyClasses(FALSE);

		$connectionOptions = array(
			'driver' => $driver,
			'host' => $host,
			'dbname' => $db,
			'user' => $username,
			'password' => $password
		);

		// Create EntityManager
		$em = self::create($connectionOptions, $config);
		$em->getConnection()->setCharset("utf8");

		return $em;
	}
}

