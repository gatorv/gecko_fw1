<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once('Gecko/DataSource/Select/Interface.php');

/**
 * SQL Data Source
 *
 * @package Gecko.DataSource.Select;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v1.0$ 2008
 * @access public
 **/
class Gecko_DataSource_Select_SQL implements Gecko_DataSource_Select_Interface {
	/**
	 * DB Adapter
	 *
	 * @see Zend_Db
	 * @var Object
	 */
	private $db;
	/**
	 * SQL Query
	 *
	 * @var string
	 */
	private $sql;

	/**
	 * Creates a new instance of the Select SQL DataSource,
	 * the SQL query must return two values, the rest will
	 * be ignored
	 *
	 * @param Zend_Db_Adapter The DB Adapter to use
	 * @param string The sql Query
	 */
	public function __construct( Zend_Db_Adapter $db, $sql ) {
		$this->db = $db;
		$this->sql = $sql;
	}

	/**
	 * Returns the data from the query
	 *
	 * @return array
	 */
	public function getSelectData() {
		$this->db->setFetchMode( Zend_Db::FETCH_NUM );
		$data = $this->db->fetchPairs( $this->sql );

		return $data;
	}
}
?>