# magento_shell_script
Data import script, update of specific data, Update of price

## Script `backupBdd.php`

[backupBdd.php](https://github.com/gabi77/magento_shell_script/blob/master/shell/backupBdd.php), this will allow you to make a complete database backup of your magento

## Script `Redisgarbage.php`

[Redisgarbage.php](https://github.com/gabi77/magento_shell_script/blob/master/shell/Redisgarbage.php), Empty old cache backend

## The folder magerun

The n98 magerun cli tools provides some handy tools to work with Magento from command line.
Link : [magerun](https://github.com/netz98/n98-magerun)

Files :
- n98-magerun.phar
- [runforme.sh](https://github.com/gabi77/magento_shell_script/blob/master/shell/magerun/runforme.sh)
- [warmcache.sh](https://github.com/gabi77/magento_shell_script/blob/master/shell/magerun/warmcache.sh)

RUNFORME 
Launches a reindex one to one via magerun
Then it completely empties the application cache
It regenerates the cache using the file warmcache.sh

## The folder import

Script Import data for magento

""""""""""""""""""""""""""""""

- [ImportNewProduct.php](https://github.com/gabi77/magento_shell_script/blob/master/shell/import/ImportNewProduct.php)
- [ImportNewProductPorte.php](https://github.com/gabi77/magento_shell_script/blob/master/shell/import/ImportNewProductPorte.php)
- [UpdateWebsiteAttribute.php](https://github.com/gabi77/magento_shell_script/blob/master/shell/import/UpdateWebsiteAttribute.php)
- [UpdateWebsiteAttributeName.php](https://github.com/gabi77/magento_shell_script/blob/master/shell/import/UpdateWebsiteAttributeName.php)
- [ImageGalleryImport.php](https://github.com/gabi77/magento_shell_script/blob/master/shell/import/ImageGalleryImport.php)
- [UpdateLivraisonRapide.php](https://github.com/gabi77/magento_shell_script/blob/master/shell/import/UpdateLivraisonRapide.php)
- [import_attr.php](https://github.com/gabi77/magento_shell_script/blob/master/shell/import/import_attr.php)
- [UpdatePrice.php](https://github.com/gabi77/magento_shell_script/blob/master/shell/import/UpdatePrice.php)
- [UpdateStatusProduct.php](https://github.com/gabi77/magento_shell_script/blob/master/shell/import/UpdateStatusProduct.php)

