<?php

if ( !class_exists( 'com_greentiedev_wppf_data_webServiceUrl' ) ) {

/**
 * @package       com.greentiedev.wppf.data
 * @title         webServiceUrl.php
 * @extends       com_greentiedev_phpCommon_abstractClass
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

class com_greentiedev_wppf_data_webServiceUrl
extends com_greentiedev_phpCommon_abstractClass
{


	/* PROPERTIES ******************************************************************************* */

	private $domain       = '';
	private $scriptPath   = '';
	private $parameters   = array();
	private $cacheSetting = 'none';
	private $context      = array();


	/* CONSTRUCTOR ****************************************************************************** */

	public function __construct ()
	{

	}


	/* PUBLIC METHODS *************************************************************************** */

	public function hasParameter ( $name )
	{

		return array_key_exists( $this->parameters, $name );
	}


	public function removeParameter ( $name )
	{

		if ( $this->hasParameter( $name ) ) {
			unset( $this->parameters[$name] );
		}
	}


	public function clearParameters ()
	{

		$this->parameters = array();
	}


	public function toString ()
	{

		$url  = $this->getDomain();
		$url .= $this->getScriptPath();
		$params = $this->getParameters();
		if ( count($params) > 0 ) {
			$url .= '?';
			$firstParam = true;
			foreach( $params as $parameter => $value ) {
				if ( $firstParam ) {
					$firstParam = false;
				}
				else {
					$url .= '&';
				}
				$url .= $parameter . '=' . $value;
			}
		}
		return $url;
	}

	public function __toString ()
	{
		return $this->toString();
	}


	/* ACCESSOR METHODS ************************************************************************* */

	public function setDomain ( $domain )
	{

		$this->domain = $domain;
	}


	public function getDomain ()
	{

		return $this->domain;
	}


	public function setScriptPath ( $path )
	{

		$this->scriptPath = $path;
	}


	public function getScriptPath ()
	{

		return $this->scriptPath;
	}


	public function setParameter ( $name, $value = '' )
	{

		$this->parameters[$name] = urlencode( $value );
	}


	public function getParameter ( $name )
	{

		return $this->parameters[$name];
	}


	public function getParameters ()
	{

		return $this->parameters;
	}


	public function setParameters ( $params = array() )
	{

		foreach ( $params as $param => $value ) {
			$this->setParameter( $param, $value );
		}
	}


	public function getCacheSetting ()
	{
		return $this->cacheSetting;
	}


	public function setCacheSetting ( $setting )
	{
		if ( $setting == 'never' ) {
			$this->setParameter( 'neverCache', uniqid() );
		}
		else if ( $setting == 'forever' ) {
			$setting = 315360000; /* 10 years */
		}
		$this->cacheSetting = $setting;
	}


	public function setContext ( $options )
	{
		$this->context = $options;
	}


	public function getContext ( )
	{
		stream_context_create( $this->context );
	}


}

} // end 'if class_exists'
