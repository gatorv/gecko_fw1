<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once('Gecko/DataGrid/CellRenderer/Interface.php');

/**
 * Abstract Base Renderer for cells
 *
 * @package Gecko.DataGrid.CellRenderer;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v1.0$ 2008
 * @access public
 **/
abstract class Gecko_DataGrid_CellRenderer implements Gecko_DataGrid_CellRenderer_Interface {
	/**
	 * The Grid that is rendering
	 */
	protected $grid;
	/**
	 * The current row Number
	 *
	 * @var int;
	 */
	protected $rowNumber;

	/**
	 * Cell Renderer especific work
	 */
	abstract public function renderValue($value);

	/**
	 * Sets the grid to use
	 *
	 * @param Gecko_Grid grid
	 */
	public function setGrid(Gecko_DataGrid $grid) {
		$this->grid = $grid;
	}

	/**
	 * Sets the current Row Number
	 *
	 * @param int $rowNum The row number
	 */
	public function setRowNum($rowNum) {
		$this->rowNumber = $rowNum;
	}
}