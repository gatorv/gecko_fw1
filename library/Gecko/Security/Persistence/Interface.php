<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

require_once 'Zend/Acl/Role/Interface.php';

/**
 * Gecko_Security_Persistence_Interface
 * 
 * Interface that provides the necessary persistence into the application
 * security.
 *
 * @package Gecko.Security.Persistence;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @access public
 **/
interface Gecko_Security_Persistence_Interface
{
	/**
	 * Sets the active user
	 * @param Zend_Acl_Role_Interface $User
	 */
	public function setUser(Zend_Acl_Role_Interface $User);
	/**
	 * Returns the active user
	 * @return Zend_Acl_Role_Interface
	 */
	public function getUser();
	/**
	 * Clear the active user
	 */
	public function clearUser();
	/**
	 * Set the timeout
	 * @param int $nTimeout
	 */
	public function setTimeout($nTimeout);
	/**
	 * Checks if the user is logged in
	 * @return bool
	 */
	public function isLoggedIn();
	/**
	 * Checks if the session has expired
	 * @return bool
	 */
	public function isExpired();
}