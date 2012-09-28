<?php

if ( !class_exists( 'com_ajmichels_wppf_bootstrap' ) ) {

/**
 * @package       com.ajmichels.wppf
 * @title         bootstrap.php
 * @contributors  AJ Michels (http://ajmichels.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, AJ Michels
 *
 *                Licensed under the Apache License, Version 2.0 (the "License");
 *                you may not use this file except in compliance with the License.
 *                You may obtain a copy of the License at
 *
 *                    http://www.apache.org/licenses/LICENSE-2.0
 *
 *                Unless required by applicable law or agreed to in writing, software
 *                distributed under the License is distributed on an "AS IS" BASIS,
 *                WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *                See the License for the specific language governing permissions and
 *                limitations under the License.
 *
 */

abstract class com_ajmichels_wppf_bootstrap
extends com_ajmichels_common_abstractClass
{

	private   $reflection = null;
	private   $className  = null;
	private   $classFile  = null;
	private   $pluginPath = '';
	private   $pluginUrl  = '';
	private   $options    = array();

	protected $wppf_serviceFactory; // Service Factory
	protected $os; // Option Manager
	protected $am; // Action Manager
	protected $fm; // Filter Manager
	protected $sm; // ShortCode Manager


	protected function __construct ()
	{
		$this->reflection = new ReflectionClass( $this );

		$this->pluginPath = dirname( $this->reflection->getFileName() );
		$this->pluginUrl  = plugin_dir_url( $this->reflection->getFileName() );

		$this->className  = $this->reflection->name;
		$this->classFile  = $this->reflection->getFileName();

		$sfXml = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'phpSpring.xml';
		$sfProps = array( 'plugin' => &$this );
		$this->wppf_serviceFactory = new com_ajmichels_phpSpring_bean_factory_default( $sfXml, array(), $sfProps );

		$this->os = $this->wppf_serviceFactory->getBean( 'OptionManager' );
		$this->am = $this->wppf_serviceFactory->getBean( 'ActionManager' );
		$this->fm = $this->wppf_serviceFactory->getBean( 'FilterManager' );
		$this->sm = $this->wppf_serviceFactory->getBean( 'ShortcodeManager' );

	}


	final protected function initPlugin ()
	{

		$this->wppf_options();
		$this->wppf_actions();
		$this->wppf_filters();
		$this->wppf_shortcodes();

		register_activation_hook(   $this->classFile, array( &$this, 'wppf_activate' ) );
		register_deactivation_hook( $this->classFile, array( &$this, 'wppf_deactivate' ) );
		//register_uninstall_hook(    $this->classFile, array( &$this, 'wppf_uninstall' ) );

		add_action( 'init',         array( &$this, 'wppf_init' ) );
		add_action( 'admin_init',   array( &$this, 'wppf_adminInit' ) );
		add_action( 'shutdown',     array( &$this, 'wppf_shutdown' ) );

		/* if the url parameter 'clearCache' is passed clear the data cache */
		if ( array_key_exists( 'clearCache', $_REQUEST ) ) {
			$dataService = $this->wppf_serviceFactory->getBean( 'XmlDataService' );
			$dataService->clearCache();
			$dataService = $this->wppf_serviceFactory->getBean( 'JsonDataService' );
			$dataService->clearCache();
		}

	}


	/* Runs after WordPress has finished loading but before any headers are sent. */
	final public function wppf_init ()
	{
		$this->init();
	}


	protected function init ()
	{
	}


	/* Runs at the beginning of every admin page before the page is rendered. */
	final public function wppf_adminInit ()
	{
		$this->adminInit();
		/* register options */
		$this->os->registerWithWP();
	}


	protected function adminInit ()
	{
	}


	/* Runs when the page output is complete and PHP script execution is about the end. */
	final public function wppf_shutdown ()
	{
		$this->shutdown();
		$this->dumpLog();
	}


	protected function shutdown ()
	{
	}


	/* Runs when the plugin is activated. */
	final public function wppf_activate ()
	{
		$this->activate();
	}


	protected function activate ()
	{
	}


	/* Runs when the plugin is deactivated. */
	final public function wppf_deactivate ()
	{
		$this->deactivate();
	}


	protected function deactivate ()
	{
	}


	/* Runs when the plugin is uninstalled. */
	final public function wppf_uninstall ()
	{
		$this->uninstall();
		/* unregister options */
		$this->os->unregisterWithWP();
	}


	protected function uninstall ()
	{
	}


	final protected function wppf_options ()
	{
		$this->options();
	}


	protected function options ()
	{
	}


	final protected function wppf_actions ()
	{
		$this->actions();
	}


	protected function actions ()
	{
	}


	final protected function wppf_filters ()
	{
		$this->filters();
	}


	protected function filters ()
	{
	}


	final protected function wppf_shortcodes ()
	{

		$this->shortcodes();
	}


	protected function shortcodes ()
	{
	}


	protected function setPluginPath()
	{
	}


	public function getClassName ()
	{
		return $this->className;
	}


	public function getPluginUrl ()
	{
		return $this->pluginUrl;
	}


	public function getPluginPath ()
	{
		return $this->pluginPath;
	}


	public function getPluginBaseDir ()
	{
		return str_replace( '/', '', str_replace( '\\', '', str_replace( WP_PLUGIN_DIR, '', $this->getPluginPath() ) ) );
	}


}

} // end 'if class_exists'