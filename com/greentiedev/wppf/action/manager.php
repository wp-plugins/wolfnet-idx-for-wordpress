<?php

if ( !class_exists( 'com_greentiedev_wppf_action_manager' ) ) {

/**
 *
 * @package       com.greentiedev.wppf.action
 * @title         manager.php
 * @extends       com_greentiedev_wppf_abstract_hookManager
 * @implements    com_greentiedev_phpCommon_iSingleton
 * @contributors  AJ Michels (http://greentiedev.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, AJ Michels
 *
 *                Licensed under the Apache License, Version 2.0 (the "License");
 *                you may not use this file except in compliance with the License.
 *                You may obtain a copy of the License at
 *
 *                   http://www.apache.org/licenses/LICENSE-2.0
 *
 *                Unless required by applicable law or agreed to in writing, software
 *                distributed under the License is distributed on an "AS IS" BASIS,
 *                WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *                See the License for the specific language governing permissions and
 *                limitations under the License.
 *
 */

class com_greentiedev_wppf_action_manager
extends com_greentiedev_wppf_abstract_hookManager
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


	/* CONSTRUCTOR METHOD *********************************************************************** */

	private function __construct()
	{
	}


	/* PUBLIC METHOD **************************************************************************** */

	public function register ( com_greentiedev_wppf_action_action &$obj, $hooks )
	{
		parent::register( $obj, $hooks );
	}


}

} // end 'if class_exists'
