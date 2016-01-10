<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once('Gecko/DataSource/Select/Ajax/Interface.php');
require_once('Gecko/DataSource/Select/DBTable.php');

/**
 * DBTable Data Source with AJAX extension (for dependant selects)
 *
 * @package Gecko.DataSource.Select.Ajax;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v1.0$ 2008
 * @access public
 **/
class Gecko_DataSource_Select_Ajax_DBTable extends Gecko_DataSource_Select_DBTable implements Gecko_DataSource_Select_Ajax_Interface {
	private $params = array();
	private $value;

	public function getSelectData() {
		if( !is_callable( array( $this->table, $this->method ) ) ) {
			throw new Gecko_DataSource_Select_Exception( "Unable to call {$this->method} on table: " . get_class( $this->table ) );
		}

		$method = $this->method;
		$data = $this->table->$method($this->value);

		return $data;
	}

	public function setValue($value) {
		$this->value = $value;
	}
	public function setParam($param, $value) {
		$this->params[$param] = $value;
	}
}
?>