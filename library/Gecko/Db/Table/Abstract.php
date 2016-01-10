<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once 'Zend/Db/Table/Abstract.php';

/**
 * Custom Db Table Object that adds some nifty features
 *
 * @package Gecko;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @access public
 */
class Gecko_Db_Table_Abstract extends Zend_Db_Table_Abstract {
	/**
	 * The Default Display Column
	 * @var string
	 */
	protected $_displayColumn = '';
	
	/**
	 * Fetch Key / Label Pairs from this table
	 * 
	 * @param $displayColumn The column used as display
	 * @return array
	 */
	public function fetchPairs($displayColumn = '', $sWhereCondition = '', $sWhereValue = '') {
		if (empty($displayColumn)) {
			$displayColumn = $this->_displayColumn;
		}
		if (empty($displayColumn)) {
			throw new Zend_Db_Table_Exception('In order to use fetchPairs() you must setup the display column or pass it as a parameter');
		}
		
		$sPrimary = $this->_primary;
		$oSelect = $this->select();
		$oSelect->from($this, array($sPrimary, $displayColumn));
		if (!empty($sWhereCondition)) {
			$oSelect->where($sWhereCondition, $sWhereValue);
		}
		
		return (array) $this->getAdapter()->fetchPairs($oSelect);
	}
}