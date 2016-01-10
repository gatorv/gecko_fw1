<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once('Gecko/URL/Engine/Interface.php');

/**
 * Gecko Framework URL Engine creator
 *
 * @package Gecko.URL.Engine;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v2.0
 * @access public
 */
class Gecko_URL_Engine_Gecko implements Gecko_URL_Engine_Interface {
	/**
	 * Creates a URL to the same page
	 *
	 * @param array $params The params to pass
	 * @param bool $emptyQuery To reset or not the query
	 * @return string
	 */
	public function getSelfURI($params = array(), $emptyQuery = false) {
		$outSeparator = "&amp;";
		parse_str( $_SERVER['QUERY_STRING'], $qString );

		if( Zend_Registry::isRegistered("base") ) {
			$base = Zend_Registry::get("base");
			$cfg = Zend_Registry::get("config");
			$controller = $base->getControllerName();
			$action = $base->getAction();
			$destination = $cfg->pageRootURL . "/";
			$end = $cfg->URITermination;
			$terminator = "/";
			if( $end ) {
				switch( $end ) {
				default:
				case 'dir':
					$terminator = "/";
					break;
				case 'html':
					$terminator = ".html";
					break;
				}
			}

			$destination .= $controller . "/" . $action . $terminator;

			if( count($qString) > 0 ) {
				unset( $qString['controller'] );
				unset( $qString['action'] );
			}
		} else {
			$destination = $_SERVER['PHP_SELF'];
		}

		if( $emptyQuery ) $qString = array();

		$query = ''; // New Query String
		switch( true ) {
		case ( count( $qString ) == 0 ) && ( count( $params ) == 0 ):
			break;
		case ( count( $params ) == 0 ):
			$query = "?" . http_build_query( $qString );
			break;
		case ( count( $qString ) == 0 ):
			$query = "?" . http_build_query( $params );
			break;
		default:
			$newQString = array();

			foreach($qString as $variable => $value) {
			    if(!array_key_exists($variable, $params)) {
			    	$newQString[$variable] = $value;
			    }
			}

			$outOriginalQString = http_build_query( $newQString );
			$outNewQString = http_build_query( $params );
			if( empty( $outOriginalQString ) ) {
				$query = "?" . $outNewQString;
			} else {
				$query = "?" . $outOriginalQString . $outSeparator . $outNewQString;
			}
			break;
		}

		$dest = $destination . $query;

		return $dest;	
	}
	
	/**
	 * Returns a parameter
	 *
	 * @param string $param The param to get
	 * @return mixed The parameter
	 */
	public function getParam($param) {
		return Gecko_Request::getVar($param);
	}
}
?>