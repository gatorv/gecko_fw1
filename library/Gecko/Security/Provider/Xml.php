<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//
require_once 'Gecko/Security/Provider/Interface.php';

/**
 * Gecko_Security_Provider_Xml
 *
 * @package Gecko.Security.Provider;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @access public
 **/
class Gecko_Security_Provider_Xml implements Gecko_Security_Provider_Interface {
	private $_rolesFile = '';
	private $_resourcesFile = '';
	private $_aclFile = '';
	
	public function __construct($roles, $resources, $acl) {
		$this->_rolesFile = $roles;
		$this->_resourcesFile = $resources;
		$this->_aclFile = $acl;	
	}
	
	public function getRoles() {
		$roles = array();
		$xml = simplexml_load_file($this->_rolesFile);
		
		if (!$xml) {
			throw new Exception('Unable to load roles XML File');
		}
		
		foreach ($xml->role as $role) {
			$roles[] = (string) $role['name'];
		}
		
		return $roles;
	}
	
	public function getResources() {
		$resources = array();
		$xml = simplexml_load_file( $this->_resourcesFile );
		
		if (!$xml) {
			throw new Exception('Unable to load resources XML File');
		}
		
		foreach ($xml->resource as $resource) {
			$resources[] = (string) $resource['name'];
		}
		
		return $resources;
	}
	
	public function getAccessList() {
		$list = array();
		$xml = simplexml_load_file( $this->_aclFile );
		if (!$xml) {
			throw new Exception('Unable to load acl XML File');
		}
		
		foreach ($xml->controller as $controller) {
			$current = array();
			$name = (string) $controller['name'];
			
			$users = $controller->user;
			$current['resource'] = $name;
			$current['users'] = array();
			foreach( $users as $user ) {
				$group = (string) $user['group'];
				$privileges = (string) $user['privileges'];
				if( $group == "all" ) {
					$group = null;
				}
				
				if( !$privileges || ( $privileges == "all" ) ) {
					$privileges = null;
				} else {
					$privileges = explode( ",", $privileges );
				}
				
				$thisUser = array();
				$thisUser['role'] = $group;
				$thisUser['privileges'] = $privileges;
				$current['users'][] = $thisUser;
			}
			
			$list[] = $current;
		}
		
		return $list;
	}
}