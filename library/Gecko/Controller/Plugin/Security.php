<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * Gecko_Controller_Plugin_Security
 * 
 * Zend Controller Plugin for securing controllers or modules
 *
 * @package Gecko.Controller.Plugin;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @access public
 **/
class Gecko_Controller_Plugin_Security
	extends Zend_Controller_Plugin_Abstract {
	
	/**
	 * Constant for throw a error on not authorized
	 * @var int
	 */
	const ACTION_THROWERROR = 0;
	
	/**
	 * Constant for redirecting upon error
	 * @var int
	 */
	const ACTION_REDIRECT = 1;
	
	/**
	 * The access denied string
	 * @var string
	 */
	const STR_ACCESSDENIED = 'Access Denied to "%value%"';
	
	/**
	 * The session expired string
	 * @var string
	 */
	const STR_SESSIONEXPIRED = 'Your session has expired';
	
	/**
	 * The default privilege to check
	 * @var string
	 */
	private $_defaultPrivilege = 'view';
	
	/**
	 * The Security Manager
	 * @var Gecko_Security_Application
	 */
	private $_securityManager;
	
	/**
	 * The default login array (module/controller/action)
	 * @var array
	 */
	private $_loginAray;
	
	/**
	 * The login map for customized login actions
	 * @see Gecko_Controller_Plugin_Security::setLoginMap
	 * @var array
	 */
	private $_loginMap;
	
	/**
	 * The error action
	 * @var int
	 */
	private $_errorAction;
	
	/**
	 * The error Controller resource for whitelisting
	 * @var string
	 */
	private $_errorResource = null;
	
	/**
	 * Whitelist the error resource
	 * @var bool
	 */
	private $_autoWhitelistErrorResource = true;
	
	/**
	 * The default separator for module / resource
	 * @var string
	 */
	private $_defaultSeparator = ':';
	
	/**
     * Translation object
     * @var Zend_Translate
     */
    protected $_translator;

    /**
     * Default translation object
     * @var Zend_Translate
     */
    protected static $_defaultTranslator;

    /**
     * Is translation disabled?
     * @var Boolean
     */
    protected $_translatorDisabled = false;
    
    /**
     * The current resource (set in preDispatch)
     * @var string
     */
    private $_currentResource = null;
    
    /**
     * Set the current resource
     * @param string $sResource
     * @return self
     */
    public function setCurrentResource($sResource)
    {
    	$this->_currentResource = $sResource;
    	
    	return $this;
    }
    
    /**
     * Returns the current resource
     * @return string
     */
    public function getCurrentResource()
    {
    	return $this->_currentResource;
    }
    
    /**
     * Set the login Url in case a login message is expected
     * @param $sLoginUrl
     * @return self
     */
    public function setLoginArray(array $aLoginArray)
    {
    	$this->_loginAray = $aLoginArray;
    	
    	return $this;
    }
    
    /**
     * Return the default login Url
     * @return string
     */
    public function getLoginArray()
    {
    	return $this->_loginAray;
    }
    
    /**
     * Set the login map, it expects a array
     * where the key is the resource, or only a module
     * to protect a whole module.
     * 
     * The value must be the login array to redirect
     * (module/controller/action)
     * 
     * Example:
     * 
     * foo:bar => array() (applies only to foo module, bar controller)
     * baz => array() (applies to the whole baz module)
     * 
     * @param $arrayMap
     * @return self
     */
    public function setLoginMap(array $arrayMap)
    {
    	$this->_loginMap = $arrayMap;
    	
    	return $this;
    }
    
    /**
     * Return the login map
     * @return array
     */
    public function getLoginMap()
    {
    	return $this->_loginMap;
    }
	
	/**
	 * Get the Autowhitelist status
	 * @return bool
	 */
	public function getAutoWhitelistErrorResource()
	{
		return $this->_autoWhitelistErrorResource;
	}
	
	/**
	 * Set to autowhitelist the error resource
	 * @param bool $sAutoWhitelist
	 * @return self
	 */
	public function setAutoWhitelistErrorResouce($sAutoWhitelist)
	{
		$this->_autoWhitelistErrorResource = $sAutoWhitelist;
		
		return $this;
	}
	
	/**
	 * Return the error resource
	 * @return string
	 */
	public function getErrorResource()
	{
		if ($this->_errorResource == null) {
			$sDefaultModule = Zend_Controller_Front::getInstance()->getDispatcher()->getDefaultModule();
			$sErrorController = 'error';
			
			$this->_errorResource = $sDefaultModule . $this->getDefaultSeparator() . $sErrorController;
		}
		
		return $this->_errorResource;
	}
	
	/**
	 * Set the error resource
	 * @param string $sErrorResource
	 * @return self
	 */
	public function setErrorResource($sErrorResource)
	{
		$this->_errorResource = $sErrorResource;
		
		return $this;
	}
	
	/**
	 * Set the separator for module / resource
	 * @param string $sSeparator
	 * @return self
	 */
	public function setDefaultSeparator($sSeparator)
	{
		$this->_defaultSeparator = $sSeparator;
		
		return $this;
	}
	
	/**
	 * Return the default separator
	 * @return string
	 */
	public function getDefaultSeparator()
	{
		return $this->_defaultSeparator;
	}
	
	/**
	 * Return the default privilege used to check
	 * @return string
	 */
	public function getDefaultPrivilege()
	{
		return $this->_defaultPrivilege;
	}
	
	/**
	 * Se the default privilege to check on the ACL
	 * @param string $sDefaultPrivilege
	 * @return self
	 */
	public function setDefaultPrivilege($sDefaultPrivilege)
	{
		$this->_defaultPrivilege = $sDefaultPrivilege;
		
		return $this;
	}
	
	/**
	 * Return the default error action
	 * @return int
	 */
	public function getDefaultErrorAction()
	{
		return $this->_errorAction;
	}
	
	/**
	 * Set the default error action thrown when unauthorized, can be one of:
	 * ACTION_THROWERROR - throw a exception that can be cached on the error controller
	 * ACTION_REDIRECT - redirect to a login page
	 * @param int $nDefaultAction
	 */
	public function setDefaultErrorAction($nDefaultAction)
	{
		$this->_errorAction = $nDefaultAction;
		
		return $this;
	}
	
	/**
	 * Set the security manager to fetch privileges and security info
	 * @param Gecko_Security_Application $SecurityManager
	 * @return self
	 */
	public function setSecurityManager(Gecko_Security_Application $SecurityManager)
	{
		$this->_securityManager = $SecurityManager;
		
		return $this;
	}
	
	/**
	 * Return the security manager used by the application
	 * @return Gecko_Security_Application
	 */
	public function getSecurityManager()
	{
		return $this->_securityManager;
	}
	
	/**
	 * Create a new instance of the plugin
	 * @param Gecko_Security_Application $SecurityManager
	 * @param int $nDefaultAction
	 */
	public function __construct(Gecko_Security_Application $SecurityManager, $nDefaultAction = self::ACTION_THROWERROR)
	{
		$this->_securityManager = $SecurityManager;
		$this->_errorAction = $nDefaultAction;
	}
	
	/**
	 * Run plugin on preDispatch
	 * @param Zend_Controller_Request_Abstract $Request
	 * @see Zend/Controller/Plugin/Zend_Controller_Plugin_Abstract::preDispatch()
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $Request)
	{
		$sController = $Request->getControllerName();
		$sModule = $Request->getModuleName();
		$sResource = $sModule . $this->getDefaultSeparator() . $sController;
		
		// Set current resource
		$this->setCurrentResource($sResource);
		
		if ($this->getAutoWhitelistErrorResource() && ($sResource == $this->getErrorResource())) {
			return; // Error Resource whitelisted
		}
		
		if (!$this->getSecurityManager()->isAuthorized($sResource, $this->getDefaultPrivilege())) {
			$this->_handleError($Request, self::STR_ACCESSDENIED, $sResource);
		} else {
			if ($this->getSecurityManager()->isSessionExpired()) {
				$this->_handleError($Request, self::STR_SESSIONEXPIRED, $sResource);
			}
		}
		
		Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl($this->getSecurityManager()->getAcl());
		Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole($this->getSecurityManager()->getActiveUser());
	}
	
	/**
	 * Handle the error action when no access or session has expired
	 * @param Zend_Controller_Request_Abstract $Request
	 * @param string $message
	 * @throws Gecko_Security_Exception
	 */
	private function _handleError(Zend_Controller_Request_Abstract $Request, $message)
	{
		if (null !== ($translator = $this->getTranslator())) {
            $message = $translator->translate($message);
        }
        
        if (strpos($message, "%value%") !== false) {
        	$sResource = $this->getCurrentResource();
        	$message = str_replace("%value%", $sResource, $message);
        }
        
		switch ($this->getDefaultErrorAction()) {
			case self::ACTION_REDIRECT:
				$this->_redirectToLogin($Request, $message);
				break;
			case self::ACTION_THROWERROR:
				throw new Gecko_Security_Exception($message);
				break;
		}
	}
	
	/**
	 * Redirect the user to a login controller in case of a error, a optional
	 * redirect_to param is set to redirect the user in case of a error
	 * 
	 * @param Zend_Controller_Request_Abstract $Request
	 * @param string $sError
	 * @param string $sResource
	 */
	private function _redirectToLogin(Zend_Controller_Request_Abstract $Request, $sError)
	{
		$sPreviousPlace = $Request->getRequestUri();
		$aLoginArray = $this->_getLoginAction();
		
		$Request->setModuleName($aLoginArray['module'])
				->setControllerName($aLoginArray['controller'])
				->setActionName($aLoginArray['action'])
				->setParam('error_message', $sError);
		
		if (!empty($sPreviousPlace)) {
			$Request->setParam('redirect_to', $sPreviousPlace);
		}
	}
	
	/**
	 * Return the login action, searches the login map or use the default
	 * one.
	 * @return array
	 */
	private function _getLoginAction()
	{
		$sResource = $this->getCurrentResource();
		$aLoginMap = $this->getLoginMap();
		if (is_array($aLoginMap) && (count($aLoginMap) > 0)) {
			if (isset($loginMap[$sResource])) { // exact match
				return $loginMap[$sResource];
			}

			$aResource = explode(':', $sResource);
			$sModule = $aResource[0];
			if (isset($loginMap[$sModule])) { // module match
				return $loginMap[$sModule];
			}
		}
		
		$aLoginArray = $this->getLoginArray();
		if (null == $aLoginArray) {
			throw new Gecko_Security_Exception('No Login Array Map found');
		}
		
		return $aLoginArray;
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