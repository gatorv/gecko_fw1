<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once( 'Gecko/DataSource/Paginate/Interface.php' );
require_once( 'Gecko/DataSource/Table/Interface.php' );

/**
 * Interface for Table Grids from a Zend_Db_Select
 * Object
 *
 * @package Gecko.DataSource.Table;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v1.0$ 2008
 * @access public
 **/
class Gecko_DataSource_Table_Select implements Gecko_DataSource_Table_Interface, Gecko_DataSource_Paginate_Interface {
	/**
	 * DBTable Select Object
	 *
	 * @var Zend_Db_Table_Select
	 */
	protected $_select = null;

	/**
	 * The DB Adapter to use
	 *
	 * @var Zend_Db
	 */
	protected $_db = null;

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
	 * Creates a new DataSource with the specified Select object
	 *
	 * @param Zend_Db_Select $select The Select object
	 * @param Zend_Db_Adapter $db The Adapter to use
	 * @access public
	 **/
	public function __construct( Zend_Db_Select $select, Zend_Db_Adapter_Abstract $db ) {
		$this->_select = $select;
		$this->_db = $db;
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
		return $this->_select;
	}

    /**
     * Get sql for get record count
     *
     * @return  string
     */
    public function getSelectCountSql() {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::DISTINCT);
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::FOR_UPDATE);

		$sql = $countSelect->__toString();
		$sql = preg_replace('/SELECT (.*) FROM/i', 'SELECT COUNT(*) FROM', $sql);

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
			$this->totalRows = (int) $this->_db->fetchOne($sql);

			list( $start, $total ) = $this->limit;
			$select->reset(Zend_Db_Select::LIMIT_COUNT);
        	$select->reset(Zend_Db_Select::LIMIT_OFFSET);
			$select->limit( $total, $start );
		} else {
			$this->totalRows = 0;
		}

		$select->reset(Zend_Db_Select::ORDER);

		if( count( $this->order ) > 0 ) {
			$select->order( $this->order );
		}

		// Fetch Select Columns
		$rawColumns = $select->getPart(Zend_Db_Select::COLUMNS);
		$columns = array();
		// Get columns and Force casting as strings
		foreach($rawColumns as $col) {
			$columns[] = (string) $col[1];
		}
		$this->cols = $columns;
		$this->totalColumns = count( $columns );

		// Fetch
		$stmt = $this->_db->query($select);
		$rows = $stmt->fetchAll(Zend_Db::FETCH_ASSOC);
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