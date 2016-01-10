<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once( 'Gecko/URL/Exception.php' );
require_once( 'Gecko/URL/Engine/Zend.php' );

/**
 * URL
 *
 * @package Gecko
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v2.0
 * @access public
 */
class Gecko_URL {
	/**
	 * URL Engine to use
	 *
	 * @see Gecko_URL_Engine_Interface
	 * @var unknown_type
	 */
	private static $urlEngine = null;
	
	/**
	 * Sets the default engine to use
	 *
	 * @param Gecko_URL_Engine_Interface $engine
	 */
	public static function setDefaultURLEngine(Gecko_URL_Engine_Interface $engine) {
		self::$urlEngine = $engine;
	}
	
	/**
	 * Returns the URL Engine
	 * 
	 * @return Gecko_Url_Engine_Interface
	 */
	public static function getEngine() {
		if( self::$urlEngine === null ) {
			self::$urlEngine = new Gecko_URL_Engine_Zend();
		}
		
		return self::$urlEngine;
	}
	
	/**
	 * Generates a URL to a self page
	 *
	 * @param array $params
	 * @param array $emptyQuery
	 * @return
	 **/
	public static function createURI( $url, $params = array() ) {
		if( !is_array( $params ) ) {
			throw new Gecko_URL_Exception('$params expected to be an array');
		}

		if( count( $params ) > 0 ) {
			$params = http_build_query( $params );
			$url .= "?" . $params;
		}

		return $url;
	}

	/**
	 * Generates a URL to a self page
	 *
	 * @param array $params The params to pass
	 * @param boolean $emptyQuery To reset the query
	 * @return
	 **/
	public static function getSelfURI( $params = array(), $emptyQuery = false ) {
		return self::getEngine()->getSelfURI( $params, $emptyQuery );
	}
	
	/**
	 * Gets a request parameter
	 *
	 * @param string $param
	 * @return mixed
	 */
	public static function getRequestParam($param) {
		return self::getEngine()->getParam( $param );
	}
}