<?php

if ( !class_exists( 'com_ajmichels_wppf_shortcode_manager' ) ) {

/**
 * @package       com.ajmichels.wppf.shortcode
 * @title         manager.php
 * @extends       com_ajmichels_wppf_abstract_manager
 * @implements    com_ajmichels_common_iSingleton
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

class com_ajmichels_wppf_shortcode_manager
extends com_ajmichels_wppf_abstract_manager
implements com_ajmichels_common_iSingleton
{
	
	
	private static $instance;
	
	
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
	
	
	public function register ( com_ajmichels_wppf_shortcode_shortcode $obj )
	{
		
		$ref = new Reflection( $obj );
		if ( !is_array( $obj->tag ) ) {
			$tags = explode( ',', $obj->tag );
		}
		else {
			$tags = $obj->tag;
		}
		foreach ( $tags as $tag ) {
			add_shortcode( $tag, array( $obj, 'execute' ) );
		}
	}
	
	
}

} // end 'if class_exists'