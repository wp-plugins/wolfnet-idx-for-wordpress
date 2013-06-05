<?php

if ( !class_exists( 'com_greentiedev_wppf_data_service' ) ) {

/**
 * @package       com.greentiedev.wppf.data
 * @title         service.php
 * @extends       com_greentiedev_wppf_abstract_service
 * @implements    com_greentiedev_wppf_interface_iDataService
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

class com_greentiedev_wppf_data_service
extends com_greentiedev_wppf_abstract_service
implements com_greentiedev_wppf_interface_iDataService
{


	/* PROPERTIES ******************************************************************************* */

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
		// Set the default cache time.
		$this->setCacheTime( 15 );

		if ( array_key_exists( '-clearcache', $_REQUEST ) ) {
			if ( $_REQUEST['-clearcache'] == 'all' && !array_key_exists( 'wppf_cachecleared', $_REQUEST ) ) {
				$_REQUEST['wppf_cachecleared'] = true;
				$this->clearEntireCache();
			}
			else {
				$this->setClearCache( true );
			}
		}

	}


	/* PUBLIC METHODS *************************************************************************** */

	public function getData ( com_greentiedev_wppf_data_webServiceUrl $url )
	{
		$cacheSetting = $url->getCacheSetting();

		$cacheData    = $this->resolveCachedData( (string) $url );
		$rawData      = ( $cacheData !== false ) ? $cacheData['data'] : false ;
		$cacheExpired = ( $cacheData !== false ) ? $cacheData['expired'] : false ;

		if ( $cacheSetting == 'never' || $rawData === false || $cacheExpired ) {

			$this->log( array( 'Getting Fresh Data', (string) $url ) );

			$httpResponse = wp_remote_get( (string) $url, array( 'timeout'=>180 ) );

			$this->log( array( "HTTP Request", (string) is_wp_error( $httpResponse ), $httpResponse ) );

			/* There was a server side error so try to reuse the cached data. */
			if ( !is_wp_error( $httpResponse ) && $httpResponse['response']['code'] >= 500 ) {

				$cacheSetting = 'never'; // There was an error on the remote end, so don't cache the response.

				if ( $rawData === false ) {
					/** TODO: Add support for XML formatted messages */
					$rawData = '{"error":{"message":"A Connection Error Occured!","status":true'
					         . ',"detail":"' . (string) $url . '"}}';
				}
				else {
					$this->log( array( "Reusing Existing Cached Data because there was an error.", (string) $url ) );
				}

				print('<!-- WPPF ERROR: Data retrieval error. There was a 500 error on the remote server. -->');

			}
			/* There was a client side error so return an error message and clear the cache. */
			elseif ( is_wp_error( $httpResponse ) || $httpResponse['response']['code'] >= 400 ) {

				$cacheSetting = 'never'; // There was an error on the remote end, so don't cache the response.

				/** TODO: Add support for XML formatted messages */
				$rawData = '{"error":{"message":"A Connection Error Occured!","status":true'
				         . ',"detail":"' . (string) $url . '"}}';

				print('<!-- WPPF ERROR: Data retrieval error. Failed to connect to the remote server. -->');

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
			$this->log( array( 'Getting Cached Data', (string) $url, $this->getCacheKey( (string) $url ) ) );
		}

		$data = $this->getDataInterpreter()->parse( $rawData );

		return $data;

	}


	/* PRIVATE METHODS ************************************************************************** */

	private function resolveCachedData ( $url )
	{
		if ( $this->getClearCache() ) {
			$this->clearCache( $url );
			return false;
		}
		else {
			$metaData = $this->getCacheMetaData();
			$cacheKey = $this->getCacheKey( $url );

			if ( is_array($metaData) && array_key_exists( $cacheKey, $metaData ) ) {
				$data = array(
					'data'    => get_transient( $cacheKey ),
					'expired' => ( time() > $metaData[$cacheKey] )
					);
				return $data;
			}
			else {
				return false;
			}
		}

	}


	private function establishCachedData ( $url, $rawData )
	{
		$metaData            = $this->getCacheMetaData();
		$cacheKey            = $this->getCacheKey( $url );
		$cacheTime           = $this->getCacheTime();

		if ( is_numeric( $cacheTime ) && $cacheTime > 0 ) {

			$metaData[$cacheKey] = time() + ( $cacheTime * 60 );

			$this->log( array( 'Saving Cache Data', $cacheKey, $cacheTime ) );

			// WordPress Transient API
			set_transient( $this->getCacheKey( $url ), $rawData, 0 );

			$this->setCacheMetaData( $metaData );

		}

	}


	private function getCacheKey ( $url )
	{
		$diClass = get_class( $this->getDataInterpreter() );
		$hash    = md5( $url . $diClass );
		$key     = $hash;

		return $key;

	}


	private function getCacheMetaData ()
	{

		return get_transient( 'wppf_cache_metadata' );

	}


	private function setCacheMetaData ( $data )
	{

		set_transient( 'wppf_cache_metadata', $data, 0 );

	}


	private function clearCache ( $url )
	{
		$metaData = $this->getCacheMetaData();
		$cacheKey = $this->getCacheKey( $url );

		if ( is_array($metaData) && array_key_exists( $cacheKey, $metaData ) ) {
			$this->log( array( 'Clearing Cache', $cacheKey ) );
			unset( $metaData[$cacheKey] );
			$this->setCacheMetaData( $metaData );
			delete_transient( $cacheKey );
		}
		else {
			return false;
		}

	}


	private function clearEntireCache ()
	{
		$metaData = $this->getCacheMetaData();

		if ( is_array($metaData) ) {
			$this->log( "Clearing Entire Cache" );
			foreach ( $metaData as $key => $data ) {
				delete_transient( $key );
			}
		}

		$this->setCacheMetaData( array() );

	}


	/* ACCESSOR METHODS ************************************************************************* */

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


	public function setDataInterpreter ( com_greentiedev_wppf_interface_iDataInterpreter $di )
	{

		$this->dataInterpreter = $di;

	}


}

} // end 'if class_exists'
