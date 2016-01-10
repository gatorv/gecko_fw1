<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

/**
 * A Singleton log object using Zend_Log
 *
 * @package Gecko;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$1.00
 * @access public
 **/class Gecko_Log {
	private static $instance = null; //@var private static Gecko_Log reference to this class
	private static $logWriter = null; //@var private static Zend_Log_Writer_Abstract a writer to save logs
	private $logger = null; //@var private Zend_Log Object to save logs

	/**
	 * Sets the default log Writer
	 *
	 * @param Zend_Log_Writer_Abstract The writer for the log
	 * @access public static
	 * @return void
	 **/
	public static function setLogWriter(Zend_Log_Writer_Abstract $writer) {
		self::$logWriter = $writer;
	}

	/**
	 * Singleton pattern implementation makes "new" unavailable
	 *
	 * Creates a new instance of this class
	 *
	 * @param Zend_Log_Writer_Abstract The writer for the log
	 * @access private
	 **/
	private function __construct($writer) {
		$this->logger = new Zend_Log($writer);
	}

	/**
	 * Returns and if needed creates a instance
	 * Singleton
	 *
	 * @access public static
	 * @return object Instance of the class
	 **/
	public static function getInstance() {
		if( self::$logWriter == null ) {
			throw new Zend_Log_Exception('Incorrect $writer, check settings, type: ' . gettype($writer));
		}

		if( self::$instance == null ) {
			self::$instance = new self(self::$logWriter);
		}

		return self::$instance;
	}

	/**
	 * Saves a message in the log buffer
	 *
	 * @param string $msg
	 * @see GeckoLog::Log
	 * @access public
	 * @return void
	 **/
	public function save( $msg ) {
		$this->log( $msg, Zend_Log::INFO );
	}

	/**
	 * Logs a message
	 *
	 * @param string $msg The Message to save
	 * @access public
	 * @return void
	 **/
	public function log( $msg, $priority = Zend_Log::DEBUG ) {
		$this->logger->log($msg, $priority);
	}
}
?>