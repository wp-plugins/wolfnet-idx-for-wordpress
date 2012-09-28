<?php

if ( !class_exists( 'com_ajmichels_wppf_abstract_view' ) ) {

/**
 *
 * @package       com.ajmichels.wppf.abstract
 * @title         view.php
 * @extends       com_ajmichels_common_abstractClass
 * @implements    com_ajmichels_wppf_interface_iView
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

abstract class com_ajmichels_wppf_abstract_view
extends com_ajmichels_common_abstractClass
implements com_ajmichels_wppf_interface_iView
{


	protected $data = array();
	protected $useTemplateEngine = false;
	protected $template = '';

	private $templateEngine = null;


	public function render ( $data = array() )
	{

		return $this->processTemplate( $data );
	}


	public function out ( $data = array() )
	{

		echo( $this->render( $data ) );
	}


	private function processTemplate ( $data = null )
	{

		if ( isset( $this->template ) && file_exists( $this->template ) ) {
			if ( $this->useTemplateEngine ) {
				return $this->getTemplateEngine()->parseTemplate( $this->template, $data );
			}
			else {
				return $this->processTemplateAsPhp( $data );
			}
		}

	}


	private function processTemplateAsPhp ( $__data = null )
	{

		$__local = array( '__local', '__data', '__outputBuffer', '__dataItem', '__dataItemKey' );

		$__outputBuffer = '';

		if ( is_array($__data) ) {
			$__data = array_merge( $__data, $this->data );
			foreach ($__data as $__dataItemKey => $__dataItem) {
				if ( !array_key_exists( $__dataItemKey, $__local ) ) {
					${$__dataItemKey} = $__dataItem;
				}
			}
		}

		ob_start();
		include $this->template;
		$__outputBuffer = ob_get_clean();

		return $__outputBuffer;

	}


	/* ACCESSORS ******************************************************************************** */

	protected function setTemplate ( $path )
	{

		$this->template = $this->formatPath( $path );
	}

	final public function setTemplateEngine ( com_ajmichels_wppf_interface_iTemplateEngine $engine )
	{

		$this->templateEngine = $engine;
	}


	final public function getTemplateEngine ()
	{

		if ( $this->templateEngine == null ) {
			$this->setTemplateEngine( com_ajmichels_wppf_template_defaultEngine::getInstance() );
		}
		return $this->templateEngine;
	}


}

} // end 'if class_exists'