#!/bin/bash

# @category   Gabi77
# @copyright  Copyright (c) 2017 gabi77 (http://www.gabi77.com)
# @author     Gabriel Janez <gabriel_janez@hotmail.com>
# @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)

datetime=$(date "+%Y-%m-%d %H:%M:%S");
echo "Date de debut" $datetime;

echo reindex catalog_product_attribute
php n98-magerun.phar index:reindex catalog_product_attribute

echo reindex catalog_product_price
php n98-magerun.phar index:reindex catalog_product_price

echo reindex catalog_category_flat
php n98-magerun.phar index:reindex catalog_category_flat

echo reindex catalog_category_product
php -d memory_limit=-1 n98-magerun.phar index:reindex catalog_category_product

echo reindex catalogsearch_fulltext
php n98-magerun.phar index:reindex catalogsearch_fulltext

echo reindex cataloginventory_stock
php n98-magerun.phar index:reindex cataloginventory_stock

echo reindex tag_summary
php n98-magerun.phar index:reindex tag_summary

echo reindex catalog_url
php n98-magerun.phar index:reindex catalog_url

echo reindex catalog_product_flat
php n98-magerun.phar index:reindex catalog_product_flat

datetimefin=$(date "+%Y-%m-%d %H:%M:%S");
echo "Date de fin index" $datetimefin;

echo info index:list
php n98-magerun.phar index:list

echo Vidage du cache 1
php n98-magerun.phar cache:clean

echo Vidage du cache 2
php n98-magerun.phar cache:clean

datetimefincache=$(date "+%Y-%m-%d %H:%M:%S");
echo "Date de fin vidage de cache" $datetimefincache;

#generate file urls website

echo Reconstruction du cache via maintenance
sh warmcache.sh
datetimefinwget=$(date "+%Y-%m-%d %H:%M:%S");
echo "Date de fin wget" $datetimefinwget;
