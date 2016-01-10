<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once( 'Gecko/DataGrid/Formatter/Interface.php' );
require_once( 'Gecko/Table.php' );

/**
 * Grid Formatter Table
 *
 * Basic formatter for drawing data into a Table
 *
 * @package Gecko.DataGrid.Formatter;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v1.0$ 2008
 * @access public
 **/
class Gecko_DataGrid_Formatter_Table implements Gecko_DataGrid_Formatter_Interface {
	/**
	 * The Table object to draw data
	 *
	 * @see Gecko_Table
	 * @var Object
	 */
	protected $table;
	/**
	 * The current row Number, used to track where you are,
	 * for the header row, the rowNum is null
	 *
	 * @var mixed
	 */
	protected $rowNum = null;
	/**
	 * The grid object to interface
	 *
	 * @see Gecko_DataGrid
	 * @var Object
	 */
	protected $grid;
	/**
	 * The columnNames for the grid
	 * 
	 * @var array
	 */
	protected $colNames = array();
	/**
	 * Array with cell renderers
	 * 
	 * @var array
	 */
	protected $cellRenderers = array();

	/**
	 * Creates a new instance of the formatter
	 * 
	 * @return void
	 */
	public function __construct() {}

	/**
	 * Sets the active Grid of the formatter
	 *
	 * @param Gecko_DataGrid The grid
	 */
	public function setGrid(Gecko_DataGrid $grid) {
		$this->grid = $grid;
	}

	/**
	 * Starts constructing the grid
	 *
	 * @param array The table properties
	 */
	public function beginTable($tableProperties) {
		$this->table = new Gecko_Table($tableProperties);
	}

	/**
	 * Finishes drawing the grid
	 */
	public function endTable() {}

	/**
	 * Starts to draw a Row
	 *
	 * @param mixed Row num
	 * @param array Row parameters
	 */
	public function beginRow($rowNum, $params = array()) {
		$this->rowNum = $rowNum;
		$this->table->addRow($params);
	}

	/**
	 * Finishes drawing the row
	 *
	 */
	public function endRow() {}

	/**
	 * Adds a new Header for the grid
	 *
	 * @param string The header to add (can contain HTML)
	 * @param array The Parameters of the grid
	 */
	public function addHeader($header, $params = array()) {
		$this->table->addHeader($header, $params);
	}

	/**
	 * Adds a new Cell to the grid
	 *
	 * @param string The value (can contain HTML)
	 * @param string The column of the cell
	 * @param array The parameters for the cell
	 */
	public function addCell($value, $colNameGroup, $params = array()) {
		if( isset( $this->cellRenderers[$colNameGroup] ) ) {
			$renderers = $this->cellRenderers[$colNameGroup];
			if( is_array( $renderers ) && ( count( $renderers ) > 0 ) ) {
				foreach($renderers as $renderer) {
					if($renderer instanceof Gecko_DataGrid_CellRenderer_Interface) {
						$renderer->setGrid($this->grid);
						$renderer->setRowNum($this->rowNum);
						$value = $renderer->renderValue($value);
					}
				}
			}
		}

		$this->table->addCell($value, $params);
	}

	/**
	 * Returns the Table Object used for constructing
	 *
	 * @return Gecko_Table The table
	 */
	public function getTable() {
		return $this->table;
	}

	/**
	 * Draws the table
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->getTable()->__toString();
	}

	/**
	 * Sets the column names of the grid
	 *
	 * @param array Columns
	 */
	public function setColumnNames($columns) {
		$this->colNames = $columns;
	}

	/**
	 * Gets a column name before rendering into table
	 *
	 * @param string The column
	 * @return string
	 */
	public function getColumnName($column) {
		if( array_key_exists( $column, $this->colNames ) ) {
			$column = $this->colNames[$column];
		}

		return $column;
	}

	/**
	 * Adds a renderer for a specficif Column,
	 * they renderers are run top to down, so
	 * be careful how you add them.
	 *
	 * For example, if you want a image link,
	 * first add the image renderer, then the URL
	 * renderer, or you will not see the expected
	 * result.
	 *
	 * @param string The column
	 */
	public function addCellRenderer($colGroup, Gecko_DataGrid_CellRenderer $renderer) {
		if(!isset($this->cellRenderers[$colGroup])) {
			$this->cellRenderers[$colGroup] = array();
		}

		$this->cellRenderers[$colGroup][] = $renderer;
	}
}
?>