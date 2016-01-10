<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

/**
 * Gecko_Security_Application
 * 
 * Class that manages security and persitence in a application
 *
 * @package Gecko.Security.Application
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @access public
 **/
class Gecko_Security_Application
{
	/**
	 * The Access Control List to use
	 * @var Zend_Acl
	 */
	private $_acl;
	
	/**
	 * The current role
	 * @var Zend_Acl_Role_Interface
	 */
	private $_currentRole;
	
	/**
	 * The application name
	 * @var string
	 */
	private $_applicationName;
	
	/**
	 * The persistence engine to use
	 * @var Gecko_Security_Persistence_Interface
	 */
	private $_persistenceManager = null;
	
	/**
	 * The guest role
	 * @var Zend_Acl_Role_Interface
	 */
	private $_guestRole = null;
	
	/**
	 * Set the application name
	 * @param string $sAppName
	 * @return self
	 */
	public function setApplicationName($sAppName)
	{
		$this->_applicationName = $sAppName;
		
		return $this;
	}
	
	/**
	 * Return the application name
	 * @return string
	 */
	public function getApplicationName()
	{
		return $this->_applicationName;
	}
	
	/**
	 * Return the persistence manager used by the class
	 * @return Gecko_Security_Persistence_Interface
	 */
	public function getPersistenceManager()
	{
		if ($this->_persistenceManager == null) {
			$this->_persistenceManager = new Gecko_Security_Persistence_Session($this->getApplicationName());
		}
		
		return $this->_persistenceManager;
	}
	
	/**
	 * Set the active persistence manager by the class
	 * @param Gecko_Security_Persistence_Interface $PersistenceManager
	 * @return self
	 */
	public function setPersistenceManager(
		Gecko_Security_Persistence_Interface $PersistenceManager)
	{
		$this->_persistenceManager = $PersistenceManager;
		
		return $this;
	}
	
	/**
	 * Set the active ACL
	 * @param Zend_Acl $Acl
	 * @return self
	 */
	public function setAcl(Zend_Acl $Acl)
	{
		$this->_acl = $Acl;
		
		return $this;
	}
	
	/**
	 * Return the active ACL
	 * @return Zend_Acl
	 */
	public function getAcl()
	{
		return $this->_acl;
	}
	
	/**
	 * Set the guest role when no role is defined
	 * @param Zend_Acl_Role_Interface $GuestRole
	 * @return self
	 */
	public function setGuestRole(Zend_Acl_Role_Interface $GuestRole)
	{
		$this->_guestRole = $GuestRole;
		
		return $this;
	}
	
	/**
	 * Return the guest role
	 * @return Zend_Acl_Role_Interface
	 */
	public function getGuestRole()
	{
		return $this->_guestRole;
	}
	
	/**
	 * Initiates a new Application Security, using a name, and a ACL.
	 * 
	 * @param string $sApplicationName
	 * @param Zend_Acl $Acl
	 */
	public function __construct($sApplicationName = 'ZendApplication', Zend_Acl $Acl = null)
	{
		$this->setApplicationName($sApplicationName);
		$this->_guestRole = new Zend_Acl_Role('Guest');
		$this->_acl = $Acl;
	}
	
	/**
	 * Return the active role
	 * @return Zend_Acl_Role_Interface
	 */
	public function getActiveUser()
	{
		$Role = $this->getPersistenceManager()->getUser();
		if ($Role === null) {
			$Role = $this->getGuestRole();
		}
		
		return $Role;
	}
	
	/**
	 * Checks if the current user is allowed
	 * @param string $sResource
	 * @param string $sPrivilege
	 * @return bool
	 */
	public function isAuthorized($sResource, $sPrivilege = null)
	{
		return $this->getAcl()->isAllowed($this->getActiveUser(), $sResource, $sPrivilege);
	}
	
	/**
	 * Registers the current user
	 * @param Zend_Acl_Role_Interface $sUser
	 */
	public function registerUser($User)
	{
		$this->getPersistenceManager()->setUser($User);
	}
	
	/**
	 * Proxy to clear the current user
	 */
	public function clearUser()
	{
		$this->getPersistenceManager()->clearUser();
	}
	
	/**
	 * Checks if the user is logged in
	 * @return bool
	 */
	public function isLoggedIn()
	{
		return $this->getPersistenceManager()->isLoggedIn();
	}
	
	/**
	 * Checks if the session has expired
	 * @return bool
	 */
	public function isSessionExpired()
	{
		return $this->getPersistenceManager()->isExpired();
	}
}