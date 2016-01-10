<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

/**
 * Request Filter Interface
 *
 * @package Gecko.Request.Filter;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v1.0$ 2008
 * @access public
 **/
interface Gecko_Request_Filter_Interface {
	public function filterInput( $input );
}
?>