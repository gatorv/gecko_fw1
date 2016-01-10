<?php

interface Gecko_Service_Interface
{
	public function setRequest(Zend_Controller_Request_Abstract $Request);
	public function allow($method);
	public function isAllowed($method);
	/**
	 * @return array
	 **/
	public function getAction();
	/**
	 * @return array
	 **/
	public function postAction();
	/**
	 * @return array
	 **/
	public function putAction();
	/**
	 * @return array
	 **/
	public function deleteAction();
}