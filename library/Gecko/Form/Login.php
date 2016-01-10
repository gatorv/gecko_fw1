<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once 'Zend/Form.php';

/**
 * Gecko_Form_Login
 *
 * Standard login form for Login Auth Controller
 *
 * @package Gecko.Controller.Plugin;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @access public
 **/
class Gecko_Form_Login extends Zend_Form
{
	public function init()
	{
		$this->setMethod(Zend_Form::METHOD_POST);
		
		$this->addElement('text', 'username', array(
			'label'		=> 'User',
			'required'	=> true,
			'filters'	=> array('StringTrim')
		));
		$this->addElement('password', 'password', array(
			'label'		=> 'Password',
			'required'	=> true,
			'filters'	=> array('StringTrim')
		));
		$this->addElement('hidden', 'redirect_to');
		
		$this->addElement('submit', 'submit', array(
            'ignore'	=> true,
            'label'		=> 'Entrar'
        ));
        
        $this->setElementDecorators(array(
			'ViewHelper'
		));
	}
}