<?php

if ( !class_exists( 'com_greentiedev_wppf_json_service' ) ) {

/**
 * @package       com.greentiedev.wppf.json
 * @title         service.php
 * @extends       com_greentiedev_wppf_abstract_service
 * @implements    com_greentiedev_wppf_interface_iDataInterpreter
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

class com_greentiedev_wppf_json_service
extends com_greentiedev_wppf_abstract_service
implements com_greentiedev_wppf_interface_iDataInterpreter
{


	private static $instance;


	private function __construct ()
	{
	}


	public static function getInstance ()
	{
		if ( !isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	public function parse ( $data )
	{
		$rtnData = array();

		if ( $data != '' ) {
			$rtnData = json_decode( $data, true );
			if ( function_exists( 'json_last_error' ) ) {
				$this->log( $this->getJsonError( json_last_error() ) );
			}
		}

		if ( !is_array( $rtnData ) ) {
			$rtnData = array( $rtnData );
		}

		return $rtnData;

	}


	private function getJsonError ( $errorCode )
	{
		$constants   = get_defined_constants(true);
		$json_errors = array();

		foreach ($constants["json"] as $name => $value) {
			if (!strncmp($name, "JSON_ERROR_", 11)) {
				$json_errors[$value] = $name;
			}
		}

		return $json_errors[$errorCode];

	}


}

} // end 'if class_exists'
