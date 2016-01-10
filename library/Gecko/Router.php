<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once( 'Gecko/Request.php' );
require_once( 'Zend/Loader.php' );

/**
 * Base MVC Router
 *
 * @package Gecko
 * @author Christopher Valderrama <gatorv@gmail.com>
 * @copyright Copyright (c) 2007-2008
 * @version $Id$
 * @access public
 */
class Gecko_Router {
	const ASSETS_DIR = "/library/Gecko/Assets"; // @const Path to the Assets dir (must be public readable (www))
	const LIBRARY_DIR = "C:\WebRoot\htdocs\library\Gecko"; //@const Path to library

	private $controllerDir = "/application/controllers/"; //@var private string Directory where Controllers are loaded (from Base)
	private $viewDir = "/application/views/"; //@var private string Directory where Views are loaded (from Base)
	private $view = null; //@var private object Holds the view object
	private $currentController = null; //@var private object Hold the current Controller
	private $currentAction = ''; //@var private string Holds the current Action
	private $controllerName = ''; //@var private string Holds the current Controller Name
	public static $DEBUG = false; //@var public static Holds if the App is in Debug Mode

	/**
	 * Initiates the object
	 * @access public
	 **/
	public function __construct() {
		Zend_Loader::registerAutoload();
	}

	/**
	 * This function serves to setup the application, can be overriden
	 * to setup custom settings..
	 *
	 * @access protected
	 * @return void
	 */
	protected function setup() {
		/** Set Debug to TRUE **/
		self::$DEBUG = true;

		/** Read Base dir and save **/
		$baseDir = getcwd();

		/** Setup Log **/
		$logFile = $baseDir . "/log/messages.log";
		Gecko_Log::setLogWriter(new Zend_Log_Writer_Stream($logFile));

		/** Get Registry Instance **/
		$registry = Zend_Registry::getInstance();
		$registry['baseDir'] = $baseDir;

		/** Set Config File and create Config parser **/
		$configFile = $baseDir . "/config/config.xml";
		$config = new Zend_Config_Xml($configFile, 'staging');

		/** Save Config to Registry **/
		$registry['config'] = $config;

		/** Create DB if we can... **/
		try {
			$db = Gecko_DB::getInstance();
		} catch( Zend_Db_Exception $zde ) {
			// There isn't a DB Settings.. silently omit...
			Gecko_Log::getInstance()->log("No database settings found", Zend_Log::INFO);
			Gecko_Log::getInstance()->log($zde->getMessage(), Zend_Log::INFO);
		}

		/** Register filters for request parameters **/
		require_once("Gecko/Request/Filter/MagicQuotes.php");
		Gecko_Request::registerFilter( new Gecko_Request_Filter_MagicQuotes() );

		header("Cache-control: private"); // IE6 Session Fix

		/** Save a copy of the base router on registry **/
		Zend_Registry::set("base", $this);

		/** Setup Complete **/
		Gecko_Log::getInstance()->log("Router Initialized", Zend_Log::INFO);
	}

	/**
	 * This function serves to shutdown the application, can be overriden
	 * to add custom settings..
	 *
	 * @access protected
	 * @return void
	 */
	protected function shutdown() {}

