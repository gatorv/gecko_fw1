<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

/**
 * Utilities class
 *
 * @package Gecko
 * @author Christopher Valderrama <gatorv@gmail.com>
 * @copyright Copyright (c) 2007-2008
 * @version $Id$
 * @access public
 */
class Gecko_Utils {
	/**
	 * Cleans a Array for duplicates
	 *
	 * @param array $array The array to clean
	 * @return array
	 */
	public static function cleanArray( $array ) {
		$array = self::cleanArraySingles( $array );

		$array = self::cleanArrayEmpty( $array );

		return $array;
	}

	/**
	 * Cleans a array from empty elements
	 *
	 * @param array $array The array to clean
	 * @return array
	 */
	public static function cleanArrayEmpty( $array ) {
		$return = NULL;

		if( is_array( $array ) ) {
			$return = array();
			foreach( $array as $key => $value ) {
				if( !empty( $value ) ) {
					$return[$key] = self::cleanArrayEmpty($value);
				}
			}
		} else {
			$return = $array;
		}

		return $return;
	}

	/**
	 * Cleans a array for single elements
	 *
	 * @param array $array The array to clean
	 * @return array
	 */
	public static function cleanArraySingles( $array ) {
		$result = NULL;

		if( is_array( $array ) && ( count( $array ) == 1 ) ) {
			$key = array_keys( $array );
			$theKey = $key[0];
			if( is_integer( $theKey ) ) {
				$result = self::cleanArraySingles( $array[0] );
			} else {
				$result[$theKey] = self::cleanArraySingles( $array[$theKey] );
			}
		} else {
			if( is_array( $array ) ) {
				$result = array();
				foreach( $array as $key => $value ) {
					$result[$key] = self::cleanArraySingles( $value );
				}
			} else {
				$result = $array;
			}
		}

		return $result;
	}

	/**
	 * Transforms a object to an array
	 *
	 * @param mixed $object The object to transform
	 * @param array $array_fields The array of fields
	 * @return array
	 */
	public static function object2Array( $object, $array_fields = array() ) {
		$return = NULL;

		if( is_object( $object ) ) {
			$return = array();
			$vars = get_object_vars($object);
			foreach( $vars as $key => $value ) {
				$val = self::object2Array( $value, $array_fields );
				if( in_array( $key, $array_fields ) ) {
					$return[] = self::object2Array( $val, $array_fields );
				} else {
					$return[$key] = $val;
				}
			}
		} else {
			if( is_array( $object ) ) {
				if( count( $object ) == 0 ) {
					$return = "";
				} else {
					$return = array();
					foreach( $object as $key => $value ) {
						$return[$key] = self::object2Array( $value, $array_fields );
					}
				}
			} else {
				$return = $object;
			}
		}

		return $return;
	}

	/**
	 * Lists a Directory
	 *
	 * @param string $dir The dir to list
	 * @param array $extensions The extensions to filter
	 * @return array of Files
	 */
	public static function list_dir( $dir, $extensions =null ) {
		if( !is_dir( $dir ) ) {
			throw new Exception( $dir . " not a Directory" );
		}

		if( substr( $dir, strlen( $dir ) - 1, 1 ) !== "/" ) {
			$dir .= "/";
		}

		$d = opendir( $dir );
		$files = array();
		while ( ( $file = readdir($d)) !== false ) {
			if( ( $file != "." ) && ( $file != ".." ) ) {
				if( is_file( $dir . $file )  ) {
					if( isset( $extensions ) ) {
						if( is_array( $extensions ) && ( in_array( self::fileExtension( $file ), $extensions ) ) ) { // extension_array
							$files[] = $file;
						} else { // single extension
							if( $extensions == self::fileExtension( $file ) ) {
								$files[] = $file;
							}
						}
					} else {
						$files[] = $file;
					}
				}
			}
		}
		closedir($d);

		return $files;
	}

