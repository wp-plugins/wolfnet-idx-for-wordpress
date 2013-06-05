<?php

if ( !class_exists( 'com_greentiedev_wppf_option_manager' ) ) {

/**
 * @package       com.greentiedev.wppf.option
 * @title         manager.php
 * @extends       com_greentiedev_wppf_abstract_manager
 * @implements    com_greentiedev_phpCommon_iSingleton
 * @contributors  AJ Michels (http://greentiedev.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, AJ Michels
 *
 *                Licensed under the Apache License, Version 2.0 (the "License");
 *                you may not use this file except in compliance with the License.
 *                You may obtain a copy of the License at
 *
 *                http://www.apache.org/licenses/LICENSE-2.0
 *
 *                Unless required by applicable law or agreed to in writing, software
 *                distributed under the License is distributed on an "AS IS" BASIS,
 *                WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *                See the License for the specific language governing permissions and
 *                limitations under the License.
 *
 */

class com_greentiedev_wppf_option_manager
extends com_greentiedev_wppf_abstract_manager
implements com_greentiedev_phpCommon_iSingleton
{


	private static $instance;
	private $groupName = 'wppfDefaultOptionGroup';


	private function __construct()
	{

	}


	public static function getInstance ()
	{
		if ( !isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	public $canRegister = true;


	public function register ( $option, $default = false )
	{

		if ( $this->canRegister ) {
			if ( is_string( $option ) ) {
				$this->managed[$option] = $default;
			}
			elseif ( $option instanceof com_greentiedev_wppf_interface_iOption ) {
				$this->managed[$option->name] = $option;
			}
		}
	}


	public function isRegistered( $option )
	{

		if ( array_key_exists( $option, $this->managed ) ) {
			return true;
		}
		else {
			return false;
		}
	}


	public function setGroupName ( $name )
	{

		if ( $this->canRegister ) {
			$this->groupName = $name;
		}
	}


	public function getGroupName ()
	{

		return $this->groupName;
	}


	public function getOptionList ()
	{

		$list = '';
		$first = true;
		foreach ( $options as $optionName => $option ) {
			if ( !$first ) {
				$list .= ',';
			}
			else {
				$first = false;
			}
			$list .= $optionName;
		}
		return $list;
	}


	public function getOption ( $option )
	{

		return $this->managed[$option];
	}


	public function getOptions ()
	{

		return $this->managed;
	}


	public function registerWithWP ()
	{

		if ( $this->canRegister ) {
			$this->canRegister = false;
			$groupName	= $this->getGroupName();
			$options	= $this->getOptions();
			foreach ( $options as $optionName => $option ) {
				$this->log( array($optionName,$groupName) );
				if ( $option instanceof com_greentiedev_wppf_interface_iOption ) {

					register_setting( $groupName, $optionName, array( &$option, 'sanitize' ) );
				}
				else {
					register_setting( $groupName, $optionName );
				}
			}
		}
	}


	public function unregisterWithWP ()
	{

		$groupName	= $this->os->getGroupName();
		$options	= $this->os->getOptionsList();
		foreach ( $options as $option ) {
			unregister_setting( $groupName, $option );
		}
	}


	public function getOptionValueFromWP ( $option )
	{

		if ( $this->isRegistered( $option) ) {
			return get_option( $option, $this->getOption( $option ) );
		}
		else {
			return get_option( $option );
		}
	}


	public function getSettingsFormHeader ()
	{
		ob_start();
		settings_fields( $this->getGroupName() );
		return ob_get_clean();
	}


}

} // end 'if class_exists'
