<?php
include(DIR_SYSTEM."library/amazon.php");
class ControllerAmazonAmazonFront extends Controller {
        
	public function test() {
            $this->load->model('amazon/amazon_front');
            $amazon_settings = $this->model_amazon_amazon_front->getSettings();
            $z = new AmazonService();
            $seller_id = $type = $this->request->get['id'];
            $token = $type = $this->request->get['token'];
            $customer_id = $this->customer->getId();
            if($customer_id == 9){
            $data = array(
                    'aws_key_id' => $amazon_settings[0]['aws_key_id'],
                    'merchant_id' => $amazon_settings[0]['merchant_id'],
                    'secret_key' => $amazon_settings[0]['secret_key'],
                    'amazon_store_name' => $amazon_settings[0]['amazon_store_name'],
                    'market_place_id' => $amazon_settings[0]['market_place_id'],
                    //'auth_token'      => $token
            	);
            $response = $z->checkSellerAccount($data,'1');
            } else{
                $data = array(
                    'aws_key_id' => $amazon_settings[0]['aws_key_id'],
                    'merchant_id' => $seller_id,
                    'secret_key' => $amazon_settings[0]['secret_key'],
                    'amazon_store_name' => $amazon_settings[0]['amazon_store_name'],
                    'market_place_id' => $amazon_settings[0]['market_place_id'],
                    'auth_token'      => $token
            	);
                $response = $z->checkSellerAccount($data);
            }
            
	     $xml = simplexml_load_string($response['content'], "SimpleXMLElement", LIBXML_NOCDATA);
            $json = json_encode($xml);
            $array = json_decode($json,TRUE);
	     if(!isset($array['Error'])){
		 echo 'success';
	     } else{
		echo $array['Error']['Message'];
	     }
		
        } 
	 public function GetFeedSubmissionResult() { 
            $this->load->model('amazon/amazon_front');
            $amazon_settings = $this->model_amazon_amazon_front->getSettings();
            $customer_settings = $this->model_amazon_amazon_front->getCustomerSettings();
	     $z = new AmazonService();
	     foreach($customer_settings as $settings){
                if($settings['customer_id'] == 9) {
                $data = array(
                    'aws_key_id' => $amazon_settings[0]['aws_key_id'],
                    'merchant_id' => $amazon_settings[0]['merchant_id'],
                    'secret_key' => $amazon_settings[0]['secret_key'],
                    'amazon_store_name' => $amazon_settings[0]['amazon_store_name'],
                    'market_place_id' => $amazon_settings[0]['market_place_id']
            	);
                } else{
                   $data = array(
                    'aws_key_id' => $amazon_settings[0]['aws_key_id'],
                    'merchant_id' => $settings['seller_id'],
                    'secret_key' => $amazon_settings[0]['secret_key'],
                    'amazon_store_name' => $amazon_settings[0]['amazon_store_name'],
                    'market_place_id' => $amazon_settings[0]['market_place_id'],
                    'auth_token'      => $settings['auth_token']
            	); 
                }
		$feed_array = $this->model_amazon_amazon_front->getFeedDetails($settings['customer_id']);
		if(!empty($feed_array)){
		   foreach($feed_array as $feed){
                    if($settings['customer_id'] == 9) {
                        $response = $z->getFeedResponse($data,$feed['type'],$feed['feed_id']);
                    } else{
                        $response = $z->getFeedResponse($data,$feed['type'],$feed['feed_id'],'1');
                    }
                    $xml = simplexml_load_string($response['content'], "SimpleXMLElement", LIBXML_NOCDATA);
                    $json = json_encode($xml);
                    $array = json_decode($json,TRUE);
                    $this->model_amazon_amazon_front->updateFeed($array,$feed['feed_id'],$feed['type'],$settings['customer_id']);
                }
		}
	     }
	     
            
        }
        public function submitProductFeed() { 
            $filter_data = array(
                    'start'             => 0,
                    'limit'              => 6000 
            );
            $type = 'list';
            $this->load->model('amazon/amazon_front');
            $amazon_settings = $this->model_amazon_amazon_front->getSettings();
            $customer_settings = $this->model_amazon_amazon_front->getCustomerSettings();
            foreach($customer_settings as $settings){
		  $status = $this->model_amazon_amazon_front->getFeedStatus($settings['customer_id'],'product');
                if($status){
                if($settings['customer_id'] == 9) {
                    $data = array(
                        'aws_key_id' => $amazon_settings[0]['aws_key_id'],
                        'merchant_id' => $amazon_settings[0]['merchant_id'],
                        'secret_key' => $amazon_settings[0]['secret_key'],
                        'amazon_store_name' => $amazon_settings[0]['amazon_store_name'],
                        'market_place_id' => $amazon_settings[0]['market_place_id']
                    );
                } else{
                    $data = array(
                        'aws_key_id' => $amazon_settings[0]['aws_key_id'],
                        'merchant_id' => $settings['seller_id'],
                        'secret_key' => $amazon_settings[0]['secret_key'],
                        'amazon_store_name' => $amazon_settings[0]['amazon_store_name'],
                        'market_place_id' => $amazon_settings[0]['market_place_id'],
                        'auth_token'      => $settings['auth_token']
                    );
                }
		$product_array = $this->model_amazon_amazon_front->getProductDetails($filter_data,$settings['customer_id'],$type);
		if(!empty($product_array)){
              $feed = $this->model_amazon_amazon_front->generateProductFeed($product_array,$settings['seller_id'],$settings['customer_id'],$type);
                
                $file_name = DIR_SYSTEM."amazon_feed/product_feed".time().".xml";
                file_put_contents($file_name, $feed);
                $z = new AmazonService();
                if($settings['customer_id'] == 9) {
                    $response = $z->submitProductFeed($data,$file_name,'product_feed');
                } else{
                    $response = $z->submitProductFeed($data,$file_name,'product_feed','1');
                }
		  //echo "<pre>";print_r($response);echo "</pre>";die;
                $xml = simplexml_load_string($response['content'], "SimpleXMLElement", LIBXML_NOCDATA);
                $json = json_encode($xml);
                $array = json_decode($json,TRUE); 
                $this->model_amazon_amazon_front->insertFeedId($array,'product',$product_array,$settings['customer_id']);
		}
		}
                
	    }
            $filter_data = array(
                    'start'             => 0,
                    'limit'              => 6000 
            );
	    $type = 'delete';
            $this->load->model('amazon/amazon_front');
            $amazon_settings = $this->model_amazon_amazon_front->getSettings();
            $customer_settings = $this->model_amazon_amazon_front->getCustomerSettings();
            foreach($customer_settings as $settings){
		  $status = $this->model_amazon_amazon_front->getFeedStatus($settings['customer_id'],'product_delete');
                if($status){
                if($settings['customer_id'] == 9) {
                    $data = array(
                        'aws_key_id' => $amazon_settings[0]['aws_key_id'],
                        'merchant_id' => $amazon_settings[0]['merchant_id'],
                        'secret_key' => $amazon_settings[0]['secret_key'],
                        'amazon_store_name' => $amazon_settings[0]['amazon_store_name'],
                        'market_place_id' => $amazon_settings[0]['market_place_id']
                    );
                } else{
                    $data = array(
                        'aws_key_id' => $amazon_settings[0]['aws_key_id'],
                        'merchant_id' => $settings['seller_id'],
                        'secret_key' => $amazon_settings[0]['secret_key'],
                        'amazon_store_name' => $amazon_settings[0]['amazon_store_name'],
                        'market_place_id' => $amazon_settings[0]['market_place_id'],
                        'auth_token'      => $settings['auth_token']
                    );
                }
		$product_array = $this->model_amazon_amazon_front->getProductDetails($filter_data,$settings['customer_id'],$type);
		if(!empty($product_array)){
               $feed = $this->model_amazon_amazon_front->generateProductFeed($product_array,$settings['seller_id'],$settings['customer_id'],$type);
                
                $file_name = DIR_SYSTEM."amazon_feed/product_delete_feed".time().".xml";
                file_put_contents($file_name, $feed);
                $z = new AmazonService();
                if($settings['customer_id'] == 9) {
                    $response = $z->submitProductFeed($data,$file_name,'product_feed');
                } else{
                    $response = $z->submitProductFeed($data,$file_name,'product_feed','1');
                }
		  //echo "<pre>";print_r($response);echo "</pre>";die;
                $xml = simplexml_load_string($response['content'], "SimpleXMLElement", LIBXML_NOCDATA);
                $json = json_encode($xml);
                $array = json_decode($json,TRUE); 
                $this->model_amazon_amazon_front->insertFeedId($array,'product_delete',$product_array,$settings['customer_id']);
		}
		}
                
	    }
	     
             
            
        }
        public function submitImageFeed() {
	     $this->load->model('amazon/amazon_front');
            $amazon_settings = $this->model_amazon_amazon_front->getSettings();
            $customer_settings = $this->model_amazon_amazon_front->getCustomerSettings();
	     $filter_data = array(
                    'start'             => 0,
                    'limit'              => 6000 
            );
            foreach($customer_settings as $settings){
		  $status = $this->model_amazon_amazon_front->getFeedStatus($settings['customer_id'],'image');
                if($status){
                if($settings['customer_id'] == 9) {
			$data = array(
                    'aws_key_id' => $amazon_settings[0]['aws_key_id'],
                    'merchant_id' => $amazon_settings[0]['merchant_id'],
                    'secret_key' => $amazon_settings[0]['secret_key'],
                    'amazon_store_name' => $amazon_settings[0]['amazon_store_name'],
                    'market_place_id' => $amazon_settings[0]['market_place_id']
            	);
                } else {
                    $data = array(
                    'aws_key_id' => $amazon_settings[0]['aws_key_id'],
                    'merchant_id' => $settings['seller_id'],
                    'secret_key' => $amazon_settings[0]['secret_key'],
                    'amazon_store_name' => $amazon_settings[0]['amazon_store_name'],
                    'market_place_id' => $amazon_settings[0]['market_place_id'],
                    'auth_token'      => $settings['auth_token']
            	);
                }
			$product_array = $this->model_amazon_amazon_front->getImageProductDetails($filter_data,$settings['customer_id']);
                        if(!empty($product_array)){
			$string = '<?xml version="1.0" encoding="utf-8" ?>
<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amznenvelope.xsd">
<Header>
<DocumentVersion>1.01</DocumentVersion>
<MerchantIdentifier>'.$settings['seller_id'].'</MerchantIdentifier>
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
	 if( strpos( $product['image'], 'quarkscm' ) !== false) {
        	$string .= '<ImageLocation>'.str_replace('//',"",$product['image']).'</ImageLocation>';
	 } else{
	 	$string .= '<ImageLocation>'.$product['image'].'</ImageLocation>';
	 }
        $string .= '</ProductImage>
        </Message>';
        $index++;
        $image_array = $this->model_amazon_amazon_front->getProductImage($product['product_id']);
        $index_image = 1;
        foreach($image_array as $image){
	     
            if($image['image'] != ''){
                    $string .= '<Message>
                <MessageID>'.$index.'</MessageID>
                <OperationType>Update</OperationType>
                <ProductImage>
                <SKU>'.$product['model'].'</SKU>
                <ImageType>PT'.$index_image.'</ImageType>';
		  if( strpos( $product['image'], 'quarkscm' ) !== false) {
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
	     $file_name = DIR_SYSTEM."amazon_feed/product_image_feed".time().".xml";
            file_put_contents($file_name, $string);
            $z = new AmazonService();
            if($settings['customer_id'] == 9) {
                $response = $z->submitImageFeed($data,$file_name,'image_feed');
            } {
                $response = $z->submitImageFeed($data,$file_name,'image_feed','1');
            }
            $xml = simplexml_load_string($response['content'], "SimpleXMLElement", LIBXML_NOCDATA);
            $json = json_encode($xml);
            $array = json_decode($json,TRUE);
	     
	     $this->model_amazon_amazon_front->insertFeedId($array,'image',$product_array,$settings['customer_id']);
             }
		}
	     }
            
             
        }
public function submitPriceFeed() {
	     $this->load->model('amazon/amazon_front');
            $amazon_settings = $this->model_amazon_amazon_front->getSettings();
            $customer_settings = $this->model_amazon_amazon_front->getCustomerSettings();
	     $filter_data = array(
                    'start'             => 0,
                    'limit'              => 6000 
            );
            foreach($customer_settings as $settings){
		  $status = $this->model_amazon_amazon_front->getFeedStatus($settings['customer_id'],'price');
                if($status){
                if($settings['customer_id'] == 9) {
			$data = array(
                    'aws_key_id' => $amazon_settings[0]['aws_key_id'],
                    'merchant_id' => $amazon_settings[0]['merchant_id'],
                    'secret_key' => $amazon_settings[0]['secret_key'],
                    'amazon_store_name' => $amazon_settings[0]['amazon_store_name'],
                    'market_place_id' => $amazon_settings[0]['market_place_id']
            	);
                } else{
                    $data = array(
                    'aws_key_id' => $amazon_settings[0]['aws_key_id'],
                    'merchant_id' => $settings['seller_id'],
                    'secret_key' => $amazon_settings[0]['secret_key'],
                    'amazon_store_name' => $amazon_settings[0]['amazon_store_name'],
                    'market_place_id' => $amazon_settings[0]['market_place_id'],
                    'auth_token'      => $settings['auth_token']
            	);
                }
		$product_array = $this->model_amazon_amazon_front->getProductPrice($filter_data,$settings['customer_id']);
		if(!empty($product_array)){
            $string = '<?xml version="1.0" encoding="utf-8" ?>
            <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amznenvelope.xsd">
            <Header>
            <DocumentVersion>1.01</DocumentVersion>
            <MerchantIdentifier>'.$settings['seller_id'].'</MerchantIdentifier>
            </Header>
            <MessageType>Price</MessageType>';
            $i = 1;
            foreach($product_array as $products){
		  //$price_product = $this->model_amazon_amazon_front->getProductListingPrice($products['product_id'],$products['price']);
                $price_product = ($products['price'] * 5);
		  if($price_product > 0){
		  $price_product = $price_product + 200;
		  }
		  $price_product = round($price_product,2);
                  $products['tongkart_price'] = $this->model_amazon_amazon_front->getProductTongkartPrice($settings['customer_id'],$products['product_id']);
                  $mprice = $this->model_amazon_amazon_front->getmaxPrice($products['product_id'],$products['tongkart_price'],$settings['customer_id']);
                  $minprice = $this->model_amazon_amazon_front->getminPrice($products['product_id'],$products['tongkart_price'],$settings['customer_id']);
                  $amprice = $this->model_amazon_amazon_front->getAmPrice($products['product_id'],$products['tongkart_price'],$settings['customer_id']);
                $string .= '<Message>
                <MessageID>'.$i.'</MessageID>
                <Price>
                <SKU>'.$products['model'].'</SKU> 
                <StandardPrice currency="INR">'.$amprice.'</StandardPrice>
                <MinimumSellerAllowedPrice currency="INR">'.$minprice.'</MinimumSellerAllowedPrice>
                <MaximumSellerAllowedPrice currency="INR">'.$mprice.'</MaximumSellerAllowedPrice>
                </Price>
                </Message>';
		  $i++;
            }
            $string .= '</AmazonEnvelope>';
	     $file_name = DIR_SYSTEM."amazon_feed/product_price_feed".time().".xml";
            file_put_contents($file_name, $string);
            $z = new AmazonService();
            if($settings['customer_id'] == 9) {
                $response = $z->submitPriceFeed($data,$file_name,'price_feed');
            } else {
                $response = $z->submitPriceFeed($data,$file_name,'price_feed','1');
            }
	     $xml = simplexml_load_string($response['content'], "SimpleXMLElement", LIBXML_NOCDATA);
            $json = json_encode($xml);
            $array = json_decode($json,TRUE);
	     $this->model_amazon_amazon_front->insertFeedId($array,'price',$product_array,$settings['customer_id']); 
	     }
	  }
	     }
	     
        }
        public function submitInventoryFeed() { 
            $this->load->model('amazon/amazon_front');
            $amazon_settings = $this->model_amazon_amazon_front->getSettings();
            $customer_settings = $this->model_amazon_amazon_front->getCustomerSettings();
	     $filter_data = array(
                    'start'             => 0,
                    'limit'              => 6000 
            );
            foreach($customer_settings as $settings){
		  $status = $this->model_amazon_amazon_front->getFeedStatus($settings['customer_id'],'inventory');
                if($status){
                if($settings['customer_id'] == 9) {
			$data = array(
                    'aws_key_id' => $amazon_settings[0]['aws_key_id'],
                    'merchant_id' => $amazon_settings[0]['merchant_id'],
                    'secret_key' => $amazon_settings[0]['secret_key'],
                    'amazon_store_name' => $amazon_settings[0]['amazon_store_name'],
                    'market_place_id' => $amazon_settings[0]['market_place_id']
            	);
                } else{
                    $data = array(
                    'aws_key_id' => $amazon_settings[0]['aws_key_id'],
                    'merchant_id' => $settings['seller_id'],
                    'secret_key' => $amazon_settings[0]['secret_key'],
                    'amazon_store_name' => $amazon_settings[0]['amazon_store_name'],
                    'market_place_id' => $amazon_settings[0]['market_place_id'],
                    'auth_token'      => $settings['auth_token']
            	);
                }
		$product_array = $this->model_amazon_amazon_front->getProductInventory($settings['customer_id']);
		if (!empty($product_array)){
            $string = '<?xml version="1.0" encoding="utf-8" ?>
            <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amznenvelope.xsd">
            <Header>
            <DocumentVersion>1.01</DocumentVersion>
            <MerchantIdentifier>'.$settings['seller_id'].'</MerchantIdentifier>
            </Header>
	     <MessageType>Inventory</MessageType>';
            $i = 1;
            foreach($product_array as $products){
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
                    <FulfillmentLatency>7</FulfillmentLatency>
                    </Inventory>
                    </Message>';
                      $i++;
            } 
           $string .= '</AmazonEnvelope>';
	    $file_name = DIR_SYSTEM."amazon_feed/product_inventory_feed.xml";
           file_put_contents($file_name, $string);
           $z = new AmazonService();
           if($settings['customer_id'] == 9) {
                $response = $z->submitInventoryFeed($data,$file_name,'inventory_update_feed');
           } else {
                $response = $z->submitInventoryFeed($data,$file_name,'inventory_update_feed','1');
           }
           $xml = simplexml_load_string($response['content'], "SimpleXMLElement", LIBXML_NOCDATA);
           $json = json_encode($xml);
           $array = json_decode($json,TRUE);
	    $this->model_amazon_amazon_front->insertFeedId($array,'inventory',$product_array,$settings['customer_id']);
		}
	    }
	     }
	     
	    
            
        }
	 public function ListOrder() { 
            $this->load->model('amazon/amazon_front');
            $amazon_settings = $this->model_amazon_amazon_front->getSettings();
            $customer_settings = $this->model_amazon_amazon_front->getCustomerSettings();
	     $filter_data = array(
                    'start'             => 0,
                    'limit'              => 6000 
            );
            foreach($customer_settings as $settings){
                    $date = gmdate("Y-m-d H:i:s");
                    $date = strtotime($date);
                    $date = strtotime("-4 day", $date);
                    $date = date("Y-m-d\TH:i:s\Z", $date);
                    if($settings['customer_id'] == 9) {
                    $data = array(
                    'aws_key_id' => $amazon_settings[0]['aws_key_id'],
                    'merchant_id' => $amazon_settings[0]['merchant_id'],
                    'secret_key' => $amazon_settings[0]['secret_key'],
                    'amazon_store_name' => $amazon_settings[0]['amazon_store_name'],
                    'market_place_id' => $amazon_settings[0]['market_place_id'],
                    'last_order_fetch' => $date
            	);
                    } else{
                        $data = array(
                    'aws_key_id' => $amazon_settings[0]['aws_key_id'],
                    'merchant_id' => $settings['seller_id'],
                    'secret_key' => $amazon_settings[0]['secret_key'],
                    'amazon_store_name' => $amazon_settings[0]['amazon_store_name'],
                    'market_place_id' => $amazon_settings[0]['market_place_id'],
                    'auth_token'      => $settings['auth_token'],
                    'last_order_fetch' => $date
            	);
                    }
                $z = new AmazonService();
                if($settings['customer_id'] == 9) {
                    $response = $z->ListOrder($data); 
                } else{
                    $response = $z->ListOrder($data,1);
                }
                 $xml = simplexml_load_string($response['content'], "SimpleXMLElement", LIBXML_NOCDATA);

                $json = json_encode($xml);
                $array = json_decode($json,TRUE);
                 $this->model_amazon_amazon_front->insertOrder($array,1,$settings['customer_id']);
            }
            
        }
	 public function ListOrderItem() { 
            $this->load->model('amazon/amazon_front');
            $amazon_settings = $this->model_amazon_amazon_front->getSettings();
            $customer_settings = $this->model_amazon_amazon_front->getCustomerSettings();
	     $filter_data = array(
                    'start'             => 0,
                    'limit'              => 6000 
            );
            foreach($customer_settings as $settings){
                    if($settings['customer_id'] == 9) {
                    $data = array(
                    'aws_key_id' => $amazon_settings[0]['aws_key_id'],
                    'merchant_id' => $amazon_settings[0]['merchant_id'],
                    'secret_key' => $amazon_settings[0]['secret_key'],
                    'amazon_store_name' => $amazon_settings[0]['amazon_store_name'],
                    'market_place_id' => $amazon_settings[0]['market_place_id']
            	);
                    } else{
                        $data = array(
                    'aws_key_id' => $amazon_settings[0]['aws_key_id'],
                    'merchant_id' => $settings['seller_id'],
                    'secret_key' => $amazon_settings[0]['secret_key'],
                    'amazon_store_name' => $amazon_settings[0]['amazon_store_name'],
                    'market_place_id' => $amazon_settings[0]['market_place_id'],
                    'auth_token'      => $settings['auth_token'],
            	);
                    }
                $amazon_order = $this->model_amazon_amazon_front->getAmazonOrder($settings['customer_id']);
                if(!empty($amazon_order)){
                foreach($amazon_order as $orders){
                    $z = new AmazonService();
                    if($settings['customer_id'] == 9) {
                        $response = $z->ListOrderItems($data,'in',$orders['amazon_order_id']);
                    } else {
                        $response = $z->ListOrderItems($data,'in',$orders['amazon_order_id'],1);
                    }
                    $xml = simplexml_load_string($response['content'], "SimpleXMLElement", LIBXML_NOCDATA);
                    $json = json_encode($xml);
                    $array = json_decode($json,TRUE);
                      //echo "<pre>";print_r($array);echo "</pre>";die;
                    $this->model_amazon_amazon_front->insertOrderItems($array,$orders['amazon_order_id']);
                }
                }
            }
            
        }
        public function ShippingConfirmation() {
            $this->load->model('amazon/amazon_front');
            $amazon_settings = $this->model_amazon_amazon_front->getSettings();
            $customer_settings = $this->model_amazon_amazon_front->getCustomerSettings();
	     $filter_data = array(
                    'start'             => 0,
                    'limit'              => 6000 
            );
            foreach($customer_settings as $settings){
			$status = $this->model_amazon_amazon_front->getFeedStatus($settings['customer_id'],'shipment');
                if($status){
                    if($settings['customer_id'] == 9) {
                    $data = array(
                    'aws_key_id' => $amazon_settings[0]['aws_key_id'],
                    'merchant_id' => $amazon_settings[0]['merchant_id'],
                    'secret_key' => $amazon_settings[0]['secret_key'],
                    'amazon_store_name' => $amazon_settings[0]['amazon_store_name'],
                    'market_place_id' => $amazon_settings[0]['market_place_id']
            	);
                    } else {
                        $data = array(
                    'aws_key_id' => $amazon_settings[0]['aws_key_id'],
                    'merchant_id' => $settings['seller_id'],
                    'secret_key' => $amazon_settings[0]['secret_key'],
                    'amazon_store_name' => $amazon_settings[0]['amazon_store_name'],
                    'market_place_id' => $amazon_settings[0]['market_place_id'],
                    'auth_token'      => $settings['auth_token'],
            	);
                    }
                $amazon_order = $this->model_amazon_amazon_front->getOrderTracking($settings['customer_id']);
                if(!empty($amazon_order)){
            $string = '<?xml version="1.0" encoding="ISO-8859-1"?>
<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
<Header>
<DocumentVersion>1.01</DocumentVersion>
<MerchantIdentifier>'.$settings['seller_id'].'</MerchantIdentifier>
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
                if($settings['customer_id'] == 9) {
                    $response = $z->ShippingConfirmation($data,DIR_SYSTEM."amazon_feed/shipping_feed.xml");
                } else{
                    $response = $z->ShippingConfirmation($data,DIR_SYSTEM."amazon_feed/shipping_feed.xml",1);
                }
                $xml = simplexml_load_string($response['content'], "SimpleXMLElement", LIBXML_NOCDATA);
                $json = json_encode($xml);
                $array = json_decode($json,TRUE);
		  $this->model_amazon_amazon_front->insertFeedId($array,'shipment',$amazon_order,$settings['customer_id']);
		}
	    }
                
            }
            
        }
	 public function UpdateImage() { 
            $this->load->model('amazon/amazon_front');
            $products = $this->model_amazon_amazon_front->getImageUpdate();
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
                    $product_images = $this->model_amazon_amazon_front->getProductImages($product['product_id']);
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
