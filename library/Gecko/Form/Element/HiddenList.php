<?php
class Gecko_Form_Element_HiddenList extends Zend_Form_Element
{
	public function init()
	{
		$this->addPrefixPath('Gecko_Form_Decorator', 'Gecko/Form/Decorator/', 'decorator');
	}
	
	public function getValue()
	{
		return (array) $this->_value;
	}

	public function setValue($value)
	{
		$this->_value = (array) $value;
	}

	public function loadDefaultDecorators()
	{
		if ($this->loadDefaultDecoratorsIsDisabled()) {
			return;
		}

		$decorators = $this->getDecorators();
		if (empty($decorators)) {
			$this->addDecorator('ArrayElement');
		}
	}

}