<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

/**
 * Mapper Factory with Cache
 *
 * @package Gecko;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @access public
 **/
class Gecko_Mapper
{
	private static $_mapperCache = array();
	private static $_defaultNamespace = null;
	
	public static function setDefaultNamespace($namespace)
	{
		self::$_defaultNamespace = $namespace;
	}
	
	public static function getDefaultNamespace()
	{
		return self::$_defaultNamespace;
	}
	
	public static function factory($mapper, $namespace = '')
	{
		$Class = '';
		if (!empty(self::$_defaultNamespace)) {
			$Class = self::$_defaultNamespace . '_';
		}
		if (!empty($namespace))
		{
			$Class = $namespace . '_';
		}
		
		$Class .= 'Model_Mapper_' . ucfirst($mapper);
		
		if (!isset(self::$_mapperCache[$Class])) {
			$MapperClass = new $Class();
			self::$_mapperCache[$Class] = $MapperClass;
		}
		
		return self::$_mapperCache[$Class];
	}
}