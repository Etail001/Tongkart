<?php
include(DIR_SYSTEM."library/amazon.php");
class ControllerAmazonAmazon extends Controller {
        
	public function test() {
            $z = new AmazonService();
            $data = array(
                'aws_key_id' => 'AKIAJBIBQUN2FA3Z6JGQ',
                'merchant_id' => 'AP6R8FW7VLG4L',
                'secret_key' => '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',
                'amazon_store_name' => 'in'
            );
            $z->checkSellerAccount($data);
        }
	 public function GetFeedSubmissionResult() { 
            $z = new AmazonService();
            $data = array(
                'aws_key_id' => 'AKIAISN7ADS7K5W2FHZA',
                'merchant_id' => 'A3EXBLBBCU4DIN',
                'secret_key' => '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',
                'amazon_store_name' => 'in',
                'market_place_id' => 'A21TJRUUN4KGV'
            	);  
            $this->load->model('amazon/amazon');
            $feed_array = $this->model_amazon_amazon->getFeedDetails(0);
            if(!empty($feed_array)){
                foreach($feed_array as $feed){
                    $response = $z->getFeedResponse($data,$feed['type'],$feed['feed_id']);
		      //echo "<pre>";print_r($response);echo "</pre>12345";die;
                    $xml = simplexml_load_string($response['content'], "SimpleXMLElement", LIBXML_NOCDATA);
                    $json = json_encode($xml);
                    $array = json_decode($json,TRUE);
		      //echo "<pre>";print_r($array);echo "</pre>12345";die;
                    $this->model_amazon_amazon->updateFeed($array,$feed['feed_id'],$feed['type']);
                }
            }
	  $data = array(
                'aws_key_id' => 'AKIAJXF3ES5TJOYMXDAA',
                'merchant_id' => 'A1LTO87O7AHC09',
                'secret_key' => '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',
                'amazon_store_name' => 'in',
                'market_place_id' => 'A21TJRUUN4KGV'
            );  
            $this->load->model('amazon/amazon');
            $feed_array = $this->model_amazon_amazon->getFeedDetails(2);
            if(!empty($feed_array)){
                foreach($feed_array as $feed){
                    $response = $z->getFeedResponse($data,$feed['type'],$feed['feed_id']);
		      //echo "<pre>";print_r($response);echo "</pre>12345";die;
                    $xml = simplexml_load_string($response['content'], "SimpleXMLElement", LIBXML_NOCDATA);
                    $json = json_encode($xml);
                    $array = json_decode($json,TRUE);
		      //echo "<pre>";print_r($array);echo "</pre>12345";die;
                    $this->model_amazon_amazon->updateFeed($array,$feed['feed_id'],$feed['type']);
                }
            }
            
        }
        public function submitProductFeed() { 
            $filter_data = array(
                    'start'             => 0,
                    'limit'              => 6000 
            );
	     if(isset($this->request->get['type']) && $this->request->get['type'] != ''){
		$type = $this->request->get['type'];
	     } else{
	     	$type = 'list';
	     }
            $this->load->model('amazon/amazon');
            $product_array = $this->model_amazon_amazon->getProductDetails($filter_data,$this->request->get['seller_id'],$type);
	     //echo "<pre>";print_r($product_array);echo "</pre>12345";die;  
	     if($this->request->get['seller_id'] == '1'){
		$data = array(
                'aws_key_id' => 'AKIAISN7ADS7K5W2FHZA',
                'merchant_id' => 'A3EXBLBBCU4DIN',
                'secret_key' => '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',
                'amazon_store_name' => 'in',
                'market_place_id' => 'A21TJRUUN4KGV'
            	);
	     } else if($this->request->get['seller_id'] == '2'){
		$data = array(
                'aws_key_id' => 'AKIAJXF3ES5TJOYMXDAA',
                'merchant_id' => 'A1LTO87O7AHC09',
                'secret_key' => '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',
                'amazon_store_name' => 'in',
                'market_place_id' => 'A21TJRUUN4KGV'
            );
	     } 
            //echo "<pre>";print_r($data);echo "</pre>12345";die;
            $feed = $this->model_amazon_amazon->generateProductFeed($product_array,$type,$this->request->get['seller_id']);
	     
            file_put_contents(DIR_SYSTEM."amazon_feed/product_feed.xml", $feed);
            $z = new AmazonService();
            $response = $z->submitProductFeed($data,DIR_SYSTEM."amazon_feed/product_feed.xml",'product_feed');
	     $xml = simplexml_load_string($response['content'], "SimpleXMLElement", LIBXML_NOCDATA);
            $json = json_encode($xml);
            $array = json_decode($json,TRUE); 
	     $this->model_amazon_amazon->insertFeedId($array,'product',$product_array); 
            
        }
        public function submitImageFeed() {
            if($this->request->get['seller_id'] == '1'){
		$data = array(
                'aws_key_id' => 'AKIAISN7ADS7K5W2FHZA',
                'merchant_id' => 'A3EXBLBBCU4DIN',
                'secret_key' => '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',
                'amazon_store_name' => 'in',
                'market_place_id' => 'A21TJRUUN4KGV'
            	);
	     } else if($this->request->get['seller_id'] == '2'){
		$data = array(
                'aws_key_id' => 'AKIAJXF3ES5TJOYMXDAA',
                'merchant_id' => 'A1LTO87O7AHC09',
                'secret_key' => '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',
                'amazon_store_name' => 'in',
                'market_place_id' => 'A21TJRUUN4KGV'
            );
	     } 
            $filter_data = array(
                    'start'             => 0,
                    'limit'              => 100
            );
            $this->load->model('amazon/amazon');
            $product_array = $this->model_amazon_amazon->getImageProductDetails($filter_data,$this->request->get['seller_id']);
            $string = '<?xml version="1.0" encoding="utf-8" ?>
<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amznenvelope.xsd">
<Header>
<DocumentVersion>1.01</DocumentVersion>
<MerchantIdentifier>A1LTO87O7AHC09</MerchantIdentifier>
</Header>
<MessageType>ProductImage</MessageType>';
            $index = 1;
foreach($product_array as $product){
        $string .= '<Message>
        <MessageID>'.$index.'</MessageID>
        <OperationType>Update</OperationType>
        <ProductImage>
        <SKU>'.$product['model'].'</SKU>
        <ImageType>Main</ImageType>';
	 if($this->request->get['seller_id'] == '2'){
        	$string .= '<ImageLocation>'.str_replace('//',"",$product['image']).'</ImageLocation>';
	 } else{
	 	$string .= '<ImageLocation>'.$product['image'].'</ImageLocation>';
	 }
        $string .= '</ProductImage>
        </Message>';
        $index++;
        $image_array = $this->model_amazon_amazon->getProductImage($product['product_id']);
        $index_image = 1;
        foreach($image_array as $image){
	     
            if($image['image'] != ''){
                    $string .= '<Message>
                <MessageID>'.$index.'</MessageID>
                <OperationType>Update</OperationType>
                <ProductImage>
                <SKU>'.$product['model'].'</SKU>
                <ImageType>PT'.$index_image.'</ImageType>';
		  if($this->request->get['seller_id'] == '2'){
                	$string .= '<ImageLocation>'.str_replace('//',"",$image['image']).'</ImageLocation>';
		  } else{
			$string .= '<ImageLocation>'.$image['image'].'</ImageLocation>';
		  }
                $string .= '</ProductImage>
                </Message>';
                    $index_image++;
                    $index++;
            }
        }
}
$string .= '</AmazonEnvelope>';
            file_put_contents(DIR_SYSTEM."amazon_feed/product_image_feed.xml", $string);
            $z = new AmazonService();
            $response = $z->submitImageFeed($data,DIR_SYSTEM."amazon_feed/product_image_feed.xml",'image_feed');
	     //echo "<pre>";print_r($response);echo "</pre>12345";die; 
            $xml = simplexml_load_string($response['content'], "SimpleXMLElement", LIBXML_NOCDATA);
            $json = json_encode($xml);
            $array = json_decode($json,TRUE);
	     $this->model_amazon_amazon->insertFeedId($array,'image',$product_array); 
        }
public function submitPriceFeed() {
            if($this->request->get['seller_id'] == '1'){
		$data = array(
                'aws_key_id' => 'AKIAISN7ADS7K5W2FHZA',
                'merchant_id' => 'A3EXBLBBCU4DIN',
                'secret_key' => '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',
                'amazon_store_name' => 'in',
                'market_place_id' => 'A21TJRUUN4KGV'
            	);
	     } else if($this->request->get['seller_id'] == '2'){
		$data = array(
                'aws_key_id' => 'AKIAJXF3ES5TJOYMXDAA',
                'merchant_id' => 'A1LTO87O7AHC09',
                'secret_key' => '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',
                'amazon_store_name' => 'in',
                'market_place_id' => 'A21TJRUUN4KGV'
            );
	     }
            $filter_data = array(
                    'start'             => 0,
                    'limit'              => 500
            );
            $this->load->model('amazon/amazon');
            $product_array = $this->model_amazon_amazon->getProductPrice($filter_data,$this->request->get['seller_id']);
	     //echo "<pre>";print_r($product_array);echo "</pre>";die;
	     if(!empty($product_array)){
            $string = '<?xml version="1.0" encoding="utf-8" ?>
            <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amznenvelope.xsd">
            <Header>
            <DocumentVersion>1.01</DocumentVersion>
            <MerchantIdentifier>A1LTO87O7AHC09</MerchantIdentifier>
            </Header>
            <MessageType>Price</MessageType>';
            $i = 1;
            foreach($product_array as $products){
		  //$price_product = $this->model_amazon_amazon->getProductListingPrice($products['product_id'],$products['price']);
                $price_product = ($products['price'] * 5);
		  if($price_product > 0){
		  $price_product = $price_product + 200;
		  }
		  $price_product = round($price_product,2);
                $string .= '<Message>
                <MessageID>'.$i.'</MessageID>
                <Price>
                <SKU>'.$products['model'].'</SKU> 
                <StandardPrice currency="INR">'.$price_product.'</StandardPrice>
                </Price>
                </Message>';
		  $i++;
            }
            $string .= '</AmazonEnvelope>';
            file_put_contents(DIR_SYSTEM."amazon_feed/product_price_feed.xml", $string);
            $z = new AmazonService();
            $response = $z->submitPriceFeed($data,DIR_SYSTEM."amazon_feed/product_price_feed.xml",'price_feed');
	     $xml = simplexml_load_string($response['content'], "SimpleXMLElement", LIBXML_NOCDATA);
            $json = json_encode($xml);
            $array = json_decode($json,TRUE);
	     $this->model_amazon_amazon->insertFeedId($array,'price',$product_array); 
	  }
        }
        public function submitInventoryFeed() { 
            $data = array(
                'aws_key_id' => 'AKIAISN7ADS7K5W2FHZA',
                'merchant_id' => 'A3EXBLBBCU4DIN',
                'secret_key' => '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',
                'amazon_store_name' => 'in',
                'market_place_id' => 'A21TJRUUN4KGV'
            ); 
            $this->load->model('amazon/amazon');
            $product_array = $this->model_amazon_amazon->getProductInventory(1);
	     if (!empty($product_array)){
            $string = '<?xml version="1.0" encoding="utf-8" ?>
            <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amznenvelope.xsd">
            <Header>
            <DocumentVersion>1.01</DocumentVersion>
            <MerchantIdentifier>A3EXBLBBCU4DIN</MerchantIdentifier>
            </Header>
	     <MessageType>Inventory</MessageType>';
            $i = 1;
            foreach($product_array as $products){ 
		  $product_category = $this->model_amazon_amazon->getProductCategory($products['product_id']);
                if($product_category['name'] != "Phones & Accessories" && $product_category['name'] != "Tablets & Accessories") {
		      $products['quantity'] = $products['quantity'] - 5;
		      if($products['quantity'] < 0){
			  $products['quantity'] = 0;
		      }
                    $string .= '<Message>
                    <MessageID>'.$i.'</MessageID>
                    <OperationType>Update</OperationType>
                    <Inventory>
                    <SKU>'.$products['model'].'</SKU>
                    <Quantity>0</Quantity>
                    <FulfillmentLatency>1</FulfillmentLatency>
                    </Inventory>
                    </Message>';
                      $i++;
                }
            } 
           $string .= '</AmazonEnvelope>';
           file_put_contents(DIR_SYSTEM."amazon_feed/product_inventory_feed.xml", $string);
           $z = new AmazonService();
           $response = $z->submitInventoryFeed($data,DIR_SYSTEM."amazon_feed/product_inventory_feed.xml",'inventory_update_feed');
           $xml = simplexml_load_string($response['content'], "SimpleXMLElement", LIBXML_NOCDATA);
           $json = json_encode($xml);
           $array = json_decode($json,TRUE);
	    $this->model_amazon_amazon->insertFeedId($array,'inventory',$product_array);
	    }
	    $data = array(
                'aws_key_id' => 'AKIAJXF3ES5TJOYMXDAA',
                'merchant_id' => 'A1LTO87O7AHC09',
                'secret_key' => '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',
                'amazon_store_name' => 'in',
                'market_place_id' => 'A21TJRUUN4KGV'
            ); 
            $this->load->model('amazon/amazon');
            $product_array = $this->model_amazon_amazon->getProductInventory(2);
	     //echo "<pre>";print_r($product_array);echo "</pre>-----";die;
	     if (!empty($product_array)){
            $string = '<?xml version="1.0" encoding="utf-8" ?>
            <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amznenvelope.xsd">
            <Header>
            <DocumentVersion>1.01</DocumentVersion>
            <MerchantIdentifier>A1LTO87O7AHC09</MerchantIdentifier>
            </Header>
	     <MessageType>Inventory</MessageType>';
            $i = 1;
            foreach($product_array as $products){ 
		  $product_category = $this->model_amazon_amazon->getProductCategory($products['product_id']);
                if($product_category['name'] != "Phones & Accessories" && $product_category['name'] != "Tablets & Accessories") {
		      $products['quantity'] = $products['quantity'] - 5;
		      if($products['quantity'] < 0){
		      	  $products['quantity'] = 0;
		      }
                    $string .= '<Message>
                    <MessageID>'.$i.'</MessageID>
                    <OperationType>Update</OperationType>
                    <Inventory>
                    <SKU>'.$products['model'].'</SKU>
                    <Quantity>'.$products['quantity'].'</Quantity>
                    <FulfillmentLatency>15</FulfillmentLatency>
                    </Inventory>
                    </Message>';
                      $i++;
                }
            } 
           $string .= '</AmazonEnvelope>';
           file_put_contents(DIR_SYSTEM."amazon_feed/product_inventory_feed.xml", $string);
           $z = new AmazonService();
           $response = $z->submitInventoryFeed($data,DIR_SYSTEM."amazon_feed/product_inventory_feed.xml",'inventory_update_feed');
           $xml = simplexml_load_string($response['content'], "SimpleXMLElement", LIBXML_NOCDATA);
           $json = json_encode($xml);
           $array = json_decode($json,TRUE);
	    $this->model_amazon_amazon->insertFeedId($array,'inventory',$product_array,2);
	    }
            
        }
	 public function ListOrder() { 
            $filter_data = array(
                    'start'             => 0,
                    'limit'              => 20
            );
	     $this->load->model('amazon/amazon');
            $date = gmdate("Y-m-d H:i:s");
            $date = strtotime($date);
            $date = strtotime("-4 day", $date);
            $date = date("Y-m-d\TH:i:s\Z", $date);
            $data = array(
                'aws_key_id' => 'AKIAISN7ADS7K5W2FHZA',
                'merchant_id' => 'A3EXBLBBCU4DIN',
                'secret_key' => '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',
                'amazon_store_name' => 'in',
                'market_place_id' => 'A21TJRUUN4KGV',
		  'last_order_fetch' => $date
            );
            $z = new AmazonService();
            $response = $z->ListOrder($data);
	     //echo "<pre>";print_r($response);echo "</pre>12345";die; 
	     $xml = simplexml_load_string($response['content'], "SimpleXMLElement", LIBXML_NOCDATA);
	     
            $json = json_encode($xml);
            $array = json_decode($json,TRUE);
	     //echo "<pre>";print_r($array);echo "</pre>12345";die; 
	     $this->model_amazon_amazon->insertOrder($array,1);
	     $filter_data = array(
                    'start'             => 0,
                    'limit'              => 20
            );
	     $this->load->model('amazon/amazon');
            $date = gmdate("Y-m-d H:i:s");
            $date = strtotime($date);
            $date = strtotime("-4 day", $date);
            $date = date("Y-m-d\TH:i:s\Z", $date);
            $data = array(
                'aws_key_id' => 'AKIAJXF3ES5TJOYMXDAA',
                'merchant_id' => 'A1LTO87O7AHC09',
                'secret_key' => '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',
                'amazon_store_name' => 'in',
                'market_place_id' => 'A21TJRUUN4KGV',
		  'last_order_fetch' => $date
            );
            $z = new AmazonService();
            $response = $z->ListOrder($data);
	     //echo "<pre>";print_r($response);echo "</pre>12345";die; 
	     $xml = simplexml_load_string($response['content'], "SimpleXMLElement", LIBXML_NOCDATA);
	     
            $json = json_encode($xml);
            $array = json_decode($json,TRUE);
	     $this->model_amazon_amazon->insertOrder($array,2);
            
        }
	 public function ListOrderItem() { 
            $data = array(
                'aws_key_id' => 'AKIAISN7ADS7K5W2FHZA',
                'merchant_id' => 'A3EXBLBBCU4DIN',
                'secret_key' => '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',
                'amazon_store_name' => 'in',
                'market_place_id' => 'A21TJRUUN4KGV'
            );
            $this->load->model('amazon/amazon');
            $amazon_order = $this->model_amazon_amazon->getAmazonOrder(1);
	     if(!empty($amazon_order)){
            foreach($amazon_order as $orders){
                $z = new AmazonService();
                $response = $z->ListOrderItems($data,'in',$orders['amazon_order_id']);
                $xml = simplexml_load_string($response['content'], "SimpleXMLElement", LIBXML_NOCDATA);
                $json = json_encode($xml);
                $array = json_decode($json,TRUE);
		  //echo "<pre>";print_r($array);echo "</pre>";die;
                $this->model_amazon_amazon->insertOrderItems($array,$orders['amazon_order_id']);
            }
	    }
	     $data = array(
                'aws_key_id' => 'AKIAJXF3ES5TJOYMXDAA',
                'merchant_id' => 'A1LTO87O7AHC09',
                'secret_key' => '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',
                'amazon_store_name' => 'in',
                'market_place_id' => 'A21TJRUUN4KGV'
            );
            $this->load->model('amazon/amazon');
            $amazon_order = $this->model_amazon_amazon->getAmazonOrder(2);
            foreach($amazon_order as $orders){
                $z = new AmazonService();
                $response = $z->ListOrderItems($data,'in',$orders['amazon_order_id']);
                $xml = simplexml_load_string($response['content'], "SimpleXMLElement", LIBXML_NOCDATA);
                $json = json_encode($xml);
                $array = json_decode($json,TRUE);
		  //echo "<pre>";print_r($array);echo "</pre>";die;
                $this->model_amazon_amazon->insertOrderItems($array,$orders['amazon_order_id']);
            }
            
        }
        public function ShippingConfirmation() {
            $data = array(
                'aws_key_id' => 'AKIAISN7ADS7K5W2FHZA',
                'merchant_id' => 'A3EXBLBBCU4DIN',
                'secret_key' => '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',
                'amazon_store_name' => 'in',
                'market_place_id' => 'A21TJRUUN4KGV'
            );
            $this->load->model('amazon/amazon');
            $amazon_order = $this->model_amazon_amazon->getOrderTracking(1); 
	     if(!empty($amazon_order)){
            $string = '<?xml version="1.0" encoding="ISO-8859-1"?>
<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
<Header>
<DocumentVersion>1.01</DocumentVersion>
<MerchantIdentifier>AP6R8FW7VLG4L</MerchantIdentifier>
</Header>
<MessageType>OrderFulfillment</MessageType>';
            $i = 1;
            foreach($amazon_order as $orders){
                $date = strtotime($orders['ship_date']);
                $date = date("Y-m-d\TH:i:s\Z", $date);
                $orders['ship_date'] = $date;
                $string .= '<Message>
<MessageID>'.$i.'</MessageID>
<OrderFulfillment>
<AmazonOrderID>'.$orders['amazon_order_id'].'</AmazonOrderID>
<FulfillmentDate>'.$orders['ship_date'].'</FulfillmentDate>
<FulfillmentData>
<CarrierCode>'.$orders['shipping_name'].'</CarrierCode>
<ShippingMethod>'.$orders['shipping_method'].'</ShippingMethod>
<ShipperTrackingNumber>'.$orders['tracking_number'].'</ShipperTrackingNumber>
</FulfillmentData>
</OrderFulfillment>
</Message>';
            $i++;
            }
            $string .= '</AmazonEnvelope>';
            file_put_contents(DIR_SYSTEM."amazon_feed/shipping_feed.xml", $string);
                $z = new AmazonService();
                $response = $z->ShippingConfirmation($data,DIR_SYSTEM."amazon_feed/shipping_feed.xml");
                $xml = simplexml_load_string($response['content'], "SimpleXMLElement", LIBXML_NOCDATA);
                $json = json_encode($xml);
                $array = json_decode($json,TRUE);
		  //echo "<pre>";print_r($array);echo "</pre>-----";die;
		  $this->model_amazon_amazon->insertFeedId($array,'shipment',$amazon_order);
	    }
		$data = array(
                'aws_key_id' => 'AKIAJXF3ES5TJOYMXDAA',
                'merchant_id' => 'A1LTO87O7AHC09',
                'secret_key' => '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',
                'amazon_store_name' => 'in',
                'market_place_id' => 'A21TJRUUN4KGV'
            );
            $this->load->model('amazon/amazon');
            $amazon_order = $this->model_amazon_amazon->getOrderTracking(0);
	     //echo "<pre>";print_r($amazon_order);echo "</pre>-----";die;
	     if(!empty($amazon_order)){
            $string = '<?xml version="1.0" encoding="ISO-8859-1"?>
<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
<Header>
<DocumentVersion>1.01</DocumentVersion>
<MerchantIdentifier>A1LTO87O7AHC09</MerchantIdentifier>
</Header>
<MessageType>OrderFulfillment</MessageType>';
            $i = 1;
            foreach($amazon_order as $orders){
                $date = strtotime($orders['ship_date']);
                $date = date("Y-m-d\TH:i:s\Z", $date);
                $orders['ship_date'] = $date;
                $string .= '<Message>
<MessageID>'.$i.'</MessageID>
<OrderFulfillment>
<AmazonOrderID>'.$orders['amazon_order_id'].'</AmazonOrderID>
<FulfillmentDate>'.$orders['ship_date'].'</FulfillmentDate>
<FulfillmentData>
<CarrierCode>'.$orders['shipping_name'].'</CarrierCode>
<ShippingMethod>'.$orders['shipping_method'].'</ShippingMethod>
<ShipperTrackingNumber>'.$orders['tracking_number'].'</ShipperTrackingNumber>
</FulfillmentData>
</OrderFulfillment>
</Message>';
            $i++;
            }
            $string .= '</AmazonEnvelope>';
            file_put_contents(DIR_SYSTEM."amazon_feed/shipping_feed.xml", $string);
                $z = new AmazonService();
                $response = $z->ShippingConfirmation($data,DIR_SYSTEM."amazon_feed/shipping_feed.xml");
                $xml = simplexml_load_string($response['content'], "SimpleXMLElement", LIBXML_NOCDATA);
                $json = json_encode($xml);
                $array = json_decode($json,TRUE);
		  //echo "<pre>";print_r($array);echo "</pre>";die;
		  $this->model_amazon_amazon->insertFeedId($array,'shipment',$amazon_order,2);
	 }
        }
	 public function UpdateImage() { 
            $this->load->model('amazon/amazon');
            $products = $this->model_amazon_amazon->getImageUpdate();
                foreach ($products as $product) {
                    $paramsorder = array(
                        'HeaderRequest' => array(
                            'appkey' => 'AKIAJTCLTKVHPESLG56QDFRT',
                            'token' => 'fee9f1b97e95d01f05beb5042e6a5ee2'
                        ),
                        'addRequestInfoArray' => array(
                            'filefath' => $product['image'],
                            'method' => 'putFile',
                            'bucketName' => 'tngimages'
                        )
                    );

                    $url = "store.tongkart.com/aws/s3/index.php";
                    $data_string = json_encode($paramsorder);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($ch);
                    $obj = json_decode($response);
                    curl_close($ch);
                    $this->db->query("UPDATE " . DB_PREFIX . "product set image = '".$obj->data->return_url."' WHERE product_id = '".$product['product_id']."'");
                    $product_images = $this->model_amazon_amazon->getProductImages($product['product_id']);
                    foreach ($product_images as $images) {
                        $paramsorder = array(
                            'HeaderRequest' => array(
                                'appkey' => 'AKIAJTCLTKVHPESLG56QDFRT',
                                'token' => 'fee9f1b97e95d01f05beb5042e6a5ee2'
                            ),
                            'addRequestInfoArray' => array(
                                'filefath' => $images['image'],
                                'method' => 'putFile',
                                'bucketName' => 'tngimages'
                            )
                        );

                        $url = "store.tongkart.com/aws/s3/index.php";
                        $data_string = json_encode($paramsorder);
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $response = curl_exec($ch);
                        $obj = json_decode($response);
                        curl_close($ch);
                        $this->db->query("UPDATE " . DB_PREFIX . "product_image set image = '".$obj->data->return_url."' WHERE product_image_id = '".$images['product_image_id']."'");
                    }
                    $this->db->query("UPDATE " . DB_PREFIX . "product set image_moved = '1' WHERE product_id = '".$product['product_id']."'");
        }
    } 
}
