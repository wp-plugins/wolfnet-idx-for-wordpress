<?php

/**
 * @package       com.greentiedev.phpSpring.bean
 * @title         definition.php
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
 * @todo          Add support for parent/child beans.
 */
class com_greentiedev_phpSpring_bean_definition
extends com_greentiedev_phpCommon_abstractClass
{


	/**
	 * This property is a reference to the bean factory to which this bean definition belongs.
	 * @type com_greentiedev_phpSpring_bean_factory_iFactory
	 */
	private $beanFactory;

	/**
	 * This property is the unique id string for this bean definition. This value is use to reference
	 * and retrieve a bean from a bean factory.
	 * @type string
	 */
	private $id;

	/**
	 * This property contains the PHP class which is used to create the bean.
	 * @type string
	 */
	private $beanClass;

	/**
	 * This property contains a reference to the bean created using the definition. (Note: this value
	 * will always be null if the bean is not a singleton.
	 * @type object
	 */
	private $beanInstance        = null;

	private $properties          = array(); // array of all the properties for this bean definition
	private $singleton           = true;    // whether this bean is a singleton or a prototype
	private $innerBean           = false;   // whether this bean is an inner bean
	private $initMethod          = '';      // name of an init-method to call on this bean once all dependencies are set
	private $initMethodWasCalled = false;   // if the init-method exists, whether it has been called
	private $lazyInit            = true;    // whether this bean should be constructed imeediately upon the beanFactory receiving its definition
	private $dependencies        = array(); // list of known dependent beans (by name)
	private $constructed         = false;

	/* whether the dependencies have actually been checked already */
	private $dependenciesChecked = false;
	private $dependentBeans      = 0;


	public function __construct ( com_greentiedev_phpSpring_bean_factory_iFactory &$beanFactory )
	{

		$this->setBeanFactory( $beanFactory );
	}


	public function getInstance ()
	{

		return $this->getBeanFactory()->getBeanFromSingletonCache( $this->getID() );
	}


	public function getBeanInstance ()
	{

		/* create this if it doesn't exist */
		try {

			$constructArgs = array();

			$ref = new ReflectionClass( $this->getClass() );

			if ( $this->isSingleton() ) {
				if ( $this->beanInstance == null ) {
					if ( array_search( 'com_greentiedev_phpCommon_iSingleton', $ref->getInterfaceNames() ) !== false ) {
						$this->beanInstance = call_user_func_array( $this->getClass() . '::getInstance', $constructArgs );
					}
					else {
						if ( $ref->getConstructor() ) {
							$this->beanInstance = $ref->newInstanceArgs( $constructArgs );
						}
						else {
							$this->beanInstance = $ref->newInstance();
						}
					}
				}
				$bean = $this->beanInstance;
			}
			else {
				if ( $ref->getConstructor() ) {
					$bean = $ref->newInstanceArgs( $constructArgs );
				}
				else {
					$bean = $ref->newInstance();
				}
			}

		}
		catch ( Exception $e ) {
			if ( version_compare( PHP_VERSION , '5.3.0' ) >= 0 ) {
				throw new Exception( 'Bean creation exception in ' . $this->getClass(), null, $e );
			}
			else {
				throw new Exception( 'Bean creation exception in ' . $this->getClass() . "\n" . $e->getMessage(), null );
			}
		}

		return $bean;

	}


	/* ACCESSORS ******************************************************************************** */


	public function getBeanFactory ()
	{

		return $this->beanFactory;
	}


	public function setBeanFactory ( com_greentiedev_phpSpring_bean_factory_iFactory &$beanFactory )
	{

		$this->beanFactory = $beanFactory;
	}


	public function getID ()
	{

		return $this->id;
	}


	public function setID ( $id )
	{

		$this->id = $id;
	}


	public function getClass ()
	{

		return $this->beanClass;
	}


	public function setClass ( $beanClass )
	{

		$ref = new ReflectionClass( $beanClass );
		if ( array_search( 'com_greentiedev_phpCommon_iSingleton', $ref->getInterfaceNames() ) !== false ) {
			$this->setSingleton( true );
		}
		$this->beanClass = $beanClass;
	}


	public function getProperties ()
	{

		return $this->properties;
	}


	public function setProperties ( $properties )
	{

		$this->properties = $properties;
	}


	public function addProperty ( com_greentiedev_phpSpring_bean_property $property )
	{

		$this->properties[$property->getName()] = $property;
	}


	public function getProperty ( $propertyName )
	{

		if ( array_key_exists( $propertyName, $this->properties ) ) {
			return $this->properties[$propertyName];
		}
		else {
			throw new Exception( 'Property requested "' . $propertyName . '" does not exist for bean: ' . $this->getID() );
		}
	}


	public function addDependency ( $refName )
	{

		if ( !array_search( $refName, $this->dependencies ) ) {
			array_push( $this->dependencies, $refName );
		}
	}


	public function setDependenciesForCopy ( $dependencies )
	{

		$this->dependencies = $dependencies;
	}


	public function getDependenciesForCopy ()
	{

		return $this->dependencies;
	}


	public function getDependencies ( $grouping = 'local' )
	{

		$dependencies = $this->dependencies;
		if ( $grouping == 'all' ) {
			foreach ( $this->dependencies as $dependency ) {
				$beanDefinition = $this->getBeanFactory()->getBeanDefinition( $dependency );
				$dependencies = array_merge( $dependencies, $beanDefinition->getDependencies( 'all' ) );
			}
			/* Reverse the array, remove duplicates, Reverse the array
			 * this is done so that the "most dependent" items are on the bottom of the array */
			$dependencies = array_reverse( array_unique( array_reverse( $dependencies ) ) );
		}
		return array_unique( $dependencies );
	}


	public function dependenciesChecked ()
	{

		return $this->dependenciesChecked;
	}


	public function getDependentBeans ()
	{

		return $this->dependentBeans;
	}


	public function isSingleton ()
	{

		return $this->singleton;
	}


	public function setSingleton ( $singleton )
	{

		if ( $singleton == false && $this->getClass() != '' ) {
			$ref = new ReflectionClass( $this->getClass() );
			if ( array_search( 'com_greentiedev_phpCommon_iSingleton', $ref->getInterfaceNames() ) !== false ) {
				$singleton = true;
			}
		}
		$this->singleton = $singleton;
	}


	public function isLazyInit ()
	{

		return $this->lazyInit;
	}


	public function setLazyInit ( $lazyInit )
	{

		$this->lazyInit = $lazyInit;
	}


	public function isInnerBean ()
	{

		return $this->InnerBean;
	}


	public function setInnerBean ( $innerBean )
	{

		$this->innerBean = $innerBean;
	}


	public function isConstructed ()
	{

		return $this->constructed;
	}


	public function setIsConstructed ( $constructed )
	{

		$this->constructed = $constructed;
	}


	public function setInitMethod ( $initMethod )
	{

		$this->initMethod = $initMethod;
	}


	public function getInitMethod ()
	{

		return $this->initMethod;
	}


	public function hasInitMethod ()
	{

		if ( strlen( trim( $this->initMethod ) ) > 0 ) {
			return true;
		}
		else {
			return false;
		}
	}


	public function setInitMethodWasCalled ( $initMethodWasCalled )
	{

		$this->initMethodWasCalled = $initMethodWasCalled;
	}


	public function getInitMethodWasCalled ()
	{

		return $this->initMethodWasCalled;
	}


}
