<?php

if ( !class_exists( 'com_greentiedev_wppf_abstract_widget' ) ) {

/**
 *
 * @package       com.greentiedev.wppf.abstract
 * @title         widget.php
 * @extends       WP_Widget
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

abstract class com_greentiedev_wppf_abstract_widget
extends WP_Widget
{


	/* PROPERTIES ******************************************************************************* */

	public $options  = array();
	public $controls = array();


	/* CONSTRUCTOR ****************************************************************************** */

	public function __construct ( $id_base = false, $name )
	{
		$this->log( 'Initializing Widget: ' . $name );
		parent::WP_Widget( $id_base, $name, $this->options, $this->controls );
	}


	/* PROTECTED METHODS ************************************************************************ */

	/**
	 * This method both gets and parses widget option data.
	 *
	 * @param   array  $instance  An array of widget instance data
	 * @return  array
	 *
	 */
	protected function getOptionData ( $instance = null )
	{
		$options = array();

		foreach ( $this->options as $opt => $value ) {

			$options[$opt]	= array(
								'name'  =>$this->get_field_name( $opt ),
								'id'    =>$this->get_field_id( $opt ),
								'value' =>$value
								);

			if ( $instance != null && isset( $instance[$opt] ) ) {
				$options[$opt]['value'] = $instance[$opt];
			}

		}

		return $options;

	}


	/* LOGGING METHODS ************************************************************************** */
	/* Implementing Logging functionality as it cannont be inherited from abstractClass */

	private $logger = null;


	private function initLogger ()
	{
		if ( !isset( $this->logger ) ) {
			$this->logger = com_greentiedev_phpCommon_logger::getInstance();
		}
	}


	public function dump ( $data = null )
	{
		$this->initLogger();
		$args = func_get_args();
		call_user_func_array( array( &$this->logger, 'dump' ), $args );
	}


	public function log ( $data = null )
	{
		$this->initLogger();
		$args = func_get_args();
		call_user_func_array( array( &$this->logger, 'log' ), $args );
	}


	public function logAdd ( $level, $data = null )
	{
		$this->initLogger();
		$args = func_get_args();
		call_user_func_array( array( &$this->logger, 'logAdd' ), $args );
	}


	public function trace ( $data = null )
	{
		$this->initLogger();
		$args = func_get_args();
		call_user_func_array( array( &$this->logger, 'trace' ), $args );
	}


	public function dumpLog ()
	{
		$this->initLogger();
		$this->logger->dumpLog();
	}


	public function loggerSetting( $setting, $value )
	{
		$this->initLogger();
		$this->logger->settings->$setting = $value;
	}


}

} // end 'if class_exists'
