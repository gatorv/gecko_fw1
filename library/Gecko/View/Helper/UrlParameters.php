<?php
class Gecko_View_Helper_UrlParameters extends Zend_View_Helper_Abstract
{
	public function urlParameters($page)
	{
		$Front = Zend_Controller_Front::getInstance();
		$Request = $Front->getRequest();
		
		$aParams = array();
		foreach ($Request->getParams() as $sParam => $sValue) {
			if (!empty($sValue)) {
				$aParams[$sParam] = $sValue;
			}
		}
		$aParams['page'] = $page;
		
		return $this->view->url($aParams);
	}
}