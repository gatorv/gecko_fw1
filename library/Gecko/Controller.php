<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

/**
 * Base Controller with utility methods
 *
 * @package Gecko;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @access public
 **/
class Gecko_Controller extends Zend_Controller_Action
{
	protected $_session;
	protected $_logger;
	protected $_authsession;
	protected $_registry;
	
	public function init()
	{
		$controller = $this->getRequest()->getControllerName();
		$this->_session = new Zend_Session_Namespace('geckosess_' . $controller);
		$this->_logger = Zend_Registry::get('logger');
		$this->_authsession = new Zend_Session_Namespace('auth_'.$this->getRequest()->getModuleName());
		$this->loadRegistry();
	}

	public function _getPersistentParam($param, $default = null)
	{
		return $this->getPersistentParam($param, $default);
	}

	public function getPersistentParam($param, $default = null)
	{
		$value = $this->getRequest()->getParam($param);
		
		if ($value == null) {
			// See in session
			if (isset($this->_session->{$param})) {
				$value = $this->_session->{$param};
			}
		}

		if ($value == null) {
			$value = $default;
		}

		// Persist value in session
		$this->_session->{$param} = $value;

		return $value;
	}

	protected function getOption($name)
	{
		if (isset($this->_registry[$name]))
			return $this->_registry[$name];

		return null;
	}

	protected function loadRegistry()
	{
		if (!Zend_Registry::isRegistered('extras')) {
			return;
		}
		
		$extras = Zend_Registry::get('extras');
		if (!$extras) {
			$extras = array();
		}

		$this->_registry = $extras;
	}
	
	public function getSessionAuth()
	{
		return $this->_authsession;
	}
	
	public function setSession(Zend_Session_Namespace $Session)
	{
		$this->_session = $Session;
		return $this;
	}
	
	public function getSession()
	{
		return $this->_session;
	}
	
	public function setLogger($logger)
	{
		$this->_logger = $logger;
		return $this;
	}
	
	public function getLogger()
	{	
		return $this->_logger;
	}
	
	public function saveMessage($sMessage)
	{
		$this->_helper->FlashMessenger($sMessage);
	}
	
	public function redirectArray(array $redirectArray)
	{
		$sController = null;
		$sAction = null;
		$sModule = null;
		$aParams = array();
		if(isset($redirectArray['controller'])) {
			$sController = $redirectArray['controller'];
			unset($redirectArray['controller']);
		}
		if(isset($redirectArray['action'])) {
			$sAction = $redirectArray['action'];
			unset($redirectArray['action']);
		}
		if(isset($redirectArray['module'])) {
			$sModule = $redirectArray['module'];
			unset($redirectArray['module']);
		}
		$aParams = $redirectArray;
		$Redirector = $this->_helper->Redirector;
		$Redirector->gotoSimple($sAction, $sController, $sModule, $aParams);
	}
	
	public function redirectMessage($sMessage, $mRedirectTo = 'index')
	{
		$this->_helper->FlashMessenger($sMessage);
		if (is_array($mRedirectTo)) {
			$this->redirectArray($mRedirectTo);
		} else {
			$this->_helper->Redirector($mRedirectTo);
		}
	}
	
	protected function _getRequiredParam($sParam, $sMessage = '', $mRedirectTo = '')
	{
		$sParam = $this->_getParam($sParam, null);
		if (empty($sMessage)) {
			$sMessage = 'Empty required param: ' . $sParam;
		}
		if ($sParam == null) {
			if (empty($mRedirectTo)) {
				throw new Gecko_Exception($sMessage);
			} else {
				$this->_helper->FlashMessenger($sMessage);
				if (is_array($mRedirectTo)) {
					$sController = null;
					$sAction = null;
					$sModule = null;
					$aParams = array();
					if(isset($mRedirectTo['controller'])) {
						$sController = $mRedirectTo['controller'];
						unset($mRedirectTo['controller']);
					}
					if(isset($mRedirectTo['action'])) {
						$sAction = $mRedirectTo['action'];
						unset($mRedirectTo['action']);
					}
					if(isset($mRedirectTo['module'])) {
						$sModule = $mRedirectTo['module'];
						unset($mRedirectTo['module']);
					}
					$aParams = $mRedirectTo;
					$Redirector = $this->_helper->Redirector;
					$Redirector->gotoSimple($sAction, $sController, $sModule, $aParams);
				} else {
					$this->_helper->Redirector($mRedirectTo);
				}
			}
		}
		
		return $sParam;
	}
}