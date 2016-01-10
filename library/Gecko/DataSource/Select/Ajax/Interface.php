<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once('Gecko/DataSource/Select/Interface.php');

/**
 * Interface for Ajax Requests
 *
 * @package Gecko.DataSource.Paginate;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v1.0$ 2008
 * @access public
 **/
interface Gecko_DataSource_Select_Ajax_Interface extends Gecko_DataSource_Select_Interface {
	/**
	 * Sets the current value
	 *
	 * @param string the value of the request
	 */
	public function setValue($value);
	/**
	 * Sets the paramaters for request
	 *
	 * @param string Parameter
	 * @param string The value
	 */
	public function setParam($param, $value);
}
?>