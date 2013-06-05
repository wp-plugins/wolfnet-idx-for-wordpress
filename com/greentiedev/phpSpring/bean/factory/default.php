<?php

/**
 * This class is a specific bean factory implementation ({@see com_greentiedev_phpSpring_bean_factory_aFactory}})
 * which takes in xml configuration files to define beans and their properties/dependencies. Only
 * code specific to the XML implementation of this bean factory, its bean definitions and properties.
 *
 * @package       com.greentiedev.phpSpring.bean.factory
 * @title         default.php
 * @contributors  AJ Michels (http://greentiedev.com)
 * @version       1.0
 * @extends      com_greentiedev_phpSpring_bean_factory_aFactory
 * @implements   com_greentiedev_phpSpring_bean_factory_iFactory
 * @copyright    Copyright (c) 2012, AJ Michels
 *
 *               ColdSpring, Copyright (c) 2005, David Ross, Chris Scott, Kurt Wiersma, Sean Corfield
 *
 *               Licensed under the Apache License, Version 2.0 (the "License");
 *               you may not use this file except in compliance with the License.
 *               You may obtain a copy of the License at
 *
 *                  http://www.apache.org/licenses/LICENSE-2.0
 *
 *               Unless required by applicable law or agreed to in writing, software
 *               distributed under the License is distributed on an "AS IS" BASIS,
 *               WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *               See the License for the specific language governing permissions and
 *               limitations under the License.
 *
 */