	/**
	 * Returns the extension of a file
	 *
	 * @param string $file The file
	 * @return string
	 */
	public static function fileExtension( $file ) {
		$fext = substr( $file, strrpos( $file, '.' ) + 1 );

		return $fext;
	}

	/**
	 * Nice printing of a variable, or variables
	 *
	 */
	public static function printvar() {
		$vars = func_get_args();
		echo "<pre>";
		foreach( $vars as $var )
			var_dump( $var );
		echo "</pre>";
	}

	/**
	 * Now time
	 */
	public static function now() {
			list($usec, $sec) = explode(" ", microtime());
			return ((float)$usec + (float)$sec);
	}

	/**
	 * Creates a error message
	 *
	 * @param string $msg
	 * @param string $log_msg
	 * @return void
	 **/
	public static function Error( $msg, $title = "", $log_msg = "" ) {
		ob_end_clean();
		$cfg = Zend_Registry::get("config");
		$baseDir = Zend_Registry::get("baseDir");
		$error = $baseDir . $cfg->Template->error;
		if( empty( $error ) ) {
			$template = Gecko_Router::LIBRARY_DIR . "/Assets/files/error.php";
		} else {
			$template = $error;
		}

		if( !empty( $log_msg ) ) {
			Gecko_Log::getInstance()->Log($log_msg, Zend_Log::DEBUG);
		}

		if( empty( $title ) ) {
			$title = "Critical Error";
		}

		$output = Gecko_Template::renderTemplate( $template, array( "title" => $title, "error" => $msg ), true );

		die( $output );
	}

	/**
	 * Loads a file into the scope
	 *
	 * @param string $file
	 * @return string
	 */
	public static function loadFile( $file ) {
		if( !file_exists( $file ) ) {
			throw new Exception( "$file file not found" );
		}

		$buff = file_get_contents( $file );

		return addslashes( $buff );
	}

	/**
	 * Saves a message in the session Buffer for later retreival
	 *
	 * @param string $msg
	 * @param string $debug
	 * @return void
	 */
	public static function saveMsg( $msg, $debug = "" ) {
		$msg = str_replace( "\n", "\\n", $msg );

		$_SESSION['GeckoSpMsg'] = $msg;
		if( !empty( $debug ) ) Gecko_Utils::Log( $debug );
	}

	/**
	 * Displays a session message via a Popup
	 *
	 * @return string
	 */
	public static function displayMsg() {
		if( !isset( $_SESSION ) ) return "";

		$msg = $_SESSION['GeckoSpMsg'];
		unset( $_SESSION['GeckoSpMsg'] );

		if( empty( $msg ) ) return "";

		$html = "window.alert(\"$msg\");\n";

		$html = Gecko_HTML::getJavaScriptTag( $html );



		return $html;
	}

	/**
	 * Redirects to a new place
	 *
	 * @param string $uri
	 * @return void
	 **/
	public static function Redirect( $uri ) {
		if( headers_sent() ) {
			throw new Exception( "Headers Already Sent! Cannot redirect, please check your code." );
		}
		header( "Location: $uri" );
		exit();
	}

	/**
	 * Redirects inside a controller
	 *
	 * @param string $uri
	 * @return void
	 **/
	public static function ControllerRedirect( $place ) {
		$cfg = Zend_Registry::get("config");

		if(!($cfg instanceof Zend_Config )) {
			throw new Exception( "Configuration not set, maybe not in router, check code." );
		}

		$location = $cfg->pageRootURL . $place;

		if( headers_sent() ) {
			throw new Exception( "Headers Already Sent! Cannot redirect, please check your code." );
		}

		header( "Location: $location" );
		exit();
	}

	/**
	 * Logs a error Message
	 *
	 * @param string $string
	 * @param integer $type
	 * @return void
	 **/
	public static function Log( $string ) {
		$log = GeckoLog::getInstance();
		$log->Log( $string, Zend_Log::DEBUG );
	}
}
?>