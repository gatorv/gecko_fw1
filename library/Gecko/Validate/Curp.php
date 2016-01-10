<?php
class Gecko_Validate_Curp extends Zend_Validate_Abstract
{
	const CURP = 'curp';
	
	protected $_messageTemplates = array(
		self::CURP => "'%value%' no parece ser un curp válido"
	);
	
	public function isValid($value)
	{
		$this->_setValue($value);
		
		if (!preg_match('/^([a-z]{4})([0-9]{6})([a-z]{6})([0-9]{2})$/i', $value)) {
			$this->_error(self::CURP);
			return false;
		}
		
		return true;
	}
}