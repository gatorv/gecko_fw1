<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

/**
 * Grid Cell Renderer Interface
 *
 * Interface for rendering grid
 *
 * @package Gecko.DataGrid.CellRenderer;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v1.0$ 2008
 * @access public
 **/
interface Gecko_DataGrid_CellRenderer_Interface {
	/**
	 * Sets the active grid
	 *
	 * @param Gecko_Grid grid
	 */
	public function setGrid(Gecko_DataGrid $grid);
	/**
	 * Sets the active Row Number
	 *
	 * @param int $rowNum The row number
	 */
	public function setRowNum($rowNum);
}
?>