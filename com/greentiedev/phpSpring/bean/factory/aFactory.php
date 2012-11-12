<?php

/**
 * @package       com.greentiedev.phpSpring.bean.factory
 * @title         aFactory.php
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
abstract class com_greentiedev_phpSpring_bean_factory_aFactory
extends com_greentiedev_phpCommon_abstractClass
{


	public  $id;
	protected $beanDefinitions                = array();
	protected $parentFactory                  = null;
	protected $singletonCache                 = array();
	protected $aliasMap                       = array();
	protected $knownBeanFactoryPostProcessors = array();
	protected $knownBeanPostProcessors        = array();
	protected $frameworkPropertiesFile        = '';
	protected $frameworkProperties            = array();
	protected $defaultProperties              = array();
	protected $defaultAttributes              = array();


	protected function __construct ( $id, $defaultAttributes = array(), &$defaultProperties = array() )
	{

		$this->id = $id;
		$this->setDefaultAttributes( $defaultAttributes );
		$this->setDefaultProperties( $defaultProperties );
	}


	public function getParent ()
	{

		return $this->parentFactory;
	}


	public function setParent ( com_greentiedev_phpSpring_bean_factory_iFactory $parent )
	{

		$this->parentFactory = $parent;
	}


	public function singletonCacheContainsBean ( $beanName )
	{

		$objExists = array_key_exists( $beanName, $this->singletonCache );
		if ( !$objExists && is_object( $this->parentFactory ) ) {
			$objExists = $this->parentFactory->singletonCacheContainsBean( $beanName );
		}
		return $objExists;
	}


	public function getBeanFromSingletonCache ( $beanName )
	{

		$objExists = true;
		if ( array_key_exists( $beanName, $this->singletonCache ) ) {
			$objRef = $this->singletonCache[$beanName];
		}
		else {
			$objExists = false;
		}

		if ( !$objExists ) {
			if ( is_object( $this->parentFactory ) ) {
				$objRef = $this->parentFactory->getBeanFromSingletonCache( $beanName );
			}
			else {
				if ( version_compare( PHP_VERSION , '5.3.0' ) >= 0 ) {
					throw new Exception( 'Cache error, ' . $beanName . ' does not exist;', null, $e );
				}
				else {
					throw new Exception( 'Cache error, ' . $beanName . ' does not exist;' . "\n" . $e->getMessage(), null );
				}
			}
		}

		return $objRef;
	}


	public function addBeanToSingletonCache ( $beanName, &$beanObject )
	{

		if ( array_key_exists( $beanName, $this->singletonCache ) ) {
			throw new Exception( 'Cache error, ' . $beanName . ' already exists in cache' );
		}
		else {
			$this->singletonCache[$beanName] = $beanObject;
		}
	}


	public function getBeanDefinition ( $beanName )
	{

		/* the supplied 'beanName' could be an alias, so we want to resolve that to the concrete name first */
		$resolvedName = $this->resolveBeanName( $beanName );
		if ( !array_key_exists( $resolvedName, $this->beanDefinitions ) ) {
			if ( is_object( $this->parentFactory ) ) {
				return $this->parentFactory->getBeanDefinition( $resolvedName );
			}
			else {
				throw new Exception( 'There is no bean registered with the factory with the id ' . $beanName );
			}
		}
		else {
			return $this->beanDefinitions[$resolvedName];
		}
	}


	public function beanDefinitionExists ( $beanName )
	{

		/* the supplied 'beanName' could be an alias, so we want to resolve that to the concrete name first */
		$resolvedName = $this->resolveBeanName( $beanName );
		if ( !array_key_exists( $resolvedName, $this->beanDefinitions ) ) {
			return true;
		}
		else {
			if ( is_object( $this->parentFactory ) ) {
				return $this->parentFactory->beanDefinitionExists( $resolvedName );
			}
			else {
				return false;
			}
		}
	}


	public function getBeanDefinitionList ()
	{

		return $this->beanDefinitions;
	}


	public function registerAlias ( $beanName, $alias )
	{

		if ( array_key_exists( $alias, $this->aliasMap ) ) {
			throw new Exception( 'The alias ' . $alias . ' is already registered for bean ' . $this->aliasMap[$alias] );
		}
		else {
			$this->aliasMap[$alias] = $beanName;
		}
	}


	public function resolveBeanName ( $name )
	{

		/* first look to resolve alias, if we don;t have the alias mapped, return supplied bean name */
		if ( array_key_exists( $name, $this->aliasMap ) ) {
			return $this->aliasMap[$name];
		}
		else {
			return $name;
		}
	}


	public function getVersion ()
	{

		return $this->getFrameworkProperties()->getProperty( 'majorVersion' );
	}


	public function getFrameworkProperties ()
	{

		return $this->frameworkProperties;
	}


	private function loadFrameworkProperties ( $propertiesFile )
	{

		// TODO: Implement this method.  CF version used Java.
	}


	/**
	 * This method takes in several parameters and uses them to create a new BeanDefinition object.
	 * It also facilitates the creation of bean properties.
	 *
	 * @param  string   $beanID       Identification key for a bean.
	 * @param  string   $beanClass    PHP class name of the bean
	 * @param  array    $children     Array of child XML nodes.
	 * @param  boolean  $isSingleton  Boolean to determine if the bean is a singleton or transient.
	 * @param  boolean  $isInnerBean  Boolean to determine if the bean is nested inside another bean.
	 * @param  boolean  $isLazyInit   Boolean to determine if the bean should be initialized
	 *                                imediately or upon request.
	 * @param string    $initMethod   Name of a method with the bean to call imediately after it
	 *                                is created. (Note: this is not the same as the constructor.)
	 * @return com_greentiedev_phpSpring_bean_definition
	 *
	 * @todo   Add support for parent/child beans.
	 * @todo   Add support for constructor-arg child nodes.
	 * @todo   Move bean child xml parsing into this class rather than doing it inside the property
	 *         definition object.
	 */
	protected function createBeanDefinition ( $beanID, $beanClass, $isSingleton, $isInnerBean,
											$isLazyInit = false, $initMethod = '' )
	{

		/* Create a new instance of a bean definition and set its properties. */
		$beanDefinition = new com_greentiedev_phpSpring_bean_definition( $this );
		$beanDefinition->setID( $beanID );
		$beanDefinition->setClass( $beanClass );
		$beanDefinition->setSingleton( $isSingleton );
		$beanDefinition->setInnerBean( $isInnerBean );
		$beanDefinition->setLazyInit( $isLazyInit );

		/* Add a reference to the new bean definintion to the factory's beanDefinition array. */
		$this->beanDefinitions[$beanDefinition->getID()] = &$beanDefinition;

		/* If the initMethod is not empty string assign the property to the definition. */
		if ( strlen( $initMethod ) > 0 ) {
			$this->beanDefinitions[$beanID]->setInitMethod( $initMethod );
		}

		/* Return a reference to this new bean definition so it can be used by the concrete
		 * bean factory. */
		return $beanDefinition;

	}


	protected function createPropertyDefinition (	com_greentiedev_phpSpring_bean_definition &$beanDefinition,
												$name, $type )
	{

		/* Create a new instance of a bean definition and set its properties. */
		$property = new com_greentiedev_phpSpring_bean_property( $beanDefinition );
		$property->setName( $name );
		$property->setType( $type );
		$beanDefinition->addProperty( $property );

		/* Return a reference to this new property definition so it can be used by the concrete
		 * bean factory. */
		return $property;

	}


	/**
	 * This method takes in a beanName/ID uses it to determine if the bean has already been registered
	 * with the local bean factory.
	 *
	 * @param   string   $beanName  Identification key for a bean.
	 * @return  boolean
	 */
	public function localFactoryContainsBean ( $beanName )
	{

		return array_key_exists( $beanName, $this->beanDefinitions );
	}


	/**
	 * This method takes in a beanName/ID uses it to determine if the bean has already been registered
	 * with the local bean factory or any factories within the inheritance tree.
	 *
	 * @param   string   $beanName  Identification key for a bean.
	 * @return  boolean
	 */
	public function containsBean ( $beanName )
	{

		$resolvedName = $this->resolveBeanName( $beanName );
		if ( array_key_exists( $beanName, $this->beanDefinitions ) ) {
			return true;
		}
		else {
			if ( is_object( $this->parentFactory ) ) {
				return $this->parentFactory->containsBean( $resolvedName );
			}
			else {
				return false;
			}
		}
	}


	/**
	 * This method takes in a beanName/ID and uses it to pull a bean out of the beanDefinition tree.
	 * It is at this point that if the bean hasn't already been initialized or is not a singleton it
	 * will be created.
	 *
	 * @param   string  $beanName  Identification key for a bean.
	 * @return  object
	 *
	 * @todo    Move this functionaility into the abstractFactory.
	 */
	public function getBean ( $beanName )
	{

		/* The supplied 'beanName' could be an alias, so we want to resolve it to the concrete name. */
		$beanName = $this->resolveBeanName( $beanName );

		if ( $this->localFactoryContainsBean( $beanName ) ) {

			$beanDefinition = $this->getBeanDefinition( $beanName );

			/* Check if the requested bean is a singleton. */
			if ( $beanDefinition->isSingleton() ) {

				/* Check if the request bean has already been initialized. If it hasn't it needs to
				 * created, otherwise we can get the already create instance. This is called lazy
				 * initialization (lazy-init). */
				if ( !$beanDefinition->isConstructed() ) {
					$this->constructBean( $beanName );
				}

				$bean = $beanDefinition->getInstance( $beanName );

			}
			else {

				/* This bean is not a singleton so we need to create a new instance of it. */
				$bean = $this->constructBean( $beanName, true );

			}
			return $bean;

		}

		/* The local factory doesn't have this bean so if there is a parent bean factory we will try
		 * to get the bean from there. */
		elseif ( is_object( $this->parentFactory ) ) {
			return $this->parentFactory->getBean( $beanName );
		}

		/* For some reason this requested bean is has no definition so it cannot be created. Throw
		 * an error to notify the user an undefined bean was requested. */
		else {
			$msg = 'Bean definition for bean named: ' . $beanName . ' could not be found.';
			throw new Exception( $msg );
		}

	}


	/**
	 * This method loops over all bean definitions in the local bean factory and initilizes them if
	 * they match the criteria for a non-lazy bean.
	 *
	 * @return  void
	 */
	protected function initNonLazyBeans ()
	{

		foreach ( $this->beanDefinitions as $n => $d ) {
			if (    $d->isSingleton()
				&& !$d->isLazyInit()
				&& !$d->isConstructed()
				&& !$d->isInnerBean()
				&& !$d->isFactory() )
			{
				$this->getBean( $n );
			}
		}
	}


	/**
	 * This method is probably the most complicated method in the aFactory class. Taking in a bean
	 * name/id, this function first retrieves the beanDefinition to confirm that it is a valid bean.
	 * then it creates an array of bean definitions based on the hiararchy of dependent beans. The
	 * list is then looped through in reverse, ensuring that the "most dependent" beans are dealt
	 * with first. During this loop singleton beans are initialized if they not not already been
	 * then added to the singleton cache and transient beans are instantiated and added to a local
	 * variable for later reference. After each bean is created it's properties are fetched and
	 * looped through to ensure any value and dependency injection that needs to occur does. Once
	 * this loop is complete the the bean definitions are again looped over in reverse this time
	 * executing any init methods that have been defined in the bean definitions. Finally the bean
	 * is returned if the $returnInstance parameter is true.
	 *
	 * @param   string   $beanName        The name/id of the bean to be constructed.
	 * @param   boolean  $returnInstance  Whether or not to return the bean once it has been cunstructed.
	 *
	 * @return  void[object]
	 */
	private function constructBean ( $beanName, $returnInstance = false )
	{

		$localBeanDef		= $this->getBeanDefinition( $beanName );
		$localBeanCache		= array();
		$dependentBeanDefs	= array( $localBeanDef );

		$dependencies = $localBeanDef->getDependencies( 'all' );
		foreach( $dependencies as $dependency ) {
			array_push( $dependentBeanDefs, $this->getBeanDefinition( $dependency ) );
		}

		/* now resolve all dependencies by looping through list backwards, causing the
		 * "most dependent" beans to get created first  */

		$dependentBeanDefsLen = count( $dependentBeanDefs );

		for ( $i=$dependentBeanDefsLen - 1; $i>=0; $i-- ) {

			$beanDef = $dependentBeanDefs[$i];

			if ( !$beanDef->isConstructed() ) {

				if ( $beanDef->isSingleton() ) {
					if ( !$this->singletonCacheContainsBean( $beanDef->getID() ) ) {
						$beanDef->getBeanFactory()->addBeanToSingletonCache( $beanDef->getID(), $beanDef->getBeanInstance() );
					}
					$beanInstance = $this->getBeanFromSingletonCache( $beanDef->getID() );
				}
				else {
					$localBeanCache[$beanDef->getID()] = $beanDef->getBeanInstance();
					$beanInstance = $localBeanCache[$beanDef->getID()];
				}

				$propDefs = $beanDef->getProperties();

				/* now do dependency injection via setters */
				foreach ( $propDefs as $propDef ) {

					$propType = $propDef->getType();

					$propertyName = $propDef->getName();

					$propertyMethod = 'set' . strtoupper( substr( $propertyName, 0, 1 ) ) . substr( $propertyName, 1, strlen($propertyName) - 1 );

					if ( $propType == 'value' ) {

						$propertyValue = $propDef->getValue();

						call_user_func_array( array( &$beanInstance, $propertyMethod ), array( &$propertyValue ) );

					}
					elseif ( $propType == 'map' || $propType == 'list' ) {

						call_user_func( array( &$beanInstance, $propertyMethod ),
							$this->constructComplexProperty( $propDef->getValue(),
								$propType,
								$localBeanCache ) );

					}
					elseif ( $propType == 'ref' || $propType == 'bean' ) {

						$dependentBeanDef = $this->getBeanDefinition( $propDef->getValue() );

						if ( $dependentBeanDef->isSingleton() ) {
							$dependentBeanInstance = $dependentBeanDef->getBeanInstance();
						}
						else {
							$dependentBeanInstance = $localBeanCache[$dependentBeanDef->getID()];
						}

						call_user_func_array( array( &$beanInstance, $propertyMethod ), array( &$dependentBeanInstance ) );

					}
				}

				if ( $beanDef->isSingleton() ) {
					$beanDef->setIsConstructed( true );
				}

			}

		}

		/* now loop again (same direction: backwards) for init-methods */
		$dependentBeanDefsLen = count( $dependentBeanDefs );

		for ( $i=$dependentBeanDefsLen - 1; $i>=0; $i-- ) {

			$beanDef = $dependentBeanDefs[$i];

			if ( $beanDef->isSingleton() ) {
				$beanInstance = $this->getBeanFromSingletonCache( $beanDef->getID() );
			}
			else {
				$beanInstance = $localBeanCache[$beanDef->getID()];
			}

			/* now call an init-method if it's defined */
			if ( $beanDef->hasInitMethod() && !$beanDef->getInitMethodWasCalled() ) {

				call_user_func( array( &$beanInstance, $beanDef->getInitMethod() ) );

				/* make sure it only gets called once */
				$beanDef->setInitMethodWasCalled( true );

			}

		}

		/* if we're supposed to return the new object, do it */
		if ( $returnInstance ) {
			if ( $dependentBeanDefs[0]->isSingleton() ) {
				return $this->getBeanFromSingletonCache( $dependentBeanDefs[0]->getID() );
			}
			else {
				return $localBeanCache[$dependentBeanDefs[0]->getID()];
			}
		}

	}


	private function constructComplexProperty ( &$complexProperty, $type, &$localBeanCache )
	{

		if ( $type == 'map' ) {
			/* just return the struct because it's passed by ref */
			$this->findComplexPropertyRefs( $complexProperty, $type, $localBeanCache );
		}
		elseif ( $type == 'list' ) {
			/* tail recursion for the array (and return the result) */
			$this->findComplexPropertyRefs( $complexProperty, $type, $localBeanCache );
		}
		return $complexProperty;
	}


	private function findComplexPropertyRefs ( &$complexProperty, $type, &$localBeanCache )
	{

		/* based on the the type of property/con-arg */
		switch ( $type ) {

			case 'list':
			case 'map':
				foreach( $complexProperty as &$entry ) {

					/* loop thru the map (struct) */
					if ( is_object( $entry )
						&& $this->getMetaData( $entry )->getName() == 'com_greentiedev_phpSpring_bean_reference' ) {
						/*	this key's value is a beanReference, basically a placeholder that we
							replace with the actual bean, right now */
						if ( $this->singletonCacheContainsBean( $entry->getID() ) ) {
							$entry = $this->getBeanFromSingletonCache( $entry->getID() );
						}
						else {
							$entry = $localBeanCache[$entry->getID()];
						}
					}
					elseif ( $this->is_assoc( $entry ) ) {
						/* ok, we found a map within this map, so recurse */
						$this->findComplexPropertyRefs( $entry, 'map', $localBeanCache );
					}
					elseif ( is_array( $entry ) ) {
						/* ok, we found a list within this map, so recurse */
						$entry = $this->findComplexPropertyRefs( $entry, 'list', $localBeanCache);
					}
				}
				break;
		}
	}


	/* ACCESSORS ******************************************************************************** */


	public function getDefaultProperties ()
	{

		return $this->defaultProperties;
	}


	public function setDefaultProperties ( &$defaultProperties )
	{

		$this->defaultProperties = $defaultProperties;
	}


	public function getDefaultAttributes ()
	{

		return $this->defaultAttributes;
	}


	public function setDefaultAttributes ( $defaultAttributes )
	{

		$this->defaultAttributes = $defaultAttributes;
	}


	public function getDefaultValue ( $attributeName, $attributeValue )
	{

		if ( $attributeValue == 'default' ) {
			if ( array_key_exists( $attributeName, $this->defaultAttributes ) ) {
				return $this->defaultAttributes[$attributeName];
			}
			else {
				switch ( $attributeName ) {

					case 'singleton':
						return true;
						break;

					default:
						return false;
						break;
				}
			}
		}
		else {
			return $attributeValue;
		}
	}


	public function getClass ( $beanName )
	{

	}


}
