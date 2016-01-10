<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once 'Zend/Auth/Adapter/Interface.php';
require_once 'Zend/Auth/Exception.php';

/**
 * Gecko_Auth_Adapter_Config
 *
 * @package Gecko.Auth.Adapter;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @access public
 **/
class Gecko_Auth_Adapter_Config implements Zend_Auth_Adapter_Interface {
	private $_validUsername;
	private $_validPassword;
	private $_hashAlgorithm = '';
	private $_hashKey = '';
	
	private $_username;
	private $_password;
	
	public function __construct($config) {
		if ($config instanceof Zend_Config) {
			$config = $config->toArray();
		}
		
		if (!isset($config['username'])) {
			throw new Zend_Auth_Exception('Adapter expected key "username" in array, none given');
		}
		if (!isset($config['password'])) {
			throw new Zend_Auth_Exception('Adapter expected key "password" in array, none given');
		}
		
		$this->_validUsername = $config['username'];
		$this->_validPassword = $config['password'];
		$this->_hashAlgorithm = isset($config['hash_algorithm']) ? $config['hash_algorithm'] : '';
		$this->_hashKey = isset($config['key']) ? $config['key'] : '';
	}
	
	public function setHashKey($key) {
		$this->_hashKey = $key;
		
		return $this;
	}
	
	public function getHashKey($key) {
		return $this->_hashKey;
	}
	
	public function setHashAlgorithm($hashAlgorithm) {
		$this->_hashAlgorithm = $hashAlgorithm;
		
		return $this;
	}
	
	public function getHashAlgorithm() {
		return $this->_hashAlgorithm;
	}
	
	public function setIdentity($username) {
		$this->_username = $username;
		
		return $this;
	}

	public function getIdentity() {
		return $this->_username;
	}
	
	public function setCredential($password) {
		$this->_password = $password;
		
		return $this;
	}

	public function getCredential() {
		return $this->_password;
	}
	
	public function authenticate() {
		$username = $this->_username;
		$password = $this->_password;
		
		if ($this->_hashAlgorithm != '') {
			$hashAlgorithm = $this->_hashAlgorithm;
			$hashPassword = $password;
			if ($this->_hashKey != '') {
				$hashPassword .= $this->_hashKey;
			}
			
			$password = $hashAlgorithm($hashPassword);
		}
		
		// Check Username
		if ($username !== $this->_validUsername) {
			return new Zend_Auth_Result(
	            Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND,
	            array(),
	            array('Invalid or absent identy')
	        );
		}
		
		// Check Password
		if ($password !== $this->_validPassword) {
			return new Zend_Auth_Result(
	            Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
	            array(),
	            array('Invalid or absent credentials')
	        );
		}
		
		// If valid return a valid object
		return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $this->_validUsername);
	}
}