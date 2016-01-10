<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once( 'Gecko/Auth/Exception.php' );

/**
 * Wrapper for Zend Auth to validate incoming users against a DB
 *
 * @package Gecko;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v1.0$ 2008
 * @access public
 **/
class Gecko_Auth {
	/**
	 * Adapter for validation (Zend_Auth)
	 *
	 * @var Zend_Auth
	 **/
	private $adapter;

	/**
	 * The Password Column
	 *
	 * Use this var to skip saving password in session
	 *
	 * @var String
	 */
	private $passwordColumn = 'Password';

	/**
	 * The Class name to use when successful Auth
	 *
	 * @var string
	 */
	private static $userClass = 'Gecko_User';

	/**
	 * Sets the default class to use when you authenticate
	 * correctly, if you are using a custom class, remember
	 * to include it before performing authentification
	 *
	 * @param string $class The Class to use
	 */
	public static function setDefaultUserClass($class) {
		self::$userClass = $class;
	}

	/**
	 * Creates a new Gecko_Auth object, it uses Zend_Auth to validate
	 * directly with the database
	 *
	 * @params array $settings los settings para levantar el objeto
	 **/
	public function __construct($settings) {
		// Check Input settings array
		$this->checkSettings($settings);

		// Get DB Adapter
		if(!isset($settings['db'])) {
			$db = Gecko_DB::getInstance();
		} else {
			$db = $settings['db'];
		}

		$adapter = new Zend_Auth_Adapter_DbTable($db, $settings['tableName'], $settings['identityColumn'], $settings['credentialColumn']);

		// Check if there is a credential treatment
		if(isset($settings['credentialTreatment']) && !empty($settings['credentialTreatment'])) {
			$adapter->setCredentialTreatment($settings['credentialTreatment']);
		}

		$this->adapter = $adapter;
		$this->settings = $settings;
		$this->passwordColumn = $settings['credentialColumn'];

		$auth = Zend_Auth::getInstance();
		$auth->setStorage(new Zend_Auth_Storage_Session($settings['sessionNamespace']));
	}

	/**
	 * Checks that the correct settings are submitted in the array
	 *
	 * @param array $settings The array to check for settings
	 **/
	private function checkSettings($settings) {
		$error = "";

		if(empty($settings['tableName'])) {
			$error .= "tableName element not defined";
		}

		if(empty($settings['identityColumn'])) {
			$error .= "identityColumn element not defined";
		}

		if(empty($settings['credentialColumn'])) {
			$error .= "credentialColumn element not defined";
		}

		if(empty($settings['sessionNamespace'])) {
			$error .= "sessionNamespace element not defined";
		}

		if(!empty($error)) {
			throw new Gecko_Auth_Exception($error);
		}
	}

	/**
	 * Perform the actual login against the DB
	 *
	 * @param string The User to Validate
	 * @param string The User password to Validate
	 * @return boolean
	 **/
	public function login($user, $password) {
		if(!($this->adapter instanceof Zend_Auth_Adapter_DbTable)) {
			throw new Gecko_Auth_Exception("The Adapter is not valid or not initialized");
		}

		$auth = Zend_Auth::getInstance();

		$this->adapter->setIdentity($user);
		$this->adapter->setCredential($password);

		$result = $auth->authenticate($this->adapter);
		if( $result->isValid() ) {
			$data = $this->adapter->getResultRowObject(null, $this->passwordColumn);
			$userObject = new self::$userClass(get_object_vars($data));
			$auth->getStorage()->write($userObject);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Closes the active session
	 *
	 * @access public
	 * @return void
	 **/
	public function logout() {
		Zend_Auth::getInstance()->clearIdentity();
	}

	/**
	 * Check if the user is logged in
	 *
	 * @access public
	 * @return boolean
	 **/
	public function isLoggedIn() {
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
		    return true;
		} else {
			return false;
		}
	}

	/**
	 * Return the actual user
	 *
	 * @access public
	 * @return object
	 **/
	public function getUser() {
		$auth = Zend_Auth::getInstance();

		return $auth->getIdentity();
	}

	/**
	 * Sets the password column to avoid saving it in
	 * session
	 *
	 * @param string $col The Password Column
	 */
	public function setPasswordCol($col) {
		$this->passwordColumn = $col;
	}

	/**
	 * Returns the password column
	 *
	 * @return string The password column
	 */
	public function getPasswordCol() {
		return $this->passwordColumn;
	}
}
