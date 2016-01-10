<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once 'Zend/Controller/Action.php';

/**
 * Gecko_Controller_Login
 * 
 * Abstract controller that can handle login/logout tasks, must
 * be subclassed to configure
 *
 * @package Gecko.Controller;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @access public
 **/
abstract class Gecko_Controller_Login extends Gecko_Controller
{
	protected $sessionAuthNameTemplate = 'auth_%s';
	protected $loginForm = null;
	protected $authPluginClass = 'Gecko_Controller_Plugin_Auth';
	protected $requestedModule = '';
	protected $requestedController = '';
	protected $requestedAction = '';
	protected $identity;
	
	/**
	 * Return the correct Zend_Auth_Adapter based on the module
	 * name.
	 * 
	 * @param string $module
	 */
	abstract protected function getAdapter($module = '');
	/**
	 * Perform login setup
	 */
	abstract protected function setup();
	/**
	 * Perform logout actions
	 */
	abstract protected function logout();
	
	public function init()
	{
		parent::init();
		
		$this->loginForm = new Gecko_Form_Login();
		$this->setup();
	}

	public function setRequestedModule($module)
	{
		$this->requestedModule = $module;

		return $this;
	}

	public function getRequestedModule()
	{
		return $this->requestedModule;
	}

	public function setRequestedController($controller)
	{
		$this->requestedController = $controller;

		return $this;
	}

	public function getRequestedController()
	{
		return $this->requestedController;
	}

	public function setRequestedAction($action)
	{
		$this->requestedAction = $action;

		return $this;
	}

	public function getRequestedAction()
	{
		return $this->requestedAction;
	}
	
	private function getAuthSession($module)
	{
		$sessionName = sprintf($this->sessionAuthNameTemplate, $module);
		$session = new Zend_Session_Namespace($sessionName);
	
		return $session;
	}
	
	public function loginAction()
	{
		$Auth				= Zend_Auth::getInstance();
		$Request			= $this->getRequest();
		$Redirector			= $this->_helper->getHelper('Redirector');
		$sErrorMessage		= $Request->getParam('error_message');
		$sPreviousLocation	= $Request->getParam('redirect_to');
		$originalRequest	= $Request->getParam('original_request');
		$originalModule		= $originalRequest->getModuleName();
		$originalController = $originalRequest->getControllerName();
		$originalAction     = $originalRequest->getActionName();
		$session			= $this->getAuthSession($originalModule);

		$this->setRequestedModule($originalModule);
		$this->setRequestedController($originalController);
		$this->setRequestedAction($originalAction);
		
		if (!$Request->has('original_request')) {
			throw new \RuntimeException('No se puede accesar de forma directa');
		}
		
		$this->preLogin();
	
		if ($session->loggedIn == true) {
			$Redirector->gotoSimple('index', 'index', $originalModule);
		}
		
		$this->view->error_message = $sErrorMessage;
	
		if (!($this->loginForm instanceof Zend_Form)) {
			throw new \RuntimeException('Incorrect login form, must belong to Zend_Form');
		}
		$LoginForm = $this->loginForm;
	
		if (!empty($sPreviousLocation)) {
			$LoginForm->redirect_to->setValue($sPreviousLocation);
		}
	
		if ($Request->isPost()) {
			if ($LoginForm->isValid($Request->getPost())) {
				$aValues = $LoginForm->getValues();
				$authAdapter = $this->getAdapter($originalModule);
				$authAdapter->setIdentity($aValues['username'])
							->setCredential($aValues['password']);
				
				$Result = $Auth->authenticate($authAdapter);
				if (!$Result->isValid()) {
					switch ($Result->getCode()) {
						case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
							$this->view->error_message = 'No se encontró el usuario seleccionado';
							break;
						case Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS:
							$this->view->error_message = 'Más de un usuario encontrado';
							break;
						case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
							$this->view->error_message = 'Contraseña incorrecta';
							break;
						default:
							$this->view->error_message = implode('<br />', $Messages);
							break;
					}
					$this->view->error_class = ' alert-error';
				} else {
					$this->preAuth();
					
					$Storage = $Auth->getStorage();
					if ($authAdapter instanceof Zend_Auth_Adapter_DbTable) {
						$identity = $authAdapter->getResultRowObject(null, 'password');
					} else {
						$identity = $authAdapter->getIdentity();
					}
					$this->identity = $identity;
					
					$this->afterAuth();
						
					$session->loggedIn = true;
					$session->lastTime = time();
					$session->identity = $identity;
						
					if (!empty($sPreviousLocation)) {
						$Redirector->gotoUrl($sPreviousLocation);
					} else {
						$Redirector->gotoSimple('index', 'index', $originalModule);
					}
				}
			}
		}
	
		$this->view->login = $LoginForm;
	}
	
	protected function getIdentity()
	{
		return $this->identity;
	}
	
	protected function preLogin()
	{
		
	}
	
	/**
	 * Called before performing the authentication
	 */
	protected function preAuth()
	{
		
	}
	
	/**
	 * Called after a successfull authentication
	 */
	protected function afterAuth()
	{
		
	}
	
	public function logoutAction()
	{
		$Auth = Zend_Auth::getInstance();
		$Auth->clearIdentity();
		$fc = Zend_Controller_Front::getInstance();
		
		if ($fc->hasPlugin($this->authPluginClass)) {
			$auth = $fc->getPlugin($this->authPluginClass);
			
			foreach ($auth->getSecuredModules() as $module) {
				$this->getAuthSession($module)->loggedIn = false;
			}
		}
		
		$this->logout();
	}
}