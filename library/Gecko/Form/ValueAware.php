<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

/**
 * Gecko_Form_ValueAware
 *
 * Interface for the FilterHelper to make forms aware of changes
 *
 * @package Gecko.Controller.Plugin;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @access public
 **/
interface Gecko_Form_ValueAware
{
	/**
	 * Prepare the filter query with the form values
	 * @param Zend_Db_Select $select
	 */
	public function onValueSet($element, $value);
}