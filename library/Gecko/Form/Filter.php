<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

/**
 * Gecko_Form_Filter
 *
 * Interface for the FilterHelper
 *
 * @package Gecko.Controller.Plugin;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @access public
 **/
interface Gecko_Form_Filter
{
	/**
	 * Prepare the filter query with the form values
	 * @param Zend_Db_Select $select
	 */
	public function prepareFilterQuery(Zend_Db_Select $select);
}