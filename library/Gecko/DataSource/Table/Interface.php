<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

/**
 * Interface for Table Grids
 *
 * @package Gecko.DataSource.Table;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v1.0$ 2008
 * @access public
 **/
interface Gecko_DataSource_Table_Interface {
	/**
	 * Returns the total Number of Columns
	 *
	 * @return int
	 */
	public function getTotalColumns();
	/**
	 * Returns the total number of rows
	 *
	 * @return int
	 */
	public function getTotalRowset();
	/**
	 * Gets a row of data at the specified row
	 * number
	 *
	 * @param int Row Number
	 * @return array The row
	 */
	public function getRowAt($rowNumber);
	/**
	 * Returns the Column name at the specified
	 * col number
	 *
	 * @param int Col Number
	 * @return string
	 */
	public function getColumnAt($number);
	/**
	 * Sets the order of the source
	 *
	 * @param string The column to order
	 * @param string The order to sort by
	 */
	public function setOrder($column, $order);
	/**
	 * Setups the DataSource (binding)
	 *
	 * @return void
	 */
	public function setup();
}
?>