	/**
	 * Main function, acts as a dispatcher, reads the controller
	 * and actions from the request URI, and calls the correct
	 * function, and renders the data.
	 *
	 * @access private
	 * @return void
	 **/
	private function run() {
		/** Load Config from cache **/
		$cfg = Zend_Registry::get("config");
		if(!($cfg instanceof Zend_Config)) {
			throw new Exception("Config not set, check your setup");
		}

		/** Get Controller from URI **/
		$controller = Gecko_Request::getVar( "controller" );
		$controller = ( isset( $controller ) ? $controller : 'index' );

		/** Clean up Controller **/
		if( strpos( $controller, "/" ) !== false ) {
			$temp = explode( "/", $controller );
			$controller = $temp[0];
		}
		if( strpos( $controller, ".php" ) !== false ) { // weird htaccess error with index controller
			$controller = str_replace( ".php", "", $controller );
		}

		/** Get Action from URI **/
		$action = Gecko_Request::getVar( "action" );

		if( !$action && !empty( $temp[1] ) ) {
			$action = $temp[1];
		} elseif( !$action ) {
			$action = "index";
		}

		/** Check we have a valid value for controller and action **/
		if( !isset($controller) || !isset($action) ) {
			throw new Exception( "Controller and Action missing, please check your request URI" );
		}

		/** Save values **/
		$this->controllerName = $controller;
		$this->currentAction = $action;

		/** Load Controller **/
		$controller = $this->loadController( $controller );
		$this->currentController = $controller;

		/** Create a input request object **/
		$request = new Gecko_Request();

		/** Call Correct Action **/
		$CallAction = $action . "Action";
		/** All actions are called with the original $request parameter see GeckoGPC for more info **/
		if( !is_callable( array( $controller, $CallAction ) ) ) {
			$controller->noRoute( $request ); /* call noRoute if Action is not callable */
		} else {
			$controller->$CallAction( $request ); /** or call action **/
		}

		/** Load View from Controller **/
		$view = $this->loadView( $controller->getView() );
		$view->setVars( $controller->getVars() );

		/** Set Correct Layout **/
		if( $controller->isLayoutSet() ) {
			$view->setLayout( $controller->getLayout() );
		}

		/** Set Correct Template **/
		if( $controller->isTemplateSet() ) {
			$view->setTemplate( $controller->getTemplate() );
		} else {
			$view->setTemplate( $action );
		}

		/** Save View **/
		$this->view = $view;

		/** Load Config Template Helpers **/
		$helpers = $cfg->TemplateHelpers->toArray();

		if( is_array( $helpers ) ) {
			foreach( $helpers as $helperFile ) {
				$view->registerHelper( $helperFile );
			}
		} elseif( !empty( $helpers ) ) {
			$view->registerHelper( $helpers );
		}

		/** Finally Render data and output to browser or osd **/
		$view->render();

		/** Cleanup and quit application **/
		$controller = null;
		$view = null;
	}

	/**
	 * Main Entry point, this function should be called from the
	 * boot file to begin the application
	 *
	 * @access public static
	 * @return void
	 **/
	public function dispatch() {
		try {
			$this->setup();
			$this->run();
			$this->shutdown();
		} catch( Zend_Log_Exception $zle ) {
			echo "[INIT ERROR] ";
			echo $zle->getMessage();
		} catch( Exception $e ) {
			if( self::$DEBUG ) {
				Gecko_Utils::Error( $e->getMessage() . "<br />Trace: " . $e->getTraceAsString(), "Uncaught Exception", $e->getMessage() . "\nTrace\n" . $e->getTraceAsString() );
			} else {
				Gecko_Log::getInstance()->log( $e->getMessage() . "\nTrace:\n" . $e->getTraceAsString(), Zend_Log::DEBUG );
				Gecko_Utils::Error( "Internal Server Error", "Internal Server Error" );
			}
		}
	}

	/**
	 * Returns a unique link to the current page, rewriting
	 * current controller and action and generating a valid
	 * XHTML string to print out in a HTML page
	 *
	 * @access public static
	 * @return void
	 **/
	public static function PHP_SELF() {
		return Gecko_Utils::genSURI();
	}

	/**
	 * This function will search the class path and then will try to include
	 * the required class
	 *
	 * @param string $classpath The Class Path to search
	 * @return boolean
	 */
	public static function loadClassFile( $classpath ) {
		$path = get_include_path();
		$dirs = explode(PATH_SEPARATOR, $path);

		foreach ($dirs as $dir) {
			if( file_exists($dir . DIRECTORY_SEPARATOR . $classpath) ) {
				require_once( $dir . DIRECTORY_SEPARATOR . $classpath );
				return true;
			}
		}

		throw new Exception("$classpath wasn't found in current directories");
	}

