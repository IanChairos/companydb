<?php

namespace model\database\dao;

/**
 * DAOException - database access error
 * @author lang
 */
class DAOException extends \Exception {
	
	static public function notFound($id,$name='record') {
		return new self(ucfirst($name).' ['.$id.'] not found');
	}
	
}
