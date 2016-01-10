<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once('Zend/Db/Exception.php');

/**
 * Singleton class for Zend_DB
 *
 * @package Gecko;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v2.0$ 2008
 * @access public
 */
class Gecko_DB {
	/**
     * Singleton instance
     *
     * @var Gecko_DB
     */
	private static $_instance = null;

	/**
     * Private var that holds true DB Adapter
     *
     * @var Zend_Auth_Adapter_Interface
     */
	private $dbConn = null;

	/**
     * Singleton pattern implementation makes "new" unavailable
     *
     * @return void
     */
	private function __construct() {
		$this->dbConn = Zend_Db_Table_Abstract::getDefaultAdapter();
	}

	/**
     * Returns an instance of Zend_DB_Adapter
     *
     * Singleton pattern implementation
     *
     * @return Zend_DB_Adapter_Interface
     */
	public static function getInstance() {
		if( self::$_instance == null ) {
			self::$_instance = new self();
		}

		return self::$_instance->dbConn;
	}
}