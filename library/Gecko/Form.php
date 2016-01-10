<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

/**
 * Form loading for Zend_Form
 *
 * @package Gecko;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2009
 * @version $Id$2.0
 **/
class Gecko_Form {
	/**
	 * The Model Path
	 * @var string
	 */
	private static $_formPath = '';
	
	/**
	 * Sets the default Form Path
	 * 
	 * @param string $sPath
	 * @return void
	 */
	public static function setDefaultFormPath($sPath) {
		self::$_formPath = $sPath;
	}
	
	/**
	 * Loads a Form from the Form Path
	 * 
	 * @param string $sModel
	 * @return mixed
	 */
	public static function factory($sForm) {
		$sFormFile = $sForm . '.php';
		$sFormClass = 'Form_' . $sForm;
		
		Zend_Loader::loadFile($sFormFile, self::$_formPath);
		
		return new $sFormClass(); 
	}
}