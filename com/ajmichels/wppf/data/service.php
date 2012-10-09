<?php

if ( !class_exists( 'com_ajmichels_wppf_data_service' ) ) {

/**
 * @package       com.ajmichels.wppf.data
 * @title         service.php
 * @extends       com_ajmichels_wppf_abstract_service
 * @implements    com_ajmichels_wppf_interface_iDataService
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

class com_ajmichels_wppf_data_service
extends com_ajmichels_wppf_abstract_service
implements com_ajmichels_wppf_interface_iDataService
{


	/* PROPERTIES ******************************************************************************* */

	private $cacheDir;
	private $cacheTime;
	private $clearCache = false;


	/* SINGLETON ENFORCEMENT ******************************************************************** */

	private static $instance;


	public static function getInstance ()
	{
		if ( !isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/* CONSTRUCTOR ****************************************************************************** */

	private function __construct ()
	{
		$this->setCacheDir( $this->formatPath( dirname(__FILE__) . '/cache/' ) );
		$this->setCacheTime( 15 );
	}


	/* PUBLIC METHODS *************************************************************************** */

	public function getData ( com_ajmichels_wppf_data_webServiceUrl $url )
	{
		$cacheSetting = $url->getCacheSetting();


		if ( $cacheSetting == 'never' || !$rawData = $this->resolveCachedData( (string) $url ) ) {

			$this->log( array( 'Getting Fresh Data', (string) $url ) );
			$httpResponse = wp_remote_get( (string) $url );
			$this->log( $httpResponse );

			if ( is_wp_error( $httpResponse ) || $httpResponse['response']['code'] != '200' ) {
				/** TODO: Add support for XML formatted messages */
				$rawData = '{"error":{"message":"A Connection Error Occured!","status":true'
				         . ',"detail":"' . (string) $url . '"}}';
			}
			else {
				$rawData = $httpResponse['body'];
			}

			if ( is_numeric( $cacheSetting ) ) {
				$this->setCacheTime( $cacheSetting );
				$this->establishCachedData( (string) $url, $rawData );
			}

		}
		else {
			$this->log( array( 'Getting Cached Data', (string) $url, $this->getCachedFileName( (string) $url ) ) );
		}

		$data = $this->getDataInterpreter()->parse( $rawData );

		return $data;
	}


	public function clearCache ()
	{
		if ( file_exists( $this->getCacheDir() ) ) {
			$this->deleteDir( $this->getCacheDir() );
		}
	}


	/* PRIVATE METHODS ************************************************************************** */


	private function resolveCachedData ( $url )
	{
		$clearCache  = $this->getClearCache();

		if ( $clearCache ) {
			$this->clearCache();
		}

		$cacheFile   = $this->getCacheDir() . $this->getCachedFileName( $url );
		$fileExists  = file_exists( $cacheFile );
		$cacheTime   = $this->getCacheTime();
		$currentTime = time();
		$expireTime  = $currentTime - ( $cacheTime * 60 );

		if ( $fileExists && $expireTime < filemtime( $cacheFile ) ) {
			return file_get_contents( $cacheFile );
		}
		else {
			return false;
		}

	}


	private function establishCachedData ( $url, $rawData )
	{
		$cacheTime = $this->getCacheTime();

		if ( $cacheTime > 0 ) {

			$cacheFilename = $this->getCacheDir() . $this->getCachedFileName( $url );

			$this->log( $cacheFilename );

			if ( file_exists( $this->getCacheDir() ) ) {
				if ( file_exists( $cacheFilename ) ) {
					unlink( $cacheFilename );
				}
			}
			else {
				mkdir( $this->getCacheDir() );
			}

			$cacheFile    = fopen(  $cacheFilename, 'x' );
			$writeResults = fwrite( $cacheFile, $rawData );
			                fclose( $cacheFile );

		}

	}


	private function getCachedFileName ( $url )
	{
		$diClass = get_class( $this->getDataInterpreter() );
		$hash = md5( $url . $diClass );
		$filename = $hash . '.tmp';
		return $filename;
	}


	/* ACCESSOR METHODS ************************************************************************* */

	private function getCacheDir ()
	{
		return $this->cacheDir;
	}


	private function setCacheDir ( $path )
	{
		$this->cacheDir = $path;
	}

	public function getCacheTime ()
	{
		return $this->cacheTime;
	}


	public function setCacheTime ( $minutes )
	{
		$this->cacheTime = $minutes;
	}

	public function getClearCache ()
	{
		return $this->clearCache;
	}


	public function setClearCache ( $clear )
	{
		$this->clearCache = $clear;
	}


	public function getDataInterpreter ()
	{

		return $this->dataInterpreter;
	}


	public function setDataInterpreter ( com_ajmichels_wppf_interface_iDataInterpreter $di )
	{

		$this->dataInterpreter = $di;
	}


}

} // end 'if class_exists'
