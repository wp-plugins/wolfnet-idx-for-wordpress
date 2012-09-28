<?php

if ( !class_exists( 'com_ajmichels_wppf_json_service' ) ) {

/**
 * @package       com.ajmichels.wppf.json
 * @title         service.php
 * @extends       com_ajmichels_wppf_abstract_service
 * @implements    com_ajmichels_wppf_interface_iDataInterpreter
 * @contributors  AJ Michels (http://ajmichels.com)
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

class com_ajmichels_wppf_json_service
extends com_ajmichels_wppf_abstract_service
implements com_ajmichels_wppf_interface_iDataInterpreter
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
		}
		
		if ( !is_array( $rtnData ) ) {
			$rtnData = array( $rtnData );
		}
		
		return $rtnData;
		
	}
	
	
}

} // end 'if class_exists'