<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * Gecko_Controller_Plugin_FlashRenderer
 * 
 * Zend Controller Plugin for setting the flash messages
 *
 * @package Gecko.Controller.Plugin;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @access public
 **/
class Gecko_Controller_Plugin_FlashRenderer
	extends Zend_Controller_Plugin_Abstract {
	/**
	 * Check if there are Messages and assing them to the view
	 * 
	 * @param Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		$FlashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
		$aMessages = $FlashMessenger->getMessages();
		$ViewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		$ViewRenderer->initView();
		$ViewRenderer->view->FlashMessages = $aMessages;
	}	
}