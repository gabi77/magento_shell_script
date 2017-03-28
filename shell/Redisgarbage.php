<?php 
/**
 *
 * @category   Gabi77
 * @copyright  Copyright (c) 2017 gabi77 (http://www.gabi77.com)
 * @author     Gabriel Janez <gabriel_janez@hotmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */

PHP_SAPI == 'cli' or die('<h1>:P</h1>');
ini_set('memory_limit','1024M');
set_time_limit(0);
error_reporting(E_ALL | E_STRICT);
require_once '../app/Mage.php';

Mage::app()->getCache()->getBackend()->clean('old');
// uncomment this for Magento Enterprise Edition
// Enterprise_PageCache_Model_Cache::getCacheInstance()->getFrontend()->getBackend()->clean('old');