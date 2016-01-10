<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

require_once( 'Gecko/Request/Exception.php' );
require_once( 'Gecko/Request/Filter/Interface.php' );
require_once( 'Gecko/Request/Filter/MagicQuotes.php' );

/**
 * A Request Class to Filter User submitted input
 *
 * @package Gecko
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$
 * @access public
 */
class Gecko_Request implements ArrayAccess {
	private $input = array(); //@var private array Array wich holds the input variables
	private static $filters = array(); //@var private static Array with Filters

	/**
	 * Register a new Filter in the Input Variables
	 *
	 * @param object $filter A Initiated filter
	 * @return void
	 */
	public static function registerFilter( $filter ) {
		if( !( $filter instanceof Gecko_Request_Filter_Interface ) ) {
			throw new Gecko_Request_Exeption( '$filter doesnt implement interface GeckoRequestFilter' );
		}

		self::$filters[] = $filter;
	}

	/**
	 * Creates a new Filter implementing the Factory Pattern, can be used
	 * to create a Filter more quickly and in conjuction with registerFilter
	 * to add filters:
	 *
	 * <code>
	 *     GeckoRequest::registerFilter( GeckoRequest::createFilter( "RemoveXSS" ) );
	 * </code>
	 *
	 * @param string $filter The Filter to create
	 * @return object A new Filter
	 */
	public static function createFilter( $filter ) {
		$filterFile = "Gecko/Request/Filter/" . $filter . ".php";
		Gecko::loadClassFile( $filterFile );

		$filterClass = "Gecko_Request_Filter_$filter";

		return new $filterClass();
	}

	/**
	 * Creates a new instance of a Request Object, cleans and
	 * parses data as specified, by default it will filter:
	 * - Get
	 * - Post
	 * - Cookie
	 *
	 * You can supplu a custom order by setting the parameter to gpc,
	 * or cpg, pgc, etc.
	 *
	 * This function is ideal to work on input functions that you need
	 * to filter like $_GET, $_POST, or $_COOKIE.
	 *
	 * As a rule you shouldnt trust input from the user, so this function
	 * works as a fool proof mechanism to filter input variables.
	 *
	 * @param string $order The order to filter the input
	 */
	public function __construct( $order = 'gpc' ) {
		$places = array(
			"g" => "_GET",
			"p" => "_POST",
			"c" => "_COOKIE"
		);

		$order = preg_split('//', $order, -1, PREG_SPLIT_NO_EMPTY);
		foreach( $order as $current ) {
			if( !array_key_exists( $current, $places ) ) continue;

			$varArr = $places[$current];
			eval( '$arr = $' . $varArr . ';' );
			if( !is_array( $arr ) ) continue;

			foreach( $arr as $var => $value ) {
				if( is_array( $value ) ) {
					$this->input[$var] = self::workArray( $value );
				} else {
					foreach( self::$filters as $filter ) {
						$value = $filter->filterInput( $value );
					}
				}

				if( !isset( $this->input[$var] ) ) {
					$this->input[$var] = $value;
				}
			}
		}
	}

	/**
	 * Internal function for filtering arrays
	 *
	 * @param mixed $array The Array or String to Filter
	 * @return mixed The Array or String Filtered
	 */
	private static function workArray( $array ) {
		$output = null;
		if( is_array( $array ) ) {
			$output = array();
			foreach( $array as $key => $value ) {
				if( is_array( $value ) ) {
					$output[$key] = self::workArray( $value );
				} else {
					foreach( self::$filters as $filter ) {
						$value = $filter->filterInput( $value );
					}

					$output[$key] = $value;
				}
			}

			return $output;
		} else {
			foreach( self::$filters as $filter ) {
				$array = $filter->filterInput( $array );
			}

			return $array;
		}

		return null;
	}

	/**
	 * Magic PHP Function to read a variable
	 * ej:
	 * $request->var
	 *
	 * @param string $varname
	 * @return mixed The Var content
	 */
	public function __get( $varname ) {
		if( isset( $this->input[$varname] ) ) {
			return $this->input[$varname];
		}

		return null;
	}

	/**
	 * Magic PHP Function to unset a variable
	 *
	 * @param string $varname
	 * @return void
	 */
	private function __unset( $varname ) {
		if( isset( $this->input[$varname] ) ) {
			unset( $this->input[$varname] );
		}
	}

	/**
	 * ArrayAccess Used to check if a offset exists
	 *
	 * @param mixed $offset
	 * @return
	 */
	public function offsetExists( $offset ) {
		return (isset( $this->input[$offset] ) );
	}

	/**
	 * ArrayAccess Used to check if you can get a offset
	 *
	 * @param mixed $offset The offset to get
	 * @return mixed
	 */
	public function offsetGet( $offset ) {
		return $this->input[$offset];
	}

	/**
	 * ArrayAccess Used to set a Variable, Request variables
	 * are read-only
	 *
	 * @param string $offset The offset to set
	 * @param mixed $value The Value to set
	 * @return void
	 */
	public function offsetSet( $offset, $value ) {
		throw new Exception( "Input values can't be set, only read" );
	}

	/**
	 * ArrayAccess used to unset a request variable
	 *
	 * @param mixed $offset The offset to unset
	 * @return void
	 */
	public function offsetUnset( $offset ) {
		if( isset( $this->input[$offset] ) ) {
			unset( $this->input[$offset] );
		}
	}

	/**
	 * Static Function to get a single var from a request
	 *
	 * @param string $var The Variable to get
	 * @param string $place The place to search for
	 * @return mixed The found variable
	 */
	public static function getVar( $var, $place = "get" ) {
		$place 		= strtoupper( $place );
		$allowed	= array( "GET", "POST", "COOKIE", "REQUEST" );

		if( !in_array( $place, $allowed ) ) {
			throw new Gecko_Request_Exception( $place . " not in allowed places for extraction" );
		}

		eval( "\$place = \$_$place;" );
		$value = isset( $place[$var] ) ? $place[$var] : false;
		if( !$value ) {
			return null;
		}
		if( is_array( $value ) ) {
			return self::workArray( $value );
		} else {
			foreach( self::$filters as $filter ) {
				$value = $filter->filterInput( $value );
			}

			return $value;
		}
	}

	/**
	 * Returns the filtered POST values
	 *
	 * @return array The filtered values
	 */
	public static function getPost() {
		if( is_array( $_POST ) ) {
			return self::workArray( $_POST );
		} else {
			return array();
		}
	}

	/**
	 * Returns the filtered GET values
	 *
	 * @return array The filtered values
	 */
	public static function getGet() {
		if( is_array( $_GET ) ) {
			return self::workArray( $_GET );
		} else {
			return array();
		}
	}

	/**
	 * Returns a Request, it can extract, post, get, or request variables
	 *
	 * @param string $method The method to search (POST|GET|COOKIE|REQUEST
	 * @return array The filtered request
	 */
	public static function getRequest($method) {
		$place 		= strtoupper( $method );
		$allowed	= array( "GET", "POST", "COOKIE", "REQUEST" );

		if( !in_array( $place, $allowed ) ) {
			throw new Gecko_Request_Exception( $place . " not in allowed places for extraction" );
		}

		eval( "\$place = \$_$place;" );

		if( is_array( $place ) ) {
			return self::workArray( $place );
		} else {
			return array();
		}
	}
}