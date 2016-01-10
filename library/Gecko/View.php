<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

/**
 * MVC View object
 *
 * @package gecko;
 * @author Christopher Valderrama <christopher@geckowd.com>
 * @copyright Copyright (c) 2006
 * @version $Id$v1.0$26 Oct 2006
 * @access public
 **/
class Gecko_View {
	protected $_vars = array(); //@var protected array Variables passed from controller to template
	protected $_template = ''; //@var protected string Current template set via controller

	/**
	 * Creates a new Instance of View
	 *
	 * @access public
	 **/
	public function __construct() {
		$this->_vars = array();
	}

	/**
	 * Register a form Template helper to parse and display data on a template
	 *
	 * @param string $helper
	 * @access public
	 * @return void
	 **/
	public function registerHelper( $helper ) {
		Gecko_Template::registerViewHelpers( $helper );
	}

	/**
	 * Set Variables to pass to template
	 *
	 * @access public
	 * @param array $data
	 * @return void
	 **/
	public function setVars( $data ) {
		$this->_vars = $data;
	}

	/**
	 * Return template variables
	 *
	 * @return array
	 * @access public
	 **/
	public function getVars() {
		return $this->_vars;
	}

	/**
	 * Use Custom Layout to render data
	 *
	 * @param string $layout
	 * @access public
	 * @return void
	 **/
	public function setLayout( $layout ) {
		Gecko_Template::useLayout( $layout );
	}

	/**
	 * Render a Template once variables are set
	 *
	 * @access public
	 * @return void
	 **/
	public function render() {
		$this->display();
	}

	/**
	 * Set a custom template, to render after controller has been rendered
	 *
	 * @param string $template
	 * @access public
	 * @return void
	 **/
	public function setTemplate( $template ) {
		$this->_template = $template;
	}

	/**
	 * Called after render, and displays the data onto the browser
	 *
	 * @access private
	 * @return void
	 **/
	private function display() {
		$base = Zend_Registry::get("base");
		Gecko_Template::setTemplateDir( $base->getControllerName() );
		print Gecko_Template::controllerRender( $this->_template, $this->getVars() );
	}
}
?>