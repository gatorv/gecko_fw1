<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once('Gecko/DataGrid/CellRenderer.php');

/**
 * Printf wrapper renderer
 *
 * @package Gecko.DataGrid.CellRenderer;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v1.0$ 2008
 * @access public
 **/
class Gecko_DataGrid_CellRenderer_Printf extends Gecko_DataGrid_CellRenderer {
	/**
	 * The format for the printf
	 *
	 * @var string
	 */
	private $format;

	/**
	 * Creates a new formatter with
	 * the specified format
	 */
	public function __construct($format) {
		$this->format = $format;
	}

	/**
	 * Renders the value using the specified settings
	 *
	 * @param string The cell value
	 */
	public function renderValue($value) {
		return sprintf($this->format, $value);
	}
}
?>