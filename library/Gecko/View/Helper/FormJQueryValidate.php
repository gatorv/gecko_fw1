<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once 'Zend/View/Helper/Abstract.php';

/**
 * Generates the markup needed for jQuery Validate using
 * a Zend_Form
 *
 * @package Gecko;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2013
 * @access public
 **/
class Gecko_View_Helper_FormJQueryValidate extends Zend_View_Helper_Abstract
{
	/**
	 * Translation object
	 * @var Zend_Translate
	 */
	protected $_translator;
	
	/**
	 * Default translation object for all validate objects
	 * @var Zend_Translate
	 */
	protected static $_defaultTranslator;
	
	/**
	 * Is translation disabled?
	 * @var Boolean
	 */
	protected $_translatorDisabled = false;
	
	/**
	 * Generates the Javascript markup needed for jQuery Validate
	 * using the parameters of the Zend_Form object
	 * @param Zend_Form $form The Zend Form to use
	 * @param array $otherOptions A array with additional options if needed
	 * @return string
	 */
	public function formJQueryValidate(Zend_Form $form, array $otherOptions = array())
	{
		$id = $form->getAttrib('id');
		if (!$id) {
			$id = uniqid('zfForm');
		}
		
		$fields = array();
		$messages = array();
		foreach ($form->getElements() as $elemName => $element) {
			$options = array();
			$elemMessages = array();
			
			if ($element->getIgnore() == true) {
				continue;
			}
			
			if ($element->isRequired()) {
				$options['required'] = true;
				$elemMessages['required'] = 'Este elemento es necesario';
			}
			
			$validators = $element->getValidators();
			foreach ($validators as $validator) {
				$validatorMessages = $validator->getMessageTemplates();
				switch (true) {
					case ($validator instanceof Zend_Validate_NotEmpty):
						$options['required'] = true;
						$elemMessages['required'] = $this->toJQueryValidateFormat($validatorMessages['isEmpty']);
						break;
					case ($validator instanceof Zend_Validate_EmailAddress):
						$options['email'] = true;
						$elemMessages['email'] = $this->toJQueryValidateFormat($validatorMessages['emailAddressInvalidFormat']);
						break;
					case ($validator instanceof Zend_Validate_StringLength):
						$min = $validator->getMin();
						$max = $validator->getMax();
						if ($min) {
							$options['minlength'] = $min;
							$elemMessages['minlength'] = $this->toJQueryValidateFormat($validatorMessages['stringLengthTooShort']);
						}
						if ($max) {
							$options['maxlength'] = $max;
							$elemMessages['maxlength'] = $this->toJQueryValidateFormat($validatorMessages['stringLengthTooLong']);
						}
						break;
					case ($validator instanceof Zend_Validate_Identical):
						$token = $validator->getToken();
						if ($token) {
							$options['equalTo'] = '#' . $token;
							$elemMessages['equalTo'] = $this->toJQueryValidateFormat($validatorMessages['notSame']);
						}
						break;
					case ($validator instanceof Zend_Validate_Date):
						$options['mxdate'] = true;
						$elemMessages['equalTo'] = $this->toJQueryValidateFormat($validatorMessages['dateInvalidDate']);
						break;
				}
			}
			
			$fields[$elemName] = $options;
			$messages[$elemName] = $elemMessages;
		}
		
		$validateOptions = json_encode(array_merge($otherOptions, array(
			'rules' => $fields,
			'messages' => $messages
		)));
		
		$js = "$('#{$id}').validate($validateOptions);";
		
		return $js;
	}
	
	/**
	 * Messages in jQuery Validate are different than the ones in
	 * Zend_Validate so we need to change the format, also if needed
	 * it translates the message using the specified translator, or
	 * Zend_Translate in the registry.
	 * @param string $message
	 * @param bool $tranlate
	 * @return string
	 */
	public function toJQueryValidateFormat($message, $translate = true)
	{
		if ((null !== ($translator = $this->getTranslator())) && $translate) {
			$message = $translator->translate($message);
		}
		
		$message = str_replace("'%value%' ", '', $message);
		$counter = 0;
		$message = preg_replace_callback('/%(\S+)%/mi', function($matches) use (&$counter) {
			$replace = "{{$counter}}";
			$counter++;
			return $replace;
		}, $message);
		
		return $message;
	}
	
	/**
	 * Set translation object
	 *
	 * @param  Zend_Translate|Zend_Translate_Adapter|null $translator
	 * @return Zend_Validate_Abstract
	 */
	public function setTranslator($translator = null)
	{
		if ((null === $translator) || ($translator instanceof Zend_Translate_Adapter)) {
			$this->_translator = $translator;
		} elseif ($translator instanceof Zend_Translate) {
			$this->_translator = $translator->getAdapter();
		} else {
			require_once 'Zend/Validate/Exception.php';
			throw new Zend_Validate_Exception('Invalid translator specified');
		}
		return $this;
	}
	
	/**
	 * Return translation object
	 *
	 * @return Zend_Translate_Adapter|null
	 */
	public function getTranslator()
	{
		if ($this->translatorIsDisabled()) {
			return null;
		}
	
		if (null === $this->_translator) {
			return self::getDefaultTranslator();
		}
	
		return $this->_translator;
	}
	
	/**
	 * Does this validator have its own specific translator?
	 *
	 * @return bool
	 */
	public function hasTranslator()
	{
		return (bool)$this->_translator;
	}
	
	/**
	 * Set default translation object for all validate objects
	 *
	 * @param  Zend_Translate|Zend_Translate_Adapter|null $translator
	 * @return void
	 */
	public static function setDefaultTranslator($translator = null)
	{
		if ((null === $translator) || ($translator instanceof Zend_Translate_Adapter)) {
			self::$_defaultTranslator = $translator;
		} elseif ($translator instanceof Zend_Translate) {
			self::$_defaultTranslator = $translator->getAdapter();
		} else {
			require_once 'Zend/Validate/Exception.php';
			throw new Zend_Validate_Exception('Invalid translator specified');
		}
	}
	
	/**
	 * Get default translation object for all validate objects
	 *
	 * @return Zend_Translate_Adapter|null
	 */
	public static function getDefaultTranslator()
	{
		if (null === self::$_defaultTranslator) {
			require_once 'Zend/Registry.php';
			if (Zend_Registry::isRegistered('Zend_Translate')) {
				$translator = Zend_Registry::get('Zend_Translate');
				if ($translator instanceof Zend_Translate_Adapter) {
					return $translator;
				} elseif ($translator instanceof Zend_Translate) {
					return $translator->getAdapter();
				}
			}
		}
	
		return self::$_defaultTranslator;
	}
	
	/**
	 * Is there a default translation object set?
	 *
	 * @return boolean
	 */
	public static function hasDefaultTranslator()
	{
		return (bool)self::$_defaultTranslator;
	}
	
	/**
	 * Indicate whether or not translation should be disabled
	 *
	 * @param  bool $flag
	 * @return Zend_Validate_Abstract
	 */
	public function setDisableTranslator($flag)
	{
		$this->_translatorDisabled = (bool) $flag;
		return $this;
	}
	
	/**
	 * Is translation disabled?
	 *
	 * @return bool
	 */
	public function translatorIsDisabled()
	{
		return $this->_translatorDisabled;
	}
}