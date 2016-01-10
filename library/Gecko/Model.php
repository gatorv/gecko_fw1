<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

/**
 * Model Factory with Cache
 *
 * @package Gecko;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @access public
 **/
class Gecko_Model
{
	/**
	 * The Internal Cache
	 * @var array
	 */
	private static $_mapperCache = array();
	/**
	 * The Default Namespace to load from
	 * @var string
	 */
	private static $_defaultNamespace = null;
	
	/**
	 * Sets the default namespace
	 * 
	 * @param string $namespace
	 * @return void
	 */
	public static function setDefaultNamespace($sNamespace)
	{
		self::$_defaultNamespace = $sNamespace;
	}
	
	/**
	 * Returns the default namespace
	 * 
	 * @return string
	 */
	public static function getDefaultNamespace()
	{
		return self::$_defaultNamespace;
	}
	
	/**
	 * Loads and caches the model
	 * 
	 * @param string $mapper
	 * @param string $namespace
	 * @return Object
	 */
	public static function factory($sModel, $sNamespace = '')
	{
		$sClass = '';
		if (!empty(self::$_defaultNamespace)) {
			$sClass = self::$_defaultNamespace . '_';
		}
		if (!empty($sNamespace))
		{
			$sClass = $sNamespace . '_';
		}
		
		$sClass .= 'Model_' . ucfirst($sModel);
		
		if (!isset(self::$_mapperCache[$sClass])) {
			if (!class_exists($sClass, true)) {
				throw new Exception("$sClass not found");
			}
			$MapperClass = new $sClass();
			self::$_mapperCache[$sClass] = $MapperClass;
		}
		
		return self::$_mapperCache[$sClass];
	}
}