<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

/**
 * Filter Helper class for table displays
 *
 * @package Gecko;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @access public
 **/
class Gecko_FilterHelper
{
	protected $form;
	protected $request;
	protected $values;
	protected $select;
	
	/**
	 * Construct a new instance of the class
	 * 
	 * @param Gecko_Form_Filter $form
	 * @param Zend_Controller_Request_Http $request
	 * @param Zend_Db_Select $select
	 */
	public function __construct(
		Gecko_Form_Filter $form, Zend_Controller_Request_Http $request,
		Zend_Db_Select $select = null)
	{
		$this->form = $form;
		$this->request = $request;
		$this->select = $select;
		$this->values = array();
	}
	
	/**
	 * Returns the filtered values that matched in the request query
	 * 
	 * @return array
	 */
	public function getFiltered()
	{
		$form = $this->form;
		$request = $this->request;
		
		$elements = $form->getElements();
		foreach ($elements as $Element) {
			$elementName = $Element->getName();
			if ($request->has($elementName)) {
				$Element->setValue($request->get($elementName));
				$this->values[$elementName] = $request->get($elementName);
			}
		}
		
		return $this->values;
	}
	
	/**
	 * Return the filtered select query
	 * 
	 * @return Zend_Db_Select
	 */
	public function getFilteredSelect()
	{
		$values = $this->getFiltered();
		$this->form->prepareFilterQuery($this->select);
		
		return $this->select;
	}
	
	/**
	 * Return the form used to filter
	 * 
	 * @return Gecko_Form_Filter
	 */
	public function getForm()
	{
		return $this->form;
	}
	
	/**
	 * Set a default filter value if needed
	 * 
	 * @param string $name
	 * @param mixed $value
	 * @return Gecko_FilterHelper
	 */
	public function setDefaultFilterValue($name, $value)
	{
		if ($value) {
			$this->form->{$name}->setValue($value);
			$this->values[$name] = $value;

			if ($this->form instanceof Gecko_Form_ValueAware) {
				$this->form->onValueSet($name, $value);
			}
		}
		
		return $this;
	}
	
	/**
	 * Return the value that matched on the query for a form,
	 * or the default value sent (defaults to null)
	 * 
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	public function getFilterValue($name, $default = null)
	{
		if (isset($this->values[$name])) {
			return $this->values[$name];
		}
		
		return $default;
	}
	
	/**
	 * Magic function that prints the returns the form
	 * as string
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->form->__toString();
	}
}