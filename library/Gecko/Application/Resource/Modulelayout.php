<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once 'Zend/Application/Resource/ResourceAbstract.php';

/**
 * Gecko_Application_Resource_Modulelayout
 * 
 * Resource Loader to allow multiple layouts per module to use
 * add this to your application.ini:
 * 
 * pluginPaths.Gecko_Application_Resource_ = "Gecko/Application/Resource/"
 * 
 * And make sure you are autoloading Gecko Framework:
 * 
 * autoloaderNamespaces[] = "Gecko_"
 * 
 * Then you can use
 * resources.Modulelayout.layouts.frontend = "frontend"
 * resources.Modulelayout.layouts.backend = "backend"
 * resources.Modulelayout.layouts.module = "layouttouse"
 * 
 * Optionally you can supply the plugin name to use, by default it will
 * load and use Gecko_Controller_Plugin_ModuleLayout
 *
 * @package Gecko.Application.Resource;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @access public
 **/
class Gecko_Application_Resource_ModuleLayout 
	extends Zend_Application_Resource_ResourceAbstract {
	
	/**
	 * The default controller plugin name
	 * 
	 * @var string
	 */
	protected $_pluginName = 'Gecko_Controller_Plugin_ModuleLayout';
	
	/**
	 * Initialize the plugin and layouts
	 * 
	 * @throws Zend_Application_Resource_Exception
	 * @override
	 * @return void
	 */
	public function init() {
		$Bootstrap = $this->getBootstrap();
		$Bootstrap->bootstrap('frontController');
		$Bootstrap->bootstrap('layout');
		$Front = $Bootstrap->getResource('frontController');
		$Layout = $Bootstrap->getResource('layout');
		
		$aOptions = $this->getOptions();
		$aLayouts = $aOptions['layouts'];
		
		if (!is_array($aLayouts)) {
			throw new Zend_Application_Resource_Exception('layouts is expected to be an array');
		}
		
		$plugin = isset($aOptions['plugin']) ? $aOptions['plugin'] : null;
		if ($plugin === null) {
			$plugin = $this->_pluginName;
		}
		
		$Front->registerPlugin(new $plugin($aLayouts, $Layout));
	}
}