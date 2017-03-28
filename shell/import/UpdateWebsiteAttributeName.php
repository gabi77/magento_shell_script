<?php
/**
 *
 * @category   Gabi77
 * @copyright  Copyright (c) 2017 gabi77 (http://www.gabi77.com)
 * @author     Gabriel Janez <gabriel_janez@hotmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * Update attribute default for traduction
 */

set_time_limit(0);
ini_set("memory_limit","512M");
require_once dirname($argv[0]) . '/../abstract.php';
/** Include PHPExcel */
echo __DIR__ . '/PHPExcel.php';
require_once __DIR__ . '/PHPExcel.php';
/** PHPExcel_IOFactory */
require_once 'PHPExcel/IOFactory.php';
class UpdateWebsiteAttributeName extends Mage_Shell_Abstract
{

    /**
     * Run script
     *
     */
    public function run()
    {

        $store_id = '4';

        Mage::app()->setCurrentStore($store_id);
        $this->print_message('Debut ' . date('d-m-Y-H-i'));


        $readfile = __DIR__ . '/mistergatesdirectname.xlsx';
        $csv_read = PHPExcel_IOFactory::load($readfile);

        //$resultfile = __DIR__ . '/mistergatesdirect-' . date('d-m-Y-H-i') . '.xlsx';
        //$csv_write = new XLSXReader();


        //$csv_read->setDelimiter(';');
        //$csv_write->setDelimiter(';');

        $data = $csv_read->getActiveSheet()->toArray(null,true,true,true);
        $dataResult = array();

        for ($i = 1; $i < count($data); $i++) {

            //$this->print_message(print_r($data[$i], true));
            $products = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('description')
                ->addAttributeToSelect('caracteristiquestechniques')
                ->addAttributeToSelect('garantie_nv')
                ->addAttributeToFilter('sku', array('eq' => $data[$i]['A']))
                ->setStoreId(4);
            $product = $products->getFirstItem();
            
            if ($product && $product->getId()) {

                try {


                    $product_data = array();
                    $product_data['id'] = $product->getId();
                    $product_data['sku'] = $product->getSku();
                    $product_data['name'] = $product->getName();

                    $product->load();
                    $product->setName($data[$i]['B']);
                    $product_data['ap_name'] = $data[$i]['B'];
                    $product->getResource()->saveAttribute($product, 'name');

                    $product_data['error'] = '0';
                    $this->print_message(print_r($product_data, true));

                } catch (Exception $e) {
                    $product_data['id'] = 'error_onSave';
                    $product_data['sku'] = $data[$i]['A'];
                    $product_data['error'] = $e->getMessage();
                    $this->print_error($e->getMessage().":".$data[$i]['A']."\n");
                }

            } else {
                $product_data['id'] = 0;
                $product_data['sku'] = $data[$i]['A'];
                $product_data['error'] = 'Product not found';
                $this->print_error('Product not found '.$data[$i]['A']);
            }
            $product->clearInstance();
            $dataResult[] = $product_data;
        }
        //$csv_write->saveData($resultfile, $dataResult);


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

$shell = new UpdateWebsiteAttributeName();
$shell->run();