<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

/**
 * HTML Table OO Wraper
 *
 * @package Gecko
 * @author Christopher Valderrama <gatorv@gmail.com>
 * @copyright Copyright (c) 2007-2008
 * @version $Id$
 * @access public
 */
class Gecko_Table {
	private $_html_string; 	// @var private string variable that holds all of HTML and Table properties
	private $_rows; 		// @var private integer variable that holds the total rows
	private $_tbody_open; // @var private boolean variable that holds if a table body is open
	private $_row_open; // @var private boolean variable that shows if a table row is open

	/**
	 * Constructor
	 *
	 * Creates a new Instance of a Object Oriented Table
	 *
	 * @param array $table_props Table properties
	 * @access public
	 **/
	public function __construct( $table_props = array()  ) {
		$table_properties 	= '';
		$table_properties 	= $this->prop2String( $table_props );
		$this->_html_string = "<table$table_properties>\n";
		$this->_rows = 0;
		$this->_tbody_open = false;
		$this->_row_open = false;
	}

	/**
	 * prop2String
	 *
	 * Converts a array of key/value pairs to a attribute string
	 * ej:
	 *     array(
	 *         'attrib1' => 'value1',
	 *     );
	 * to:
	 *     attrib1="value1" (escaped)
	 *
	 * @param array $properties Array of properties
	 * @acccess private
	 * @return string converted array
	 **/
	private function prop2String( $properties ) {
		$properties_str = '';
		foreach( $properties as $property => $value ) {
			$properties_str .= " $property=\"$value\"";
		}

		return $properties_str;
	}

	/**
	 * addCaption
	 *
	 * Adds a Caption to the Table
	 *
	 * @param string $caption
	 * @access public
	 * @return void
	 **/
	public function addCaption( $caption ) {
		$this->_html_string .= "<caption>$caption</caption>\n";
	}

	/**
	 * getRows
	 *
	 * Gets the number of rows from the table
	 *
	 * @access public
	 * @return integer
	 **/
	public function getRows() {
		return $this->_rows;
	}

	/**
	 * addBody
	 *
	 * Adds a Body to a Table
	 *
	 * @param array $body_props
	 * @access public
	 * @return void
	 **/
	public function addBody( $body_props = array() ) {
		if( $this->_tbody_open ) {
			if( $this->_row_open ) { $this->closeRow(); }
			$this->closeBody();
		}

		$bprops = $this->prop2String( $body_props );
		$this->_html_string.="\n<tbody$bprops>\n";
		$this->_tbody_open = true;
	}

	/**
	 * closeBody
	 *
	 * Closes a open Body (automatically called)
	 *
	 * @access private
	 * @return void
	 **/
	private function closeBody() {
		$this->_html_string.="\n</tbody>\n";
		$this->_tbody_open = false;
	}

	/**
	 * addRow
	 *
	 * Adds a new Row to the Table
	 *
	 * @param array $row_props
	 * @access public
	 * @return void
	 **/
	public function addRow( $row_props = array() ) {
		if( $this->_row_open ) { $this->closeRow(); }
		$rowprop = $this->prop2String( $row_props );

		$this->_html_string.="<tr$rowprop>\n";
		$this->_rows++;
		$this->_row_open = true;
	}

	/**
	 * closeRow
	 *
	 * Closes a Row
	 *
	 * @access private
	 * @return void
	 **/
	private function closeRow() {
		$this->_html_string.= "\n</tr>\n";
		$this->_row_open = false;
	}

	/**
	 * addHeader
	 *
	 * Adds a Table Header to the Table
	 *
	 * @param string $label
	 * @param array $header_props
	 * @access public
	 * @return void
	 **/
	public function addHeader( $label, $header_props = array() ) {
		$header_props = $this->prop2String( $header_props );
		$th = "\t<th$header_props>$label</th>\n";
		$this->_html_string .= $th;
	}

	/**
	 * addCell
	 *
	 * Adds a new Cell to the Table
	 *
	 * @param string $text
	 * @param array $cell_props
	 * @access public
	 * @return void
	 **/
	public function addCell( $text = '&nbsp;', $cell_props = array() ) {
		if( $this->_row_open === false ) { $this->addRow(); }

		$cell_props = $this->prop2String( $cell_props );
		$cell = "\t<td$cell_props>$text</td>\n";
		$this->_html_string .= $cell;
	}

	/**
	 * getOutput
	 *
	 * Returns the Table Output after construction
	 *
	 * @access public
	 * @return string
	 **/
	public function getOutput() {
		if( $this->_row_open ) { $this->closeRow(); }
		if( $this->_tbody_open ) { $this->closeBody(); }

		$str = $this->_html_string;
		$str.= "</table>\n";

		return $str;
	}

	/**
	 * __toString
	 *
	 * Returns the Table Output after construction
	 *
	 * @access public
	 * @return string
	 **/
	public function __toString() {
		return $this->getOutput();
	}
}
?>