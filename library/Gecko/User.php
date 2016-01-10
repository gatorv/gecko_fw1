<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

/**
 * @see Zend_Acl_Role_Interface
 */
require_once 'Zend/Acl/Role.php';

/**
 * Basic User class
 *
 * @package Gecko;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v1.0$ 2008
 * @access public
 **/
class Gecko_User extends Zend_Acl_Role {
	/**
	 * The user Id
	 *
	 * @var int
	 */
	protected $_user_id;

	/**
	 * Creates a new instance of a Gecko_User
	 * It needs a stdClass object to populate
	 * the data
	 *
	 * @param object $data The data to create
	 */
	public function __construct($nUserId) {
		$this->_user_id = (int) $nUserId;
	}
}