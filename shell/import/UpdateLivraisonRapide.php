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
ini_set("memory_limit","-1");
require_once dirname($argv[0]) . '/../abstract.php';

class setShipping extends Mage_Shell_Abstract
{

    const   VISIBILITY_NOT_VISIBLE = 1; // non visible individuellement
    const   VISIBILITY_IN_CATALOG = 2;
    const   VISIBILITY_IN_SEARCH = 3;
    const   VISIBILITY_BOTH = 4; // Catalogue; recherche

    protected $_categorye_liv_rapid = 841;  // Category Livraison Rapide
    //protected $_categorye_liv_rapid = 809;  // Category Livraison Rapide


    public $_argname = 1;

    public function __construct() {
        parent::__construct();

        // Time limit to infinity
        set_time_limit(0);
        $this->_argname = $this->getArg('optionss');
    }

    /**
     * Run script
     *
     */
    public function run()
    {
        $this->print_message($this->_argname);
        
        Mage::register('dont_run_events', true);
        $store_id = '0';

        Mage::app()->setCurrentStore($store_id);
        $this->print_message('Debut ' . date('d-m-Y-H-i'));

        if($this->_argname == 1){
            $readfile = __DIR__ . '/livraison_rapide.csv';
            $csv_read = new Varien_File_Csv();
        }elseif($this->_argname == 2){
            $readfile = __DIR__ . '/livraison_rapide_stock.csv';
            $csv_read = new Varien_File_Csv();
        }
        $resultfile = __DIR__ . '/livraison_rapide-ok-' . date('d-m-Y-H-i') . '.csv';
        $csv_write = new Varien_File_Csv();

        $csv_read->setDelimiter(';');
        $csv_write->setDelimiter(';');

        $data = $csv_read->getData($readfile);
        $dataResult = array();
        for ($i = 1; $i < count($data); $i++) {
            $products = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect('qty')
                ->addAttributeToSelect('livraison_delais')
                ->addAttributeToSelect('visibility')
                ->addAttributeToFilter('sku', array('eq' => $data[$i][0]))
                ->setStore($store_id);

            $product = $products->getFirstItem();
            if ($product && $product->getId()) {

               try {
                    $id = $product->getId();
                    $qty = $data[$i][1];
                    //$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($id);
                    $product_data = array();
                    $product_data['id'] = $product->getId();
                    $product_data['sku'] = $product->getSku();
                    $product_data['livraisondelais'] = $product->getLivraisonDelais();
                    $product_data['visibility'] = $product->getVisibility();
                    
                    $product->load();
                    if($this->_argname == 1){
                        $product->setLivraisonDelais($data[$i][2]);
                        $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH); //catalog and search visibility
                        $product->getResource()->saveAttribute($product, 'livraison_delais');
                        $product->getResource()->saveAttribute($product, 'visibility');
                        $product_data['StockUpdate'] = $this->UpdateMagento($id,$qty);
                        $product_data['category'] = self::assignCategory($this->_categorye_liv_rapid, $id);
                    } elseif($this->_argname == 2) {
                        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
                        $verifstock = $read->fetchCol("SELECT item_stock.qty FROM cataloginventory_stock_item item_stock WHERE item_stock.product_id = '$id'");
                        if($verifstock[0] <= 0) {
                            $product->setLivraisonDelais($data[$i][5]);
                            $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE); //catalog and search visibility
                            $product->getResource()->saveAttribute($product, 'livraison_delais');
                            $product->getResource()->saveAttribute($product, 'visibility');
                            $product_data['StockUpdate'] = $this->UpdateMagento($id,10000);
                            $product_data['category'] = self::removeCategory($this->_categorye_liv_rapid, $id);
                        } else {
                            $product_data['status'] = "Produit non traité car stock pas <= 0";
                        }
                        unset($read);
                    }

                    $product_data['error'] = '0';
                    $dataResult[] = $product_data;
                    $this->print_message(print_r($product_data, true));
                
                } catch (Exception $e) {
                    $product_data['sku'] = $data[$i][0];
                    $product_data['qty'] = $data[$i][1];
                    $product_data['status'] = 'error_onSave';
                    $product_data['error'] = $e->getMessage();
                    $this->print_error($e->getMessage().":$data[$i][0]\n");
                }

        
            } else {
                $product_data['sku'] = $data[$i][0];
                $product_data['qty'] = $data[$i][1];
                $product_data['error'] = 'Product not found';
                $this->print_error('Product not found');
            }
            $product->clearInstance();
            $dataResult[] = $product_data;         

        }
        $csv_write->saveData($resultfile, $dataResult);
        Mage::unregister('dont_run_events');
        $this->print_message('Fin');

    }

    /**
     * Update stock product
     *
     * @int $id
     * @string $new_quantity
     *
     * @return string
     **/

    public function UpdateMagento($id, $new_quantity) {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $write->query("UPDATE cataloginventory_stock_item item_stock, cataloginventory_stock_status status_stock
                       SET item_stock.qty = '$new_quantity', item_stock.is_in_stock = IF('$new_quantity'>0, 1,0),
                       status_stock.qty = '$new_quantity', status_stock.stock_status = IF('$new_quantity'>0, 1,0)
                       WHERE item_stock.product_id = '$id' AND item_stock.product_id = status_stock.product_id");

        return "Update qty " . $new_quantity . " to product " . $id;
    }

    /**
     * Remove category id product
     *
     * @int $idcategory
     * @int $idproduct
     *
     * @return string
     **/
    public function removeCategory($idcategory, $idproduct) {
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $read->query("DELETE FROM `catalog_category_product` where `category_id`=$idcategory and `product_id`= $idproduct");
        return "remove product " . $idproduct . " to category " . $idcategory;
    }


    /**
     * add category id product
     *
     * @int $idcategory
     * @int $idproduct
     *
     * @return string
     **/

    public function assignCategory($idcategory, $idproduct) {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $productPosition = 0;
        $write->query("REPLACE INTO `catalog_category_product` (`category_id`, `product_id`,  `position`) VALUES ($idcategory, $idproduct, $productPosition)");
        return "add product " . $idproduct . " to category " . $idcategory;
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

    /** 
     * Usage instructions
     **/
    public function usageHelp()
    {
        return <<<USAGE
        Usage:  php -f UpdateLivraisonRapide.php -- [options]
         
          --optionss <argvalue>       
          \t[1] -  - Run for update Delai livraison + visibility + Qty + Add category product 
          \t[2] -  - Run for update Delai livraison + visibility + Qty + Remove category product
         
          help                   This help 
USAGE;
    }

}

$shell = new setShipping();
$shell->run();