	/**
	 * Returns the active View Object used to render the
	 * pages to the browser
	 *
	 * @access public
	 * @return object The used View (commonly GeckoView)
	 **/
	public function getView() {
		return $this->view;
	}

	/**
	 * Returns the current Action fired up by the request
	 * URI
	 *
	 * @access public
	 * @return string The current action
	 **/
	public function getAction() {
		return $this->currentAction;
	}

	/**
	 * Returns the current controller Name
	 *
	 * @access public
	 * @return string The controller Name
	 **/
	public function getControllerName() {
		return $this->controllerName;
	}

	/**
	 * Returns the current loaded controller
	 *
	 * @access public
	 * @return object The Active Controller
	 **/
	public function getController() {
		return $this->currentController;
	}

	/**
	 * This method searches the controller path, and
	 * loads the requested controller
	 *
	 * @access private
	 * @param string $controller
	 * @return object the Loaded controller
	 * @throws Exception if a error occours
	 **/
	private function loadController( $controller ) {
		$baseDir = Zend_Registry::get("baseDir");
		$conFile = $baseDir . $this->controllerDir . "/$controller.php";

		if( !file_exists( $conFile ) ) {
			throw new Exception( "$controller controller file not found" );
		}

		/** Include controller File and Dismiss any output **/
		@ob_start();
		include( $conFile );
		@ob_end_clean();

		$controller = $controller . "Controller";
		if( !class_exists( $controller, false ) ) {
			throw new Exception( "$controller controller class not found, but file was included" );
		}

		$controller = new $controller();
		if( !$controller instanceof Gecko_Controller ) {
			throw new Exception( "Controller isn't a instance of GeckoController, check your code" );
		}

		return $controller;
	}

	/**
	 * This method loads the view set by the controller
	 * and renders the data into the browser
	 *
	 * @param string $view
	 * @return object Loaded view
	 * @access private
	 * @throws Exception on error
	 **/
	private function loadView( $view ) {
		if( empty( $view ) ) {
			throw new Exception( "No View Returned from controller" );
		}

		if( $view == "Gecko_View" ) {
			return new Gecko_View();
		}

		$baseDir = Zend_Registry::get("baseDir");
		$viewFile = $baseDir . $this->viewDir . "/$view.php";

		if( !file_exists( $viewFile ) ) {
			throw new Exception( "$view view file not found (named $view.php)" );
		}

		@ob_start();
		include( $viewFile );
		@ob_end_clean();

		$view = $view . "View";
		if( !class_exists( $view, false ) ) {
			throw new Exception( "$view controller class not found" );
		}

		$view = new $view();
		if( !$view instanceof Gecko_View ) {
			throw new Exception( "View isn't a instance of GeckoView, check your code" );
		}

		return $view;
	}
}

/**
 * import
 *
 * This function is for easier package handling
 * for example suppose you save all of your work
 * in the application dir like this:
 * com /
 *    your_company /
 *                models /
 *                      - client.php
 *                      - supplier.php
 *                      - products.php
 *                - base.php
 *                - supplier_search.php
 *
 * This function makes easy to import one or more
 * classes when using the controller:
 *
 * Include "client" -> import( 'com.your_company.models.client' );
 * Include all models -> import( 'com.your_company.models.*' );
 *
 * @param string $class
 * @throw Exception on error
 * @return void
 **/
function import( $class ) {
	$baseDir = Zend_Registry::get("baseDir");

	$importDir = $baseDir . "/application/";
	$classDir = str_replace( ".", DIRECTORY_SEPARATOR, $class );

	if( substr( $classDir, -1, 1 ) === "*" ) { // Import several Classes
		$classDir = str_replace( "*", "", $classDir );
		$importDir .= $classDir;
		$files = Gecko_Utils::list_dir( $importDir, array( "php" ) );
		foreach( $files as $file ) {
			require_once( $importDir . $file );
		}
	} else {
		$file = $importDir . $classDir . '.php';
		if( file_exists( $file ) ) {
			require_once( $file );
		} else {
			throw new Exception( "$class not found" );
		}
	}
}
