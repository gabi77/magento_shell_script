<?php

/**
 *
 * @category   Gabi77
 * @copyright  Copyright (c) 2017 gabi77 (http://www.gabi77.com)
 * @author     Gabriel Janez <gabriel_janez@hotmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */

set_time_limit(0);
require_once dirname($argv[0]) . '/abstract.php';

class Mage_BackupBdd extends Mage_Shell_Abstract {

	/**
	 * Information Database Connexion by local.xml
	 **/
    public function info() {
		
		// get Magento config
	    $config  = Mage::getConfig()->getResourceConnectionConfig("default_setup");

	    $dbinfo = array(
	        "host" => $config->host,
	        "user" => $config->username,
	        "pass" => $config->password,
	        "dbname" => $config->dbname
	    );

	    return $dbinfo;    	
    }

    /**
     * Run script
     *
     */
    public function run() {
    	$dbinfo = self::info();
 		echo (' mysqldump -u"'.$dbinfo['user'].'" -p"'.$dbinfo['pass'].'" -h"'.$dbinfo['host'].'"  --single-transaction "'.$dbinfo['dbname'].'" | gzip > '.Mage::getBaseDir('base') . DS . 'bck/prod_'.date("N",time()).'.sql.gz');
    	shell_exec(' mysqldump -u"'.$dbinfo['user'].'" -p"'.$dbinfo['pass'].'" -h"'.$dbinfo['host'].'"  --single-transaction "'.$dbinfo['dbname'].'" | gzip > '.Mage::getBaseDir('base') . DS . 'bck/prod_'.date("N",time()).'.sql.gz');
    }


}

$shell = new Mage_BackupBdd();
$shell->run();