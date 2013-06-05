<?php

/**
 * @package       com.greentiedev.phpSpring.bean
 * @title         property.php
 * @contributors  AJ Michels (http://greentiedev.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, AJ Michels
 *
 *                ColdSpring, Copyright (c) 2005, David Ross, Chris Scott, Kurt Wiersma, Sean Corfield
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
class com_greentiedev_phpSpring_bean_property
extends com_greentiedev_phpCommon_abstractClass
{


	private $beanDefinition = null;
	private $name           = '';
	private $type           = '';
	private $value          = '';
	private $argName        = null;


	public function __construct ( com_greentiedev_phpSpring_bean_definition &$bean )
	{

		$this->setParentBeanDefinition( $bean );
	}


	public function addParentDefinitionDependency ( $refName )
	{

		$this->getParentBeanDefinition()->addDependency( $refName );
	}


	/* ACCESSORS ******************************************************************************** */


	public function getParentBeanDefinition ()
	{

		return $this->beanDefinition;
	}


	public function setParentBeanDefinition ( com_greentiedev_phpSpring_bean_definition &$bean )
	{

		$this->beanDefinition = $bean;
	}


	public function getName ()
	{

		return $this->name;
	}


	public function setName ( $name )
	{

		$this->name = $name;
	}


	public function getType ()
	{

		return $this->type;
	}


	public function setType ( $type )
	{

		$this->type = $type;
	}


	public function getValue ()
	{

		return $this->value;
	}


	public function setValue ( &$value )
	{

		$this->value = $value;
	}


	public function getArgumentName ()
	{

		if ( $this->argName != null ) {
			return $this->argName;
		}
		else {
			return $this->getName();
		}
	}


	public function setArgumentName ( $argName )
	{

		$this->argName = $argName;
	}


}