class com_greentiedev_phpSpring_bean_factory_default
extends com_greentiedev_phpSpring_bean_factory_aFactory
implements com_greentiedev_phpSpring_bean_factory_iFactory
{


	/**
	 * This constructor method accepts a file path to an XML configuration file. It then uses this
	 * path hashed as the id for this beanFactory. Then it sends the path on to the
	 * loadBeansFromXmlFile method.
	 *
	 * @param   string  $xmlFilePath        A path to an xml configuration file.
	 * @param   array   $defaultAttributes
	 * @param   array   $defaultProperties  Associative array of properties which can be reference by
	 *                                      ${key} in the XML configuration.
	 * @return  void
	 *
	 */
	public function __construct ( $xmlFilePath, $defaultAttributes = array(), $defaultProperties = array() )
	{

		parent::__construct( md5( $xmlFilePath ), $defaultAttributes, $defaultProperties );
		/*$this->cacheDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cache';
		$this->cacheFile = $this->id . '_defs.tmp';
		if ( $this->cacheFileExists() ) {
			$this->loadBeanDefinitionsFromCache();
		}
		else {
		*/
			$this->loadBeansFromXmlFile( $xmlFilePath );
		/*	$this->saveBeanDefinitionsToCache();
		}*/
	}


	/**
	 * This method takes in a path to an xml configuration file and passes it to an the findImports
	 * method along with a reference to an array of XML objects. Then it passes the XML objects over
	 * to the processBeanLoad method.
	 *
	 * @param   string  $xmlFilePath  A path to an xml configuration file.
	 * @return  void
	 */
	private function loadBeansFromXmlFile ( $xmlFilePath )
	{

		$xmlObjects = array();
		$this->findImports( $xmlObjects, $xmlFilePath );
		$this->processBeanLoad( $xmlObjects );
	}


	/**
	 * This method takes in an array of xmlData from other files and a xml file path.  If the file
	 * exists it is loaded in as a "simpleXml" object and added to array.
	 *
	 * @param   array   $xmlObjects   An array of simpleXml objects
	 * @param   string  $xmlFilePath  A path to an xml configuration file.
	 * @return  void
	 *
	 * @todo    Add recurive look up of include files
	 */
	private function findImports ( &$xmlObjects, $xmlFilePath )
	{

		if ( file_exists( $xmlFilePath ) ) {
			$xmlData = simplexml_load_file( $xmlFilePath );
		}
		array_push( $xmlObjects, $xmlData );
	}


	/**
	 * This method takes in an array of simpleXml objects and loops over them passing each object to
	 * the loadBeanDefinition method for conversion into a BeanDefinition object. Then it call the
	 * initNonLazyBeans method to insure that any beans marked as non-lazy (lazy-init=false) are
	 * automatically loaded.
	 *
	 * @param   array  $beanXmlObjects  An array of simpleXML objects.
	 * @return  void
	 */
	private function processBeanLoad ( $beanXmlObjects )
	{
		foreach ( $beanXmlObjects as $beanXmlObject ) {
			$this->loadBeanDefinitions( $beanXmlObject );
		}
		$this->initNonLazyBeans();
	}


	/**
	 * This method takes in a simpleXml object containing bean configuration XML. It loops over each
	 * individual bean object within the XML and prepares the data to be passed to the
	 * createBeanDefinition method.
	 *
	 * @param   SimpleXMLElement  $beanXmlObject  A simpleXml object containing configuration data for a bean.
	 * @return  void
	 *
	 * @todo    Add support for custom factory beans and any other beans that do not have both an
	 *          'id' and 'class' attribute.
	 * @todo    Pull default lazy-init from the factory config.
	 * @todo    Add support for 'autowire' by type and name attribute. Also pull default from config
	 *          file.
	 * @todo    Add support for 'factory-post-processor' attribute.
	 * @todo    Add support for 'bean-post-processor' attribute.
	 * @todo    Add support for 'abstract' attribute.
	 * @todo    Add support for 'parent' attribute.
	 */
	private function loadBeanDefinitions ( SimpleXMLElement $beanXmlObject )
	{

		foreach ( $beanXmlObject as $beanXml ) {

			/* Loop over the simpleXml attributes and add each one to a standard array. */
			$beanAttributes = array();
			foreach ( $beanXml->attributes() as $beanAttribute => $beanAttributeValue ) {
				$beanAttributes[$beanAttribute] = (string) $beanAttributeValue;
			}

			/* If there is not both and 'id' and 'class' attribute in the <bean/> node the
			 * configuration file is invalid.
			 */
			if ( !array_key_exists( 'id', $beanAttributes) || !array_key_exists( 'class', $beanAttributes) ) {
				throw new Exception( 'The phpSpring configuration file is invalid!' );
			}

			/* Flatted the 'id' and 'class' attributes into simple reference variables. */
			$id		= trim( $beanAttributes['id'] );
			$class	= trim( $beanAttributes['class'] );

			/* If the 'singleton' attribute is present flatten its value to a reference variable. */
			if ( array_key_exists( 'singleton', $beanAttributes )
				&& trim( $beanAttributes['singleton'] ) == 'false' ) {
				$isSingleton = false;
			}
			else {
				$isSingleton = true;
			}

			/* If the 'lazy-init' attribute is present flatten its value to a reference variable. */
			if ( array_key_exists( 'lazy-init', $beanAttributes )
				&& trim( $beanAttributes['lazy-init'] ) == 'false' ) {
				$lazyInit = false;
			}
			else {
				$lazyInit = true;
			}

			/* If the 'init-method' attribute is present flatten its value to reference variable. */
			if ( array_key_exists( 'init-method', $beanAttributes ) ) {
				$initMethod = trim( $beanAttributes['init-method'] );
			}
			else {
				$initMethod = '';
			}

			/* Loop over te simpleXml children and add add each child to a standard array. This will
			 * result in an array of simpleXml objects representing the beans properties which will
			 * be used later to create property definitions. */
			$children = array();
			foreach ( $beanXml->children() as $beanChild => $beanChildValue ) {
				array_push( $children, $beanChildValue );
			}

			/* Pass the extracted values to the createBeanDefinition method. */
			$beanDefinition = $this->createBeanDefinition(	$id, $class, $isSingleton, false,
															$lazyInit, $initMethod );

			$this->parseBeanChildren ( $beanDefinition, $children );

		}
	}


	/**
	 * This method takes in a reference to a newly created Bean Definition ({@see com_greentiedev_phpSpring_bean_definition}}),
	 * an xml representation of the bean's children and then passes them to an appropriate method for
	 * conversion into a definition object.
	 *
	 * @param   com_greentiedev_phpSpring_bean_definition  $beanDefinition  A simpleXml object containing configuration data for a bean.
	 * @param   array  $childrenXml  An array of simpleXml object containing configuration data
	 *                               for a bean property.
	 * @return  void
	 */
	private function parseBeanChildren (	com_greentiedev_phpSpring_bean_definition &$beanDefinition,
											$childrenXml )
	{

		foreach ( $childrenXml as $childXml ) {

			if ( $childXml->getName() == 'property' ) {
				$this->parsePropertyDefinition( $beanDefinition, $childXml );
			}

		}
	}


	/**
	 * This method takes in a reference to a newly created Bean Definition ({@see com_greentiedev_phpSpring_bean_definition}}),
	 * an xml representation of a property, and an array of properties. It then parses the xml and
	 * creates a new Property Definition object ({@see com_greentiedev_phpSpring_bean_property}})
	 *
	 * @param   com_greentiedev_phpSpring_bean_definition  $beanDefinition  A simpleXml object containing configuration data for a bean.
	 * @param   SimpleXMLElement  $propertyXml  A simpleXml object containing configuration data
	 *                                          for a bean property.
	 * @param   array             $properties
	 * @return  void
	 */
	private function parsePropertyDefinition (	com_greentiedev_phpSpring_bean_definition &$beanDefinition,
												SimpleXMLElement $propertyXml )
	{

		/* Loop over the simpleXml attributes and add each one to a standard array. */
		$attributes = array();
		foreach ( $propertyXml->attributes() as $attribute => $value ) {
			$attributes[$attribute] = (string) $value;
		}

		/* Loop over te simpleXml children and add add each child to a standard array. */
		$children = array();
		foreach ( $propertyXml->children() as $child ) {
			array_push( $children, $child );
		}

		/* Check that there is at least a name attribute on the property and it has at least one
		 * child element. */
		if ( !array_key_exists( 'name', $attributes ) || count( $children ) == 0 ) {
			throw new Exception( 'Xml properties must contain a "name" and a child element!' );
		}

		$name = $attributes['name'];

		/* Currently this bean factory implementation is only cocerned with one child, the first one. */
		$child = $children[0];

		/* The property type is base on the type of child node within the property XML. */
		$type = $child->getName();

		/* Create a new Property Definition */
		$propertyDefinition = $this->createPropertyDefinition( $beanDefinition, $name, $type );

		/* Now we send the child xml object to a method which will parse its contents and then assign
		 * back to the property. */
		$this->parseChildNode( $propertyDefinition, $child );

	}


	/**
	 * This method takes in a reference to a newly created Property Definition ({@see com_greentiedev_phpSpring_bean_definition}}),
	 * an xml representation of a property child, and an array of properties. It then parses the xml
	 * and parses the child XML to determine the property value.
	 *
	 * @param   com_greentiedev_phpSpring_bean_definition	$beanDefinition	A simpleXml object containing configuration data for a bean.
	 * @param   SimpleXMLElement  $propertyXml  A simpleXml object containing configuration data
	 *                                          for a bean property.
	 * @param   array             $properties
	 * @return  void
	 */
	private function parseChildNode (	com_greentiedev_phpSpring_bean_property &$propertyDefinition,
										SimpleXMLElement $propertyChild )
	{

		/* First establish the string value of the child node contents. */
		$value = (string) $propertyChild;

		/* loop over the simpleXml attributes and add them to a standard associative array. */
		$attributes = array();
		foreach ( $propertyChild->attributes() as $attribute => $value ) {
			$attributes[$attribute] = (string) $value;
		}

		/* Loop over the simpleXml children and add them to a standard numerical array. */
		$children = array();
		foreach ( $propertyChild->children() as $c ) {
			array_push( $children, $c );
		}

		/* Now use the node name to determine how to parse the property value. */
		switch ( $propertyChild->getName() ) {

			/* If the property child is 'ref' the value is a reference to another bean definition.
			 * The bean definition might not be defined yet so set the name of the bean to the
			 * property value and add a dependency to the properties bean definition. */
			case 'ref':
				if ( !array_key_exists( 'bean', $attributes ) ) {
					throw new Exception( 'There must be a "bean" attribute in all "ref" nodes.' );
				}
				$propertyDefinition->setValue( $attributes['bean'] );
				$propertyDefinition->addParentDefinitionDependency( $attributes['bean'] );
				break;

			/* If the property child is 'bean' the value is an anonymous bean that will only be used
			 * by this property's bean, so pass the data to a method which will create the anonymous
			 * bean and return its name so that we can set the property value and add a dependency
			 * to this properties bean definition. */
			case 'bean':
				$beanUID = $this->createInnerBeanDefinition( $propertyDefinition, $propertyChild );
				$propertyDefinition->setValue( $beanUID );
				$propertyDefinition->addParentDefinitionDependency( $beanUID );
				break;

			/* If the property child is 'list' (num array) or 'map' (assoc array) then we will pass
			 * its children to another parser to evaluate it down to simple values within arrays.
			 * However, the value of the node could be a dynamic string, something resembling jSON,
			 * in which case we will need to pass the string to the parser rather than the XML object. */
			case 'list':
			case 'map':
				if ( strlen( $value ) > 2 && substr( $value, 0, 2 ) == '${' ) {
					$propertyDefinition->setValue(
						$this->parseEntries( $propertyDefinition, $value, $propertyChild->getName() )
						);
				}
				else {
					$propertyDefinition->setValue(
						$this->parseEntries( $propertyDefinition, $children, $propertyChild->getName() )
						);
				}
				break;

			/* This is the simplist scenario. If the property child is 'value' then it is a string and
			 * can either be taken for its face value or it can be parsed as a dynamic string. Either
			 * way pass it to the parseValue method to determine the final value. */
			case 'value':
				$propertyDefinition->setValue( $this->parseValue( $value ) );
				break;

		}
	}


	/**
	 * This method is used to parse map and list property values. A reference to a property definition
	 * is passed to that the data can be inserted into it once it is parsed. The map/list xml data is
	 * also passed in along with the type of property it is and an array of possible default properties.
	 *
	 * @param  com_greentiedev_phpSpring_bean_property  $propertyDefinition
	 * @param  array   $mapEntries
	 * @param  string  $returnType
	 * @param  array   $properties
	 */
	private function parseEntries (	com_greentiedev_phpSpring_bean_property &$propertyDefinition,
									$mapEntries, $returnType )
	{

		if ( $returnType == 'map' || $returnType == 'list' ) {
			$rtn = array();
		}
		else {
			throw new Exception( 'phpSpring only supports map and list as complex types' );
		}

		foreach ( $mapEntries as &$entry ) {

			if ( $returnType == 'map' ) {

				$attributes = array();
				foreach ( $entry->attributes() as $attribute => $value ) {
					$attributes[$attribute] = (string) $value;
				}

				$children = array();
				foreach ( $entry->children() as $child ) {
					array_push( $children, $child );
				}

				if ( !array_key_exists( 'key', $attributes ) ) {
					throw new Exception( 'Map entries must have an attribute named "key".');
				}
				if ( count( $children ) != 1 ) {
					throw new Exception( 'Map entries must have one child.' );
				}

				$entryChild = $children[0];
				$entryKey = $attributes['key'];

				$attributes = array();
				foreach ( $entryChild->attributes() as $attribute => $value ) {
					$attributes[$attribute] = (string) $value;
				}

				$children = array();
				foreach ( $entryChild->children() as $child ) {
					array_push( $children, $child );
				}

			}
			elseif ( $returnType == 'list' ) {
				$entryChild = $entry;
				$entryKey = count( $rtn );
			}

			/*	ok so the above code created a place to put something (e.g. array[key] or array[n])
				now lets find out what should placed there */

			switch ( $entryChild->getName() ) {

				case 'value':
					/* easy, just put in your parsed value */
					$rtn[$entryKey] = $this->parseValue( (string) $entryChild );
					break;

				/*	for <ref/> and <bean/> elements within complex properties, we need make a 'placeholder'
					so that the beanFactory can replace this element with an actual bean instance when it
					actually contructs the bean who this property belongs to
					coldspring.beans.BeanReference is used for this purpose... it's just a glorified beanID */

				case 'ref':
					$childAttributes = array();
					foreach ( $entryChild->attributes() as $childAttribute => $value ) {
						$childAttributes[$childAttribute] = (string) $value;
					}
					/* just put in a beanReference with the id of the bean */
					if ( !array_key_exists( 'bean', $childAttributes ) ) {
						throw new Exception( 'There must be a "bean" attribute in all "ref" nodes.' );
					}
					$entryBeanID = $childAttributes['bean'];
					$rtn[$entryKey] = new com_greentiedev_phpSpring_bean_reference( $entryBeanID );
					$propertyDefinition->addParentDefinitionDependency( $entryBeanID );
					break;

				case 'bean':
					/* createInnerBeanDefinition now takes care of all the xml parsing and returns new beanUId */
					$entryBeanID = $this->createInnerBeanDefinition( $propertyDefinition, $entryChild );
					$rtn[$entryKey] = new com_greentiedev_phpSpring_bean_reference( $entryBeanID );
					$propertyDefinition->addParentDefinitionDependency( $entryBeanID );
					break;

				case 'map':
				case 'list':
					$childChildren = array();
					foreach ( $entryChild->children() as $childChild ) {
						array_push( $childChildren, $childChild );
					}
					/* recurse if we find another complex property */
					$rtn[$entryKey] = $this->parseEntries( $propertyDefinition, $childChildren, $entryChild->getName() );
					break;
			}
		}
		return $rtn;

	}


	private function parseValue ( $rawValue, $returnType = null )
	{

		/* resolve anything that looks like it should get replaced with a beanFactory default property */
		if ( substr( $rawValue, 0, 2 ) == '${' && substr( $rawValue, -1 ) == '}' ) {
			/* grab the default properties out of the enclosing bean factory */
			$beanFactoryDefaultProperties = $this->getDefaultProperties();
			$propertyPlaceholder = substr( $rawValue, 2, strlen( $rawValue )-3 );
			/* look for this property value in the bean factory (using isDefined/evaluate incase of "." in property name)
				OR look for the property in the passed in struct ( do that first, as we may be postProcessing ) */
			if ( array_key_exists( $propertyPlaceholder, $beanFactoryDefaultProperties ) ) {
				return $beanFactoryDefaultProperties[$propertyPlaceholder];
			}
		}

		return $rawValue;
	}


	private function createInnerBeanDefinition ( &$propertyDefinition, SimpleXMLElement $beanXml )
	{

		/* loop over the simpleXml attributes and add them to a standard associative array. */
		$attributes = array();
		foreach ( $beanXml->attributes() as $attribute => $value ) {
			$attributes[$attribute] = (string) $value;
		}

		if ( !array_key_exists( 'class', $attributes ) ) {
			$msg = 'Xml inner bean definitions must contain a "class" attribute!';
			throw new Exception( $msg );
		}

		/* create uid for new Bean, store as value for lookup */
		$id = 'anonymousBean_' . uniqid();
		$class = $attributes['class'];

		/* check for an init-method */
		if ( array_key_exists( 'init-method', $attributes )
			&& strlen( $attributes['init-method'] ) > 0 ) {
			$initMethod = $attributes['init-method'];
		}
		else {
			$initMethod = '';
		}

		$innerBeanDefinition = $this->createBeanDefinition( $id, $class, false, true, true, $initMethod);

		/* Loop over the simpleXml children and add them to a standard numerical array. */
		$children = array();
		foreach ( $beanXml->children() as $child ) {
			array_push( $children, $child );
		}

		$this->parseBeanChildren ( $innerBeanDefinition, $children );

		/* now return the new beanUID */
		return $id;
	}


	private function cacheDirExists ()
	{

		if ( file_exists( $this->cacheDir ) ) {
			return true;
		}
		else {
			return false;
		}
	}


	private function cacheFileExists ()
	{

		if ( file_exists( $this->cacheDir . DIRECTORY_SEPARATOR . $this->cacheFile ) ) {
			return true;
		}
		else {
			return false;
		}
	}


	private function loadBeanDefinitionsFromCache ()
	{

		$cacheData = file_get_contents( $this->cacheDir . DIRECTORY_SEPARATOR . $this->cacheFile );
		$this->beanDefinitions = unserialize( $cacheData );
	}


	private function saveBeanDefinitionsToCache ()
	{

		$cacheFilename = $this->cacheDir . DIRECTORY_SEPARATOR . $this->cacheFile;
		if ( $this->cacheDirExists() ) {
			if ( $this->cacheFileExists() ) {
				// Delete the file
				unlink( $cacheFilename );
			}
		}
		else {
			// Create the directory
			mkdir( $this->cacheDir );
		}
		$cacheData = serialize( $this->beanDefinitions );
		$cacheFile = fopen( $cacheFilename, 'x');
		$writeResults = fwrite( $cacheFile, $cacheData );
		$this->dump( $writeResults );
		fclose( $cacheFile );
	}


}
