<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once('Zend/Filter/Interface.php');

/**
 * PasswordHash Filter
 * 
 * Uses password_hash (PHP5.5+) or compat lib to hash a password
 *
 * @package Gecko.Request.Filter;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v1.0$ 2008
 * @access public
 **/
class Gecko_Filter_PasswordHash implements Zend_Filter_Interface
{
	public function filter($value)
	{
		if (!function_exists('password_hash')) {
			require_once 'Gecko/password.php'; // Compatibility layer	
		}
		
		if (empty($value)) return ''; // No blank passwords hashed
		
		return password_hash($value, PASSWORD_BCRYPT);
	}
}