<?php

if ( !class_exists( 'com_greentiedev_wppf_abstract_hook' ) ) {

/**
 *
 * @package       com.greentiedev.wppf.abstract
 * @title         hook.php
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

class com_greentiedev_wppf_abstract_hook
extends com_greentiedev_phpCommon_abstractClass
{


	/* PROPERTIES ******************************************************************************* */

	private $plugin   = null;
	private $isAction = false;
	private $isFilter = false;
	private $hookName = '';
    protected $arguments = array();


	/* CONSTRUCTOR METHOD *********************************************************************** */

	public function __construct ()
	{
		if ( is_a( $this, 'com_greentiedev_wppf_action_action' ) ) {
			$this->isAction = true;
		}

		if ( is_a( $this, 'com_greentiedev_wppf_filter_filter' ) ) {
			$this->isFilter = true;
		}

		$ref  = new ReflectionObject( $this );
		$file = $ref->getFileName();
		$dir  = dirname( $file );

		$this->hookName = str_replace( '/', '', str_replace( '\\', '', str_replace( '.php', '', str_replace( $dir, '', $file ) ) ) );

	}


	/* PUBLIC METHODS *************************************************************************** */

	final public function _execute ()
	{

        $this->arguments = func_get_args();

		if ( $this->isAction ) {
			do_action( $this->getPlugin()->getClassName() . '_pre_' . $this->hookName );
		}

		$this->execute();

		if ( $this->isAction ) {
			do_action( $this->getPlugin()->getClassName() . '_post_' . $this->hookName );
		}

	}


	/* ACCESSOR METHODS ************************************************************************* */

	public function getPlugin ()
	{
		return $this->plugin;
	}


	public function setPlugin ( com_greentiedev_wppf_bootstrap &$obj )
	{
		$this->plugin = $obj;
	}


}

} // end 'if class_exists'
