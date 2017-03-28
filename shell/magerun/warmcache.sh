#!/usr/bin/env bash

# @category   Gabi77
# @copyright  Copyright (c) 2017 gabi77 (http://www.gabi77.com)
# @author     Gabriel Janez <gabriel_janez@hotmail.com>
# @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)

#generate file urls website
php -d memory_limit=-1 n98-magerun.phar sys:url:list --add-all 1 '{host}{path}' > urls-.csv
for i in `cat urls-website.csv`; do wget -v --delete-after $i; done
