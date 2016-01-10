<?php
class Gecko_Auth_Adapter_Simple implements Zend_Auth_Adapter_Interface
{
	private $_allowedPasswords = array();
	private $_usedPassword = '';
	private $_errorMessage = 'Invalid Password';
	
	public function __construct(array $allowedPasswords, $password, $error_message = '')
	{
		$this->_allowedPasswords = $allowedPasswords;
		$this->_usedPassword = $password;
		if (!empty($error_message)) {
			$this->_errorMessage = $error_message;
		}
	}
	
	public function authenticate()
	{
		if (in_array($this->_usedPassword, $this->_allowedPasswords)) {
			$identity = array_search($this->_usedPassword, $this->_allowedPasswords);
			$code = Zend_Auth_Result::SUCCESS;
			$messages = array();
		} else {
			$code = Zend_Auth_Result::FAILURE;
			$identity = '';
			$messages = array(
				$this->_errorMessage
			);
		}
		
		return new Zend_Auth_Result($code, $identity, $messages);
	}
}