<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * Gecko_Controller_Plugin_ExcelExport
 *
 * Zend Controller Plugin for exporting the body content as excel
 *
 * @package Gecko.Controller.Plugin;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @access public
 **/
class Gecko_Controller_Plugin_ExcelExport extends Zend_Controller_Plugin_Abstract
{
	private $_exportName = '';
	
	public function __construct($sExportName)
	{
		$this->_exportName = $sExportName;
	}
	
	public function dispatchLoopShutdown()
	{
		$sExportName = $this->_exportName;
		$Response = $this->getResponse();
		$Response->setHeader('Pragma', 'Public')
				 ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
				 ->setHeader('Cache-Control', 'pre-check=0, post-check=0, max-age=0')
				 ->setHeader('Pragma', 'no-cache')
				 ->setHeader('Expires', '0')
				 ->setHeader('Content-Transfer-Encoding', 'binary')
				 ->setHeader('Content-Type', 'application/vnd.ms-excel; charset=ISO-8859-1')
				 ->setHeader('Content-Disposition', "attachment; filename=\"$sExportName\"");

		$Response->setBody(utf8_decode($Response->getBody()));
	}
}