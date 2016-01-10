<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once('Gecko/DataGrid/CellRenderer/Interface.php');

/**
 * URL Renderer for Cells
 *
 * @package Gecko.DataGrid.CellRenderer;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v1.0$ 2008
 * @access public
 **/
class Gecko_DataGrid_CellRenderer_Url extends Gecko_DataGrid_CellRenderer {
	/**
	 * The url of the cell
	 *
	 * @var string
	 */
	private $url;
	/**
	 * The ID column to add to the link
	 * by default it replaces %VALUE%
	 *
	 * @var string
	 */
	private $idColumn;
	/**
	 * The label of the link (optional)
	 *
	 * @var string
	 */
	private $label;

	/**
	 * Creates a new renderer with the
	 * specified settings
	 *
	 * @param string The url of the cell
	 * @param string The column name to add to the link
	 * @param string The URL label
	 */
	public function __construct($url, $idColumn = null, $label = null) {
		$this->url = $url;
		$this->idColumn = $idColumn;
		$this->label = $label;
	}

	/**
	 * Renders the value using the specified settings
	 *
	 * @param string The cell value
	 */
	public function renderValue($value) {
		$url = $this->url;
		if( $this->idColumn !== null ) {
			$data = $this->grid->getDataSource()->getRowAt($this->rowNumber);
			$key = $data[$this->idColumn];

			$url = str_replace("%VALUE%", $key, $url);
		}

		$label = (($this->label) !== null ? $this->label : $value);
		return Gecko_HTML::LinkTag( $label, $url );
	}
}
?>