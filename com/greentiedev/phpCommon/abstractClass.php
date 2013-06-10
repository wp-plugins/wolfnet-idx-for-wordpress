<?php

if ( !class_exists( 'com_greentiedev_phpCommon_abstractClass' ) ) {

/**
 *
 * @package       com.greentiedev.phpCommon
 * @title         abstractClass.php
 * @contributors  AJ Michels (http://greentiedev.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, AJ Michels
 *
 *                Licensed under the Apache License, Version 2.0 (the "License");
 *                you may not use this file except in compliance with the License.
 *                You may obtain a copy of the License at
 *
 *                    http://www.apache.org/licenses/LICENSE-2.0
 *
 *                Unless required by applicable law or agreed to in writing, software
 *                distributed under the License is distributed on an "AS IS" BASIS,
 *                WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *                See the License for the specific language governing permissions and
 *                limitations under the License.
 *
 */

abstract class com_greentiedev_phpCommon_abstractClass
{


	private $logger = null;


	private function initLogger ()
	{
		if ( !isset( $this->logger ) ) {
			$this->logger = com_greentiedev_phpCommon_logger::getInstance();
		}
	}


	protected function dump ( $data = null )
	{
		$this->initLogger();
		$args = func_get_args();
		call_user_func_array( array( &$this->logger, 'dump' ), $args );
	}


	protected function log ( $data = null )
	{
		$this->initLogger();
		$args = func_get_args();
		call_user_func_array( array( &$this->logger, 'log' ), $args );
	}


	protected function logAdd ( $level, $data = null )
	{
		$this->initLogger();
		$args = func_get_args();
		call_user_func_array( array( &$this->logger, 'logAdd' ), $args );
	}


	protected function trace ( $data = null )
	{
		$this->initLogger();
		$args = func_get_args();
		call_user_func_array( array( &$this->logger, 'trace' ), $args );
	}


	protected function dumpLog ()
	{
		$this->initLogger();
		$this->logger->dumpLog();
	}


	protected function loggerSetting( $setting, $value )
	{
		$this->initLogger();
		$this->logger->settings->$setting = $value;
	}


	protected function formatPath ( $path )
	{
		$path = str_replace( '\\', '|', $path );
		$path = str_replace( '/', '|', $path );
		$path = str_replace( '|', DIRECTORY_SEPARATOR, $path );
		return $path;
	}


	public function _formatURL ( $url, $length = 'full' )
	{
		$string = trim($url);
		if (trim($string) != '') {
			if ($length == 'short') {
				$string = str_replace('http://www.', '', $string);
				$string = str_replace('https://www.', '', $string);
				$string = str_replace('www.', '', $string);
			}
			/* remove http and/or https */
			$string = str_replace('http://', '', $string);
			$string = str_replace('https://', '', $string);
			if ($length != 'short') {
				/* add http:// back to beginning of string */
				$string = 'http://' . $string;
			}
			if (substr($string, -1) == '/') {
				$string = substr($string, 0, strlen($string) - 1);
			}
		}
		return $string;
	}


	protected function is_assoc ( $arr)
	{
		return ( is_array($arr) && count(array_filter(array_keys($arr),'is_string')) == count($arr));
	}


	protected function isSimpleValue ( $value )
	{
		if ( is_numeric($value) || is_string($value) || is_bool($value) ) {
			return true;
		}
		else {
			return false;
		}
	}


	protected function getMetaData ( $object )
	{
		$reflection = new ReflectionClass( $object );
		return $reflection;
	}

	/*
	public function flattenMetaData ( $metaData )
	{
		$flattenedMetaData = array( 'name'=>'', 'functions'=>array() );
		return $flattenedMetaData;
	}
	*/

	protected function isClass ( $object )
	{
	}


	protected static function deleteDir ($dirPath)
	{
		if ( !is_dir($dirPath) ) {
			throw new InvalidArgumentException('$dirPath must be a directory.');
		}

		if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
			$dirPath .= '/';
		}

		$files = glob($dirPath . '*', GLOB_MARK);

		foreach ( $files as $file ) {
			if ( is_dir( $file ) ) {
				self::deleteDir( $file );
			} else {
				unlink( $file );
			}
		}

		rmdir( $dirPath );

	}


}

} // end 'if class_exists'
