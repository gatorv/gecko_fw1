<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once('Gecko/URL/Engine/Interface.php');
require_once('Zend/View/Helper/Url.php');

/**
 * Gecko Framework URL Engine creator
 *
 * @package Gecko.URL.Engine;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2010
 * @version $Id$v2.0
 * @access public
 */
class Gecko_URL_Engine_Zend implements Gecko_URL_Engine_Interface {
	/**
	 * Creates a URL to the same page using
	 * Zends Framework helper (for MVC Routing)
	 *
	 * @param array $params
	 * @param bool $emptyQuery
	 * @return string
	 */
	public function getSelfURI($params = array(), $emptyQuery = false) {
		$helper = new Zend_View_Helper_Url();

		return $helper->url($params, null, $emptyQuery);
	}
	
	/**
	 * Returns a parameter
	 *
	 * @param string $param The param to get
	 * @return mixed The parameter
	 */
	public function getParam($param) {
		$front = Zend_Controller_Front::getInstance();
		$request = $front->getRequest();
		
		return $request->getParam($param);
	}
}