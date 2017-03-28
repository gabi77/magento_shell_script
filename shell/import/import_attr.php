<?php
/**
 *
 * @category   Gabi77
 * @copyright  Copyright (c) 2015 gabi77 (http://www.gabi77.com)
 * @author     Gabriel Janez <gabriel_janez@hotmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
set_time_limit(0);
ini_set('memory_limit', '-1');

require_once ("app/Mage.php");
umask(0);
Mage::app("");

$store_id = '0';

Mage::app()->setCurrentStore($store_id);

function getProductFromSku($sku){
    $_catalog = Mage::getModel('catalog/product');
    $_productId = $_catalog->getIdBySku($sku);
    $product = Mage::getModel('catalog/product')->load($_productId);

    echo $product->getSku.'--'.$product->getId().'
    ';
    return $product;
}

function getProductFromId($id){
    $product = Mage::getModel('catalog/product')->load($id);

    echo $product->getSku.'--'.$product->getId().'
    ';
    return $product;
}

function readCsv(){
    $file = 'mep.csv';
    $all_rows = array();
    if (($handle = fopen($file, "r")) !== FALSE) {
        $header = null;
        while (($row = fgetcsv($handle)) !== FALSE) {
            if ($header === null) {
                $header = $row;
                continue;
            }
            $all_rows[] = array_combine($header, $row);
        }
    }
    return $all_rows;
}

$all_rows = readCsv();

if (count($all_rows)) {
    foreach($all_rows as $product_row){
        $product = getProductFromSku($product_row['sku']);

        if($product && $product->getId()){
            foreach($product_row as  $attr => $value){
                if($attr != 'sku' && $attr != 'entity_id' && $attr != 'id' && $attr != 'entity_id'){
                    $attribute = $product->getResource()->getAttribute($attr);
                    if ($attribute){
                        try{
                            echo $attr.'
';
                            $product->setData($attr,$value);
                            $product->getResource()->saveAttribute($product, $attr);

                        } catch (Exception $e){
                            echo $e;
                        }
                    }
                }
            }

            if($product->isConfigurable()){
                $simplesIds = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($product->getId());
                foreach($simplesIds as $simpleId){
                    $productSimple = getProductFromId($simpleId);
                    if($productSimple && $productSimple->getId()) {
                        foreach ($product_row as $attr => $value) {
                            if ($attr != 'sku' && $attr != 'entity_id' && $attr != 'id' && $attr != 'entity_id') {
                                $attribute = $productSimple->getResource()->getAttribute($attr);
                                if ($attribute) {
                                    try {
                                        echo $attr . '
';
                                        $productSimple->setData($attr, $value);
                                        $productSimple->getResource()->saveAttribute($productSimple, $attr);

                                    } catch (Exception $e) {
                                        echo $e;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }else{
            echo '############Not Exist: '.$product_row['sku'].'
';
        }
        $product->clearInstance();
    }
}

die('END BY DIE');