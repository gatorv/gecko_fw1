<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

/**
 * Simple Encoder Class
 *
 * @package Gecko;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2009
 * @version $Id$v1.0$ 2009
 * @access public
 **/
class Gecko_Encoder {
	/**
	 * Passphrase to encode
	 * @var sting
	 */
	private $_passPhrase = '';
	
	/**
	 * Creates a new Encoder object
	 * 
	 * @param $sPassPhrase
	 * @return new Instance
	 */
	public function __construct($sPassPhrase) {
		$this->_passPhrase = $sPassPhrase;
	}
	
	/**
	 * Encodes a string with a passphrase, returns a 
	 * 40 hexadeximal number using sha1
	 * 
	 * @param $string
	 * @return string
	 */
	public function encode($string) {
		$sToEncode = $this->_passPhrase . $string;
		
		return sha1($sToEncode);
	}
	
}