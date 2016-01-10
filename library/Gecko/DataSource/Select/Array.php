<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once('Gecko/DataSource/Select/Interface.php');

/**
 * Array Data Source (Wrapper)
 *
 * @package Gecko.DataSource.Select;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v1.0$ 2008
 * @access public
 **/
class Gecko_DataSource_Select_Array implements Gecko_DataSource_Select_Interface {
	/**
	 * The source (array)
	 *
	 * @var array
	 */
	private $source;

	/**
	 * Creates a new instance of the DS
	 *
	 * @param array The array
	 */
	public function __construct( $array ) {
		$this->source = $array;
	}

	/**
	 * Returns the array
	 */
	public function getSelectData() {
		return $this->source;
	}
}
?>