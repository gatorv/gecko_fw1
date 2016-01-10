<?php

class Gecko_Controller_Service extends Gecko_Controller
{
	protected $allowedMethods = array(
		'get', 'post', 'put', 'delete'
	);
	public function indexAction()
	{
		$Request = $this->getRequest();
		$service = ucfirst(strtolower($Request->getParam('service')));

		$namespace = $this->getAppNamespace();
		
		$className = $namespace . '_Service_' . $service;
		$class = new $className();
		if (!$class instanceof Gecko_Service_Interface) {
			throw new Exception('Invalid Service Class');
		}
		$class->setRequest($Request);
		$method = strtolower($Request->getMethod());
		if (!in_array($method, $this->allowedMethods) || !$class->isAllowed($method)) {
			$Response = $this->getResponse();
			$Response->setHttpResponseCode(405);
			$Response->setHeader('Content-Type', 'text/plain', true);
			$Response->setBody('NOT ALLOWED');

			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();

			return $Response;
		}

		$method = "{$method}Action";
		$response = call_user_func_array(array($class, $method), array());
		
		return $this->_helper->json($response);
	}

	protected function getAppNamespace()
	{
		$bootstrap = $this->getInvokeArg('bootstrap');
		return $bootstrap->getAppNamespace();
	}
}