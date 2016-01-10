<?php

abstract class Gecko_Service_Abstract implements Gecko_Service_Interface
{
	protected $request;
	protected $allowedMethods = array(
		'get' => false,
		'post' => false,
		'put' => false,
		'delete' => false
	);

	public function setRequest(Zend_Controller_Request_Abstract $Request)
	{
		$this->request = $Request;

		return $this;
	}

	public function getRequest()
	{
		return $this->request;
	}

	public function allow($method)
	{
		if (is_array($method)) {
			foreach ($method as $m) {
				$this->allowedMethods[$m] = true;
			}
		} else {
			$this->allowedMethods[$method] = true;
		}

		return $this;
	}

	public function isAllowed($method)
	{
		return $this->allowedMethods[$method];
	}

	/**
	 * @return array
	 **/
	public function getAction() { return array(); }
	/**
	 * @return array
	 **/
	public function postAction() { return array(); }
	/**
	 * @return array
	 **/
	public function putAction() { return array(); }
	/**
	 * @return array
	 **/
	public function deleteAction() { return array(); }
}