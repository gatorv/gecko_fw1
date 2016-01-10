<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once('Gecko/Request/Filter/Interface.php');

/**
 * MagicQuotes Filter
 *
 * @package Gecko.Request.Filter;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v1.0$ 2008
 * @access public
 **/
class Gecko_Request_Filter_MagicQuotes implements Gecko_Request_Filter_Interface {
	/**
	 * Removes magic quotes if enabled
	 *
	 * @param string The input
	 * @return string
	 */
	public function filterInput( $input ) {
		if( get_magic_quotes_gpc() ) {
			$input = stripslashes( $input );
		}

		return $input;
	}
}
?>