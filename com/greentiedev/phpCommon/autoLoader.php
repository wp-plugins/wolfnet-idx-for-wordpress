<?php

if ( !class_exists( 'com_greentiedev_phpCommon_autoLoader' ) ) {

/**
 * @package       com.greentiedev.phpCommon
 * @title         autoLoader.php
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

class com_greentiedev_phpCommon_autoLoader
{


	private static $instance;
	private $extension = '.php';
	private $paths = array();


	private function __construct( $path )
	{
		$this->addPath( $path );
		spl_autoload_register( array( &$this, 'load' ) );
	}


	public static function getInstance ( $path )
	{
		if ( !isset( self::$instance ) ) {
			self::$instance = new self( $path );
		}
		return self::$instance;
	}


	public function load ( $class )
	{
		$classPath = str_replace( '_', '\\', $class );
		$filePath = $classPath . $this->extension;
		foreach ( $this->getPaths() as $baseDir ) {
			$absPath = $this->formatPath( $baseDir . '\\' . $filePath );
			$fileExists = file_exists( $absPath );
			if ( $fileExists ) {
				include_once( $absPath );
				break;
			}
		}
	}


	private function formatPath ( $path )
	{
		$dirSeparator = DIRECTORY_SEPARATOR;
		$path = str_replace( '\\', '|', $path );
		$path = str_replace( '/', '|', $path );
		$path = str_replace( '|', $dirSeparator, $path );
		return $path;
	}


	private function addPath ( $path )
	{
		if ( is_array( $path ) ) {
			$this->paths = array_merge( $this->paths, $path );
		}
		elseif ( is_string( $path ) ) {
			array_push( $this->paths, $path );
		}
		else {
			$exceptionMsg =	 'The "path" value passed to autoLoader is not valid.  It should be '
							.'either a path (string) or array of paths.';
			throw new Exception( $exceptionMsg );
		}
	}


	private function getPaths ()
	{
		return $this->paths;
	}


	public function __clone ()
	{
		trigger_error( 'Clone is not allowed.', E_USER_ERROR );
	}


	public function __wakeup ()
	{
		trigger_error( 'Unserializing is not allowed.', E_USER_ERROR );
	}


}

} // end 'if class_exists'
