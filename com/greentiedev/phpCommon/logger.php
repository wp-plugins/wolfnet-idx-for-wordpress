<?php

if ( !class_exists( 'com_greentiedev_phpCommon_logger' ) ) {

/**
 * @package       com.greentiedev.phpCommon
 * @title         logger.php
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

class com_greentiedev_phpCommon_logger
{


	private static $instance;
	private $logData = array();
	private $levels  = array(
							'error'   => 0,
							'warning' => 1,
							'info'    => 2,
							'entry'   => 3,
							'param'   => 4,
							'debug'   => 5
							);
	public $settings;


	private function __construct()
	{
		$this->__id = spl_object_hash( $this );
		$this->settings = new com_greentiedev_phpCommon_loggerSettings();
		$this->logAdd( 'info', 'Post Logger Initialization' );
	}


	public static function getInstance ()
	{
		if ( !isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	public function __clone()
	{
		trigger_error('Clone is not allowed.', E_USER_ERROR);
	}


	public function __wakeup()
	{
		trigger_error('Unserializing is not allowed.', E_USER_ERROR);
	}


	public function dump ( $var )
	{
		if ( $this->settings->enabled ) {
			$arguments = func_get_args();
			foreach ( $arguments as $argument ) {
				$this->outputWrapper( $this->dataDumpRecursion( $var ) );
			}
		}
	}


	public function log ( $data = null )
	{
		$arguments = func_get_args();
		foreach ( $arguments as $argument ) {
			$this->_log( 'debug', $argument, false );
		}
	}


	public function logAdd ( $level, $data = null, $label = '' )
	{
		$this->_log( $level, $data, false, $label );
	}


	public function trace ( $data = null )
	{
		$arguments = func_get_args();
		foreach ( $arguments as $argument ) {
			$this->_log( 'debug', $argument, true );
		}
	}


	private function _log ( $level = 'debug', $data = null, $includeTrace = false, $label = '' )
	{

		$le              = array();
		if ( array_key_exists( $level, $this->levels ) ) {
			$le['level'] = $this->levels[$level];
		}
		else {
			$le['level'] = $this->levels[ max( $this->levels ) ];
		}
		$le['timestamp'] = $this->getTimestamp();
		$le['data']      = $data;
		$le['trace']     = ($includeTrace) ? debug_backtrace() : null ;
		$le['label']     = $label;

		array_push( $this->logData, $le );

	}


	public function dumpLog ()
	{
		if ( $this->settings->enabled ) {

			$this->logAdd( 'info', 'Initializing Log Dump' );
			if ( array_key_exists( min( $this->levels ) , $this->levels ) ) {
				$outputLevel = $this->levels[ min( $this->levels ) ];
			}
			if ( array_key_exists( $this->settings->level, $this->levels ) ) {
				$outputLevel = $this->levels[ $this->settings->level ];
			}

			$currentTime = $this->calcTime( $this->logData[0]['timestamp'] );

			$out  = '<h1>Debug Output</h1>';
			$out .= '<table class="log" border="1" cellspacing="0" cellpadding="5">';

			foreach ( $this->logData as $i => $le ) {

				$leTimeElapse = $this->calcTime( $le['timestamp'] );
				$leTimeDiff = $leTimeElapse - $currentTime;

				if ( array_key_exists( $i+1, $this->logData ) ) {
					$leTimeDur = $this->calcTime( $this->logData[$i+1]['timestamp'] ) - $leTimeElapse;
				}
				else {
					$leTimeDur = 0;
				}

				if ( $le['level'] <= $outputLevel && $leTimeDur >= $this->settings->minTime ) {

					$out .= '<tr>';
						$out .= '<td class="headerColumn">' . $leTimeElapse . ' ms'
							 . ' ( ' . $leTimeDur . ' ms )</td>';
						$out .= '<td>' . $le['level'] . '</td>';
						$out .= '<td>';
						if ( trim( $le['label'] ) != '' ) {
							$out .= '<strong>' . $le['label'] . '</strong><br/>';
						}
						$out .= '<div>' . $this->dataDumpRecursion( $le['data'] ) . '</div>';
						if ( $le['trace'] != null ) {
							$out .= '<div style="margin-top:1em;">' . $this->dataDumpRecursion( $le['trace'] ) . '</div>';
						}
						$out .= '</td>';
					$out .= '</tr>';

				}

				$currentTime = $leTimeElapse;

			}

			if ( $outputLevel >= $this->levels['info'] ) {

				$out .= '<tr>';
					$out .= '<td class="headerColumn">'
						 . $this->calcTime( $this->getTimestamp() ) . ' ms'
						 . ' ( ' . ( $this->calcTime( $this->getTimestamp() ) - $currentTime ) . ' ms )</td>';
					$out .= '<td>' . $this->levels['info'] . '</td>';
					$out .= '<td>Dump Generation Time</td>';
				$out .= '</tr>';

			}

			$out .= '</table>';

			$this->outputWrapper( $out );

			$this->dump(
					array(
						'GET'     => $_GET,
						'POST'    => $_POST,
						'REQUEST' => $_REQUEST,
						'SESSION' => ( isset( $_SESSION ) ) ? $_SESSION : 'Not Set',
						'FILES'   => $_FILES,
						'COOKIE'  => $_COOKIE,
						'SERVER'  => $_SERVER,
						'ENV'     => $_ENV
						)
					);

		}
	}


	private function dataDumpRecursion ( $data, $depth = 0 )
	{
		$out = '';
		if ( $depth >= $this->settings->recursionLimit ) {
			return '<span class="overflow">Recursion Overflow</span>';
		}
		if ( is_array( $data ) || is_object( $data ) ) {
			$tableClass = (is_array( $data )) ? 'array' : 'object';
			$out .= '<table class="' . $tableClass . '" border="1" cellspacing="0" cellpadding="5">';
			$out .= '<tr class="headerRow">';
			$out .= '<th colspan="2">';
			$out .= (is_array( $data )) ? 'Array' : 'Object : ' . get_class( $data ) . ' : ' . spl_object_hash( $data ) ;
			$out .= '</th>';
			$out .= '</tr>';
			if ( is_object( $data ) ) {
				$reflection = new ReflectionClass($data);
				$out .= '<tr>';
				$out .= '<td colspan="2">' . $this->dumpObjectMethods( $reflection ) . '</td>';
				$out .= '</tr>';
			}
			foreach( $data as $key => $value ) {
				$out .= '<tr>';
				$out .= '<td class="headerColumn">' . $key .  '</td>';
				$out .= '<td><pre>' . $this->dataDumpRecursion( $value, $depth+1 ) . '</pre></td>';
				$out .= '</tr>';
			}
			$out .= '</table>';

		}
		else {
			$out = $data;
		}

		return $out;

	}


	private function dumpObjectMethods ( $reflectionClass )
	{
		$methods = $reflectionClass->getMethods();

		$out = '';

		$out .= '<table class="objectMethods" border="1" cellspacing="0" cellpadding="5">';

		foreach ( $methods as $method ) {
			$parameters = $method->getParameters();
			if ( $method->class == $reflectionClass->getName() ) {
				$out .= '<tr>';
				$out .= '<td class="headerColumn">' . $method->getName() .  '</td>';
				$out .= '<td>';
				if ( $method->getNumberOfParameters() > 0 ) {
					foreach ( $parameters as $parameter ) {
						$out .= $parameter->name . '; ';
					}
				}
				else {
					$out .= '<span class="void">void</span>';
				}
				$out .= '</td>';
				$out .= '</tr>';
			}
		}

		$out .= '</table>';

		return $out;

	}


	private function outputWrapper ( $output, $pre=false )
	{
		$sep = DIRECTORY_SEPARATOR;

		$html = file_get_contents( dirname(__FILE__) . $sep . 'logger' . $sep . 'dump.html' );

		$html = str_replace( '[[fontSize]]', $this->settings->fontSize, $html );
		$html = str_replace( '[[fontFace]]', $this->settings->fontFace, $html );

		if ( $pre ) {
			$html = str_replace( '[[pre]]', '<pre>', $html );
			$html = str_replace( '[[/pre]]', '</pre>', $html );
		}
		else {
			$html = str_replace( '[[pre]]', '', $html );
			$html = str_replace( '[[/pre]]', '', $html );
		}

		$html = str_replace( '[[output]]', $output, $html );

		echo $html;
	}


	private function calcTime ( $time )
	{
		if ( count( $this->logData ) > 0 ) {
			$initTime = $this->logData[0]['timestamp'];
		}
		else {
			$initTime = $this->getTimestamp();
		}
		return round( $time - $initTime );
	}


	private function getTimestamp ()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ( ( (float) $usec + (float) $sec ) * 1000 );
	}


}

} // end 'if class_exists'



if ( !class_exists( 'com_greentiedev_phpCommon_loggerSettings' ) ) {

class com_greentiedev_phpCommon_loggerSettings
{


	public $enabled        = false;
	public $fontSize       = '13px';
	public $fontFace       = 'Arial, sans-serif';
	public $recursionLimit = 10;
	public $level          = 'error';
	public $minTime        = 2;


}

} // end 'if class_exists'
