<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * Gecko_Controller_Plugin_ModuleLayout
 * 
 * Zend Controller Plugin for setting layouts depending on the
 * module
 * 
 * Changed to use DI instead of singleton for Layout instance
 *
 * @package Gecko.Controller.Plugin;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @access public
 **/
class Gecko_Controller_Plugin_ModuleLayout 
	extends Zend_Controller_Plugin_Abstract
{
	/**
	 * Array that holds the layouts per module
	 * @var array
	 */
	protected $layouts = array();
	
	/**
	 * Layout instance to use
	 * @var Zend_Layout
	 */
	protected $layout = null;
	
	/**
	 * Initiates the plugin
	 * 
	 * @param array $layouts
	 * @param Zend_Layout $Layout
	 * @return void
	 */
	public function __construct(array $aLayouts, Zend_Layout $Layout = null)
	{
		$this->layouts = $aLayouts;
		$this->layout = $Layout;
	}
	
	/**
	 * Set the layout to use for the plugin, if null
	 * it will get the singleton mvc instance
	 * @param Zend_Layout $Layout
	 * @return this
	 */
	public function setLayout(Zend_Layout $Layout = null)
	{
		if ($Layout == null) {
			$Layout = Zend_Layout::getMvcInstance();
		}
		
		$this->layout = $Layout;
		
		return $this;
	}
	
	/**
	 * Return the layout used by the plugin
	 * @return Zend_Layout
	 */
	public function getLayout()
	{
		if ($this->layout == null) {
			$this->setLayout();
		}
		
		return $this->layout;
	}
	
	/**
	 * Set the layouts used by the plugin
	 * @param array $aLayouts
	 * @return this
	 */
	public function setLayouts(array $aLayouts)
	{
		$this->layouts = $aLayouts;
		
		return $this;
	}
	
	/**
	 * Return the layouts registered by the plugin
	 * @return array
	 */
	public function getLayouts()
	{
		return $this->layouts;
	}
	
	/**
	 * On dispatchLoopStartup read and set the correct layout
	 * this is called on the startup to allow custom setup
	 * if needed.
	 * 
	 * @override
	 * @param Zend_Controller_Request_Abstract $Request
	 * @return void
	 */
	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $Request)
	{
		$sModule = $Request->getModuleName();
		$aLayouts = $this->getLayouts();
		$Layout = $this->getLayout();
		
		if (isset($aLayouts[$sModule])) {
			$Layout->setLayout($aLayouts[$sModule]);
		}
	}
}