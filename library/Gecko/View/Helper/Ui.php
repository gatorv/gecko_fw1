<?php
class Gecko_View_Helper_Ui extends Zend_View_Helper_Abstract
{
	public function ui()
	{
		return $this;
	}
	
	public function highlight($message)
	{
		return "<div class=\"ui-widget\"><div class=\"ui-state-highlight ui-corner-all\"><p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;\"></span>$message</p></div></div>";
	}
	
	public function error($message)
	{
		return "<div class=\"ui-widget\"><div class=\"ui-state-error ui-corner-all\"><p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;\"></span>$message</p></div></div>";
	}
	
	public function createWindow($title, $width = "90%")
	{
		return "<div class=\"ui-widget ui-corner-all ui-widget-content\" style=\"$width\"><div class=\"ui-widget-header ui-corner-all ui-helper-clearfix ui-padded\"><span class=\"ui-dialog-title\">$title</span></div><div class=\"ui-dialog-content ui-widget-content ui-padded\">";
	}
	
	public function closeWindow()
	{
		return "</div></div>";
	}
	
	public function icon($url, $label, $icon, $class='', $id='')
	{
		$uri = $this->view->url($url);
		if (empty($id)) {
			$id = 'element_' . uniqid();
		}
		
		return "<a href=\"$uri\" id=\"$id\" class=\"$class\"><span class=\"ui-icon $icon\" style=\"float: left; margin-righ: .3em;\"></span>&nbsp;$label</a>";
	}
}