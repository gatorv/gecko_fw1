<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once 'Zend/View/Helper/Abstract.php';

/**
 * Displays the flash messages using a Bootstrap Modal object
 *
 * @package Gecko;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2013
 * @access public
 **/
class Gecko_View_Helper_DisplayFlashMessagesBootstrap extends Zend_View_Helper_Abstract
{
	/**
	 * Generates the modal of the flash messages
	 * @return string
	 */
	public function displayFlashMessagesBootstrap()
	{
		$aMessages = $this->view->FlashMessages;
		
		if (count($aMessages) > 0) {
			$sMessageOutput = '<div id="flashMessages" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="Mensaje del sistema" aria-hidden="true">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
<h3>Mensaje del sistema</h3>
</div><div class="modal-body"><ul>';
			foreach ($aMessages as $sMessage) {
				$sMessageOutput .= '<li>' . $sMessage . '</li>';
			}
			$sMessageOutput .= '</ul> </div>
<div class="modal-footer">
<a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Cerrar</a>
</div>
</div>';
			$sJqueryScript = '$(document).ready(function() {';
			$sJqueryScript.= '	$("#flashMessages").modal();';
			$sJqueryScript.= '});';
			
			$this->view->headScript()->appendScript($sJqueryScript);
			
			return $sMessageOutput;
		}
	}
}