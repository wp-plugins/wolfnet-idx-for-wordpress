<?php

/**
 * @package       com.ajmichels.phpSpring.bean.factory
 * @title         iFactory.php
 * @contributors  AJ Michels (http://ajmichels.com)
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
 */

interface com_ajmichels_phpSpring_bean_factory_iFactory
{
	
	public function getBean ( $beanName );
	
	public function containsBean ( $beanName );
	
	public function getClass ( $beanName );
	
}