<?php

/**
 * This class is the Listing Branding DAO (Data Access Object) and is responsible for translating 
 * the incoming and outgoing data to and from the Listing Branding Entity object. This object should 
 * be replaced in the event that the method for data i/o is changed.
 * 
 * @package       com.wolfnet.wordpress
 * @subpackage    listing.branding
 * @title         dao.php
 * @extends       com_ajmichels_wppf_abstract_dao
 * @implements    com_ajmichels_wpff_interface_iDao
 * @singleton     True
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 *                
 *                This program is free software; you can redistribute it and/or
 *                modify it under the terms of the GNU General Public License
 *                as published by the Free Software Foundation; either version 2
 *                of the License, or (at your option) any later version.
 *                
 *                This program is distributed in the hope that it will be useful,
 *                but WITHOUT ANY WARRANTY; without even the implied warranty of
 *                MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *                GNU General Public License for more details.
 *                
 *                You should have received a copy of the GNU General Public License
 *                along with this program; if not, write to the Free Software
 *                Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * 
 */
class com_wolfnet_wordpress_listing_branding_dao
extends com_ajmichels_wppf_abstract_dao
implements com_ajmichels_wppf_interface_iDao
{
	
	
	/* SINGLETON ENFORCEMENT ******************************************************************** */
	
	/**
	 * This private static property holds the singleton instance of this class.
	 *
	 * @type  com_wolfnet_wordpress_listing_dao
	 *
	 */
	private static $instance;
	
	
	/**
	 * This static method is used to retrieve the singleton instance of this class.
	 *
	 * @return  com_wolfnet_wordpress_listing_dao
	 *
	 */
	public static function getInstance ()
	{
		if ( !isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	
	/* CONSTRUCTOR METHOD *********************************************************************** */
	
	/**
	 * This constructor method is private becuase this class is a singleton and can only be retrieved
	 * by statically calling the getInstance method.
	 *
	 * @return  void
	 *
	 */
	private function __construct ()
	{
	}
	
	
	/* PUBLIC METHODS *************************************************************************** */
	
	/**
	 * This method returns all property listing branding avaiable to this WordPress plugin instance.
	 *
	 * @return  array  An array of listing Branding objects 
	 *                 (com_wolfnet_wordpress_listing__branding_entity)
	 *
	 */
	public function findAll ()
	{
		return array( clone $this->getEntityPrototype() );
	}
	
	
	/**
	 * This method returns only a single listing branding object based on the primary key that is 
	 * passed.
	 *
	 * @param   mixed  $id  The primary key of a single listing.
	 * @return  com_wolfnet_wordpress_listing_entity  Listing object with a matching primary key.
	 *
	 */
	public function findById ( $id = 0 )
	{
		return clone $this->getEntityPrototype();
	}
	
	
	/**
	 * This method returns only a single listing branding object based on the primary key that is 
	 * passed.
	 *
	 * @param   mixed  $id  The primary key of a single listing.
	 * @return  com_wolfnet_wordpress_listing_entity  Listing object with a matching primary key.
	 *
	 */
	public function firstItem ()
	{
		$data = $this->getData();
		$branding = clone $this->getEntityPrototype();
		if ( is_array( $data ) && count( $data ) ) {
			$branding->setMemento( $data[0] );
		}
		return $branding;
	}
	
	
	/* ACCESSORS ******************************************************************************** */
	
	/**
	 * GETTER: This getter method is used to get the data property, which is an inherited property.
	 *
	 * @return  array  An array of data.
	 *
	 */
	public function getData ()
	{
		return $this->data;
	}
	
	
	/**
	 * SETTER: This setter method is used to set the data property, which is an inherited property.
	 *
	 * @param   array  $data  An array of data.
	 * @return  void
	 *
	 */
	public function setData ( $data )
	{
		$this->data = $data;
	}
	
	
}