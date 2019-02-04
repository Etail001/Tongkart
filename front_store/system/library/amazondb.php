<?php

class AmazonDB {

    public $image_location = '';
    public $server_folder = '';
    public $opencart_server_folder = '';
    public $image_absolute_path = '';
    public $feedRequestLocation = '';
    public $feedResponseLocation = '';
    //public $ebay_feed_location = '';
    public $response_inventory_feed_xml = 'response_amazon_inventory_feed.xml';
    public $response_product_feed_xml = 'response_amazon_product_feed.xml';
    public $response_price_feed_xml = 'response_amazon_price_feed.xml';
    public $response_relation_feed_xml = 'response_amazon_relation_feed.xml';
    public $response_delete_feed_xml = 'response_amazon_delete_feed.xml';
    public $response_image_feed_xml = 'response_amazon_image_feed.xml';
    public $submitted_feed_id_xml = 'submitted_feed_id_response.xml';
    public $response_shipment_feed_xml = 'response_amazon_shipment_feed.xml';
    public $barcode_blank_error_xml = 'barcode_blank_errors.xml';
    public $barcode_dup_error_xml = 'barcode_duplicate_errors.xml';
    public $threeDCart = 1;
    public $amazon = 2;
    public $openCart = 3;
    public $eBay = 4; //Added on 8 May 2014
    public $vendor_id = '1';
    public $error_file_size = 5242880;

    function __construct() {
        $this->image_location = DIR_IMAGE;
        $this->feedRequestLocation = '';
    }

    public function generateImageName($ext) {
        $length = 15;
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        if (file_exists($image_location . $result . $ext)) {
            generateImageName($ext);
        }
        return $result . $ext;
    }

    public function getFeedType($type) {
        $feedType = '';
        if ($type == 'inventory_update_feed') {
            $feedType = '_POST_INVENTORY_AVAILABILITY_DATA_';
        } else if ($type == 'product_feed' || $type == 'delete_product_feed') {
            $feedType = '_POST_PRODUCT_DATA_';
        } else if ($type == 'price_feed') {
            $feedType = '_POST_PRODUCT_PRICING_DATA_';
        } else if ($type == 'image_feed') {
            $feedType = '_POST_PRODUCT_IMAGE_DATA_';
        } else if ($type == 'relation_feed') {
            $feedType = '_POST_PRODUCT_RELATIONSHIP_DATA_';
        }

        return $feedType;
    }

    public function setFeedFetchPath($type) {
        $path = '';
        if ($type == 'inventory_update_feed') {
            $path = $this->feedRequestLocation;
        } else if ($type == 'product_feed') {
            $path = $this->feedRequestLocation;
        }
        return $path;
    }

    public function SaveFeedResponsePath($type, $sellercentral) {
        $path = '';
        if ($type == 'inventory_update_feed') {
            $path = $this->feedResponseLocation . $sellercentral . "/Response/" . $this->response_inventory_feed_xml;
        } else if ($type == 'product_feed') {
            $path = $this->feedResponseLocation . $sellercentral . "/Response/" . $this->response_product_feed_xml;
        } else if ($type == 'price_feed') {
            $path = $this->feedResponseLocation . $sellercentral . "/Response/" . $this->response_price_feed_xml;
        } else if ($type == 'image_feed') {
            $path = $this->feedResponseLocation . $sellercentral . "/Response/" . $this->response_image_feed_xml;
        } else if ($type == 'relation_feed') {
            $path = $this->feedResponseLocation . $sellercentral . "/Response/" . $this->response_relation_feed_xml;
        } else if ($type == 'feed_id_result') {
            $path = $this->feedResponseLocation . $sellercentral . "/Response/" . $this->submitted_feed_id_xml;
        } else if ($type == 'shipment_feed_id_result') {
            $path = $this->feedResponseLocation . $sellercentral . "/Response/" . $this->response_shipment_feed_xml;
        } else if ($type == 'delete_product_feed') {
            $path = $this->feedResponseLocation . $sellercentral . "/Response/" . $this->response_delete_feed_xml;
        } else if ($type == 'barcode_blank_error') {
            $path = $this->barcode_blank_error_xml;
        } else if ($type == 'barcode_dup_error') {
            $path = $this->barcode_dup_error_xml;
        }
        return $path;
    }

}

$db = new AmazonDB();
?>