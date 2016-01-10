<?php
class Gecko_Form_Element_ArrayElement extends Zend_Form_Decorator_Abstract
{
	public function render($content)
	{
		$element = $this->getElement();
		$view    = $element->getView();
		$markup  = '';
		$name    = $element->getName() . '[]';

		foreach ($element->getValues() as $value) {
			$markup .= $view->formHidden($name, $value) . "\n";
		}

		$separator = $this->getSeparator();
		switch ($this->getPlacement()) {
			case 'PREPEND':
				return $markup . $separator . $content;
			case 'APPEND':
			default:
				return $content . $separator . $markup;
		}
	}
}