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
ini_set("memory_limit","512M");
require_once dirname($argv[0]) . '/../abstract.php';

class ImageGalleryImport extends Mage_Shell_Abstract
{

    /**
     * Run script
     *
     */
    public function run()
    {   
        $this->print_message('Debut ' . date('d-m-Y-H-i'));

        $remove = "noremove";
        $timestart=microtime(true);
        $readfile = __DIR__ . '/import_image_mm.csv';
        $csv_read = new Varien_File_Csv();
        // Add three image sizes to media gallery

        $csv_read->setDelimiter(';');

        $data = $csv_read->getData($readfile);

        for ($i = 0; $i < count($data); $i++) {
                Mage::register('dont_run_events', true);
                $product = Mage::getModel ( 'catalog/product' );
                $product_id = $product->getIdBySku( trim($data[$i][0]));
                $product->load ($product_id);
                 
                /**
                 * BEGIN REMOVE EXISTING MEDIA GALLERY
                 */
                if($remove == "yesremove") {
                $attributes = $product->getTypeInstance ()->getSetAttributes ();
                    if (isset ( $attributes['media_gallery'] )) {
                        $gallery = $attributes['media_gallery'];
                        //Get the images
                        $galleryData = $product->getMediaGallery ();
                        foreach ( $galleryData['images'] as $image ) {
                            //If image exists
                            if ($gallery->getBackend ()->getImage ( $product, $image['file'] )) {
                                $gallery->getBackend ()->removeImage ( $product, $image['file'] );
                            }
                        }
                        $product->save ();
                    }
                }
                /**
                 * END REMOVE EXISTING MEDIA GALLERY
                 */
                try {

                    $mediaArray = array(
                        'image'       => $data[$i][1],
                    );
                    foreach ( $mediaArray as $imageType => $fileName ) {
                        $importDir = Mage::getBaseDir('media') . DS . 'import/';
                        $filePath = $importDir . $fileName; 

                        //$this->print_message($filePath);
                        /**
                     * @param directory where import image reides
                     * @param leave 'null' so that it isn't imported as thumbnail, base, or small
                     * @param false = the image is copied, not moved from the import directory to it's new location
                     * @param false = not excluded from the front end gallery
                     */
                        $product->setMediaGallery ( array ('images' => array (), 'values' => array () ) );
                        if (file_exists($filePath)) {
                            $product->addImageToMediaGallery($filePath, array('image','thumbnail','small_image', 'nologo'), false, false)->save ();
                            $this->print_message($data[$i][0]);
                            Mage::log('Image OK : '.$data[$i][0], null, 'imageimport.log');
                        }
                    }
                } catch ( Exception $e ) {
                    $this->print_error($e->getMessage ());
                    Mage::log('Image NOK : '.$data[$i][0], null, 'imageimport.log');
                }
                Mage::unregister('dont_run_events');



                /*
            $mediaArray = array(
                'image'       => $data[$i][1],
            );

            // Remove unset images, add image to gallery if exists
            $importDir = Mage::getBaseDir('media') . DS . 'import/';

            foreach ( $mediaArray as $imageType => $fileName ) {
                $filePath = $importDir . $fileName;
                //$this->print_message($filePath);
                if ( file_exists($filePath) ) {
                    try {
                        $product->addImageToMediaGallery($filePath, array('image','thumbnail','small_image', 'nologo'), false, false);
                        $product->save();
                        $this->print_message($data[$i][0]);
                    } catch (Exception $e) {
                        $this->print_error($e->getMessage());
                    }
                } else {
                    $this->print_message($data[$i][0]."Product does not have an image or the path is incorrect. Path was: {$filePath}");
                }
            } */
        }

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

$shell = new ImageGalleryImport();
$shell->run();

