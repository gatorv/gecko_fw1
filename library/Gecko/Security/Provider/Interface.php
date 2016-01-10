<?php
//
// +------------------------------------------------------------------------+
// | Gecko Framework                                                        |
// +------------------------------------------------------------------------+
//

/**
 * Security Provider Interface
 *
 * @package Gecko.Security.Provider;
 * @author Christopher Valderrama <valderrama.christopher@gmail.com>
 * @copyright Copyright (c) 2008
 * @version $Id$v1.0$ 2008
 * @access public
 **/
interface Gecko_Security_Provider_Interface {
	/**
	 * getRoles()
	 *
	 * Returns a array with roles to check
	 **/
	public function getRoles();
	/**
	 * getResources()
	 *
	 * Returns a array with resources to access to
	 **/
	public function getResources();
	/**
	 * getAccessList()
	 *
	 * Returns a Array with the access list, in the form of:
	 * array(
	 *     'resource' => 'resource_name',
	 *     'users' => array(
	 *             'role' => 'role_name', or 'all',
	 *             'privileges' => array( 'privileges' ) or 'all',
	 *      ),
	 * );
	 *
	 **/
	public function getAccessList();
}