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


class ImportNewproduct extends Mage_Shell_Abstract
{

    /**
     * Run script
     *
     */
    public function run()
    {   
        $this->print_message('Debut ' . date('d-m-Y-H-m-i'));


        $timestart=microtime(true);
        $readfile = __DIR__ . '/portedentrer.csv';
        $csv_read = new Varien_File_Csv();
        // Add three image sizes to media gallerya


        $resultfile = __DIR__ . '/portedentrer-' . date('d-m-Y-H-m-i') . '.csv';
        $csv_write = new Varien_File_Csv();
        $dataResult = array();

        $csv_read->setDelimiter(';');
        $csv_write->setDelimiter(';');

        $data = $csv_read->getData($readfile);

        for ($i = 0; $i < count($data); $i++) {
            Mage::register('dont_run_events', true);
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        $product = Mage::getModel('catalog/product');

        $id = Mage::getModel('catalog/product')->getIdBySku($data[$i][0]);

       //var_dump($data);
        $this->print_message($data[$i][0].' ' .$this->remplissage($data[$i][25]) . ' ' .$data[$i][25]);
        if(!$id) {
        try{
           // var_dump($data[$i][2],$data[$i][3]);
           //die;
        $product
        //    ->setStoreId(1) //you can set data in store scope
            ->setWebsiteIds(array(1)) //website ID the product is assigned to, as an array
            ->setAttributeSetId(33) //ID of a attribute set named 'default'
            ->setTypeId("simple") //product type
            ->setCreatedAt(strtotime('now')) //product creation time
        //    ->setUpdatedAt(strtotime('now')) //product update time

            ->setSku($data[$i][0]) //SKU
            ->setName($data[$i][5]) //product name
            ->setWeight(70.0000)
            ->setWeightReel(70.0000)
            ->setStatus(2) //product status (1 - enabled, 2 - disabled)
            ->setTaxClassId(4) //tax class (0 - none, 1 - default, 2 - taxable, 4 - shipping)
            ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH) //catalog and search visibility
            ->setManufacturer(2305) //manufacturer id
            //->setFichePdf($data[$i][10])
            ->setNoticePdf($data[$i][11])
            ->setCouleur($this->attribute('couleur',$data[$i][12]))
            ->setOuverturePortail($this->attribute('ouverture_portail',$data[$i][13]))
            ->setHauteurPortail($this->attribute('hauteur_portail',$data[$i][15]))
            ->setLargeurPortail($this->attribute('largeur_portail',$data[$i][14]))
            ->setGarantieAssurance($this->attribute('garantie_assurance',$data[$i][21]))
            ->setGarantieZend($this->attribute('garantie_zend',$data[$i][22]))
            ->setPoignee($this->attribute('poignee',$data[$i][16]))
            //->setStockRestant()
            ->setGarantie(64)
            ->setGarantieNv($data[$i][19])
            ->setGarentieType($this->garantie_type($data[$i][20]))
            //->setRemplissagePortail($this->remplissage($data[$i][25]))
            //->setNewsFromDate('06/26/2014') //product set as new from
            //->setNewsToDate('06/30/2014') //product set as new to
            ->setCountryOfManufacture('FR') //country of manufacture (2-letter country code)
            ->setPrice($data[$i][2]) //price in form 11.22
            //->setCost(22.33) //price in form 11.22
            ->setSpecialPrice($data[$i][3]) //special price in form 11.22
            ->setSpecialFromDate('10/26/2016') //special price from (MM-DD-YYYY)
            ->setSpecialToDate('') //special price to (MM-DD-YYYY)
            //->setMsrpEnabled(1) //enable MAP
            //->setMsrpDisplayActualPriceType(1) //display actual price (1 - on gesture, 2 - in cart, 3 - before order confirmation, 4 - use config)
            //->setMsrp(99.99) //Manufacturer's Suggested Retail Price
         
            ->setMetaTitle($data[$i][9])
            //->setMetaKeyword('test meta keyword 2')
            ->setMetaDescription($data[$i][10])
         
            ->setDescription($data[$i][6])
            ->setShortDescription($data[$i][7])
         
            //->setMediaGallery (array('images'=>array (), 'values'=>array ())) //media gallery initialization
            //->addImageToMediaGallery('media/catalog/product/1/0/10243-1.png', array('image','thumbnail','small_image'), false, false) //assigning image, thumb and small image to media gallery
         
            ->setStockData(array(
                               'use_config_manage_stock' => 0, //'Use config settings' checkbox
                               'manage_stock'=>1, //manage stock
                               //'min_sale_qty'=>1, //Minimum Qty Allowed in Shopping Cart
                               //'max_sale_qty'=>2, //Maximum Qty Allowed in Shopping Cart
                               'is_in_stock' => 1, //Stock Availability
                               'qty' => 10000 //qty
                           )
            );
         
            //->setCategoryIds(array(3, 10)); //assign product to categories
            $product->save();

            $product_data['id'] = 0;
            $product_data['sku'] = $data[$i][0];
            $product_data['error'] = 'OK add product';
            Mage::log('Import OK : '.$data[$i][0], null, 'importmass.log');
        //endif;
        }catch(Exception $e){
        $this->print_error($e->getMessage());
        //Mage::log($e->getMessage());
        Mage::log('Import exception : '.$e->getMessage() . ' ' .$data[$i][0], null, 'importmass.log');
        }
    } else {
        $this->print_error("Product exists : " . $data[$i][0]);
        $product_data['id'] = $id;
        $product_data['sku'] = $data[$i][0];
        $product_data['error'] = 'Product exists not action';
        Mage::log('Import NOK : '.$id . ' ' .$data[$i][0], null, 'importmass.log');
    }
            Mage::unregister('dont_run_events');

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

    public function attribute($attributeCode , $attributeAdminValue) {

        $attribute = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
        $collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($attribute->getId())
            ->setStoreFilter(Mage_Core_Model_App::ADMIN_STORE_ID)
            ->addFieldToFilter('tdv.value', $attributeAdminValue);

        if ($collection->getSize() > 0) {
            return $collection->getFirstItem()->getId();
        }
    }
   
    public function ouverture_portail($value){
        $valeur = '';
        switch ($value) {
            case 'Vers la droite':
                $valeur = 962;
                break;
            case 'Vers la gauche':
                $valeur = 961;
                break;
        }
        return $valeur;
    }
    // Attribut garantie_type

    public function motorisation($value){
        $valeur = '';
        switch ($value) {
            case 'Oui':
                $valeur = 968;
                break;
            case 'Non':
                $valeur = 967;
                break;
        }
        return $valeur;
    }
    // Attribut garantie_type

    public function garantie_type($value){
        $valeur = '';
        switch ($value) {
            case 'Année':
                $valeur = 62;
                break;
            case 'Mois':
                $valeur = 63;
                break;
        }
        return $valeur;
    }
    // Attribut remplissage

    public function remplissage($value){
        $valeur = '';
        switch ($value) {
            case 'Lames fougères':
                $valeur = 1176;
                break;
            case 'Lames horizontales':
                $valeur = 1137;
                break;
            case 'Lames verticales':
                $valeur = 1138;
                break;
        }
        return $valeur;
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

$shell = new ImportNewproduct();
$shell->run();

