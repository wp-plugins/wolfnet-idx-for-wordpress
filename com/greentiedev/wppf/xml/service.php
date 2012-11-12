<?php

if ( !class_exists( 'com_greentiedev_wppf_xml_service' ) ) {

/**
 * @package       com.greentiedev.wppf.xml
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

class com_greentiedev_wppf_xml_service
extends com_greentiedev_wppf_abstract_service
implements com_greentiedev_wppf_interface_iDataInterpreter
{


	private static $instance;
	private $data = array ();


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


		if ($data != '') {
			$sxe = simplexml_load_string($data);
			$this->parseNode($sxe, $this->data);
			$rtnData = $this->data;
		}
		else {
			$rtnData = array();
		}
		return $rtnData;
	}


	private function parseNode ( $node, &$data, $isAssociative = true )
	{

		if (count($node)==0) {
			$data[$node->getName()] = (string)$node;
		}
		else {
			if ($isAssociative) {
				$data[$node->getName()] = array ();
				$childData = &$data[$node->getName()];
			}
			else {
				$childData = &$data[];
			}
			foreach ($node->attributes() as $attribute) {
				$childData[$attribute->getName()] = (string)$attribute;
			}
			$childAssociative = $this->associativeTest($node);
			foreach ($node as $child) {
				$this->parseNode($child, $childData, $childAssociative);
			}
		}
	}


	private function associativeTest ( $node )
	{

		$firstChildName = '';
		foreach ($node as $child) {
			if ($firstChildName == '') {
				$firstChildName = $child->getName();
			}
			elseif ($firstChildName == $child->getName()) {
				return false;
			}
		}
		return true;
	}


}

} // end 'if class_exists'
