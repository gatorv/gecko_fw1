<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

require_once 'Gecko/Security/Persistence/Interface.php';
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
class Gecko_Security_Persistence_Session
	implements Gecko_Security_Persistence_Interface
{
	/**
	 * The Zend Session Namespace to store the session
	 * data.
	 * @var Zend_Session_Namespace
	 */
	private $_session;
	
	public function __construct($sSessionName = 'ZendApp')
	{
		$this->_session = new Zend_Session_Namespace($sSessionName);
		$this->setTimeout(1200); // Default is 20 minutes
	}
	
	/**
	 * Sets the active user
	 * @param Zend_Acl_Role_Interface $User
	 */
	public function setUser(Zend_Acl_Role_Interface $User)
	{
		$this->_session->user = serialize($User);
		$this->_session->loggedIn = true;
		$this->_session->expiringLogin = true;
	}
	
	/**
	 * Returns the active user
	 * @return Zend_Acl_Role_Interface
	 */
	public function getUser()
	{
		$sUser = $this->_session->user;
		if ($sUser === null) {
			return null;
		}
		
		return unserialize($sUser);
	}
	
	/**
	 * Clear the active user
	 */
	public function clearUser()
	{
		$this->_session->user = null;
		$this->_session->loggedIn = false;
		$this->_session->expiringLogin = false;
	}
	
	/**
	 * Set the timeout
	 * @param int $nTimeout
	 */
	public function setTimeout($nTimeout)
	{
		$this->_session->setExpirationSeconds($nTimeout, 'expiringLogin');
		$this->_session->setExpirationSeconds($nTimeout, 'user');
	}
	
	/**
	 * Checks if the user is logged in
	 * @return bool
	 */
	public function isLoggedIn()
	{
		return ($this->_session->loggedIn === true && $this->_session->expiringLogin === true);
	}
	
	/**
	 * Checks if the session has expired
	 * @return bool
	 */
	public function isExpired()
	{
		return ($this->_session->loggedIn === true && !$this->_session->expiringLogin);
	}
}