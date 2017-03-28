<?php
/**
 *
 * @category   Gabi77
 * @copyright  Copyright (c) 2015 gabi77 (http://www.gabi77.com)
 * @author     Gabriel Janez <gabriel_janez@hotmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */

set_time_limit(0);
ini_set("memory_limit","-1");
require_once dirname($argv[0]) . '/../abstract.php';

class setShipping extends Mage_Shell_Abstract
{

    /**
     * Run script
     *
     */
    public function run()
    {
        Mage::register('dont_run_events', true);
        $store_id = '0';

        Mage::app()->setCurrentStore($store_id);
        $this->print_message('Debut ' . date('d-m-Y-H-i'));


        $readfile = __DIR__ . '/import_specialprice.csv';
        $csv_read = new Varien_File_Csv();

        $resultfile = __DIR__ . '/import_specialprice-ok-' . date('d-m-Y-H-i') . '.csv';
        $csv_write = new Varien_File_Csv();

        $resultfileko = __DIR__ . '/import_specialprice-ko-' . date('d-m-Y-H-i') . '.csv';
        $csv_write_ko = new Varien_File_Csv();

        $csv_read->setDelimiter(';');
        $csv_write->setDelimiter(';');
        $csv_write_ko->setDelimiter(';');

        $data = $csv_read->getData($readfile);
        $dataResult = array();
        $dataResultKo = array();

        for ($i = 1; $i < count($data); $i++) {
            //$this->print_message(print_r($data[$i], true));
            $products = Mage::getResourceModel('catalog/product_collection')
               //->addAttributeToSelect('price')
                ->addAttributeToSelect('special_price')
                ->addAttributeToFilter('sku', array('eq' => $data[$i][0]))
                ->setStore($store_id);

            $product = $products->getFirstItem();

            $data[$i][1] = str_replace(",",".",$data[$i][1]);
            //$data[$i][2] = str_replace(",",".",$data[$i][2]);


            if ($product && $product->getId()) {

                try {


                    $product_data = array();
                    $product_data['id'] = $product->getId();
                    $product_data['sku'] = $product->getSku();
                    //$product_data['price'] = $product->getPrice();
                    $product_data['special_price'] = $product->getSpecialPrice();

                    $product->load();
                    //$product->setPrice($data[$i][1]);
                    //$product_data['ap_price'] = $data[$i][1];
                    $product->setSpecialPrice($data[$i][1]);
                    $product_data['ap_special_price'] = $data[$i][1];
                    //product->getResource()->saveAttribute($product, 'price');
                    $product->getResource()->saveAttribute($product, 'special_price');

                    $product_data['error'] = '0';
                    $dataResult[] = $product_data;
                    $this->print_message(print_r($product_data, true));

                } catch (Exception $e) {
                    $product_data['sku'] = $data[$i][0];
                    $product_data['special_price'] = $data[$i][1];
                    $product_data['status'] = 'error_onSave';
                    $product_data['error'] = $e->getMessage();
                    $dataResultKo[] = $product_data;
                    $this->print_error($e->getMessage().":$data[$i][0]\n");
                }

            } else {
                $product_data['sku'] = $data[$i][0];
                $product_data['special_price'] = $data[$i][1];
                $product_data['error'] = 'Product not found';
                $dataResultKo[] = $product_data;
                $this->print_error('Product not found');
            }
            $product->clearInstance();
            

        }
        $csv_write->saveData($resultfile, $dataResult);
        $csv_write_ko->saveData($resultfileko, $dataResultKo);
        Mage::unregister('dont_run_events');
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

$shell = new setShipping();
$shell->run();



