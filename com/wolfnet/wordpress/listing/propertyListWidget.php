<?php

/**
 * This is the propertyListWidget object. This object inherites from the base WP_Widget object and
 * defines the display and functionality of this specific widget.
 *
 * @see http://codex.wordpress.org/Widgets_API
 * @see http://core.trac.wordpress.org/browser/tags/3.3.2/wp-includes/widgets.php
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    listing
 * @title         propertyListWidget.php
 * @extends       com_wolfnet_wordpress_listing_listingGridWidget
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
 */
class com_wolfnet_wordpress_listing_propertyListWidget
extends com_wolfnet_wordpress_listing_listingGridWidget
{


	/* CONSTRUCTOR METHOD *********************************************************************** */

	/**
	 * This constructor method passes some key information up to the parent classes and eventionally
	 * the information gets registered with the WordPress application.
	 *
	 * @return  void
	 *
	 */
	public function __construct ()
	{
		$this->id = 'wolfnet_propertyListWidget';
		$this->name = 'WolfNet Property List';
		$this->options['description'] = 'Define criteria to display a text list of matching properties. The text display includes the property address and price for each property.';
		parent::__construct();
		/* The 'sf' property is set in the abstract widget class and is pulled from the plugin instance */
		$this->setListingGridView( $this->sf->getBean( 'PropertyListView' ) );
	}


}