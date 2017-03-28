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
ini_set("memory_limit","1024M");
require_once dirname($argv[0]) . '/../abstract.php';
header('Content-type: text/plain; charset=utf-8');


class UpdateStatusProduct extends Mage_Shell_Abstract
{

    /**
     * Run script
     *
     */
    public function run()
    {
        $this->print_message('Debut ' . date('d-m-Y-H-m-i'));


        $timestart=microtime(true);
        $readfile = __DIR__ . '/import_activation_mm.csv';
        $csv_read = new Varien_File_Csv();

        $resultfile = __DIR__ . '/import_activation_mm-' . date('d-m-Y-H-i') . '.csv';
        $csv_write = new Varien_File_Csv();


        $csv_read->setDelimiter(';');
        $csv_write->setDelimiter(';');

        $data = $csv_read->getData($readfile);
        $dataResult = array();

        for ($i = 1; $i < count($data); $i++) {
            //$this->print_message(print_r($data[$i], true));
            $products = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect('status')
                ->addAttributeToFilter('sku', array('eq' => $data[$i][0]));

            $product = $products->getFirstItem();

            //$data[$i][1] = str_replace(",",".",$data[$i][1]);


            if ($product && $product->getId()) {

                try {


                    $product_data = array();
                    $product_data['id'] = $product->getId();
                    $product_data['sku'] = $product->getSku();
                    $product_data['status'] = $product->getStatus();

                    $product->load();
                    $product->setStatus(1);
                    $product_data['ap_status'] = 1;
                    $product->getResource()->saveAttribute($product, 'status');

                    $product_data['error'] = '0';
                    $this->print_message(print_r($product_data, true));

                } catch (Exception $e) {
                    $product_data['id'] = 'error_onSave';
                    $product_data['sku'] = $data[$i][0];
                    $product_data['error'] = $e->getMessage();
                    $this->print_error($e->getMessage().":$data[$i][0]\n");
                }

            } else {
                $product_data['id'] = 0;
                $product_data['sku'] = $data[$i][0];
                $product_data['error'] = 'Product not found';
                $this->print_error('Product not found');
            }
            $product->clearInstance();
            $dataResult[] = $product_data;

        }
        $csv_write->saveData($resultfile, $dataResult);


            //Fin du code PHP
        $timeend=microtime(true);
        $time=$timeend-$timestart;
 
        //Afficher le temps d'éxecution
        $page_load_time = number_format($time, 3);
        $this->print_message("Debut du script: ".date("H:i:s", $timestart));
        $this->print_message("Fin du script: ".date("H:i:s", $timeend));
        $this->print_message("Script execute en " . $page_load_time . " sec");


        $this->print_message(microtime(true));
        $this->print_message('Fin');
    }

    /**
     * Message print error
     **/

    public function print_error($message)
    {

        echo "\033[41m";
        echo $message;
        echo "\n";
        echo "\033[0m";
        echo "\n";
    }

    /**
     * Message print info
     **/

    public function print_message($message)
    {

        echo "\033[33m";
        echo $message;
        echo "\033[37m";
        echo "\n";
    }

}

$shell = new UpdateStatusProduct();
$shell->run();

