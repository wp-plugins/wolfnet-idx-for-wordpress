<?php

/* *********************************************************************************************** /

Plugin Name:  WolfNet IDX for WordPress
Plugin URI:   http://wordpress.wolfnet.com
Description:  The WolfNet IDX for WordPress plugin provides IDX search solution integration with any WordPress website.
Version:      1.1.3
Author:       WolfNet Technologies, LLC.
Author URI:   http://www.wolfnet.com

/ *********************************************************************************************** */

/* Include and Initialize Class Autoloader */
require_once( 'com/greentiedev/phpCommon/autoLoader.php' );
com_greentiedev_phpCommon_autoLoader::getInstance( dirname( __FILE__ ) );

/**
 *
 * @title         wolfnet.php
 * @contributors  AJ Michels (http://aj.michels@wolfnet.com)
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
 */
class wolfnet
extends com_greentiedev_wppf_bootstrap
implements com_greentiedev_phpCommon_iSingleton
{


	/* SINGLETON ENFORCEMENT ******************************************************************** */

	private static $instance;

	public static function getInstance ()
	{
		if ( !isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/* PROPERTIES ******************************************************************************* */

	public $majorVersion = '1.1';
	public $minorVersion = '3';
	public $version      = '1.1.3';


	/* CONSTRUCT PLUGIN ************************************************************************* */

	public function __construct ()
	{
		$this->log( 'Init wolfnet Plugin' );

		if ( !session_id() ) {
			session_start();
		}

		parent::__construct();

		/* If the wordpress install is set to debug mode enable logging and debug output */
		if ( WP_DEBUG ) {
			$this->loggerSetting( 'enabled', true );
			$this->loggerSetting( 'level',   'debug' );
			$this->loggerSetting( 'minTime', 0 );
		}

		$webServiceDomain = 'http://services.mlsfinder.com/v1';

		/* Create Plugin Service Factory */
		$sfXml = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'com/wolfnet/wordpress/phpSpring.xml';
		$sfProps = array(
					'pluginUrl'          => $this->getPluginUrl(),
					'webServiceDomain'   => $webServiceDomain,
					'pluginMajorVersion' => $this->majorVersion,
					'pluginMinorVersion' => $this->minorVersion,
					'pluginVersion'      => $this->version
					);
		$this->sf = new com_greentiedev_phpSpring_bean_factory_default( $sfXml, array(), $sfProps );
		$this->sf->setParent( $this->wppf_serviceFactory );

		$defaultUrl = $this->sf->getBean( 'DefaultWebServiceUrl' );
		$defaultUrl->setParameter( 'pluginVersion', $this->version );

		if ( !session_id() ) {
			session_start();
		}

		/* Notify the bootstrap that we are ready to initialize the plugin. */
		parent::initPlugin();

	}


	/* PLUGIN LIFE-CYCLE HOOKS ****************************************************************** */

	public function activate ()
	{
		$this->sf->getBean( 'RegisterRewriteRules' )->execute();
		flush_rewrite_rules();
	}

	public function deactivate ()
	{
		flush_rewrite_rules();
	}


	/* MANAGER REGISTRATIONS ******************************************************************** */

	/* Register Options with the Option Manager */
	protected function options ()
	{
		$this->os->setGroupName( 'wolfnet' );
		$this->os->register( 'wolfnet_productKey' );
	}


	/* Register Actions with the Action Manager */
	protected function actions ()
	{
		$this->am->register( $this->sf->getBean( 'RegisterRewriteRules' ),      array( 'init' ) );
		$this->am->register( $this->sf->getBean( 'RegisterCustomPostTypes' ),   array( 'init' ) );
		$this->am->register( $this->sf->getBean( 'AddShortcodeBuilderButton' ), array( 'admin_init' ) );
		$this->am->register( $this->sf->getBean( 'EnqueueResources' ),          array( 'wp_enqueue_scripts' ) );
		$this->am->register( $this->sf->getBean( 'CreateAdminPages' ),          array( 'admin_menu' ) );
		$this->am->register( $this->sf->getBean( 'RegisterWidgets' ),           array( 'widgets_init' ) );
		$this->am->register( $this->sf->getBean( 'EnqueueAdminResources' ),     array( 'admin_enqueue_scripts' ) );
		$this->am->register( $this->sf->getBean( 'ManageRewritePages' ),        array( 'template_redirect' ) );
		$this->am->register( $this->sf->getBean( 'FooterDisclaimer' ),          array( 'wp_footer' ) );
	}


	/* Register Shortcodes with the Shortcode Manager */
	protected function shortcodes ()
	{
		$this->sm->register( $this->sf->getBean( 'ListingQuickSearchShortcode' ) );
		$this->sm->register( $this->sf->getBean( 'FeaturedListingsShortcode' ) );
		$this->sm->register( $this->sf->getBean( 'ListingGridShortcode' ) );
		$this->sm->register( $this->sf->getBean( 'PropertyListShortcode' ) );
	}


}


/* INSTANTIATE PLUGIN *************************************************************************** */

$wolfnet = wolfnet::getInstance();
