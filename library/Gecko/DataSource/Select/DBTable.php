<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once('Gecko/DataSource/Select/Interface.php');

/**
 * DBTable Data Source
 *
 * @package Gecko.DataSource.Select;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v1.0$ 2008
 * @access public
 **/
class Gecko_DataSource_Select_DBTable implements Gecko_DataSource_Select_Interface {
	/**
	 * The table object
	 *
	 * @see Zend_Db_Table
	 * @var Object
	 */
	protected $table;
	/**
	 * The method of the object to
	 * call
	 *
	 * @var string
	 */
	protected $method;
	/**
	 * The data array
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Creates a new instance of the DBTable Data Source
	 *
	 * @see Zend_Db_Table
	 * @param Zend_Db_Table_Abstract The table
	 * @param string The method to call
	 */
	public function __construct( Zend_Db_Table_Abstract $table, $method ) {
		$this->table = $table;
		$this->method = $method;
	}

	/**
	 * Gets the current select Data
	 *
	 * @return array The data from the DS
	 */
	public function getSelectData() {
		if( !is_callable( array( $this->table, $this->method ) ) ) {
			throw new Gecko_DataSource_Select_Exception( "Unable to call {$this->method} on table: " . get_class( $this->table ) );
		}

		$method = $this->method;
		$data = $this->table->$method();

		$this->data = $data;

		return $data;
	}
}
?>