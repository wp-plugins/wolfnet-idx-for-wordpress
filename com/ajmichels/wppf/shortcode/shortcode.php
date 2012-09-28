<?php

if ( !class_exists( 'com_ajmichels_wppf_shortcode_shortcode' ) ) {

/**
 * @package       com.ajmichels.wppf.shortcode
 * @title         shortcode.php
 * @extends       com_ajmichels_common_abstractClass
 * @implements    com_ajmichels_wppf_interface_iManaged
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

abstract class com_ajmichels_wppf_shortcode_shortcode
extends com_ajmichels_common_abstractClass
implements com_ajmichels_wppf_interface_iManaged
{
	
	
	/* PROPERTIES ******************************************************************************* */
	
	private   $plugin     = null;
	protected $attributes = array();
	
	
	/* ABSTRACT METHODS ************************************************************************* */
	
	abstract public function execute ( $attr, $content = null );
	
	
	/* PROTECTED METHODS ************************************************************************ */
	
	/**
	 * This method both gets and parses widget option data.
	 * 
	 * @param   array  $instance  An array of shortcode instance data
	 * @return  array
	 * 
	 */
	protected function getAttributesData ( $instance = null )
	{
		$attributes = array();
		
		foreach ( $this->attributes as $attr => $value ) {
			
			$attributes[$attr] = array(
									'name'  => $attr,
									'id'    => $attr,
									'value' => $value
									);
			
			if ( $instance != null && isset( $instance[$attr] ) ) {
				$attributes[$attr]['value'] = $instance[$attr];
			}
			
		}
		
		return $attributes;
		
	}
	
	
	/* ACCESSOR METHODS ************************************************************************* */
	
	public function getPlugin ()
	{
		return $this->plugin;
	}
	
	
	public function setPlugin ( com_ajmichels_wppf_bootstrap &$obj )
	{
		$this->plugin = $obj;
	}
	
	
}

} // end 'if class_exists'