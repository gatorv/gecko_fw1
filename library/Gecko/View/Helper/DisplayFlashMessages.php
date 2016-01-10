<?php
class Gecko_View_Helper_DisplayFlashMessages extends Zend_View_Helper_Abstract
{
	private static $displayMode = 'jqueryui';

	public static function setDisplayMode($mode)
	{
		$validModes = array('jqueryui', 'alert', 'inline');
		if (!in_array($mode, $validModes)) {
			throw new DomainException("Invalid Mode $mode specified");
		}

		self::$displayMode = $mode;
	}

	public function displayFlashMessages()
	{
		$aMessages = $this->view->FlashMessages;
		$sMessageOutput = '';
		if (count($aMessages) > 0) {
			switch (self::$displayMode) {
				case 'jqueryui':
					$sMessageOutput = $this->renderJqueryUi($aMessages);
					break;
				case 'alert':
					$sMessageOutput = $this->renderAlert($aMessages);
					break;
				case 'inline':
					$sMessageOutput = $this->renderInline($aMessages);
					break;	
			}
		}

		return $sMessageOutput;
	}

	private function renderJqueryUi($messages)
	{
		$sMessageOutput = '<div id="flashM" title="Mensaje"><ul>';
		foreach ($messages as $sMessage) {
			$sMessageOutput .= '<li>' . $sMessage . '</li>';
		}
		$sMessageOutput .= '</ul></div>';
		
		$jQuery = $this->view->jQuery();
    	$jQuery->enable()->uiEnable(); // enable jQuery Core Library
		
		$jqHandler = ZendX_JQuery_View_Helper_JQuery::getJQueryHandler();
		$sJqueryScript = $jqHandler.'("#flashM").dialog({
			modal:true, 
			buttons: {
				Ok: function() {
					'.$jqHandler.'(this).dialog("close");
				}
			}
		});';
		
		$jQuery->addOnload($sJqueryScript);

		return $sMessageOutput;
	}

	private function renderAlert($messages)
	{
		$sMessageOutput = '';
		foreach ($messages as $sMessage) {
			$sMessageOutput .= '- ' . $sMessage . '\n';
		}
		
		$jQuery = $this->view->jQuery();
		
		$jqHandler = ZendX_JQuery_View_Helper_JQuery::getJQueryHandler();
		$sJqueryScript = 'alert("' . $sMessageOutput . '")';
		
		$jQuery->addOnload($sJqueryScript);

		return '';
	}

	private function renderInline($messages)
	{
		$sMessageOutput = '<div class="flash-messages"><ul>';
		foreach ($messages as $sMessage) {
			$sMessageOutput .= '<li>' . $sMessage . '</li>';
		}
		$sMessageOutput .= '</ul></div>';

		return $sMessageOutput;
	}
}