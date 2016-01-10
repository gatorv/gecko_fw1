<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once('Gecko/Exception.php');
require_once('Gecko/Utils.php');

/**
 * Base utilities class
 *
 * @package Gecko
 * @author Christopher Valderrama <gatorv@gmail.com>
 * @copyright Copyright (c) 2007-2008
 * @version $Id$
 * @access public
 */
class Gecko {
	const ASSETS_DIR = "/library/Gecko/Assets"; // @const Path to the Assets dir (must be public readable (www))
	const LIBRARY_DIR = "/home/chris/public_html/library/Gecko"; //@const Path to library

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

		throw new Gecko_Exception("$classpath wasn't found in current directories");
	}

	/**
	 * SPL Autoload
	 *
	 * @param string $class_name The class to load
	 */
	public static function autoload($class_name) {
		$libraryPath = self::LIBRARY_DIR;
		$filePath = str_replace("_", DIRECTORY_SEPARATOR, $class_name) . ".php";

		if(file_exists($libraryPath.$filePath)) {
			require_once( $libraryPath.$filePath );
			return;
		}

		$baseDir = Zend_Registry::get("baseDir");
		$file = $baseDir . "/application/" . str_replace("_", DIRECTORY_SEPARATOR, $class_name) . ".php";
		if( file_exists( $file ) ) {
			require_once( $file );
		} else {
			ob_start();
			var_dump(debug_backtrace());
			$back = ob_get_clean();
			Gecko_Utils::Error( "Attempted to Load a un-existant class ($class_name)\n$back" , "Internal Server Error" );
		}
	}
}
?>