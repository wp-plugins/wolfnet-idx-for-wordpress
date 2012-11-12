<?php

if ( !class_exists( 'com_greentiedev_wppf_filter_filter' ) ) {

/**
 * @package       com.greentiedev.wppf.filter
 * @title         filter.php
 * @extends       com_greentiedev_wppf_abstract_hook
 * @implements    com_greentiedev_wppf_interface_iManaged
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

abstract class com_greentiedev_wppf_filter_filter
extends com_greentiedev_wppf_abstract_hook
implements com_greentiedev_wppf_interface_iManaged
{


	abstract public function execute ();


}

} // end 'if class_exists'
