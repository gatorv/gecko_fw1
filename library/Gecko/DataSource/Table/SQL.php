<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once( 'Gecko/DataSource/Paginate/Interface.php' );
require_once( 'Gecko/DataSource/Table/Interface.php' );

/**
 * This class populates and creates a Data Source to
 * populate a Grid
 *
 * @package Gecko.DataSource.Table;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v1.0$ 2008
 * @access public
 **/
class Gecko_DataSource_Table_SQL implements Gecko_DataSource_Table_Interface, Gecko_DataSource_Paginate_Interface {
	/**
	 * DataSource properties
	 **/
	protected $db;
	protected $sql;
	protected $totalColumns;
	protected $totalRowset;
	protected $rows;
	protected $cols;

	/**
	 * Special properties for paginating
	 **/
	protected $paginate = false;
	protected $limitedSql;
	protected $totalRows;

	/**
	 * Creates a new DataSource with the specified SQL, and a Zend_Db_Adapter
	 * interface
	 *
	 * @param string The Sql Query
	 * @param Zend_Db_Adapter_Abstract The Db Adapter
	 * @access public
	 **/
	public function __construct($sql, Zend_Db_Adapter_Abstract $db) {
		$this->sql = $sql;
		$this->db = $db;
	}

	/**
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
		$this->limitedSql = $this->db->limit( $this->sql, $total, $start );
	}

	/**
	 * Checks the SQL Query, performs paginating
	 * and downloads needed data
	 *
	 * @access public
	 * @return void
	 **/
	public function setup() {
		/**
		 * Check if we are paginating
		 **/
		if( $this->paginate ) {
			$countQuery = eregi_replace("select[[:space:]](.*)[[:space:]]from", "SELECT COUNT(*) FROM", strtolower($this->sql));

			$totalRowsArray = $this->db->fetchCol($countQuery);
			$this->totalRows = array_sum($totalRowsArray);

			$sql = $this->limitedSql;
		} else {
			$sql = $this->sql;
			$this->totalRows = 0;
		}

		/**
		 * Send Query
		 **/
		$rst = $this->db->query( $sql );

		/**
		 * Fetch Column Names / Headers
		 **/
		$this->totalColumns = $rst->columnCount();
		$cols = array();
		for( $i = 0; $i < $this->totalColumns; $i++ ) {
			$data = $rst->getColumnMeta($i);
			$cols[$i] = $data['name'];
		}
		$this->cols = $cols;

		/**
		 * Fetch Rows
		 **/
		$rst->setFetchMode(Zend_Db::FETCH_ASSOC);
		$rows = array();
		while( $row = $rst->fetch() ) {
			$rows[] = $row;
		}
		$total = count($rows);
		$this->totalRowset = $total;
		$this->rows = $rows;
		$rst = null;
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
		if( strpos( strtoupper( $this->sql ), "ORDER BY" ) === false ) {
			$this->sql .= " ORDER BY `$column` $order";
		} else {
			throw new Gecko_DataSource_Table_Exception( "{$this->sql} Query already has a Order by statement" );
		}
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

	/**
	 * Returns the DB Adaptor of the source
	 *
	 * @access public
	 * @return Zend_Db_Adapter_Abstract The DB Adapter
	 **/
	public function getAdaptor() {
		return $this->db;
	}

	/**
	 * Returns the SQL Query to populate the Source
	 *
	 * @access public
	 * @return string The SQL Query
	 **/
	public function getSQL() {
		return $this->sql;
	}
}
?>
