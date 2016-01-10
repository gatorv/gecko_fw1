<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once('Gecko/Request/Filter/Interface.php');

/**
 * HTMLSpecialChars Filter
 *
 * @package Gecko.Request.Filter;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v1.0$ 2008
 * @access public
 **/
class Gecko_Request_Filter_HTMLSpecialChars implements Gecko_Request_Filter_Interface {
	/**
	 * Filter the input
	 *
	 * @param string The input to filter
	 * @return string
	 */
	public function filterInput( $input ) {
		return htmlspecialchars( $input );
	}
}
?>