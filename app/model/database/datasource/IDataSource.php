<?php
namespace model\database\dataSource;

/**
 * IDataSource
 *  - interface for all datasources, datagrid support
 * @author lang
 */
interface IDataSource {
	
	/** @return mixed */
	public function getData();
	
	/**
	 * Returns data count
	 * @return int */
	public function getCount();
	
	/**
	 * @param int $limit
	 * @param int $offset
	 */
	public function limit( $limit, $offset );
	
	/**
	 * @param array $conditions
	 */
	public function filter( array $conditions );
	
	/**
	 * Get unique identificator value for an item, that came from this DS
	 * @param mixed $item
	 * @return mixed
	 */
	public function identify( $item );
	
}

