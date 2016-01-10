<?php
class Gecko_Boostrap_Default extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initApplicationOptions()
	{
		$options = $this->getOptions();
		if (isset($options['default_page_size'])) {
			$page_size = $options['default_page_size'];
		} else {
			$page_size = 25;
		}
	
		if (isset($options['session_timeout'])) {
			$session_timeout = $options['session_timeout'];
		} else {
			$session_timeout = 1200;
		}
	
		if (isset($options['date_format'])) {
			$date_format = $options['date_format'];
		} else {
			$date_format = 'd-M-Y';
		}
		
		if (isset($options['time_format'])) {
			$time_format = $options['time_format'];
		} else {
			$time_format = 'h:i:s';
		}
		
		if (isset($options['timezone'])) {
			$timezone = $options['timezone'];
		} else {
			$timezone = 'America/Mexico_City';
		}
		
		
		date_default_timezone_set($timezone);
		Zend_Registry::set('timezone', $timezone);
		Zend_Registry::set('date_format', $date_format);
		Zend_Registry::set('time_format', $time_format);
		Zend_Registry::set('default_page_size', $page_size);
		Zend_Registry::set('session_timeout', $session_timeout);
	}
	
	protected function _initExtraOptions()
	{
		$options = $this->getOptions();
		if (!isset($options['extra'])) {
			return;
		}
	
		$extra = $options['extra'];
		Zend_Registry::set('extras', $extra);
	}
	
	protected function _initKeys()
	{
		$options = $this->getOptions();
		if (!isset($options['encodekey'])) {
			return;
		}
		
		$encode_key = $options['encodekey'];
		Zend_Registry::set('EncodeKey', $encode_key);
	}
	
	protected function setupApplication($namespace, $title, array $javascripts = array(), array $css = array())
	{
		Gecko_Model::setDefaultNamespace($namespace);
		
		$loader = Zend_Loader_Autoloader::getInstance();
		$autoloaders = $loader->getAutoloaders();
		foreach ($autoloaders as $loader) {
			$loader->addResourceType('row', 'models/Rows', 'Model_Row');
			//$loader->addResourceType('service', 'services', 'Service');
		}
		
		$this->bootstrap('view');
		$View = $this->getResource('view');
		
		$View->addHelperPath('ZendX/JQuery/View/Helper/', 'ZendX_JQuery_View_Helper');
		$View->addHelperPath(APPLICATION_PATH . '/views/helpers/', $namespace . '_View_Helper');
		$View->addHelperPath('Gecko/View/Helper/', 'Gecko_View_Helper');
		
		$View->doctype('HTML5');
		$View->headTitle($title)->setSeparator(' > ');
		
		$View->headMeta()->appendName('Content-type', 'text/html; charset=utf-8')
						 ->appendName('viewport', 'width=device-width, initial-scale=1');
		$View->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=utf-8');
		
		$defaultCss = array(
			'/css/normalize.css'
		);
		
		$css = array_merge($defaultCss, $css);
		
		foreach ($css as $cssPath) {
			$View->headLink()->appendStylesheet($cssPath);
		}
		
		$defaultJavascript = array(
			
		);
		
		$javascripts = array_merge($defaultJavascript, $javascripts);
		
		$View->jQuery()->setLocalPath('/js/jquery-1.11.1.min.js');
		foreach ($javascripts as $jsPath) {
			$View->jQuery()->addJavascriptFile($jsPath);
		}
	}
	
	protected function _initLogger()
	{
		switch(APPLICATION_ENV) {
			case 'production':
			case 'staging':
			case 'testing':
				$logger = new Zend_Log();
				
				$writer = new Zend_Log_Writer_Stream(APPLICATION_PATH . '/error.log');
				$logger->addWriter($writer);
				
				$filter = new Zend_Log_Filter_Priority(Zend_Log::CRIT);
				$logger->addFilter($filter);
				break;
			case 'development':
				$writer = new Zend_Log_Writer_Stream(APPLICATION_PATH . '/error_dev.log');
				$logger = new Zend_Log($writer);
				break;
		}
	
		Zend_Registry::set('logger', $logger);
	}
	
	protected function _initDbTableCache()
	{
		// Fronted cache options
		$frontendOptions = array(
			'lifetime' => 7200, // cache lifetime of 2 hours
			'automatic_serialization' => true
		);
	
		// Backend Cache Options
		$backendOptions = array(
			'cache_dir' => APPLICATION_PATH .'/cache/' // Directory where to put the cache files
		);
	
		// Zend_Cache object
		$cache = Zend_Cache::factory(
			'Core',
			'File',
			$frontendOptions,
			$backendOptions
		);
		
		Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
	}
	
	protected function _initSmtp()
	{
		$options = $this->getOptions();
		if (!isset($options['mail']) && !isset($options['smtp'])) {
			return;
		}
		
		$mailOptions = $options['mail'];
		$smtpOptions = $options['smtp'];
	
		$config = array(
			'auth' => $smtpOptions['auth'],
			'username' => $smtpOptions['username'],
			'password' => $smtpOptions['password'],
			'port' => $smtpOptions['port']
		);
		if (isset($smtpOptions['ssl']) && !empty($smtpOptions['ssl'])) {
			$config['ssl'] = $smtpOptions['ssl'];
		}
	
		Zend_Registry::set('mail_options', $mailOptions);
	
		switch(APPLICATION_ENV) {
			case 'production':
			case 'staging':
				$tr = new Zend_Mail_Transport_Smtp($smtpOptions['server'], $config);
				break;
			case 'testing':
			case 'development':
				$tr = new Zend_Mail_Transport_File(array(
					'path' => APPLICATION_PATH . '/sentmail',
					'callback' => function($transport) {
						return 'mail_' . mt_rand() . '.tmp';
					}
				));
				break;
		}
	
	
		Zend_Mail::setDefaultTransport($tr);
	}
}