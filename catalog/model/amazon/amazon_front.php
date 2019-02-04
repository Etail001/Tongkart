<?php
class ModelAmazonAmazonFront extends Model {
    public function getImageProductDetails($product_id,$customer_id) {
        $sql = "SELECT product_id from " . DB_PREFIX . "amazon_product_listing where customer_id = ".$customer_id." and amazon_status = 'listed' and amazon_image_update = '0'";
        $query = $this->db->query($sql);
        $product_data = array();
        foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id'],$customer_id);
		}

		return $product_data;
    }
    public function getProductListingPrice($product_id,$price) {
        $this->load->model('setting/setting');
            $result = $this->model_setting_setting->getSetting('price_filter',0);
            if($result['price_filter']['price_type'] == 1){
                if($result['price_filter']['type'] == 1){
                    if($price>0){
                        $price_list = $price + $result['price_filter']['value'];
                    } else{
                        $price_list = $price;
                    }
                } else{
                    if($price>0){
                        $price_list = (($result['price_filter']['value']/100) * $price)+$price;
                    } else{
                        $price_list = $price;
                    }
                }
                
            } else{
                $category_data = $this->getProductCategory($product_id);
                if($result['price_filter']['type'.$category_data['category_id']] == 1){
                    if($price>0){
                        $price_list = $price + $result['price_filter']['value'.$category_data['category_id']];
                    } else{
                        $price_list = $price;
                    }
                } else{
                    if($price>0){
                        $price_list = (($result['price_filter']['value'.$category_data['category_id']]/100) * $price)+$price;
                    } else{
                        $price_list = $price;
                    }
                }
            }
            return $price_list;
    }
    public function getProductPrice($filter_data,$customer_id) {
        $sql = "SELECT p.product_id from " . DB_PREFIX . "product p left join " . DB_PREFIX . "amazon_product_listing sm on(sm.product_id = p.product_id) where sm.customer_id = ".$customer_id." and sm.amazon_status = 'listed' and sm.amazon_price_update = '0' limit 6000";
        $query = $this->db->query($sql);
        $product_data = array();
        foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProductPriceDetails($result['product_id'],$customer_id);
		}

		return $product_data;
    }
    public function getProductDetails($data,$customer_id,$type) {
	if($type == 'delete'){
	   $sql = "SELECT product_id from " . DB_PREFIX . "amazon_product_listing where customer_id = '".$customer_id."' and amazon_status != 'submited' and amazon_status != 'error' and amazon_status != 'restricted' and listing_status_id = '1' and amazon_status = 'delete' ORDER BY product_id ASC";
        if (isset($data['start']) || isset($data['limit'])) {
                if ($data['start'] < 0) {
                        $data['start'] = 0;
                }

                if ($data['limit'] < 1) {
                        $data['limit'] = 20;
                }

                $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }
	 } else{
        $sql = "SELECT product_id from " . DB_PREFIX . "amazon_product_listing where customer_id = '".$customer_id."' and amazon_status != 'delete' and amazon_status != 'submited' and amazon_status != 'error' and amazon_status != 'restricted' and listing_status_id = '1' ORDER BY product_id ASC";
        if (isset($data['start']) || isset($data['limit'])) {
                if ($data['start'] < 0) {
                        $data['start'] = 0;
                }

                if ($data['limit'] < 1) {
                        $data['limit'] = 20;
                }

                $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }
        }
        $product_data = array();
        $query = $this->db->query($sql);

        foreach ($query->rows as $result) {
                $product_data[$result['product_id']] = $this->getProduct($result['product_id'],$customer_id);
        }
        $array_product = array();
        foreach($product_data as $products){
            $category = $this->getProductCategory($products['product_id']);
	     //echo $category['name'].'.......'.$products['product_id']; 
	     if(isset($category['name']) && $category['name'] !=''){
                $array_product[] = $this->getProduct($products['product_id'],$customer_id);
	     }
        }
	 

        return $array_product;
    }
    public function getProductPriceDetails($product_id,$customer_id) {
		$query = $this->db->query("SELECT p.product_id,p.model,p.price,p.tongkart_price FROM " . DB_PREFIX . "product p WHERE p.product_id = '" . (int)$product_id . "'");

		if ($query->num_rows) {
			return array(
				'product_id'       => $query->row['product_id'],
                                'model'       => $query->row['model'],
				'price'            => $query->row['price'],
                                'tongkart_price'            => $query->row['tongkart_price']
			);
		} else {
			return false;
		}
	}
    public function getProduct($product_id,$customer_id) {
		$query = $this->db->query("SELECT DISTINCT *, pd.name AS name,al.upc as barcode,pd.amazon_description,pd.bullet_point_1,pd.bullet_point_2,pd.bullet_point_3,pd.bullet_point_4,pd.bullet_point_5,pd.package_weight,pd.product_size,pd.package_size,pd.package_contents,pd.featurs,pd.compatible_with,pd.material, p.image, m.name AS manufacturer, p.sort_order, pd.color FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "amazon_product_listing al ON (p.product_id = al.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)  LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '" . (int)$product_id . "' AND al.customer_id = '".$customer_id."' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		if ($query->num_rows) {
			return array(
				'product_id'       => $query->row['product_id'],
				'name'             => $query->row['name'],
				'amazon_description'             => $query->row['amazon_description'],
				'bullet_point_1'             => $query->row['bullet_point_1'],
				'bullet_point_2'             => $query->row['bullet_point_2'],
				'bullet_point_3'             => $query->row['bullet_point_3'],
				'bullet_point_4'             => $query->row['bullet_point_4'],
				'bullet_point_5'             => $query->row['bullet_point_5'],
				'package_weight'             => $query->row['package_weight'],
				'product_size'             => $query->row['product_size'],
				'package_contents'             => $query->row['package_contents'],
				'compatible_with'             => $query->row['compatible_with'],
				'material'             => $query->row['material'],
				'featurs'             => $query->row['featurs'],
				'description'      => $query->row['description'],
				'meta_title'       => $query->row['meta_title'],
				'meta_description' => $query->row['meta_description'],
				'meta_keyword'     => $query->row['meta_keyword'],
				'tag'              => $query->row['tag'],
				'model'            => $query->row['model'],
				'sku'              => $query->row['sku'],
				'upc'              => $query->row['upc'],
				'ean'              => $query->row['barcode'],
				'jan'              => $query->row['jan'],
				'isbn'             => $query->row['isbn'],
				'mpn'              => $query->row['mpn'],
				'location'         => $query->row['location'],
				'quantity'         => $query->row['quantity'],
				'image'            => $query->row['image'],
				'manufacturer_id'  => $query->row['manufacturer_id'],
				'manufacturer'     => $query->row['manufacturer'],
				'price'            => $query->row['price'],
                                'tongkart_price'            => $query->row['tongkart_price'],
				'listing_price'            => $query->row['listing_price'],
				'length'           => $query->row['length'],
				'width'            => $query->row['width'],
				'height'           => $query->row['height'],
				'weight'           => $query->row['weight'],	
				'length_class_id'  => $query->row['length_class_id'],
				'subtract'         => $query->row['subtract'],
				'minimum'          => $query->row['minimum'],
				'sort_order'       => $query->row['sort_order'],
				'status'           => $query->row['status'],
				'date_added'       => $query->row['date_added'],
				'date_modified'    => $query->row['date_modified'],
				'viewed'           => $query->row['viewed'],
				'color'            => $query->row['color']
			);
		} else {
			return false;
		}
	}
        public function getProductCategory($product_id) {
            $query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "product_to_category p2c  WHERE product_id = '" . (int)$product_id . "'");
            if ($query->num_rows) {
                $data = $query->row;
                $category_array = array();
                 $query1 = $this->db->query("SELECT c.category_id,c.parent_id,cd.name FROM " . DB_PREFIX . "category c, " . DB_PREFIX . "category_description cd  WHERE c.category_id = cd.category_id and c.category_id = ".$data['category_id']);
                    if ($query1->num_rows) {
                        if($query1->row['parent_id'] == 0){
                            $category_array = array(
                            'category_id' => $query1->row['category_id'],
                            'parent_id' => $query1->row['parent_id'],
                            'name' => $query1->row['name'],
                        );
                        } else{
                             $query2 = $this->db->query("SELECT c.category_id,c.parent_id,cd.name FROM " . DB_PREFIX . "category c, " . DB_PREFIX . "category_description cd  WHERE c.category_id = cd.category_id and c.category_id = ".$query1->row['parent_id']);
                                if ($query2->num_rows) {
                                    if($query2->row['parent_id'] == 0){
                                        $category_array = array(
                                        'category_id' => $query2->row['category_id'],
                                        'parent_id' => $query2->row['parent_id'],
                                        'name' => $query2->row['name'],
                                    );
                                    } else{
                                        $query3 = $this->db->query("SELECT c.category_id,c.parent_id,cd.name FROM " . DB_PREFIX . "category c, " . DB_PREFIX . "category_description cd  WHERE c.category_id = cd.category_id and c.category_id = ".$query2->row['parent_id']); 
                                        if ($query3->num_rows) {
                                            if($query3->row['parent_id'] == 0){
                                                $category_array = array(
                                                'category_id' => $query3->row['category_id'],
                                                'parent_id' => $query3->row['parent_id'],
                                                'name' => $query3->row['name'],
                                            );
                                            } else{
                                                $query4 = $this->db->query("SELECT c.category_id,c.parent_id,cd.name FROM " . DB_PREFIX . "category c, " . DB_PREFIX . "category_description cd  WHERE c.category_id = cd.category_id and c.category_id = ".$query3->row['parent_id']);     
                                                if ($query4->num_rows) {
                                                    if($query4->row['parent_id'] == 0){
                                                        $category_array = array(
                                                        'category_id' => $query4->row['category_id'],
                                                        'parent_id' => $query4->row['parent_id'],
                                                        'name' => $query4->row['name'],
                                                    );
                                                    }
                                                }
                                            }
                                    }
                                }
                        }
                    }
            }
            }
	     //echo $product_id.'----';
            return $category_array;
        }
        public function getProductImage($product_id) {
            $query = $this->db->query("SELECT image FROM " . DB_PREFIX . "product_image  WHERE product_id = '" . (int)$product_id . "' limit 8");
            if ($query->num_rows) {
                return $query->rows;
            }
        }
        public function generateProductFeed($product_array,$seller_id,$customer_id,$type) { 
	     $timestamp = gmdate("Y-m-d\TH:i:s");
            $string = '<?xml version="1.0" ?>
<AmazonEnvelope
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amznenvelope.xsd">
	<Header>
		<DocumentVersion>1.01</DocumentVersion>';
        $string .= '<MerchantIdentifier>'.$seller_id.'</MerchantIdentifier>';
	$string .= '</Header>
	<MessageType>Product</MessageType>
	<PurgeAndReplace>false</PurgeAndReplace>';
	 $index = 1; 
        foreach($product_array as $products){
	 $category = $this->getProductCategory($products['product_id']);
	 if($products['manufacturer'] == ''){
            $products['manufacturer'] = 'Tongkart';
        }
        $product_name = $this->getProductName($products['name'],$category['category_id'],$customer_id);
        $brand_name = $this->getBrandName($category['name']);
	 $bullet_points = '';
	 if(isset($products['bullet_point_1']) && $products['bullet_point_1'] != ''){
	     $bullet_points .= '<BulletPoint>'.str_replace("&","And",$products['bullet_point_1']).'</BulletPoint>';
	 }
	 if(isset($products['bullet_point_2']) && $products['bullet_point_2'] != ''){
	     $bullet_points .= '<BulletPoint>'.str_replace("&","And",substr($products['bullet_point_2'],0,500)).'</BulletPoint>';
	 }
	 if(isset($products['bullet_point_3']) && $products['bullet_point_3'] != ''){
	     $bullet_points .= '<BulletPoint>'.str_replace("&","And",$products['bullet_point_3']).'</BulletPoint>';
	 }
	 if(isset($products['bullet_point_4']) && $products['bullet_point_4'] != ''){
	     $bullet_points .= '<BulletPoint>'.str_replace("&","And",substr($products['bullet_point_4'],0,500)).'</BulletPoint>';
	 }
	 if(isset($products['bullet_point_5']) && $products['bullet_point_5'] != ''){
	     $bullet_points .= '<BulletPoint>'.str_replace("&","And",$products['bullet_point_5']).'</BulletPoint>';
	 }
	 $dimension = '<ItemDimensions>'; 
	 $dimension .= '<Length unitOfMeasure="CM">'.round($products['length'],2).'</Length> 
	 <Width unitOfMeasure="CM">'.round($products['width'],2).'</Width> 
	 <Height unitOfMeasure="CM">'.round($products['height'],2).'</Height>';
	 if($seller_id == '2'){
	 	$dimension .= '<Weight unitOfMeasure="KG">'.round(($products['weight']/1000),2).'</Weight>';
	 } else if($seller_id == '1'){
		$dimension .= '<Weight unitOfMeasure="KG">'.round(($products['weight']),2).'</Weight>';
	 }
	 $dimension .= '</ItemDimensions>';
//         $mprice = $this->getmaxPrice($products['price'],$customer_id);
//	 if($mprice > 0){ 
//	  $dimension .= '<MSRP currency="INR">'.$mprice.'</MSRP>
//<MSRPWithTax currency="INR">'.$mprice.'</MSRPWithTax>';
//	 }
	 if(isset($products['package_weight']) && $products['package_weight'] != ''){
	     if($seller_id == '2'){ 
	 		$dimension .= '<PackageWeight unitOfMeasure="KG">'.round(($products['package_weight']/1000),2).'</PackageWeight>';
	     		$dimension .= '<ShippingWeight unitOfMeasure="KG">'.round(($products['package_weight']/1000),2).'</ShippingWeight>';
	 	} else if($seller_id == '1'){
			$dimension .= '<PackageWeight unitOfMeasure="KG">'.round(($products['package_weight']),2).'</PackageWeight>';
	     		$dimension .= '<ShippingWeight unitOfMeasure="KG">'.round(($products['package_weight']),2).'</ShippingWeight>';
	 	}
	     
	 }
	 	  $price_product = ($products['price'] * 5);
		  if($price_product > 0){
		     $price_product = $price_product + 350;
		  }
		  $price_product = round($price_product,2);
		  if($price_product > 0){
		   //$dimension .= '<MSRP currency="INR">'.$mprice.'</MSRP>'; 
		  }
	 
	 $node_id = $this->feedNodeId($category['name']);
	 $node_string = '';
	 if(isset($node_id) && $node_id != 'not yet defined' ){
	   $node_string .= '<RecommendedBrowseNode>'.$node_id.'</RecommendedBrowseNode>';
	 }
        if($type == 'delete'){
	   $operation_type = 'Delete';
	} else{
	   $operation_type = 'Update';	
	}
	$products['amazon_description'] = str_replace('<',"",$products['amazon_description']);
	$product_name = str_replace("&","And",$product_name);
	$string .= '<Message>
		<MessageID>'.$index.'</MessageID>
		<OperationType>'.$operation_type.'</OperationType>
		<Product>
			<SKU>'.$products['model'].'</SKU>
			<StandardProductID>
                <Type>UPC</Type>
                <Value>'.$products['ean'].'</Value>
            </StandardProductID>
			<ProductTaxCode>A_GEN_NOTAX</ProductTaxCode>
			<LaunchDate>'.$timestamp.'</LaunchDate>
			<Condition>
				<ConditionType>New</ConditionType>
			</Condition>
			<DescriptionData>
				<Title>'.substr($product_name,0,200).'</Title>
				<Brand>'.$brand_name.'</Brand> 
				<Description>'.str_replace("&","And",substr($products['amazon_description'],0,2000)).'</Description>';
				$string .= $bullet_points;
				$string .= $dimension;
				$string .= '<Manufacturer>'.$brand_name.'</Manufacturer>
                            <MfrPartNumber>'.$products['model'].'</MfrPartNumber>
				<SearchTerms>'.$brand_name.'</SearchTerms>
				<SearchTerms>'.$products['model'].'</SearchTerms>
				<ItemType>Product</ItemType>
				<TargetAudience>Male</TargetAudience>
				<IsGiftWrapAvailable>false</IsGiftWrapAvailable>
				<IsGiftMessageAvailable>false</IsGiftMessageAvailable>';
				$string .= $node_string;
			$string .= '</DescriptionData>';
                        $feed_cat = $this->feedCategory($products,$category['name']);
			   
                        $string .= $feed_cat;
            $index++;
        }
	$string .= '</AmazonEnvelope>';
        return $string;
        }
	public function feedNodeId($category) {
//	 $query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "product_to_category p2c  WHERE product_id = '" . (int)$product_id . "'");
//	 if ($query->num_rows) {
//		$data = $query->row;
//	 	$query_c = $this->db->query("SELECT c.category_id,cd.name FROM " . DB_PREFIX . "category c left join " . DB_PREFIX . "category_description cd on(c.category_id = cd.category_id)  WHERE c.category_id = '" . (int)$data['category_id'] . "' and language_id = 1");
		//echo "<pre>";print_r($query_c);echo "</pre>";die;
	 //}
	 //$category = $query_c->rows[0]['name'];
	 //echo $category.'----';
	 $node_id = '';
	 switch ($category) {
            case "Sports & Outdoor":
                $node_id = '1984444031';
                break;
	     case "Skateboards":
                $node_id = '1984444031';
                break;
	     case "Wireless Routers":
                $node_id = '1375439031';
                break;
	     case "RC Quadcopters":
                $node_id = '1378484031';
                break;
            case "Health & Beauty":
                $node_id = '1350385031';
                break;
            case "Video & Audio":
                $node_id = '1388878031';
                break;
	     case "Smart Device & Safety":
                $node_id = '1388867031';
                break;
	     case "Cellphone & Accessories":
                $node_id = '1389402031';
                break;
	     case "Car Accessories":
                $node_id = '1389226031';
                break;
	     case "Test Equipment & Tools":
                $node_id = '1388867031';
                break;
	     case "Apparel & Jewelry":
                $node_id = '1951049031';
                break;
	     case "Computer & Stationery":
                $node_id = '976393031';
                break;
	     case "Cellphone & Accessories":
                $node_id = '1389402031';
                break;
	     case "Cameras & Photo Accessories":
                $node_id = '1389402031';
                break;
	     case "Toys & Hobbies":
                $node_id = '1350381031';
                break;
	     case "Home & Garden":
                $node_id = '2454176031';
                break;
	     case "Toys & Hobbies":
                $node_id = '1350381031';
                break;
	     case "Bags":
                $node_id = '2454170031';
                break;
	     case "Lights & Lighting":
                $node_id = '1388867031';
                break;
	     case "Sports & Entertainment":
                $node_id = '1984444031';
                break;
	     case "Phones & Accessories":
                $node_id = '1388867031';
                break;
	     case "Computer & Office":
                $node_id = '976393031';
                break;
	     case "Consumer Electronics":
                $node_id = '1388867031';
                break;
	     case "Tablets & Accessories":
                $node_id = '1388867031';
                break;
	     case "String Lights":
                $node_id = '1388867031';
                break;
	     case "Other Home & Garden Products":
                $node_id = '3704993031';
                break;
	     case "Men's Backpacks":
                $node_id = '2454170031';
                break;
	     case "Phone Cases & Covers":
                $node_id = '1388978031';
                break;
	     case "Kitchen Tools & Gadgets":
                $node_id = '3704993031';
                break;
	     case "Automobiles & Motorcycles":
                $node_id = '1389226031';
                break;
	     case "Sports Toys":
                $node_id = '1350381031';
                break;
            default:
                $node_id = 'not yet defined';
        }
	 //echo $category.'----'.$node_id;
	 return $node_id;
	}
	public function feedCategory($products,$category) {
	     $products['color'] = str_replace('N/A',"",$products['color']); 
	     $products['description1'] = $products['description'];
	     $string = '';
            if($category == 'Beauty & Health' || $category == 'Health & Beauty'){
                $string = '<ProductData>
                        <Beauty>
                        <ProductType>
				<BeautyMisc>
				</BeautyMisc>
                        </ProductType>
                        </Beauty>
                        </ProductData>
		</Product>
	</Message>';
            } else if($category == 'Consumer Electronics' || $category == 'Video & Audio' || $category == 'Smart Device & Safety'){
		if($products['color'] == ''){
		  $products['color'] = 'As On Image';
		}
		$pos1 = strpos($products['description1'], 'Wearing type');
		$length_key = strlen("Wearing type");
		$pos1 = $pos1+$length_key+11;
		$pos2 = strpos($products['description1'], '</div>',$pos1);
		$len = $pos2 - $pos1-1;
		$warenty_type = substr($products['description1'], $pos1, $len);
		$string = '<ProductData>
                        <CE>
                        <ProductType>
				<ConsumerElectronics>
					<Color>'.$products['color'].'</Color>
				</ConsumerElectronics>
                        </ProductType>
			   <MfgWarrantyDescriptionLabor>Provided by Manufacturer</MfgWarrantyDescriptionLabor>
                        </CE>
                        </ProductData>
		</Product>
	</Message>';
	     } else if($category == 'Computer & Office'){
		if($products['color'] == ''){
		  $products['color'] = 'As On Image';
		}
	     	$string = '<ProductData>
                        <CE>
                        <ProductType>
				<ConsumerElectronics>
					<Color>'.$products['color'].'</Color>
				</ConsumerElectronics>
                        </ProductType>
			   <MfgWarrantyDescriptionLabor>Provided by Manufacturer</MfgWarrantyDescriptionLabor>
                        </CE>
                        </ProductData>
		</Product>
	</Message>';
	     } else if($category == 'Automobiles & Motorcycles'){
	     	$string = '<ProductData>
                        <AutoAccessory>
                        <ProductType>
				<AutoPart>
				</AutoPart>
                        </ProductType>
                        </AutoAccessory>
                        </ProductData>
		</Product>
	</Message>';
	     } else if($category == 'Sports & Entertainment' || $category == 'Sports & Outdoor'){
	     	$string = '<ProductData>
                        <Sports>
                        <ProductType>SportingGoods</ProductType>
			   <ItemTypeName>Sports</ItemTypeName>
                        </Sports>
                        </ProductData>
		</Product>
	</Message>';
	     } else if($category == 'Home & Garden' || $category == 'Test Equipment & Tools' || $category == 'Computer & Stationery'){
		if($products['color'] == ''){
		  $products['color'] = 'As On Image';
		}
	     	$string = '<ProductData>
                        <HomeImprovement>
                        <ProductType>
				<Tools>
                                <Color>'.$products['color'].'</Color>
                           </Tools>
                        </ProductType>
                        </HomeImprovement>
                        </ProductData>
		</Product>
	</Message>';
	     } else if($category == 'Watches'){
		$pos1 = strpos($products['description1'], 'Band material');
		$pos = $pos1;
		$length_key = strlen("Band material");
		$pos1 = $pos1+$length_key+11;
		$pos2 = strpos($products['description1'], '</div>',$pos1);
		$len = $pos2 - $pos1-1;
	       if($len >50){
                    $pos2 = strpos($products['description1'], '<br />',$pos1);
                    $len = $pos2 - $pos1-1;
		}
		$band_material = substr($products['description1'], $pos1, $len);
		if(!isset($pos) || $pos == ''){
		 $band_material = 'Leather';
		}
	     	$string = '<ProductData>
                        <Jewelry>
                        <ProductType>
				<Watch>
				<BandColor>'.$products['color'].'</BandColor>
				<BandMaterial>'.$band_material.'</BandMaterial>
				<DialColor>'.$products['color'].'</DialColor>
				<MovementType>Quartz</MovementType>
				<DisplayType>Digital</DisplayType>
				<TargetGender>male</TargetGender>
                            </Watch>
                        </ProductType>
			   <ModelNumber>'.$products['sku'].'</ModelNumber>
                        </Jewelry>
                        </ProductData>
		</Product>
	</Message>';
	     } else if($category == 'Phones & Accessories' || $category == 'Cellphone & Accessories' || $category == 'Cameras & Photo Accessories'){
	     	$string = '<ProductData>
                        <CE>
                        <ProductType>
				<PhoneAccessory>
				</PhoneAccessory>
                        </ProductType>
			   <MfgWarrantyDescriptionLabor>Provided by Manufacturer</MfgWarrantyDescriptionLabor>
                        </CE>
                        </ProductData>
		</Product>
	</Message>';
	     } else if($category == 'Mother & Kids'){
		if($products['color'] == ''){
		  $products['color'] = 'As On Image';
		}
	     	$string = '<ProductData>
                        <Baby>
                        <ProductType>
				<BabyProducts>
					<ColorName>'.$products['color'].'</ColorName>
					
					<MaximumManufacturerAgeRecommended>15</MaximumManufacturerAgeRecommended>
					<MinimumManufacturerAgeRecommended>5</MinimumManufacturerAgeRecommended>				   
				</BabyProducts>
                        </ProductType>
                        </Baby>
                        </ProductData>
		</Product>
	</Message>';
	     } else if($category == 'Toys & Hobbies'){
		if($products['color'] == ''){
		  $products['color'] = 'As On Image';
		}
	     	$string = '<ProductData>
                        <Toys>
                        <ProductType>
				<ToysAndGames>
				<Color>'.$products['color'].'</Color>
				</ToysAndGames>
                        </ProductType>
			   <AgeRecommendation>
          			<MinimumManufacturerAgeRecommended unitOfMeasure="years">15</MinimumManufacturerAgeRecommended>
         		   </AgeRecommendation>
                        </Toys>
                        </ProductData>
		</Product>
	</Message>';
	     } else if($category == 'Bags'){
		if($products['color'] == ''){
		  $products['color'] = 'As On Image';
		}
	     	$string = '<ProductData>
                        <Clothing>
                        <ClassificationData>
				<Size>Small</Size>
				<Color>'.$products['color'].'</Color>
				<ClothingType>Bag</ClothingType>
				<Department>womens</Department>
				<ColorMap>'.$products['color'].'</ColorMap>
				<SizeMap>Small</SizeMap>
                        </ClassificationData>
                        </Clothing>
                        </ProductData>
		</Product>
	</Message>';
		} else if($category == 'Entertainment'){
	     	$string = '<ProductData>
                        <Sports>
                        <ProductType>SportingGoods</ProductType>
			   <ItemTypeName>Sports</ItemTypeName>
                        </Sports>
                        </ProductData>
		</Product>
	</Message>';
	  } else if($category == 'Lights & Lighting'){
	     	$string = '<ProductData>
                        <Lighting>
                        <ProductType>
				<LightingAccessories>
				</LightingAccessories>
                        </ProductType>
                        </Lighting>
                        </ProductData>
		</Product>
	</Message>';
	  } else if($category == 'Shoes'){
		$material = '';
		if(isset($products['material'])&& $products['material'] !=''){
		    $material .= '<Material>'.$products['material'].'</Material>';
		}
		if($products['color'] == ''){
		  $products['color'] = 'As On Image';
		}
	     	$string = '<ProductData>
                        <Clothing>
                        <ClassificationData>
				<Size>Small</Size>
				<Color>'.$products['color'].'</Color>
				<ClothingType>Shoes</ClothingType>';
				$string .= $material;
                        $string .= '</ClassificationData>
                        </Clothing>
                        </ProductData>
		</Product>
	</Message>';
	  } else if($category == 'Jewelry' || $category == 'Apparel & Jewelry'){
		if($products['color'] == ''){
		  $products['color'] = 'As On Image';
		}
		$material = '';
		if(isset($products['material'])&& $products['material'] !=''){
		    $products['material'] = substr($products['material'],0,50);
		} else{
		    $products['material'] = 'As Described';
		}
	     	$string = '<ProductData>
                        <Jewelry>
                        <ProductType>
				<FineNecklaceBraceletAnklet>
				<Material>'.$products['material'].'</Material>
				<DepartmentName>womens</DepartmentName>
				<ItemShape>circle</ItemShape> 
                            </FineNecklaceBraceletAnklet>
                        </ProductType>
			   <Color>'.$products['color'].'</Color>
			   <ModelNumber>'.$products['sku'].'</ModelNumber>
                        </Jewelry>
                        </ProductData>
		</Product>
	</Message>';
	  } else if($category == 'Automobiles & Motorcycles' || $category == 'Car Accessories'){
	     	$string = '<ProductData>
                        <AutoAccessory>
                        <ProductType>
				<AutoPart>
				</AutoPart>
                        </ProductType>
                        </AutoAccessory>
                        </ProductData>
		</Product>
	</Message>';
	  } else if($category == "Original Design-Women's Clothing"){
		if($products['color'] == ''){
		  $products['color'] = 'As On Image';
		}
	     	$string = '<ProductData>
                        <Clothing>
                        <ClassificationData>
				<Size>S</Size>
				<Color>'.$products['color'].'</Color>
				 <ClothingType>Accessory</ClothingType>
				 <Department>womens</Department>
				 <ColorMap>'.$products['color'].'</ColorMap>
                        </ClassificationData>
                        </Clothing>
                        </ProductData>
		</Product>
	</Message>';
	  } else if($category == "Men's Clothing"){
		if($products['color'] == ''){
		  $products['color'] = 'As On Image';
		}
	     	$string = '<ProductData>
                        <Clothing>
                        <ClassificationData>
				<Size>S</Size>
				<Color>'.$products['color'].'</Color>
				 <ClothingType>Accessory</ClothingType>
				 <Department>mens</Department>
				 <ColorMap>'.$products['color'].'</ColorMap>
                        </ClassificationData>
                        </Clothing>
                        </ProductData>
		</Product>
	</Message>';
	  } else if($category == "Women's Clothing"){
		if($products['color'] == ''){
		  $products['color'] = 'As On Image';
		}
	     	$string = '<ProductData>
                        <Clothing>
                        <ClassificationData>
				<Size>S</Size>
				<Color>'.$products['color'].'</Color>
				 <ClothingType>Accessory</ClothingType>
				 <Department>womens</Department>
				 <ColorMap>'.$products['color'].'</ColorMap>
                        </ClassificationData>
                        </Clothing>
                        </ProductData>
		</Product>
	</Message>';
	  } else if($category == 'Tablets & Accessories'){
	     	$string = '<ProductData>
                        <CE>
                        <ProductType>
				<PhoneAccessory>
				</PhoneAccessory>
                        </ProductType>
			   <MfgWarrantyDescriptionLabor>Provided by Manufacturer</MfgWarrantyDescriptionLabor>
                        </CE>
                        </ProductData>
		</Product>
	</Message>';
	     } else{
		echo $category.'---'.$products['product_id'].'---';
         }
        return  $string;   
        }
	 public function insertFeedId($array,$type,$product_array = array(),$customer_id) {
	     //echo "<pre>";print_r($array);echo "</pre>";die;
            $this->db->query("INSERT INTO " . DB_PREFIX . "all_amazon_feeds SET feed_id = '" . $this->db->escape($array['SubmitFeedResult']['FeedSubmissionInfo']['FeedSubmissionId']) . "', type = '" . $this->db->escape($type) . "', status = 'submited',customer_id = '".$customer_id."'");
            if($type == 'product'){
                foreach($product_array as $product){
                    $this->db->query("UPDATE " . DB_PREFIX . "amazon_product_listing SET amazon_feed_id = '" . $this->db->escape($array['SubmitFeedResult']['FeedSubmissionInfo']['FeedSubmissionId']) . "', amazon_status = 'submited', listing_status_id = '0' where product_id = ".$product['product_id']." and customer_id = ".$customer_id);
                }
            
            } else if($type == 'product_delete'){
                foreach($product_array as $product){
                    $this->db->query("UPDATE " . DB_PREFIX . "amazon_product_listing SET amazon_feed_id = '" . $this->db->escape($array['SubmitFeedResult']['FeedSubmissionInfo']['FeedSubmissionId']) . "', amazon_status = 'submited', listing_status_id = '0' where product_id = ".$product['product_id']." and customer_id = ".$customer_id);
                }
            
            }  else if($type == 'image'){
		  foreach($product_array as $product){ 
                    $this->db->query("UPDATE " . DB_PREFIX . "amazon_product_listing SET amazon_image_feed_id = '" . $this->db->escape($array['SubmitFeedResult']['FeedSubmissionInfo']['FeedSubmissionId']) . "' where product_id = ".$product['product_id']." and customer_id = ".$customer_id);
		  }
            }
	     else if($type == 'shipment'){
		  foreach($product_array as $product){ 
                    $this->db->query("UPDATE " . DB_PREFIX . "order_tracking SET amazon_feed_id = '" . $this->db->escape($array['SubmitFeedResult']['FeedSubmissionInfo']['FeedSubmissionId']) . "' where order_id = ".$product['order_id']);
		  }
            } else if($type == 'inventory'){
		  foreach($product_array as $product){ 
                    $this->db->query("UPDATE " . DB_PREFIX . "amazon_product_listing SET amazon_inventory_feed_id = '" . $this->db->escape($array['SubmitFeedResult']['FeedSubmissionInfo']['FeedSubmissionId']) . "' where product_id = ".$product['product_id']." and customer_id = ".$customer_id);
		  }
            } else if($type == 'price'){
		  foreach($product_array as $product){ 
                    $this->db->query("UPDATE " . DB_PREFIX . "amazon_product_listing SET amazon_price_feed_id = '" . $this->db->escape($array['SubmitFeedResult']['FeedSubmissionInfo']['FeedSubmissionId']) . "' where product_id = ".$product['product_id']." and customer_id = ".$customer_id);
		  }
            }
        } 
	 public function getFeedDetails($customer_id) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "all_amazon_feeds  WHERE status = 'submited' and customer_id = ".$customer_id);
            		if ($query->num_rows) {
                		return $query->rows;
            		}
            
            
        }
        public function getFeedStatus($customer_id,$type) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "all_amazon_feeds  WHERE status = 'submited' and type = '".$type."' and customer_id = ".$customer_id);
                if ($query->num_rows) {
                        return false;
                } else{
                    return true;
                }
            
            
        }
	 public function updateFeed($response,$feed_id,$type,$customer_id) {
	     ini_set('max_execution_time', 300000);
            ini_set('memory_limit', '1024M');
	     require_once DIR_SYSTEM.'library/PHPExcel/Classes/PHPExcel.php';
            if($type == 'product'){
                if(!isset($response['Error'])){
                    $this->db->query("UPDATE " . DB_PREFIX . "amazon_product_listing SET amazon_status = 'listed', amazon_listed_date = now() where amazon_feed_id = ".$feed_id); 
                    $this->db->query("UPDATE " . DB_PREFIX . "all_amazon_feeds SET status = 'completed' where feed_id = ".$feed_id);
		      if(isset($response['Message']['ProcessingReport']['ProcessingSummary'])){
			  $this->db->query("UPDATE " . DB_PREFIX . "all_amazon_feeds SET submited = '".$response['Message']['ProcessingReport']['ProcessingSummary']['MessagesProcessed']."',processed = '".$response['Message']['ProcessingReport']['ProcessingSummary']['MessagesSuccessful']."',warnings = '".$response['Message']['ProcessingReport']['ProcessingSummary']['MessagesWithWarning']."', error = '".$response['Message']['ProcessingReport']['ProcessingSummary']['MessagesWithError']."' where feed_id = '".$feed_id."'");
		      }  
                    if(isset($response['Message']['ProcessingReport']['Result'])){
                            $objPHPExcel = new PHPExcel();
                            $objPHPExcel->getProperties()->setCreator("fd");
                            $objPHPExcel->getProperties()->setLastModifiedBy("dsf");
                            $objPHPExcel->getProperties()->setTitle("fds");
                            $objPHPExcel->getProperties()->setSubject("fds");
                            $objPHPExcel->getProperties()->setDescription("dfs");

                            // Add some data
                            $objPHPExcel->setActiveSheetIndex(0);

                            $objPHPExcel->getActiveSheet()->SetCellValue("A1","SKU"); 
                            $objPHPExcel->getActiveSheet()->SetCellValue("B1","Error");
                            
			   $is_asso = $this->isAssoc($response['Message']['ProcessingReport']['Result']);
		          if($is_asso){
				$result = $response['Message']['ProcessingReport']['Result'];
			       if($result['ResultCode'] == 'Error'){
                                $objPHPExcel->getActiveSheet()->SetCellValue("A2","{$result['AdditionalInfo']['SKU']}");
                                $objPHPExcel->getActiveSheet()->SetCellValue("B2","{$result['ResultDescription']}");
                                $this->db->query("UPDATE " . DB_PREFIX . "amazon_product_listing SET amazon_error = CONCAT(amazon_error,'".$this->db->escape($result['ResultDescription'])."'), amazon_status = 'error' where model = '".$result['AdditionalInfo']['SKU']."' and customer_id = ".$customer_id);
                            }
			   } else {
                           $i=1;    
                           $SrNo=1;
                        foreach($response['Message']['ProcessingReport']['Result'] as $result){ 
                            if($result['ResultCode'] == 'Error'){
                                $objPHPExcel->getActiveSheet()->SetCellValue("A$i","{$result['AdditionalInfo']['SKU']}");
                                $objPHPExcel->getActiveSheet()->SetCellValue("B$i","{$result['ResultDescription']}");
                                $i++;
                                $this->db->query("UPDATE " . DB_PREFIX . "amazon_product_listing SET amazon_error = CONCAT(amazon_error,'".$this->db->escape($result['ResultDescription'])."'), amazon_status = 'error' where model = '".$result['AdditionalInfo']['SKU']."' and customer_id = ".$customer_id);
                            }
                        }
			  }
                          $objPHPExcel->getActiveSheet()->setTitle('Simple');

                        $objPHPExcel->setActiveSheetIndex(0);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(45);
                        $objPHPExcel->getActiveSheet()->getStyle('A1:B1')->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'FFFF00'))));
                        $styleArray = array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN
                                )
                            )
                        );
                        $objPHPExcel->getDefaultStyle()->applyFromArray($styleArray);
                        $filename = strtotime("now").'error_product.xls';
                        $filepath = DIR_IMAGE.'export_export/'.$filename;
                        $temp_filepath = 'export_export/'.$filename;
                        $this->insertMonitorStatus($temp_filepath,$customer_id,$feed_id);
                        //$this->model_account_export_all->insertUpcExportMonitor($temp_filepath,$this->customer->getId());
                        header('Content-Type: application/vnd.ms-excel');
                        header('Content-Disposition: attachment;filename="'.$filename.'"');
                        header('Cache-Control: max-age=0');
                        header('Cache-Control: max-age=1');

                        // If you're serving to IE over SSL, then the following may be needed
                        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
                        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                        header ('Pragma: public'); // HTTP/1.0

                        ob_clean();
                        ob_flush();

                        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                        $objWriter->save($filepath);

                    }
                }
            } else if($type == 'product_delete'){
                if(!isset($response['Error'])){
                    $this->db->query("UPDATE " . DB_PREFIX . "amazon_product_listing SET amazon_status = 'deleted', amazon_listed_date = now() where amazon_feed_id = ".$feed_id); 
                    $this->db->query("UPDATE " . DB_PREFIX . "all_amazon_feeds SET status = 'completed' where feed_id = ".$feed_id);
		      if(isset($response['Message']['ProcessingReport']['ProcessingSummary'])){
			  $this->db->query("UPDATE " . DB_PREFIX . "all_amazon_feeds SET submited = '".$response['Message']['ProcessingReport']['ProcessingSummary']['MessagesProcessed']."',processed = '".$response['Message']['ProcessingReport']['ProcessingSummary']['MessagesSuccessful']."',warnings = '".$response['Message']['ProcessingReport']['ProcessingSummary']['MessagesWithWarning']."', error = '".$response['Message']['ProcessingReport']['ProcessingSummary']['MessagesWithError']."' where feed_id = '".$feed_id."'");
		      }  
                    if(isset($response['Message']['ProcessingReport']['Result'])){
                            $objPHPExcel = new PHPExcel();
                            $objPHPExcel->getProperties()->setCreator("fd");
                            $objPHPExcel->getProperties()->setLastModifiedBy("dsf");
                            $objPHPExcel->getProperties()->setTitle("fds");
                            $objPHPExcel->getProperties()->setSubject("fds");
                            $objPHPExcel->getProperties()->setDescription("dfs");

                            // Add some data
                            $objPHPExcel->setActiveSheetIndex(0);

                            $objPHPExcel->getActiveSheet()->SetCellValue("A1","SKU"); 
                            $objPHPExcel->getActiveSheet()->SetCellValue("B1","Error");
                            
			   $is_asso = $this->isAssoc($response['Message']['ProcessingReport']['Result']);
		          if($is_asso){
				$result = $response['Message']['ProcessingReport']['Result'];
			       if($result['ResultCode'] == 'Error'){
                                $objPHPExcel->getActiveSheet()->SetCellValue("A2","{$result['AdditionalInfo']['SKU']}");
                                $objPHPExcel->getActiveSheet()->SetCellValue("B2","{$result['ResultDescription']}");
                                $this->db->query("UPDATE " . DB_PREFIX . "amazon_product_listing SET amazon_error = CONCAT(amazon_error,'".$this->db->escape($result['ResultDescription'])."'), amazon_status = 'error' where model = '".$result['AdditionalInfo']['SKU']."' and customer_id = ".$customer_id);
                            }
			   } else {
                           $i=1;    
                           $SrNo=1;
                        foreach($response['Message']['ProcessingReport']['Result'] as $result){ 
                            if($result['ResultCode'] == 'Error'){
                                $objPHPExcel->getActiveSheet()->SetCellValue("A$i","{$result['AdditionalInfo']['SKU']}");
                                $objPHPExcel->getActiveSheet()->SetCellValue("B$i","{$result['ResultDescription']}");
                                $i++;
                                $this->db->query("UPDATE " . DB_PREFIX . "amazon_product_listing SET amazon_error = CONCAT(amazon_error,'".$this->db->escape($result['ResultDescription'])."'), amazon_status = 'error' where model = '".$result['AdditionalInfo']['SKU']."' and customer_id = ".$customer_id);
                            }
                        }
			  }
                          $objPHPExcel->getActiveSheet()->setTitle('Simple');

                        $objPHPExcel->setActiveSheetIndex(0);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(45);
                        $objPHPExcel->getActiveSheet()->getStyle('A1:B1')->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'FFFF00'))));
                        $styleArray = array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN
                                )
                            )
                        );
                        $objPHPExcel->getDefaultStyle()->applyFromArray($styleArray);
                        $filename = strtotime("now").'error_delete_product.xls';
                        $filepath = DIR_IMAGE.'export_export/'.$filename;
                        $temp_filepath = 'export_export/'.$filename;
                        $this->insertMonitorStatus($temp_filepath,$customer_id,$feed_id);
                        //$this->model_account_export_all->insertUpcExportMonitor($temp_filepath,$this->customer->getId());
                        header('Content-Type: application/vnd.ms-excel');
                        header('Content-Disposition: attachment;filename="'.$filename.'"');
                        header('Cache-Control: max-age=0');
                        header('Cache-Control: max-age=1');

                        // If you're serving to IE over SSL, then the following may be needed
                        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
                        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                        header ('Pragma: public'); // HTTP/1.0

                        ob_clean();
                        ob_flush();

                        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                        $objWriter->save($filepath);

                    }
                }
            } else if($type == 'image'){
                if(!isset($response['Error'])){
                    $this->db->query("UPDATE " . DB_PREFIX . "amazon_product_listing SET amazon_image_update = '1' where amazon_image_feed_id = ".$feed_id);
                    $this->db->query("UPDATE " . DB_PREFIX . "all_amazon_feeds SET status = 'completed' where feed_id = ".$feed_id);
		      if(isset($response['Message']['ProcessingReport']['ProcessingSummary'])){
			  $this->db->query("UPDATE " . DB_PREFIX . "all_amazon_feeds SET submited = '".$response['Message']['ProcessingReport']['ProcessingSummary']['MessagesProcessed']."',processed = '".$response['Message']['ProcessingReport']['ProcessingSummary']['MessagesSuccessful']."',warnings = '".$response['Message']['ProcessingReport']['ProcessingSummary']['MessagesWithWarning']."', error = '".$response['Message']['ProcessingReport']['ProcessingSummary']['MessagesWithError']."' where feed_id = '".$feed_id."'");
		      }
                      if(isset($response['Message']['ProcessingReport']['Result'])){
                            $objPHPExcel = new PHPExcel();
                            $objPHPExcel->getProperties()->setCreator("fd");
                            $objPHPExcel->getProperties()->setLastModifiedBy("dsf");
                            $objPHPExcel->getProperties()->setTitle("fds");
                            $objPHPExcel->getProperties()->setSubject("fds");
                            $objPHPExcel->getProperties()->setDescription("dfs");

                            // Add some data
                            $objPHPExcel->setActiveSheetIndex(0);

                            $objPHPExcel->getActiveSheet()->SetCellValue("A1","SKU"); 
                            $objPHPExcel->getActiveSheet()->SetCellValue("B1","Error");
			   $is_asso = $this->isAssoc($response['Message']['ProcessingReport']['Result']);
		          if($is_asso){
				$result = $response['Message']['ProcessingReport']['Result'];
			       if($result['ResultCode'] == 'Error'){
                                $objPHPExcel->getActiveSheet()->SetCellValue("A2","{$result['AdditionalInfo']['SKU']}");
                                $objPHPExcel->getActiveSheet()->SetCellValue("B2","{$result['ResultDescription']}");
                            }
			   } else {
                           $i=1;    
                           $SrNo=1;
                        foreach($response['Message']['ProcessingReport']['Result'] as $result){ 
                            if($result['ResultCode'] == 'Error'){
                                $objPHPExcel->getActiveSheet()->SetCellValue("A$i","{$result['AdditionalInfo']['SKU']}");
                                $objPHPExcel->getActiveSheet()->SetCellValue("B$i","{$result['ResultDescription']}");
                                $i++;
                            }
                        }
			  }
                          $objPHPExcel->getActiveSheet()->setTitle('Simple');

                        $objPHPExcel->setActiveSheetIndex(0);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(45);
                        $objPHPExcel->getActiveSheet()->getStyle('A1:B1')->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'FFFF00'))));
                        $styleArray = array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN
                                )
                            )
                        );
                        $objPHPExcel->getDefaultStyle()->applyFromArray($styleArray);
                        $filename = strtotime("now").'error_image.xls';
                        $filepath = DIR_IMAGE.'export_export/'.$filename;
                        $temp_filepath = 'export_export/'.$filename;
                        $this->insertMonitorStatus($temp_filepath,$customer_id,$feed_id);
                        //$this->model_account_export_all->insertUpcExportMonitor($temp_filepath,$this->customer->getId());
                        header('Content-Type: application/vnd.ms-excel');
                        header('Content-Disposition: attachment;filename="'.$filename.'"');
                        header('Cache-Control: max-age=0');
                        header('Cache-Control: max-age=1');

                        // If you're serving to IE over SSL, then the following may be needed
                        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
                        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                        header ('Pragma: public'); // HTTP/1.0

                        ob_clean();
                        ob_flush();

                        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                        $objWriter->save($filepath);

                    }
                }
            } else if($type == 'shipment'){
                if(!isset($response['Error'])){
		      $this->db->query("UPDATE " . DB_PREFIX . "all_amazon_feeds SET status = 'completed' where feed_id = ".$feed_id);
                    $this->db->query("UPDATE " . DB_PREFIX . "order_tracking SET tracking_status = '1' where amazon_feed_id = ".$feed_id);
                }
            } else if($type == 'price'){
                if(!isset($response['Error'])){
		      $this->db->query("UPDATE " . DB_PREFIX . "all_amazon_feeds SET status = 'completed' where feed_id = ".$feed_id);
                    $this->db->query("UPDATE " . DB_PREFIX . "amazon_product_listing SET amazon_price_update = '1' where amazon_price_feed_id = ".$feed_id);
		      if(isset($response['Message']['ProcessingReport']['ProcessingSummary'])){
			  $this->db->query("UPDATE " . DB_PREFIX . "all_amazon_feeds SET submited = '".$response['Message']['ProcessingReport']['ProcessingSummary']['MessagesProcessed']."',processed = '".$response['Message']['ProcessingReport']['ProcessingSummary']['MessagesSuccessful']."',warnings = '".$response['Message']['ProcessingReport']['ProcessingSummary']['MessagesWithWarning']."', error = '".$response['Message']['ProcessingReport']['ProcessingSummary']['MessagesWithError']."' where feed_id = '".$feed_id."'");
		      }
                      if(isset($response['Message']['ProcessingReport']['Result'])){
                            $objPHPExcel = new PHPExcel();
                            $objPHPExcel->getProperties()->setCreator("fd");
                            $objPHPExcel->getProperties()->setLastModifiedBy("dsf");
                            $objPHPExcel->getProperties()->setTitle("fds");
                            $objPHPExcel->getProperties()->setSubject("fds");
                            $objPHPExcel->getProperties()->setDescription("dfs");

                            // Add some data
                            $objPHPExcel->setActiveSheetIndex(0);

                            $objPHPExcel->getActiveSheet()->SetCellValue("A1","SKU"); 
                            $objPHPExcel->getActiveSheet()->SetCellValue("B1","Error");
			   $is_asso = $this->isAssoc($response['Message']['ProcessingReport']['Result']);
		          if($is_asso){
				$result = $response['Message']['ProcessingReport']['Result'];
			       if($result['ResultCode'] == 'Error'){
                                $objPHPExcel->getActiveSheet()->SetCellValue("A2","{$result['AdditionalInfo']['SKU']}");
                                $objPHPExcel->getActiveSheet()->SetCellValue("B2","{$result['ResultDescription']}");
                            }
			   } else {
                           $i=1;    
                           $SrNo=1;
                        foreach($response['Message']['ProcessingReport']['Result'] as $result){ 
                            if($result['ResultCode'] == 'Error'){
                                $objPHPExcel->getActiveSheet()->SetCellValue("A$i","{$result['AdditionalInfo']['SKU']}");
                                $objPHPExcel->getActiveSheet()->SetCellValue("B$i","{$result['ResultDescription']}");
                                $i++;
                            }
                        }
			  }
                          $objPHPExcel->getActiveSheet()->setTitle('Simple');

                        $objPHPExcel->setActiveSheetIndex(0);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(45);
                        $objPHPExcel->getActiveSheet()->getStyle('A1:B1')->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'FFFF00'))));
                        $styleArray = array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN
                                )
                            )
                        );
                        $objPHPExcel->getDefaultStyle()->applyFromArray($styleArray);
                        $filename = strtotime("now").'error_price.xls';
                        $filepath = DIR_IMAGE.'export_export/'.$filename;
                        $temp_filepath = 'export_export/'.$filename;
                        $this->insertMonitorStatus($temp_filepath,$customer_id,$feed_id);
                        //$this->model_account_export_all->insertUpcExportMonitor($temp_filepath,$this->customer->getId());
                        header('Content-Type: application/vnd.ms-excel');
                        header('Content-Disposition: attachment;filename="'.$filename.'"');
                        header('Cache-Control: max-age=0');
                        header('Cache-Control: max-age=1');

                        // If you're serving to IE over SSL, then the following may be needed
                        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
                        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                        header ('Pragma: public'); // HTTP/1.0

                        ob_clean();
                        ob_flush();

                        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                        $objWriter->save($filepath);

                    }
                }
            } else if($type == 'inventory'){
                if(!isset($response['Error'])){
		      $this->db->query("UPDATE " . DB_PREFIX . "all_amazon_feeds SET status = 'completed' where feed_id = ".$feed_id);
                    $this->db->query("UPDATE " . DB_PREFIX . "amazon_product_listing SET inv_status = '0' where amazon_inventory_feed_id = ".$feed_id);
		      if(isset($response['Message']['ProcessingReport']['ProcessingSummary'])){
			  $this->db->query("UPDATE " . DB_PREFIX . "all_amazon_feeds SET submited = '".$response['Message']['ProcessingReport']['ProcessingSummary']['MessagesProcessed']."',processed = '".$response['Message']['ProcessingReport']['ProcessingSummary']['MessagesSuccessful']."',warnings = '".$response['Message']['ProcessingReport']['ProcessingSummary']['MessagesWithWarning']."', error = '".$response['Message']['ProcessingReport']['ProcessingSummary']['MessagesWithError']."' where feed_id = '".$feed_id."'");
		      }
                }
            }
        }
	 public function insertOrder($orders,$seller_id,$customer_id) {
	     
	     if(isset($orders['ListOrdersResult']['Orders'])){
                $is_ass = $this->isAssoc($orders['ListOrdersResult']['Orders']);
                if(!$is_ass){
	     	foreach($orders['ListOrdersResult']['Orders'] as $order_array){
                        if($order_array['FulfillmentChannel'] == 'MFN'){
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order WHERE amazon_order_id = '".$order_array['AmazonOrderId']."'");
	              
            		if ($query->num_rows == 0) {
			  	$query_temp = $this->db->query("SELECT temp_id FROM " . DB_PREFIX . "order ORDER by order_id desc limit 1");
                            $temp_id = $query_temp->rows[0]['temp_id'];
                            $id = substr($temp_id,8);
                            $str_t = date("Ym");
                            $f_id = 'TK'.$str_t.(str_pad($id+1, 5, '0', STR_PAD_LEFT));
		  		$query_country = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE iso_code_2 = '".$order_array['ShippingAddress']['CountryCode']."'");
		  		if ($query_country->num_rows > 0) {
		    	 		$country_id = $query_country->rows[0]['country_id'];
		  		}
	         		$query_zone = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE code = '".$order_array['ShippingAddress']['StateOrRegion']."' and country_id = '".$country_id."'");
		  		if ($query_zone->num_rows > 0) {
		     			$zone_id = $query_zone->rows[0]['zone_id'];
		  		} else{
				  $query_zone = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE name = '".$order_array['ShippingAddress']['StateOrRegion']."' and country_id = '".$country_id."'");
				  if ($query_zone->num_rows > 0) {
				  	$zone_id = $query_zone->rows[0]['zone_id'];
				  } else {
				  	$zone_id = 0;
				  }	
				}
				if(!isset($order_array['ShippingAddress']['AddressLine2']) || $order_array['ShippingAddress']['AddressLine2'] == ''){
                                    $order_array['ShippingAddress']['AddressLine2'] = '';
                                }
                		$this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET temp_id = '".$f_id."', customer_id = '".$customer_id."', store_id = '0', amazon_seller_id = '".$seller_id."' store_name = 'Amazon', store_url = 'https://amazon.in',amazon_order_id = '".$order_array['AmazonOrderId']."', customer_group_id = '1', firstname = '" . $this->db->escape($order_array['BuyerName']) . "', lastname = '', email = '" . $this->db->escape($order_array['BuyerEmail']) . "', telephone = '" . $this->db->escape($order_array['ShippingAddress']['Phone']) . "', custom_field = '', payment_firstname = '" . $this->db->escape($order_array['BuyerName']) . "', payment_lastname = '', payment_company = '" . $this->db->escape($order_array['ShippingAddress']['CountryCode']) . "', payment_address_1 = '" . $this->db->escape($order_array['ShippingAddress']['AddressLine1']) . "', payment_address_2 = '".$this->db->escape($order_array['ShippingAddress']['AddressLine2'])."', payment_city = '" . $this->db->escape($order_array['ShippingAddress']['City']) . "', payment_postcode = '" . $this->db->escape($order_array['ShippingAddress']['PostalCode']) . "', payment_country = '" . $this->db->escape($order_array['ShippingAddress']['CountryCode']) . "', payment_country_id = '', payment_zone = '', payment_zone_id = '', payment_address_format = '', payment_custom_field = '', payment_method = '" . $this->db->escape($order_array['PaymentMethodDetails']['PaymentMethodDetail']) . "', payment_code = '', shipping_firstname = '" . $this->db->escape($order_array['BuyerName']) . "', shipping_lastname = '', shipping_company = '" . $this->db->escape($order_array['ShippingAddress']['CountryCode']) . "', shipping_address_1 = '" . $this->db->escape($order_array['ShippingAddress']['AddressLine1']) . "', shipping_address_2 = '".$this->db->escape($order_array['ShippingAddress']['AddressLine2'])."', shipping_city = '" . $this->db->escape($order_array['ShippingAddress']['City']) . "', shipping_postcode = '" . $this->db->escape($order_array['ShippingAddress']['PostalCode']) . "', shipping_country = '" . $this->db->escape($order_array['ShippingAddress']['CountryCode']) . "', shipping_country_id = '".$country_id."', shipping_zone = '', shipping_zone_id = '".$zone_id."', shipping_address_format = '', shipping_custom_field = '', shipping_method = '" . $this->db->escape($order_array['ShipmentServiceLevelCategory']) . "', shipping_code = '', comment = '', total = '" . (float)$order_array['OrderTotal']['Amount'] . "',order_status_id = '1', affiliate_id = '', commission = '', marketing_id = '', tracking = '', language_id = '1', currency_id = '4', currency_code = 'INR', currency_value = '1', ip = '', forwarded_ip = '', user_agent = '', accept_language = '', date_added = NOW(), date_modified = NOW()");		
	     		} else if ($order_array['OrderStatus'] == 'Canceled'){
	    			$this->db->query("UPDATE " . DB_PREFIX . "order SET order_status_id = '7' where amazon_order_id = '".$order_array['AmazonOrderId']."'");
	    		}
                    }

	    	}
             } else{
		  $is_ass = $this->isAssoc($orders['ListOrdersResult']['Orders']['Order']);
		  if($is_ass){
			$order_array = $orders['ListOrdersResult']['Orders']['Order'];
			if($order_array['FulfillmentChannel'] == 'MFN'){    
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order WHERE amazon_order_id = '".$order_array['AmazonOrderId']."'");
	              
            		if ($query->num_rows == 0) {
				//echo "<pre>";print_r($order_array);echo "</pre>12345";die;
				$query_temp = $this->db->query("SELECT temp_id FROM " . DB_PREFIX . "order ORDER by order_id desc limit 1");
                            $temp_id = $query_temp->rows[0]['temp_id'];
                            $id = substr($temp_id,8);
                            $str_t = date("Ym");
                            $f_id = 'TK'.$str_t.(str_pad($id+1, 5, '0', STR_PAD_LEFT));
		  		$query_country = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE iso_code_2 = '".$order_array['ShippingAddress']['CountryCode']."'");
		  		if ($query_country->num_rows > 0) {
		    	 		$country_id = $query_country->rows[0]['country_id'];
		  		}
	         		$query_zone = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE code = '".$order_array['ShippingAddress']['StateOrRegion']."' and country_id = '".$country_id."'");
		  		if ($query_zone->num_rows > 0) {
		     			$zone_id = $query_zone->rows[0]['zone_id'];
		  		} else{
				  $query_zone = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE name = '".$order_array['ShippingAddress']['StateOrRegion']."' and country_id = '".$country_id."'");
				  if ($query_zone->num_rows > 0) {
				  	$zone_id = $query_zone->rows[0]['zone_id'];
				  } else {
				  	$zone_id = 0;
				  }	
				}
				if(!isset($order_array['ShippingAddress']['AddressLine2']) || $order_array['ShippingAddress']['AddressLine2'] == ''){
                                    $order_array['ShippingAddress']['AddressLine2'] = '';
                             }
                		$this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET temp_id = '".$f_id."', customer_id = '".$customer_id."', store_id = '0', amazon_seller_id = '".(int)$seller_id."', store_name = 'Amazon', store_url = 'https://amazon.in',amazon_order_id = '".$order_array['AmazonOrderId']."', customer_group_id = '1', firstname = '" . $this->db->escape($order_array['BuyerName']) . "', lastname = '', email = '" . $this->db->escape($order_array['BuyerEmail']) . "', telephone = '" . $this->db->escape($order_array['ShippingAddress']['Phone']) . "', custom_field = '', payment_firstname = '" . $this->db->escape($order_array['BuyerName']) . "', payment_lastname = '', payment_company = '" . $this->db->escape($order_array['ShippingAddress']['CountryCode']) . "', payment_address_1 = '" . $this->db->escape($order_array['ShippingAddress']['AddressLine1']) . "', payment_address_2 = '".$this->db->escape($order_array['ShippingAddress']['AddressLine2'])."', payment_city = '" . $this->db->escape($order_array['ShippingAddress']['City']) . "', payment_postcode = '" . $this->db->escape($order_array['ShippingAddress']['PostalCode']) . "', payment_country = '" . $this->db->escape($order_array['ShippingAddress']['CountryCode']) . "', payment_country_id = '', payment_zone = '', payment_zone_id = '', payment_address_format = '', payment_custom_field = '', payment_method = '" . $this->db->escape($order_array['PaymentMethodDetails']['PaymentMethodDetail']) . "', payment_code = '', shipping_firstname = '" . $this->db->escape($order_array['BuyerName']) . "', shipping_lastname = '', shipping_company = '" . $this->db->escape($order_array['ShippingAddress']['CountryCode']) . "', shipping_address_1 = '" . $this->db->escape($order_array['ShippingAddress']['AddressLine1']) . "', shipping_address_2 = '".$this->db->escape($order_array['ShippingAddress']['AddressLine2'])."', shipping_city = '" . $this->db->escape($order_array['ShippingAddress']['City']) . "', shipping_postcode = '" . $this->db->escape($order_array['ShippingAddress']['PostalCode']) . "', shipping_country = '" . $this->db->escape($order_array['ShippingAddress']['CountryCode']) . "', shipping_country_id = '".$country_id."', shipping_zone = '', shipping_zone_id = '".$zone_id."', shipping_address_format = '', shipping_custom_field = '', shipping_method = '" . $this->db->escape($order_array['ShipmentServiceLevelCategory']) . "', shipping_code = '', comment = '', total = '" . (float)$order_array['OrderTotal']['Amount'] . "',order_status_id = '1', affiliate_id = '', commission = '', marketing_id = '', tracking = '', language_id = '1', currency_id = '4', currency_code = 'INR', currency_value = '1', ip = '', forwarded_ip = '', user_agent = '', accept_language = '', date_added = NOW(), date_modified = NOW()");		
	     		} else if ($order_array['OrderStatus'] == 'Canceled'){
	    			$this->db->query("UPDATE " . DB_PREFIX . "order SET order_status_id = '7' where amazon_order_id = '".$order_array['AmazonOrderId']."'");
	    		}
                    }
		  } else {
                foreach($orders['ListOrdersResult']['Orders']['Order'] as $order_array){
                        if($order_array['FulfillmentChannel'] == 'MFN'){    
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order WHERE amazon_order_id = '".$order_array['AmazonOrderId']."'");
	              
            		if ($query->num_rows == 0) {
				//echo "<pre>";print_r($order_array);echo "</pre>12345";die;
				$query_temp = $this->db->query("SELECT temp_id FROM " . DB_PREFIX . "order ORDER by order_id desc limit 1");
                            $temp_id = $query_temp->rows[0]['temp_id'];
                            $id = substr($temp_id,8);
                            $str_t = date("Ym");
                            $f_id = 'TK'.$str_t.(str_pad($id+1, 5, '0', STR_PAD_LEFT));
		  		$query_country = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE iso_code_2 = '".$order_array['ShippingAddress']['CountryCode']."'");
		  		if ($query_country->num_rows > 0) {
		    	 		$country_id = $query_country->rows[0]['country_id'];
		  		}
	         		$query_zone = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE code = '".$order_array['ShippingAddress']['StateOrRegion']."' and country_id = '".$country_id."'");
		  		if ($query_zone->num_rows > 0) {
		     			$zone_id = $query_zone->rows[0]['zone_id'];
		  		} else{
				  $query_zone = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE name = '".$order_array['ShippingAddress']['StateOrRegion']."' and country_id = '".$country_id."'");
				  if ($query_zone->num_rows > 0) {
				  	$zone_id = $query_zone->rows[0]['zone_id'];
				  } else {
				  	$zone_id = 0;
				  }	
				}
				if(!isset($order_array['ShippingAddress']['AddressLine2']) || $order_array['ShippingAddress']['AddressLine2'] == ''){
                                    $order_array['ShippingAddress']['AddressLine2'] = '';
                             }
                		$this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET temp_id = '".$f_id."', customer_id = '".$customer_id."', store_id = '0', amazon_seller_id = '".(int)$seller_id."', store_name = 'Amazon', store_url = 'https://amazon.in',amazon_order_id = '".$order_array['AmazonOrderId']."', customer_group_id = '1', firstname = '" . $this->db->escape($order_array['BuyerName']) . "', lastname = '', email = '" . $this->db->escape($order_array['BuyerEmail']) . "', telephone = '" . $this->db->escape($order_array['ShippingAddress']['Phone']) . "', custom_field = '', payment_firstname = '" . $this->db->escape($order_array['BuyerName']) . "', payment_lastname = '', payment_company = '" . $this->db->escape($order_array['ShippingAddress']['CountryCode']) . "', payment_address_1 = '" . $this->db->escape($order_array['ShippingAddress']['AddressLine1']) . "', payment_address_2 = '".$this->db->escape($order_array['ShippingAddress']['AddressLine2'])."', payment_city = '" . $this->db->escape($order_array['ShippingAddress']['City']) . "', payment_postcode = '" . $this->db->escape($order_array['ShippingAddress']['PostalCode']) . "', payment_country = '" . $this->db->escape($order_array['ShippingAddress']['CountryCode']) . "', payment_country_id = '', payment_zone = '', payment_zone_id = '', payment_address_format = '', payment_custom_field = '', payment_method = '" . $this->db->escape($order_array['PaymentMethodDetails']['PaymentMethodDetail']) . "', payment_code = '', shipping_firstname = '" . $this->db->escape($order_array['BuyerName']) . "', shipping_lastname = '', shipping_company = '" . $this->db->escape($order_array['ShippingAddress']['CountryCode']) . "', shipping_address_1 = '" . $this->db->escape($order_array['ShippingAddress']['AddressLine1']) . "', shipping_address_2 = '".$this->db->escape($order_array['ShippingAddress']['AddressLine2'])."', shipping_city = '" . $this->db->escape($order_array['ShippingAddress']['City']) . "', shipping_postcode = '" . $this->db->escape($order_array['ShippingAddress']['PostalCode']) . "', shipping_country = '" . $this->db->escape($order_array['ShippingAddress']['CountryCode']) . "', shipping_country_id = '".$country_id."', shipping_zone = '', shipping_zone_id = '".$zone_id."', shipping_address_format = '', shipping_custom_field = '', shipping_method = '" . $this->db->escape($order_array['ShipmentServiceLevelCategory']) . "', shipping_code = '', comment = '', total = '" . (float)$order_array['OrderTotal']['Amount'] . "',order_status_id = '1', affiliate_id = '', commission = '', marketing_id = '', tracking = '', language_id = '1', currency_id = '4', currency_code = 'INR', currency_value = '1', ip = '', forwarded_ip = '', user_agent = '', accept_language = '', date_added = NOW(), date_modified = NOW()");		
	     		} else if ($order_array['OrderStatus'] == 'Canceled'){
	    			$this->db->query("UPDATE " . DB_PREFIX . "order SET order_status_id = '7' where amazon_order_id = '".$order_array['AmazonOrderId']."'");
	    		}
                    }

	    	} 
		}
             }
           }
	}
	public function getAmazonOrder($customer_id) {
            $query = $this->db->query("SELECT amazon_order_id FROM " . DB_PREFIX . "order WHERE order_status_id = '1' and store_name = 'Amazon' and customer_id = '".$customer_id."'");
	
            if ($query->num_rows) {
                return $query->rows;
            }
        }
	 public function insertOrderItems($array, $amazon_order_id) {
		  //echo "<pre>";print_r($array);echo "</pre>12345";die;
                $product = $array['ListOrderItemsResult']['OrderItems']['OrderItem'];
                $ass = $this->isAssoc($product);
                if($ass) {
                    $query = $this->db->query("SELECT order_id,shipping_method FROM " . DB_PREFIX . "order WHERE amazon_order_id = '".$amazon_order_id."'");
                      if ($query->num_rows) {
                       $order_total = $this->getOrderTotal($product,$query->rows[0]['shipping_method']);
                           $order_id = $query->rows[0]['order_id'];
                           $query_product = $this->db->query("SELECT product_id,model FROM " . DB_PREFIX . "product WHERE model = '".$product['SellerSKU']."'");
                           $product_id = $query_product->rows[0]['product_id'];
                           $model = $query_product->rows[0]['model'];
                           $total = $product['ProductInfo']['NumberOfItems']*$product['ItemPrice']['Amount'];
                           $query_product = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE OrderItemId = '".$product['OrderItemId']."'");
                          if ($query_product->num_rows == 0) {
			      $quantity_ordered = $product['ProductInfo']['NumberOfItems'];
			      if (isset($product['QuantityOrdered']) && $product['QuantityOrdered'] > 0){
				  $quantity_ordered = $product['QuantityOrdered'];
			      }
			      $id_seller = $this->getSellerAccountId($product_id);
			      $this->db->query("UPDATE " . DB_PREFIX . "order set amazon_seller_id = '".$id_seller."' WHERE order_id = '".$order_id."'");
                           $this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int)$order_id . "', OrderItemId = '" . $this->db->escape($product['OrderItemId']) . "', product_id = '" . (int)$product_id . "', name = '" . $this->db->escape($product['Title']) . "', model = '" . $this->db->escape($model) . "', quantity = '" . (int)$quantity_ordered . "', price = '" . (float)$product['ItemPrice']['Amount'] . "', total = '" . (float)$total . "', tax = '" . (float)$product['ItemTax']['Amount'] . "', reward = '0'");
                           $this->db->query("UPDATE " . DB_PREFIX . "product set quantity = quantity - 1,inv_status = '1' WHERE model = '".$product['SellerSKU']."'"); 
                           if (isset($order_total)) {
                               foreach ($order_total['order_total'] as $total) {
                                   $this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', `value` = '" . (float)$total['value'] . "', sort_order = '" . (int)$total['sort_order'] . "'");
                               }
                           }
                         }
                      }
            } else{
                
                    $query = $this->db->query("SELECT order_id,shipping_method FROM " . DB_PREFIX . "order WHERE amazon_order_id = '".$amazon_order_id."'");
                      if ($query->num_rows) {
                           foreach($product as $products){
                                $order_id = $query->rows[0]['order_id'];
                                $query_product = $this->db->query("SELECT product_id,model FROM " . DB_PREFIX . "product WHERE model = '".$products['SellerSKU']."'");
                                $product_id = $query_product->rows[0]['product_id'];
                                $model = $query_product->rows[0]['model'];
                                $total = $products['ProductInfo']['NumberOfItems']*$products['ItemPrice']['Amount'];
                                $query_product = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE OrderItemId = '".$products['OrderItemId']."'");
                               if ($query_product->num_rows == 0) {
	 			    $quantity_ordered = $products['ProductInfo']['NumberOfItems'];
			      		if (isset($products['QuantityOrdered']) && $products['QuantityOrdered'] > 0){
				  		$quantity_ordered = $products['QuantityOrdered'];
			      		}
				    $id_seller = $this->getSellerAccountId($product_id);
			      	    $this->db->query("UPDATE " . DB_PREFIX . "order set amazon_seller_id = '".$id_seller."' WHERE order_id = '".$order_id."'");
                                $this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int)$order_id . "', OrderItemId = '" . $this->db->escape($products['OrderItemId']) . "', product_id = '" . (int)$product_id . "', name = '" . $this->db->escape($products['Title']) . "', model = '" . $this->db->escape($model) . "', quantity = '" . (int)$quantity_ordered . "', price = '" . (float)$products['ItemPrice']['Amount'] . "', total = '" . (float)$total . "', tax = '" . (float)$products['ItemTax']['Amount'] . "', reward = '0'");
                                $this->db->query("UPDATE " . DB_PREFIX . "product set quantity = quantity - 1,inv_status = '1' WHERE model = '".$products['SellerSKU']."'"); 
                               }
                         }
                         $order_total = $this->getOrderTotal($product,$query->rows[0]['shipping_method']);
                         if (isset($order_total)) {
                            foreach ($order_total['order_total'] as $total) {
                                $this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', `value` = '" . (float)$total['value'] . "', sort_order = '" . (int)$total['sort_order'] . "'");
                            }
                        }
                      }
                
                
            }
                        
                
        }
	 public function getOrderTotal($array,$shipping_method) {
            $ass = $this->isAssoc($array);
            if($ass) {
            $sub_total = 0;
            $total = $array['ProductInfo']['NumberOfItems']*$array['ItemPrice']['Amount'];
            $order_total['order_total'][] = array(
			'code' => 'tax',
                        'title' => 'TAX',
                        'value' => $array['ItemTax']['Amount'],
                        'sort_order' => '5'
		);
            $sub_total = $sub_total + $array['ItemTax']['Amount'];
            $order_total['order_total'][] = array(
			'code' => 'shipping',
                        'title' => 'Shipping: '.$shipping_method,
                        'value' => $array['ShippingPrice']['Amount'],
                        'sort_order' => '3'
		);
            $sub_total = $sub_total + $array['ShippingPrice']['Amount'];
            $order_total['order_total'][] = array(
			'code' => 'sub_total',
                        'title' => 'Sub-Total',
                        'value' => $total,
                        'sort_order' => '1'
		);
            $sub_total = $sub_total + $total;
            $order_total['order_total'][] = array(
			'code' => 'total',
                        'title' => 'Total',
                        'value' => $sub_total,
                        'sort_order' => '9'
		);
            } else{
                $sub_total = 0;
                $total = 0;
                $tax_amount = 0;
                $shipping_price = 0;
                foreach($array as $products){
                    $total = $total + ($products['ProductInfo']['NumberOfItems']*$products['ItemPrice']['Amount']);
                    $tax_amount = $tax_amount + $products['ItemTax']['Amount'];
                    $shipping_price = $shipping_price + $products['ShippingPrice']['Amount'];
                    
                }
                $sub_total = $sub_total +$total + $shipping_price + $tax_amount;
                $order_total['order_total'][] = array(
			'code' => 'tax',
                        'title' => 'TAX',
                        'value' => $tax_amount,
                        'sort_order' => '5'
		);
            $order_total['order_total'][] = array(
			'code' => 'shipping',
                        'title' => 'Shipping: '.$shipping_method,
                        'value' => $shipping_price,
                        'sort_order' => '3'
		);
            $order_total['order_total'][] = array(
			'code' => 'sub_total',
                        'title' => 'Sub-Total',
                        'value' => $total,
                        'sort_order' => '1'
		);
            $order_total['order_total'][] = array(
			'code' => 'total',
                        'title' => 'Total',
                        'value' => $sub_total,
                        'sort_order' => '9'
		);
                
            }
            
            return $order_total;
            
        }
	 public function getSellerAccountId($product_id) {
            $query = $this->db->query("SELECT seller_id FROM " . DB_PREFIX . "seller_mapping WHERE product_id = '".$product_id."'");
            return $query->row['seller_id'];
        }
	 function isAssoc(array $arr) {
   	 if (array() === $arr) return false;
    		return array_keys($arr) !== range(0, count($arr) - 1);
	}
	public function getOrderTracking($customer_id) { 
            $query = $this->db->query("SELECT ot.*,o.amazon_order_id,o.shipping_method FROM " . DB_PREFIX . "order_tracking ot, " . DB_PREFIX . "order o WHERE o.order_id = ot.order_id and ot.tracking_status = '0' and o.customer_id = '".(int)$customer_id."'");
            if ($query->num_rows) {
                return $query->rows;
            }
        }
	 public function getImageUpdate() {
            $query = $this->db->query("SELECT product_id,image FROM " . DB_PREFIX . "product WHERE image_moved = '0' limit 100");
            if ($query->num_rows) {
                return $query->rows;
            }
        }
        public function getProductImages($product_id) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id =".$product_id);
            if ($query->num_rows) {
                return $query->rows;
            }
        }
	 public function getSettings() {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "amazon_api_settings");
            if ($query->num_rows) {
                return $query->rows;
            }
        }
	 public function getCustomerSettings() {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "amazon_customer_settings");
            if ($query->num_rows) {
                return $query->rows;
            }
        }
	 public function getProductInventory($customer_id) {
            $query = $this->db->query("SELECT p.product_id,p.model,p.sku,p.quantity,p.amazon_status,p.inv_status FROM " . DB_PREFIX . "product p left join " . DB_PREFIX . "amazon_product_listing sm on(sm.product_id = p.product_id) WHERE sm.customer_id = ".$customer_id." and sm.amazon_status ='listed'");
	     
            if ($query->num_rows) {
                return $query->rows;
            }
        }
	 public function getProductName($name,$category_id,$customer_id) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "amazon_brand_settings WHERE customer_id =".$customer_id);
            if ($query->num_rows) {
                $query_all = $this->db->query("SELECT * FROM " . DB_PREFIX . "amazon_brand_settings WHERE customer_id =".$customer_id." and category_id = '0'");
                if ($query_all->num_rows) {
                    $listing_name = $query_all->row['text'].' '.$name;
                }
                $query_cat = $this->db->query("SELECT * FROM " . DB_PREFIX . "amazon_brand_settings WHERE customer_id =".$customer_id." and category_id = ".$category_id);
                if ($query_cat->num_rows) {
                    $listing_name = $query_cat->row['text'].' '.$name;
                }
            } else{
                $listing_name = $name;
            }
	    
           return $listing_name;
        }
        public function getBrandName($category) {
            if($category == 'Beauty & Health' || $category == 'Health & Beauty'){
                $listing_name = 'Segolike';
             } else if($category == 'Consumer Electronics' || $category == 'Video & Audio' || $category == 'Smart Device & Safety'){
		$listing_name = 'Jutek';
	     } else if($category == 'Computer & Office'){
	     	$listing_name = 'Jutek';
	     } else if($category == 'Automobiles & Motorcycles'){
	     	$listing_name = 'Jutek';
	     } else if($category == 'Sports & Entertainment' || $category == 'Sports & Outdoor'){
	     	$listing_name = 'Jutek';
	     } else if($category == 'Home & Garden' || $category == 'Test Equipment & Tools' || $category == 'Computer & Stationery'){
	     	$listing_name = 'Zenuss';
	     } else if($category == 'Watches'){
		$listing_name = 'Segolike';
	     } else if($category == 'Phones & Accessories' || $category == 'Cellphone & Accessories' || $category == 'Cameras & Photo Accessories'){
	     	$listing_name = 'Jutek';
	     } else if($category == 'Mother & Kids'){
	     	$listing_name = 'Segolike';
	     } else if($category == 'Toys & Hobbies'){
	     	$listing_name = 'Backgammon';
	     } else if($category == 'Bags'){
	     	$listing_name = 'Baggallini';
             } else if($category == 'Entertainment'){
	     	$listing_name = 'Jutek';
             } else if($category == 'Lights & Lighting'){
	     	$listing_name = 'Jutek';
	     } else if($category == 'Shoes'){
	     	$listing_name = 'Baggallini';
	     } else if($category == 'Jewelry' || $category == 'Apparel & Jewelry'){
	     	$listing_name = 'Segolike';
	     } else if($category == 'Automobiles & Motorcycles' || $category == 'Car Accessories'){
	     	$listing_name = 'Jutek';
             } else if($category == "Original Design-Women's Clothing"){
	     	$listing_name = 'Baggallini';
	     } else if($category == "Men's Clothing"){
	     	$listing_name = 'Baggallini';
             } else if($category == "Women's Clothing"){
	     	$listing_name = 'Baggallini';
	     } else if($category == "Tablets & Accessories"){
	     	$listing_name = 'Jutek';
	     } else{
		$listing_name = 'not_yet';
             }
	    //echo $listing_name.'---'.$category;
	    return $listing_name;
        }
	 public function getmaxPrice($product_id,$price,$customer_id) {
            $category = $this->getProductCategory($product_id);
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "amazon_price_settings WHERE customer_id =".$customer_id);
            if ($query->num_rows) {
                $query_all = $this->db->query("SELECT * FROM " . DB_PREFIX . "amazon_price_settings WHERE customer_id =".$customer_id." and category_id = '0'");
                if ($query_all->num_rows) {
                    $price = $price + ($price * ($query_all->row['sp']/100));
                    $price = $price * $query_all->row['mrp'];
                    $price = round($price,2);
                }
                $query_cat = $this->db->query("SELECT * FROM " . DB_PREFIX . "amazon_price_settings WHERE customer_id =".$customer_id." and category_id = ".$category['category_id']);
                if ($query_cat->num_rows) {
                    $price = $price + ($price * ($query_cat->row['sp']/100));
                    $price = $price * $query_cat->row['mrp'];
                    $price = round($price,2);
                }
            } else{
                $price = $price + ($price * (35/100));
                $price = $price * 1.2;
                $price = round($price,2);
            }
            return $price;
        }
        public function getminPrice($product_id,$price,$customer_id) {
            $category = $this->getProductCategory($product_id);
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "amazon_price_settings WHERE customer_id =".$customer_id);
            if ($query->num_rows) {
                $query_all = $this->db->query("SELECT * FROM " . DB_PREFIX . "amazon_price_settings WHERE customer_id =".$customer_id." and category_id = '0'");
                if ($query_all->num_rows) {
                    $price = $price + ($price * ($query_all->row['mirp']/100));
                    $price = round($price,2);
                }
                $query_cat = $this->db->query("SELECT * FROM " . DB_PREFIX . "amazon_price_settings WHERE customer_id =".$customer_id." and category_id = ".$category['category_id']);
                if ($query_cat->num_rows) {
                    $price = $price + ($price * ($query_cat->row['mirp']/100));
                    $price = round($price,2);
                }
            } else{
                $price = round($price,2);
            }
            return $price;
        }
        public function getAmPrice($product_id,$price,$customer_id) {
            $category = $this->getProductCategory($product_id);
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "amazon_price_settings WHERE customer_id =".$customer_id);
            if ($query->num_rows) {
                $query_all = $this->db->query("SELECT * FROM " . DB_PREFIX . "amazon_price_settings WHERE customer_id =".$customer_id." and category_id = '0'");
                if ($query_all->num_rows) {
                    $price = $price + ($price * ($query_all->row['sp']/100));
                    $price = round($price,2);
                }
                $query_cat = $this->db->query("SELECT * FROM " . DB_PREFIX . "amazon_price_settings WHERE customer_id =".$customer_id." and category_id = ".$category['category_id']);
                if ($query_cat->num_rows) {
                    $price = $price + ($price * ($query_cat->row['sp']/100));
                    $price = round($price,2);
                }
            } else{
                $price = $price + ($price * (35/100));
                $price = round($price,2);
            }
            return $price;
        }
	 public function insertMonitorStatus($path,$customer_id,$feed_id) {
                    $this->db->query("UPDATE `" . DB_PREFIX . "all_amazon_feeds` SET path = '" . $this->db->escape($path) . "' where customer_id = ".$customer_id." and feed_id = '".$feed_id."'");
                	}
        public function getProductTongkartPrice($customer_id,$product_id) {    
                $query = $this->db->query("SELECT updated_price FROM " . DB_PREFIX . "tongkart_price_settings WHERE customer_id = '" . (int)$customer_id . "' and product_id = '".$product_id."'");
            if ($query->num_rows) {
                return $query->row['updated_price'];
                
            } else {
                $query = $this->db->query("SELECT updated_price FROM " . DB_PREFIX . "tongkart_price_settings WHERE customer_id = '0' and product_id = '".$product_id."'");
                if ($query->num_rows) {
                    return $query->row['updated_price'];
                } else{
                    $query = $this->db->query("SELECT tongkart_price  FROM " . DB_PREFIX . "product WHERE product_id = '".$product_id."'");
                    return $query->row['tongkart_price'];
                }
            } 
               
	}
	 
	
}
