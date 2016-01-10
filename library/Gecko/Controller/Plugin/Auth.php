<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * Gecko_Controller_Plugin_Auth
 *
 * Zend Controller Plugin for managing authorization, uses Zend_Auth for
 * extracting the user info.
 * 
 * Must be subclassed to configure correctly
 *
 * @package Gecko.Controller.Plugin;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @access public
 **/
abstract class Gecko_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
	/**
	 * Default Whitelisted Resources
	 * @var array
	 */
	protected $whitelistedResources = array(
		'default:login:login',
		'default:login:logout'
	);
	protected $securedModules = array();
	protected $modulesAcl = array();
	protected $resources = array();
	protected $roles = array();
	protected $authSession = null;
	protected $error;
	protected $acl = null;
	protected $messages = array(
		'noAccess' => 'No tienes acceso a este recurso',
		'notLoggedIn' => 'Favor de iniciar sesión para entrar a esta área',
		'timedOut' => 'La sesión ha caducado, favor de volver a ingresar'
	);
	
	public function getIdentityRole()
	{
		// User Info
		$userInfo = Zend_Auth::getInstance()->getStorage()->read();
		$role = $userInfo->role;
		
		return $role;
	}
	
	public function getSecuredModulesWithAcl()
	{
		return $this->modulesAcl;
	}
	
	public function getRoles()
	{
		return $this->roles;
	}
	
	public function getResources()
	{
		return $this->resources;
	}
	
	public function getSecuredModules()
	{
		return $this->securedModules;
	}
	
	protected function addError($error)
	{
		$msg = $this->messages[$error];
		if (!empty($msg)) {
			$this->error = $msg;
		}
		
		return $this;
	}
	
	public function afterAuth() {}
	
	public function preDispatch(Zend_Controller_Request_Abstract $Request)
	{
		$sModule = $Request->getModuleName();
		$sController = $Request->getControllerName();
		$sAction = $Request->getActionName();
		
		$loggedIn = $this->checkLogin($Request);
		
		if (in_array($sModule, self::getSecuredModules()) && !$loggedIn) {
			return $this->_redirectToLogin($Request);
		} else {
			$this->getAuthSession()->lastTime = time();
		}
		
		if (in_array($sModule, self::getSecuredModulesWithAcl())) {
			$this->processAcl($Request);
			$this->afterAuth();
		}
	}
	
	public function processAcl(Zend_Controller_Request_Abstract $Request)
	{
		// Request Info
		$sModule = $Request->getModuleName();
		$sController = $Request->getControllerName();
		
		$role = $this->getIdentityRole();
		
		// Resource
		$resource = $sModule . ':' . $sController;
		if (!$this->checkAuth($role, $resource)) {
			$this->addError('noAccess');
			return $this->_redirectToLogin($Request);
		}
		
		Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl($this->getAcl());
		Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole($role);
	}
	
	public function getAuthSession()
	{
		return $this->authSession;
	}
	
	public function setAuthSession(Zend_Session_Namespace $session)
	{
		$this->authSession = $session;
		
		return $this;
	}
	
	abstract protected function buildAcl(Zend_Acl $Acl);
	
	protected function getAcl()
	{
		if ($this->acl != null) return $this->acl;
		
		$Acl = new Zend_Acl();
		$resources = $this->getResources();
		$roles = $this->getRoles();
		
		foreach ($roles as $role) {
			$Acl->addRole($role);
		}
		foreach ($resources as $resource) {
			$Acl->addResource($resource);
		}
		
		$this->buildAcl($Acl);
		
		$this->acl = $Acl;
		
		return $Acl;
	}
	
	protected function checkAuth($role, $resource)
	{
		return $this->getAcl()->isAllowed($role, $resource, 'view');
	}
	
	protected function checkLogin(Zend_Controller_Request_Abstract $Request)
	{
		$module = $Request->getModuleName();
		$controller = $Request->getControllerName();
		$action = $Request->getActionName();
		$resource = "{$module}:{$controller}:{$action}";
		
		$timeOut = Zend_Registry::get('session_timeout');
		$AuthSession = new Zend_Session_Namespace('auth_' . $module);
		$this->setAuthSession($AuthSession);
		
		if (in_array($resource, $this->whitelistedResources)) {
			return true;
		}
		
		$lastTime = $AuthSession->lastTime;
		$now 	  = time();
		$loggedIn = $AuthSession->loggedIn;
		$auth 	  = $AuthSession->identity;
		
		if (!$loggedIn) {
			$this->addError('notLoggedIn');
			return false;
		}
		if (!$lastTime || ($now > ($lastTime + $timeOut))) {
			$this->addError('timedOut');
			return false;
		}
		
		return true;
	}
	
	protected function _redirectToLogin(Zend_Controller_Request_Abstract $Request)
	{
		$Auth = Zend_Auth::getInstance();
		$Auth->clearIdentity();
		
		$session = $this->getAuthSession();
		$session->loggedIn = false;
		unset($session->lastTime);
		
		$sCurrentLocation = $Request->getRequestUri();
		$error = $this->error;
		$originalRequest = clone $Request;
		
		$Request->setControllerName('login')
				->setActionName('login')
				->setModuleName('default')
				->setParam('original_request', $originalRequest);
		
		if (!empty($error)) {
			$Request->setParam('error_message', $error);
		}
		
		if (!empty($sPreviousPlace)) {
			$Request->setParam('redirect_to', $sPreviousPlace);
		}
	}
}