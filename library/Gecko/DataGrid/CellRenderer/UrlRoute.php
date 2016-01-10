<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once('Gecko/DataGrid/CellRenderer/Interface.php');
require_once('Zend/View/Helper/Url.php');

/**
 * Zend Url Router Renderer for Cells
 *
 * @package Gecko.DataGrid.CellRenderer;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2010
 * @version $Id$v1.0$
 * @access public
 **/
class Gecko_DataGrid_CellRenderer_UrlRoute extends Gecko_DataGrid_CellRenderer {
	/**
	 * The route to use
	 *
	 * @var string
	 */
	private $_route;
	/**
	 * The params to pass to the route
	 *
	 * @var array
	 */
	private $_params;
	/**
	 * The label of the link (optional)
	 *
	 * @var string
	 */
	private $_label;

	/**
	 * Creates a new renderer with the
	 * specified settings
	 *
	 * @param string $route The route to use (pass null to use existing)
	 * @param array $params The params to pass
	 * @param string $label The optional label to replace if needed
	 */
	public function __construct($route, $params = array(), $label = null) {
		$this->_route = $route;
		$this->_params = $params;
		$this->_label = $label;
	}

	/**
	 * Renders the value using the specified settings
	 *
	 * @param string The cell value
	 */
	public function renderValue($value) {
		$route = $this->_route;
		$params = array();
		$helper = new Zend_View_Helper_Url();
		
		if( count($this->_params) > 0 ) {
			$data = $this->grid->getDataSource()->getRowAt($this->rowNumber);
			foreach ($this->_params as $column) {
				$params[$column] = $data[$column];
			}
		}

		$url = $helper->url($params, $route, true);
		$label = (($this->_label) !== null ? $this->_label : $value);
		return Gecko_HTML::LinkTag( $label, $url );
	}
}