<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

/**
 * Base Model for simple DbTables
 *
 * @package Gecko.Model;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @access public
 **/
class Gecko_Model_Base
{
	protected $_dbTable = null;
	protected $_defaultPageSize;
	protected $_pageSize;
	
	/**
	 * Creates a new Base Model
	 * 
	 * @param mixed|string,object $DbTableModel
	 */
	public function __construct($DbTableModel)
	{
		$this->setDbTable($DbTableModel);
		
		$page_size = (Zend_Registry::isRegistered('default_page_size')) ? Zend_Registry::get('default_page_size') : 20;
		$this->_defaultPageSize = $page_size;
		$this->_pageSize = $page_size;
	}
	
	public function removeByPk($nPk)
	{
		$sPrimary = $this->getDbTable()->info('primary');
		
		$aWhere = array(
			$sPrimary[1] . '=?' => $nPk
		);
		
		$Row = $this->getDbTable()->fetchRow($aWhere);
		if ($Row) {
			$Row->delete();
		} else {
			throw new Gecko_Exception('Invalid entry for: ' . $nPk);
		}
	}
	
	public function findByPk($nPk)
	{
		$sPrimary = $this->getDbTable()->info('primary');
		
		$aWhere = array(
			$sPrimary[1] . '=?' => $nPk
		);
		
		return $this->getDbTable()->fetchRow($aWhere);
	}
	
	public function getCols()
	{
		return $this->getDbTable()->info('cols');
	}
	
	public function getTableName()
	{
		return $this->getDbTable()->info('name');
	}
	
	public function restoreDefaultPageSize()
	{
		$this->_pageSize = $this->_defaultPageSize;
		
		return $this;
	}
	
	public function createNew()
	{
		return $this->getDbTable()->createRow();
	}
	
	public function setPageSize($nPageSize)
	{
		$this->_pageSize = $nPageSize;
		
		return $this;
	}
	
	public function getPageSize()
	{
		return $this->_pageSize;
	}
	
	public function getPaginator($Select, $nPage)
	{
		$Paginator = Zend_Paginator::factory($Select);
		$Paginator->setItemCountPerPage($this->getPageSize());
		$Paginator->setCurrentPageNumber($nPage);
		
		return $Paginator;
	}
	
	public function fetchAll($Select = null) {
		return $this->getDbTable()->fetchAll($Select);
	}
	
	public function getDbSelect()
	{
		return $this->getDbTable()->select();
	}
	
	public function getAdapter()
	{
		return $this->getDbTable()->getAdapter();
	}
	
	public function getSelect()
	{
		return $this->getAdapter()->select();
	}
	
	public function getDbTable()
	{
		return $this->_dbTable;
	}
	
	public function fetchFilteredAndPaged($filters, $nPage = 0)
	{
		if (!($filters instanceof Zend_Db_Select)) {
			$select = $this->getDbSelect();
			foreach ($filters as $sFilter => $sValue) {
				if (empty($sValue)) {
					$select->where($sFilter);
				} else {
					$select->where($sFilter, $sValue);
				}
			}
		} else {
			$select = $filters;
		}
		
		return $this->getPaginator($select, $nPage);
	}
	
	public function asArray($sEmptyText = '', $sEmptyValue = '')
	{
		$Select = $this->getSelect();
		$Select->from(array('t' => $this->getTableName()));
		
		$aOptions = $this->getAdapter()->fetchPairs($Select);
		if (!empty($sEmptyText)) {
			$aOptions = array($sEmptyValue => $sEmptyText) + $aOptions;
		}
		
		return $aOptions;
	}
	
	public function setDbTable($DbTable)
	{
		if (is_string($DbTable)) {
			$this->_dbTable = new $DbTable();
			if (!($this->_dbTable instanceof Zend_Db_Table_Abstract)) {
				throw new DomainException("Invalid type of $sDbTableModel a instance of Zend_Db_Table_Abstract is required");
			}
		} else if ($DbTable instanceof Zend_Db_Table_Abstract) {
			$this->_dbTable = $DbTable;
		} else {
			throw new DomainException("Invalid model domain provided");
		}
		
		return $this;
	}
}