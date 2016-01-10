<?php
class Gecko_Validate_Rfc extends Zend_Validate_Abstract
{
	const RFC = 'rfc';
	
	protected $_messageTemplates = array(
		self::RFC => "'%value%' no parece ser un rfc válido"
	);
	
	public function isValid($value)
	{
		$this->_setValue($value);
		
		$valor = str_replace("-", "", $value);
		$cuartoValor = substr($value, 3, 1);
		//RFC Persona Moral.
		if (ctype_digit($cuartoValor) && strlen($value) == 12) {
			$letras = substr($value, 0, 3);
			$numeros = substr($value, 3, 6);
			$homoclave = substr($value, 9, 3);
			if (!(ctype_alpha($letras) && ctype_digit($numeros) && ctype_alnum($homoclave))) {
				$this->_error(self::RFC);
				return false;
			}
		//RFC Persona Física.
		} else if (ctype_alpha($cuartoValor) && strlen($value) == 13) {
			$letras = substr($value, 0, 4);
			$numeros = substr($value, 4, 6);
			$homoclave = substr($value, 10, 3);
			if (!(ctype_alpha($letras) && ctype_digit($numeros) && ctype_alnum($homoclave))) {
				$this->_error(self::RFC);
				return false;
			}
		}else {
			$this->_error(self::RFC);
			return false;
		}
		
		return true;
	}
}