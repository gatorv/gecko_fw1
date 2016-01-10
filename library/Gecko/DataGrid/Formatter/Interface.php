<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

/**
 * Grid Formatter Interface
 *
 * Interface for rendering grid
 *
 * @package Gecko.DataGrid.Formatter;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v1.0$ 2008
 * @access public
 **/
interface Gecko_DataGrid_Formatter_Interface {
	/**
	 * Starts drawing the table grid
	 *
	 * @param array The table Properties
	 */
	public function beginTable($tableProperties);
	/**
	 * Finishes drawing the grid
	 */
	public function endTable();
	/**
	 * Starts drawing a grid row
	 *
	 * @param int The row Number
	 * @param array The row parameters
	 */
	public function beginRow($rowNum, $params = array());
	/**
	 * Finishes drawing the row
	 */
	public function endRow();
	/**
	 * Starts drawing the grid header
	 *
	 * @param string The header
	 * @param array The array parameters
	 */
	public function addHeader($header, $params = array());
	/**
	 * Adds a new Cell to the grid
	 *
	 * @param string the cell
	 * @param string the Column group
	 */
	public function addCell($value, $colNameGroup);
	/**
	 * Sets the active grid of the formatter
	 */
	public function setGrid(Gecko_DataGrid $grid);
	/**
	 * Sets the Cell Renderer of a column
	 *
	 * @param string The column group
	 * @param Gecko_DataGrid_CellRenderer_Interface The renderer to add
	 */
	public function addCellRenderer($colGroup, Gecko_DataGrid_CellRenderer $renderer);
}
?>