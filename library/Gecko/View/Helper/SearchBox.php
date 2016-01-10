<?php
class Gecko_View_Helper_SearchBox extends Zend_View_Helper_FormElement
{
	private function _addViewHelpers()
	{
		$view = $this->view;
		$view->headScript()->appendFile('/js/prototype.js')
						   ->appendFile('/js/gecko.select.js')
						   ->appendFile('/js/gecko.remote.js')
						   ->appendFile('/js/gecko.searchbox.js');
	}
	
	public function searchBox($name, $value = null, $attribs = null)
	{
		$this->_addViewHelpers();
		$info = $this->_getInfo($name, $value, $attribs);
		extract($info);
		
		$valueCallback = $attribs['valueCallback'];
		if (is_callable($valueCallback)) {
			$transformedValue = call_user_func($valueCallback, $value);
		}
		
		$containerName = "container_$name";
		$jsElementName = "search_$name";
		$url = $attribs['url'];
		if (is_array($url)) {
			$url = $this->view->url($url);
		}
		$script = "<div id=\"$containerName\"><input type=\"text\" name=\"$name\" value=\"$value\" id=\"$name\" /></div>\n";
		$script.= '<script type="text/javascript">';
		$script.= "Element.observe(window, 'load', function() {\n";
		$script.= "var $jsElementName = new Gecko.SearchBox('$name', '$containerName', '$url');\n";
		
		if (is_array($transformedValue) && (count($transformedValue) > 0)) {
			$encoded = Zend_Json::encode($transformedValue);
			$script.= "$jsElementName.setData($encoded);\n";
		}
		
		$script.= "$jsElementName.init();\n";
		$script.= "});";
		$script.= "</script>";
		
		return $script;
	}
}