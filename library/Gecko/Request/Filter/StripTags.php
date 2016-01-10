<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once('Gecko/Request/Filter/Interface.php');

/**
 * StripTags Filter
 *
 * @package Gecko.Request.Filter;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v1.0$ 2008
 * @access public
 **/
class Gecko_Request_Filter_StripTags implements Gecko_Request_Filter_Interface {
	/**
	 * Basic function that strips HTML Tags from user submitted input
	 *
	 * @param mixed $input
	 * @return mixed
	 */
	public function filterInput( $input ) {
		return strip_tags( $input );
	}
}
?>