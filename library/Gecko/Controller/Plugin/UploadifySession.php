<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * Gecko_Controller_Plugin_UploadifySession
 * 
 * Zend Controller Plugin for fixing the session id in uploadify, you need
 * to add this setting into your uploadify setup:
 * 
 * 'scriptData': {'PHPSESSID': '<?php echo session_id();?>'},
 * 
 * to send the SessionID into PHP/Zend to restart the session
 * 
 * It's better to put this plugin at the top of your stack to overcome Auth
 * Issues
 *
 * @package Gecko.Controller.Plugin;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @access public
 **/
class Gecko_Controller_Plugin_UploadifySession extends Zend_Controller_Plugin_Abstract
{
	/**
	 * Restore Session Upon Loading from Request
	 * 
	 * @param Zend_Controller_Request_Abstract $Request
	 * @return void
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $Request)
	{
		$phpSessId = $Request->getParam('PHPSESSID');
		if (!empty($phpSessId) && (session_id() != $phpSessId)) {
			//session_destroy();
			session_id($phpSessId);
			//session_start();
		}
	}
}