<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

/**
 * Interface for URL Engine
 *
 * @package Gecko.URL.Engine;
 * @author Christopher Valderrama <gatorv@gmail.com>
 * @copyright Copyright (c) 2007-2008
 * @version $Id$
 * @access public
 */
interface Gecko_URL_Engine_Interface {
	/**
	 * Creates a URI to the same page
	 *
	 * @param array $params The params to pass
	 * @param bool $emptyQuery To reset the query
	 */
	public function getSelfURI($params = array(), $emptyQuery = false);
	/**
	 * Returns a parameter from a request object
	 *
	 * @param string $param The param to fetch
	 * @return mixed The param
	 */ 
	public function getParam($param);
}
?>