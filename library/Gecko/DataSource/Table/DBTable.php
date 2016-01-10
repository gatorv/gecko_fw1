<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once( 'Gecko/DataSource/Paginate/Interface.php' );
require_once( 'Gecko/DataSource/Table/Interface.php' );

/**
 * Interface for Table Grids from a Zend_Db_Table
 * Object
 *
 * @package Gecko.DataSource.Table;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v1.0$ 2008
 * @access public
 **/
class Gecko_DataSource_Table_DBTable implements Gecko_DataSource_Table_Interface, Gecko_DataSource_Paginate_Interface {
	/**
	 * Zend_Db_Table
	 **/
	protected $table = null;

	/**
	 * DBTable Select Object
	 *
	 * @var Zend_Db_Table_Select
	 */
	protected $_select = null;

	/**
	 * The columns to fetch
	 **/
	protected $columns = array();

	/**
	 * DataSource properties
	 **/
	protected $totalColumns;
	protected $totalRowset;
	protected $rows;
	protected $cols;

	/**
	 * Special properties for paginating
	 **/
	protected $paginate = false;
	protected $totalRows;

	/**
	 * Arrays for storing order / limit options
	 **/
	private $order;
	private $limit;

	/**
	 * Creates a new DataSource with the specified Table interface
	 *
	 * @param Zend_Db_Table The table adapter
	 * @param array The columns to fetch (optional)
	 * @access public
	 **/
	public function __construct( Zend_Db_Table_Abstract $table, $columns = array() ) {
		$this->table = $table;
		$this->columns = $columns;
	}

	/**
	 * Sets the specified columns (must be call before setup)
	 *
	 * @param array The columns
	 * @access public
	 * @return void
	 **/
	public function setColumns($columns) {
		if(!is_array($columns)) {
			throw new Gecko_DataSource_Table_Exception( '$columns expected to be an array, ' . gettype($columns) . ' given' );
		}

		$this->columns = $columns;
	}

	/**
	 * Alters the SQL Query to allow the ordering of data
	 *
	 * @param string The column to order
	 * @param string The order ASC / DESC
	 * @return void
	 * @access public
	 **/
	public function setOrder($column, $order) {
		$this->order = "$column $order";
	}

	/**
	 * limitResults
	 *
	 * Limit the resultset to allow paginating
	 *
	 * @see Gecko_DataSource_Paginate_Interface
	 * @param int The start of the resultset (offset)
	 * @param int The total number of results
	 * @access public
	 * @return void
	 **/
	public function limitResults($start, $total) {
		$this->paginate = true;
		$this->limit = array( $start, $total );
	}

	/**
	 * Returns the Select Object
	 *
	 * @return Zend_Db_Table_Select
	 */
	public function getSelect() {
		if($this->_select === null){
			$this->_select = $this->table->select();
		}

		return $this->_select;
	}

    /**
     * Get sql for get record count
     *
     * @return  string
     */
    public function getSelectCountSql() {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);

		$sql = $countSelect->__toString();
		$sql = eregi_replace("select[[:space:]](.*)[[:space:]]from", "SELECT COUNT(*) FROM", strtolower($sql) );

		return $sql;
    }

	/**
	 * Creates and populates the DataSource
	 *
	 * @access public
	 * @return void
	 **/
	public function setup() {
		$select = $this->getSelect();

		if($this->paginate) {
			$sql = $this->getSelectCountSql();
			$this->totalRows = (int) $this->table->getAdapter()->fetchOne($sql);

			list( $start, $total ) = $this->limit;
			$select->reset(Zend_Db_Select::LIMIT_COUNT);
        	$select->reset(Zend_Db_Select::LIMIT_OFFSET);
			$select->limit( $total, $start );
		} else {
			$this->totalRows = 0;
		}

		$select->reset(Zend_Db_Select::ORDER);

		if( count( $this->columns ) > 0 ) {
			$select->reset(Zend_Db_Select::FROM);
			$select->from( $this->table, $this->columns);
		}

		if( count( $this->order ) > 0 ) {
			$select->order( $this->order );
		}

		$rows = $this->table->fetchAll( $select )->toArray();
		$info = $this->table->info();
		$this->cols = $info['cols'];
		$this->totalColumns = count( $this->cols );
		$total = count($rows);
		$this->totalRowset = $total;
		$this->rows = $rows;
	}

	/**
	 * Returns the grand total Rows (for paginating
	 *
	 * @return int The total of rows
	 * @access public
	 **/
	public function getTotalRows() {
		return $this->totalRows;
	}

	/**
	 * Return the total of columns
	 *
	 * @return int The column number
	 * @access public
	 **/
	public function getTotalColumns() {
		return $this->totalColumns;
	}

	/**
	 * Returns the total number of rows from the rowset
	 *
	 * @return int The number of rows
	 * @access public
	 **/
	public function getTotalRowset() {
		return $this->totalRowset;
	}

	/**
	 * Returns the row at the specified row number
	 *
	 * @access public
	 * @return array The row array
	 **/
	public function getRowAt($rowNumber) {
		return $this->rows[$rowNumber];
	}

	/**
	 * Returns the specified column
	 *
	 * @return string The col name
	 * @access public
	 **/
	public function getColumnAt($colNumber) {
		$col = $this->cols[$colNumber];

		return $col;
	}
}
?>