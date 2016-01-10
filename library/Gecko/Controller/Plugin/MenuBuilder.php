<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * Gecko_Controller_Plugin_MenuBuilder
 *
 * Zend Controller Plugin for setting the correct menu entries, based
 * on the current module
 *
 * @package Gecko.Controller.Plugin;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @access public
 **/
class Gecko_Controller_Plugin_MenuBuilder extends Zend_Controller_Plugin_Abstract
{
	/**
	 * The menu map entries
	 * @var array
	 */
	private $menuMap = array();
	
	/**
	 * Creates a new plugin instance, it needs a array
	 * with entries, the key is the module name, and the value
	 * must be the registry entry name where the map is set.
	 * 
	 * The menu can be created in each of the modules
	 * bootstrap, and registered there.
	 * 
	 * @param array $map
	 */
	public function __construct(array $map)
	{
		$this->menuMap = $map;
	}
	
	public function preDispatch(Zend_Controller_Request_Abstract $Request)
	{
		$module = $Request->getModuleName();
		
		// Check if is set as a map entry
		if (isset($this->menuMap[$module])) {
			// Read the menu entry from registry
			$menu = Zend_Registry::get($this->menuMap[$module]);
			
			// Set it up as default for Zend_Navigation
			Zend_Registry::set('Zend_Navigation', $menu);
		}
	}
}