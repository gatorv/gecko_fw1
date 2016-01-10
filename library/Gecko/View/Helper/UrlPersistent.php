<?php
class Gecko_View_Helper_UrlPersistent extends Zend_View_Helper_Abstract
{
	public function urlPersistent(array $newParams = array())
	{
		$front = Zend_Controller_Front::getInstance();
		$request = $front->getRequest();
		$requestParams = $request->getParams();
		
		$params = array_merge($requestParams, $newParams);
		
		return $this->view->url($params);
	}
}