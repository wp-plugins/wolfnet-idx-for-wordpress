<?php

/**
 * This class servers as a simple placeholder for a bean reference that can be passed around until a
 * concrete bean definition is available.
 *
 * @package       com.greentiedev.phpSpring.bean
 * @title         reference.php
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
class com_greentiedev_phpSpring_bean_reference
extends com_greentiedev_phpCommon_abstractClass
{


	/**
	 * This bean ID property servers the same purpose as the ID property of the Bean Definition class
	 * ({@see com_greentiedev_phpSpring_bean_definition }}).
	 *
	 * @type  string
	 *
	 */
	private $beanID;


	/**
	 * This constructor method accepts a bean id string.
	 *
	 * @param   string  $beanID
	 * @return  void
	 *
	 */
	public function __construct ( $beanID )
	{

		$this->beanID = $beanID;
	}


	/* ACCESSORS ******************************************************************************** */


	/**
	 * A getter method for the bean id property.
	 *
	 * @return  string
	 *
	 */
	public function getID ()
	{

		return $this->beanID;
	}


}
