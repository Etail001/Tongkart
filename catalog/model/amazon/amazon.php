<?php
class ModelAmazonAmazon extends Model {
    public function getImageProductDetails($product_id,$seller_id) {
        $sql = "SELECT p.product_id from " . DB_PREFIX . "product p left join " . DB_PREFIX . "seller_mapping sm on(sm.product_id = p.product_id) where sm.seller_id = ".$seller_id." and p.amazon_status = 'listed' and p.amazon_image_update = '0' limit 5000";
        $query = $this->db->query($sql);
        $product_data = array();
        foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
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
    public function getProductPrice($filter_data,$seller_id) {
        $sql = "SELECT p.product_id from " . DB_PREFIX . "product p left join " . DB_PREFIX . "seller_mapping sm on(sm.product_id = p.product_id) where sm.seller_id = ".$seller_id." and p.amazon_status = 'listed' and p.amazon_price_update = '0' limit 20000";
        $query = $this->db->query($sql);
        $product_data = array();
        foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
		}

		return $product_data;
    }
    public function getProductDetails($data,$seller_id,$type) {
	 if($type == 'delete'){
	   $sql = "SELECT p.product_id from " . DB_PREFIX . "product p left join " . DB_PREFIX . "seller_mapping sm on(sm.product_id = p.product_id) where sm.seller_id = ".$seller_id." and p.amazon_status != 'listed' and p.amazon_status != 'submited' and p.amazon_status != 'update' and p.amazon_status != 'error' and p.amazon_status != 'restricted' and p.amazon_status = 'delete' ORDER BY p.product_id ASC";
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
        $sql = "SELECT p.product_id from " . DB_PREFIX . "product p left join " . DB_PREFIX . "seller_mapping sm on(sm.product_id = p.product_id) where sm.seller_id = ".$seller_id." and p.amazon_status != 'listed' and p.amazon_status != 'submited' and p.amazon_status != 'update' and p.amazon_status != 'error' and p.amazon_status != 'restricted' and p.amazon_data = '1' ORDER BY p.product_id ASC";
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
                $product_data[$result['product_id']] = $this->getProduct($result['product_id']);
        }
        $array_product = array();
        foreach($product_data as $products){
            $category = $this->getProductCategory($products['product_id']);
	     //echo $category['name'].'.......'.$products['product_id']; 
	     if(isset($category['name']) && $category['name'] !=''){
            if($category['name'] != 'Watches' && $category['name'] != "Men's Clothing" && $category['name'] != "Original Design-Women's Clothing" && $category['name'] != 'Jewelry'  && $category['name'] != "Women's Clothing" && $category['name'] != "Beauty & Health" && $category['name'] != "Mother & Kids" && $category['name'] != "Clothing Accessories" && $category['name'] != "Shoes") {
                if (strpos($products['name'], 'gel') === false && strpos($products['name'], 'cream') === false && strpos($products['name'], 'nail print') === false && strpos($products['name'], 'cream') === false && strpos($products['name'], 'lotion') === false && strpos($products['name'], 'oil') === false && strpos($products['name'], 'sunscreen') === false && strpos($products['name'], 'facewash') === false && strpos($products['name'], 'wax') === false && strpos($products['name'], 'battery') === false && strpos($products['name'], 'blades') === false && strpos($products['name'], 'spinner') === false && strpos($products['name'], 'Spinner') === false && strpos($products['name'], 'wi-fi') === false && strpos($products['name'], 'perfume') === false && strpos($products['name'], 'body mist') === false && strpos($products['name'], 'deodorant') === false && strpos($products['name'], 'face wash') === false && strpos($products['name'], 'moisturizer') === false) {
                    $array_product[] = $this->getProduct($products['product_id']);
                } else{
                   $this->db->query("UPDATE " . DB_PREFIX . "product SET amazon_status = 'restricted' where product_id = '".$products['product_id']."'"); 
                }
            } else{
                   $this->db->query("UPDATE " . DB_PREFIX . "product SET amazon_status = 'restricted' where product_id = '".$products['product_id']."'");
            }
	     }
        }
	 

        return $array_product;
    }
    public function getProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT *, pd.name AS name,pd.amazon_description,pd.bullet_point_1,pd.bullet_point_2,pd.bullet_point_3,pd.bullet_point_4,pd.bullet_point_5,pd.package_weight,pd.product_size,pd.package_size,pd.package_contents,pd.featurs,pd.compatible_with,pd.material, p.image, m.name AS manufacturer, p.sort_order, pd.color FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)  LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

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
				'ean'              => $query->row['ean'],
				'jan'              => $query->row['jan'],
				'isbn'             => $query->row['isbn'],
				'mpn'              => $query->row['mpn'],
				'location'         => $query->row['location'],
				'quantity'         => $query->row['quantity'],
				'image'            => $query->row['image'],
				'manufacturer_id'  => $query->row['manufacturer_id'],
				'manufacturer'     => $query->row['manufacturer'],
				'price'            => $query->row['price'],
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
	     echo $product_id.'----';
            return $category_array;
        }
        public function getProductImage($product_id) {
            $query = $this->db->query("SELECT image FROM " . DB_PREFIX . "product_image  WHERE product_id = '" . (int)$product_id . "' limit 8");
            if ($query->num_rows) {
                return $query->rows;
            }
        }
        public function generateProductFeed($product_array,$type,$seller_id) { 
	     $timestamp = gmdate("Y-m-d\TH:i:s");
            $string = '<?xml version="1.0" ?>
<AmazonEnvelope
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amznenvelope.xsd">
	<Header>
		<DocumentVersion>1.01</DocumentVersion>';
		if($seller_id == '2'){
		 	$string .= '<MerchantIdentifier>A1LTO87O7AHC09</MerchantIdentifier>';
		} else if($seller_id == '1'){
			$string .= '<MerchantIdentifier>A1LTO87O7AHC09</MerchantIdentifier>';
		}
	$string .= '</Header>
	<MessageType>Product</MessageType>
	<PurgeAndReplace>false</PurgeAndReplace>';
	 //echo "<pre>";print_r($product_array);echo "</pre>123";die;
	 $index = 1; 
        foreach($product_array as $products){
	 //echo "<pre>";print_r($products);echo "</pre>123";die;
	 $category = $this->getProductCategory($products['product_id']);
	 $products['description1'] = $products['description'];
	 $products['description'] = str_replace('<link type="text/css" rel="stylesheet" href="https://css.chinabrands.com/css/S2.css">',"",$products['description']);
	 $pos1 = strpos($products['description'], '<div class="xxkkk2">');
	 $pos = $pos1;
         if(!isset($pos) || $pos == ''){
             $pos1 = strpos($products['description'], '<div class="xxkkk20">');
         } 
        $length_key = strlen('<div class="xxkkk2">');
        $pos1 = $pos1+$length_key;
        $pos2 = strpos($products['description'], '</div>',$pos1);
        $len = $pos2 - $pos1;
        $products['description'] = substr($products['description'], $pos1, $len); 
	 $products['description'] = str_replace('<br>'," ",$products['description']);
	 $products['description'] = str_replace('</b>',"",$products['description']);
        $products['description'] = str_replace('<b>',"",$products['description']);
	 $products['description'] = str_replace('<B>',"",$products['description']);
	 $products['description'] = str_replace('<span style="font-size:14px;">',"",$products['description']);
	 $products['description'] = str_replace('</span>',"",$products['description']);
	 $products['description'] = str_replace('</B>',"",$products['description']);
	 $products['description'] = str_replace('<span style="fontsize:14px;">',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/image/20150901/sms_20150901102600_82369.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<strong>',"",$products['description']);
	 $products['description'] = str_replace('</strong>',"",$products['description']);
	 $products['description'] = str_replace('<div style="display:none;">',"",$products['description']);
	 $products['description'] = str_replace('<br />',"",$products['description']);
	 $products['description'] = str_replace('<P>',"",$products['description']);
	 $products['description'] = str_replace('</P>',"",$products['description']);
	 $products['description'] = str_replace('<B style="font-size:15px">',"",$products['description']);
	 $products['description'] = str_replace('&',"",$products['description']);
	 $products['description'] = str_replace('</Strong>',"",$products['description']);
	 $products['description'] = str_replace('<Strong>',"",$products['description']);
	 $products['description'] = str_replace('<p>',"",$products['description']);
	 $products['description'] = str_replace("what's","",$products['description']);
	 $products['description'] = str_replace("Here is the product's English user manual","",$products['description']);
	 $products['description'] = str_replace("Product Description:","",$products['description']);
	 $products['description'] = str_replace('<span style="font-size:16px;">',"",$products['description']);
	 $products['description'] = str_replace('<b/>',"",$products['description']);
	 $products['description'] = str_replace('</STRONG>',"",$products['description']);
	 $products['description'] = str_replace('</a> </p>',"",$products['description']);
	 $products['description'] = str_replace('<a href="https://mega.nz/#!khMhXJSY!zNumG241R1fHtoDpojLFePuaU2vCoet-o0VJxOsSW3w" rel="nofollow" target="_blank">Frequency Modulation Video<span style="color:#333399;"> </a>, it can help you. ',"",$products['description']);
	 $products['description'] = str_replace('</a> </p>',"",$products['description']);
	 $products['description'] = str_replace('<a href="https://mega.nz/#!khMhXJSY!zNumG241R1fHtoDpojLFePuaU2vCoet-o0VJxOsSW3w" rel="nofollow" target="_blank">Frequency Modulation Video<span style="color:#333399;"> </a>',"",$products['description']);
	 $products['description'] = str_replace('<span style="font-size:14px;color:#ff0000;">',"",$products['description']);
	 $products['description'] = str_replace('<span style="color:#ff0000;">',"",$products['description']);
	 $products['description'] = str_replace('<span style="font-family:Arial;font-size:12px;LINE-HEIGHT: 18px; PADDING-LEFT: 0px"> <a style="COLOR: #ff0000" href="https://s3.amazonaws.com/download.appinthestore.com/uploads/201506/YQ8003+spoke.rar" rel="nofollow" target="_blank">click here</a> to download software and pictures) <div class="self-adaption">',"",$products['description']);
	 $products['description'] = str_replace('tps://www.gearbest.com/blog/buying-guide/robot-vacuums-shopping-guide-2660" target_blank" styletext-decoration: underline; font-size: 24px; font-family: "times new roman"; color: rgb(84, 141, 212);">',"",$products['description']); 
	 $products['description'] = str_replace('tps://www.gearbest.com/blog/buying-guide/robot-vacuums-shopping-guide-2660" target_blank" styletext-decoration: underline; font-size: 24px; font-family: "times new roman"; color: rgb(84, 141, 212);"><span stylecolor: rgb(84, 141, 212);"><em><span stylecolor: rgb(84, 141, 212); font-size: 24px; font-family: "times new roman";">Robot Vacuums Buying Guide<span stylefont-size: 24px; font-family: "times new roman"; color: rgb(84, 141, 212);"></em>',"",$products['description']);
	 $products['description'] = str_replace('</a> </p>',"",$products['description']);
	 $products['description'] = str_replace('<span stylecolor: rgb(84, 141, 212);"><em>',"",$products['description']);
	 $products['description'] = str_replace('tps://www.gearbest.com/blog/buying-guide/robot-vacuums-shopping-guide-2660" target_blank" styletext-decoration: underline; font-size: 24px; font-family: "times new roman"; color: rgb(84, 141, 212);">',"",$products['description']);
	 $products['description'] = str_replace('<span stylecolor: rgb(84, 141, 212); font-size: 24px; font-family: "times new roman";">',"",$products['description']);
	 $products['description'] = str_replace('<span style="font-size:14px;color:#FF0000;">',"",$products['description']);
	 $products['description'] = str_replace('</2015/201510/heditor/201510201326221415.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('201508/heditor/201508141758188226.jpg" alt="" />',"",$products['description']); 
	 $products['description'] = str_replace('</2015/201510/heditor/201510201326221415.jpg" alt=""',"",$products['description']); 
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509091510359000.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509091510367968.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509091510362316.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509091510362381.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505161805207287.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505161805209117.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505161805205069.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505161805215333.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505180932364226.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img alt="" src="https://des.chinabrands.com/uploads/2015/201501/heditor/201501100945393953.jpg" width="700" height="700" />',"",$products['description']);
	 $products['description'] = str_replace('<img alt="" src="https://des.chinabrands.com/uploads/2015/201501/heditor/201501100945405278.jpg" width="700" height="700" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201501/heditor/201501071430132419.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505221434465929.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201503/heditor/201503101754133321.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504130952453601.jpg" alt="" />   <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504130952455240.jpg" alt="" />   <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504130952464651.jpg" alt="" />   <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504130952476230.jpg" alt="" />   <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504130952476945.jpg" alt="" />   <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504130952481838.jpg" alt="" />   <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504130952485386.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<font color="red">',"",$products['description']);
	 $products['description'] = str_replace('</font>',"",$products['description']);
	 $products['description'] = str_replace('<br/>',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201503/heditor/201503251111035047.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201512/heditor/201512021547023037.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201503/heditor/201503031702556562.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505201343517552.jpg" alt="" width="700" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509021656131736.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504021048057955.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509021648541756.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201508/heditor/201508141758179575.jpg" alt="" />  <img src="https://des.chinabrands.com/uploads/2015/201508/heditor/201508141758179740.jpg" alt="" />  <img src="https://des.chinabrands.com/uploads/2015/201508/heditor/201508141758178543.jpg" alt="" />  <img src="https://des.chinabrands.com/uploads/2015/201508/heditor/201508141758181881.jpg" alt="" />  <img src="https://des.chinabrands.com/uploads/2015/',"",$products['description']);
	 $products['description'] = str_replace('<a href="http://files.xiaomi-mi.com/files/wifi_router/User%20Manual%20Mi%20WiFi%20(EN).pdf"> http://files.xiaomi-mi.com/files/wifi_router/User%20Manual%20Mi%20WiFi%20(EN).pdf </a>  <a href="https://www.dropbox.com/sh/1sgmqi3qyz7yius/AACFsFEHJNYhC1cJjNiK1PSza/Xiaomi%20manual%20v1.0.pdf?dl=0" rel="nofollow" target="_blank"> https://www.dropbox.com/sh/1sgmqi3qyz7yius/AACFsFEHJNYhC1cJjNiK1PSza/Xiaomi%20manual%20v1.0.pdf?dl=0  </a>',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504230941056734.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504021742558479.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504230941067862.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504230941053589.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504230941051265.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504230941056575.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504230941051861.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504230941056341.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504031003226871.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504230941054347.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504031003221063.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504031003227231.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504031003226403.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201510/heditor/201510201246292421.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('img src="https://des.chinabrands.com/uploads/2015/201510/heditor/201510201325224830.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201510/heditor/201510201325453175.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201510/heditor/201510201325458185.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201510/heditor/201510201326223240.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504091535387053.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504131330048371.jpg" alt="" />  <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504131330045825.jpg" alt="" />  <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504131330041384.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504131327051050.jpg" alt="" />  <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504131327062550.jpg" alt="" />  <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504131327063153.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504131023245646.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505261158234060.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505261158232965.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504131020218440.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504131020212135.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504131020219174.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504131020212540.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504131806054064.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504131806053131.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504131806054405.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504131803508199.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504131803501268.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504131803507837.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504131803509668.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507061911367733.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507081128039874.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507081128038397.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507081128031364.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507081128044384.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507141515583750.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506301029182068.jpg" alt="" />  <img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506301029182696.jpg" alt="" />  <img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506301029189464.jpg" alt="" />  <img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506301029185766.jpg" alt="" />  <img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506301029188121.jpg" alt="" />  <img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506301029185859.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509091520455667.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509091520459158.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509091510362316.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509091510362381.jpg" alt="" />',"",$products['description']); 
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504131127181802.jpg" alt="" />  <img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504131127183906.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504131131068199.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('</a> </p>',"",$products['description']);
	 $products['description'] = str_replace('</p>      <img src="https://des.chinabrands.com/uploads/pdm-desc-pic/Electronic/image/2017/08/28/1503889364590462.jpg" style="" title="1503889364590462.jpg"/> </p>      <img src="https://des.chinabrands.com/uploads/pdm-desc-pic/Electronic/image/2017/08/28/1503889365565309.jpg" style="" title="1503889365565309.jpg"/> </p>',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2014/201409/heditor/201409111012004608.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<span style="color:#FF0000;">',"",$products['description']);
	 $products['description'] = str_replace('<p align="center"> 	 <img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301722599270.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301722596859.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301722598429.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301722593574.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301722595489.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301723003593.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301723001759.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301723013791.jpg" /> </p>',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281757295550.jpg" alt="" />   </p><span style:"font-size:14px;">',"",$products['description']);
	 $products['description'] = str_replace('<p align="center"> 	 <img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145163220.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145185301.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145221814.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145222134.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145228550.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145239390.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145238366.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145235374.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145242348.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145242781.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145252116.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145264901.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145264440.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145274858.jpg" /> </p>',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505111843032309.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505111837131187.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505111837146082.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505111837147189.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505111837142399.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505281644517208.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2014/201412/heditor/201412021712136597.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2014/201412/heditor/201412021712536648.jpg" alt="" />  <img src="https://des.chinabrands.com/uploads/2014/201412/heditor/201412021810446725.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2014/201412/heditor/201412021714351390.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2014/201412/heditor/201412021714362773.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504021505198453.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504091928124311.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504091929042462.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504091926195932.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504091927232139.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504091817342717.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504091815176284.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504091816015082.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201504/heditor/201504091816409933.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<a href="http://en.wikipedia.org/wiki/Phone_connector_%28audio%29#TRRS_standards" rel="nofollow" target="_blank"> http://en.wikipedia.org/wiki/Phone_connector_%28audio%29#TRRS_standards  </a>',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201510/heditor/201510261753582063.JPG" alt="" />  <img src="https://des.chinabrands.com/uploads/2015/201510/heditor/201510261753589144.jpg" alt="" />  <img src="https://des.chinabrands.com/uploads/2015/201510/heditor/201510261753591533.JPG" alt="" />  <img src="https://des.chinabrands.com/uploads/2015/201510/heditor/201510261754007879.jpg" alt="" />  <img src="https://des.chinabrands.com/uploads/2015/201510/heditor/201510261754009902.jpg" alt="" />  <img src="https://des.chinabrands.com/uploads/2015/201510/heditor/201510261754012208.jpg" alt="" />',"",$products['description']); 
	 $products['description'] = str_replace('<a href="http://en.wikipedia.org/wiki/Phone_connector_%28audio%29#TRRS_standards" rel="nofollow" target="_blank"> http://en.wikipedia.org/wiki/Phone_connector_%28audio%29#TRRS_standards  </a>',"",$products['description']);
	 $products['description'] = str_replace('<p align="center"> 	 <img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145163220.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145185301.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145221814.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145222134.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145228550.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145239390.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145238366.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145235374.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145242348.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145242781.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145252116.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145264901.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145264440.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145274858.jpg" /> </p>',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509041901317941.jpg" alt="" /> , iOS users - <img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509041901311390.jpg" alt="" />  <span style="color:#cc0000;">',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505111732569612.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505111736237932.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505111736237439.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505111736232058.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505111740207944.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<p align="center"> 	 <img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301722599270.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301722596859.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301722598429.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301722593574.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301722595489.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301723003593.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301723001759.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301723013791.jpg" /> </p>',"",$products['description']); 
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281757295550.jpg" alt="" />   </p><span style:"font-size:14px;">',"",$products['description']);
	 $products['description'] = str_replace('<p align="center"> 	 <img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145163220.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145185301.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145221814.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145222134.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145228550.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145239390.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145238366.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145235374.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145242348.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145242781.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145252116.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145264901.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145264440.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145274858.jpg" /> </p>',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505111845253367.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505111845562890.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505111845561713.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505281643474480.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<p align="center"> 	 <img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145163220.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145185301.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145221814.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145222134.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145228550.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145239390.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145238366.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145235374.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145242348.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145242781.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145252116.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145264901.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145264440.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145274858.jpg" /> </p>',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281757295550.jpg" alt="" />   </p><span style:"font-size:14px;">',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2014/201407/heditor/201407111801583825.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281757295550.jpg" alt="" />   </p>',"",$products['description']);
	 $products['description'] = str_replace('<span style:"font-size:14px;">',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281757295550.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('</p>',"",$products['description']);
	 $products['description'] = str_replace('<p align="center"> 	 <img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301722599270.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301722596859.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301722598429.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301722593574.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301722595489.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301723003593.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301723001759.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301723013791.jpg" /> </p>',"",$products['description']);
	 $products['description'] = str_replace('<img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301722599270.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301722596859.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301722598429.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301722593574.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301722595489.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301723003593.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301723001759.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301723013791.jpg" />',"",$products['description']);
	 $products['description'] = str_replace('<p align="center">',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281752216697.jpg" alt="" /><img src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281751357301.jpg" alt="" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728537476.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728547771.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728542833.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728544351.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728557426.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728555157.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728567563.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728561014.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728568216.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728576557.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728571684.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728585883.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728585168.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728596031.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281729004339.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281729003538.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281729014600.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281729016004.jpg" />',"",$products['description']);
	 $products['description'] = str_replace('<img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145163220.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145185301.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145221814.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145222134.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145228550.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145239390.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145238366.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145235374.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145242348.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145242781.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145252116.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145264901.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145264440.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281145274858.jpg" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/pdm-desc-pic/Electronic/image/2017/08/28/1503889422325067.jpg" title="1503889422325067.jpg" alt="20170825_104237_034.jpg"/>',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/pdm-desc-pic/Electronic/image/2017/08/28/1503889352403179.jpg" style="" title="1503889352403179.jpg"/>       <img src="https://des.chinabrands.com/uploads/pdm-desc-pic/Electronic/image/2017/08/28/1503889355683630.jpg" style="" title="1503889355683630.jpg"/>       <img src="https://des.chinabrands.com/uploads/pdm-desc-pic/Electronic/image/2017/08/28/1503889355689894.jpg" style="" title="1503889355689894.jpg"/>       <img src="https://des.chinabrands.com/uploads/pdm-desc-pic/Electronic/image/2017/08/28/1503889364346979.jpg" style="" title="1503889364346979.jpg"/>       <img src="https://des.chinabrands.com/uploads/pdm-desc-pic/Electronic/image/2017/08/28/1503889357181784.jpg" style="" title="1503889357181784.jpg"/>       <img src="https://des.chinabrands.com/uploads/pdm-desc-pic/Electronic/image/2017/08/28/1503889360471072.jpg" style="" title="1503889360471072.jpg"/>       <img src="https://des.chinabrands.com/uploads/pdm-desc-pic/Electronic/image/2017/08/28/1503889362722208.jpg" style="" title="1503889362722208.jpg"/>       <img src="https://des.chinabrands.com/uploads/pdm-desc-pic/Electronic/image/2017/08/28/1503889361262749.jpg" style="" title="1503889361262749.jp',"",$products['description']);
	 $products['description'] = str_replace('</p>',"",$products['description']);
	 $products['description'] = str_replace('<a href="http://www.minix.com.hk/Support/14071016595071.html" rel="nofollow" target="_blank"><img height="80" alt="download" src="https://des.chinabrands.com/uploads/2014/201406/heditor/201406241405343998.jpg" width="80" border="0"></a>',"",$products['description']);
	 $products['description'] = str_replace('<p align="center"> 	<img src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281752216697.jpg" alt="" /><img src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281751357301.jpg" alt="" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728537476.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728547771.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728542833.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728544351.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728557426.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728555157.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728567563.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728561014.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728568216.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728576557.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728571684.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728585883.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728585168.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281728596031.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281729004339.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281729003538.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281729014600.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410281729016004.jpg" />',"",$products['description']);
	 $products['description'] = str_replace('<p align="center"> 	 <img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301722599270.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301722596859.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301722598429.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301722593574.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301722595489.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301723003593.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301723001759.jpg" /><img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410301723013791.jpg" /> </p>',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/pdm-desc-pic/Electronic/image/2017/08/28/1503889422325067.jpg" title="1503889422325067.jpg" alt="20170825_104237_034.jpg"/> </p>',"",$products['description']); 
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/pdm-desc-pic/Electronic/image/2017/08/28/1503889352403179.jpg" style="" title="1503889352403179.jpg"/> </p>      <img src="https://des.chinabrands.com/uploads/pdm-desc-pic/Electronic/image/2017/08/28/1503889355683630.jpg" style="" title="1503889355683630.jpg"/> </p>      <img src="https://des.chinabrands.com/uploads/pdm-desc-pic/Electronic/image/2017/08/28/1503889355689894.jpg" style="" title="1503889355689894.jpg"/> </p>      <img src="https://des.chinabrands.com/uploads/pdm-desc-pic/Electronic/image/2017/08/28/1503889364346979.jpg" style="" title="1503889364346979.jpg"/> </p>      <img src="https://des.chinabrands.com/uploads/pdm-desc-pic/Electronic/image/2017/08/28/1503889357181784.jpg" style="" title="1503889357181784.jpg"/> </p>      <img src="https://des.chinabrands.com/uploads/pdm-desc-pic/Electronic/image/2017/08/28/1503889360471072.jpg" style="" title="1503889360471072.jpg"/> </p>      <img src="https://des.chinabrands.com/uploads/pdm-desc-pic/Electronic/image/2017/08/28/1503889362722208.jpg" style="" title="1503889362722208.jpg"/> </p>      <img src="https://des.chinabrands.com/uploads/pdm-desc-pic/Electronic/image/2017/08/28/1503889361262749.jpg" sty',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509221801455346.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509221801459905.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509221801469031.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509221801464331.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509221801469992.JPG" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<a href="https://www.gearbest.com/blog/buying-guide/robot-vacuums-shopping-guide-2660" target="_blank" style="text-decoration: underline; color: rgb(0, 0, 0); font-size: 24px; font-family: impact, chicago;"><span style="color: rgb(0, 0, 0); font-size: 24px; font-family: impact, chicago;">',"",$products['description']);
	 $products['description'] = str_replace('<a href="http://download.appinthestore.com.s3.amazonaws.com/2016-globale-FAQ-data/Home/1920290%20SOOCAS%20X3%20Sonic%20Electric%20Toothbrush%20Quick%20Start%20Guide.pdf" rel="nofollow" target="_blank"></a>',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507151127352053.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507151127357771.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507151127354868.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507151127358914.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201510/heditor/201510311918204603.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201510/heditor/201510311918217012.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505051355365786.jpg" alt="" />',"",$products['description']); 
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505051355369315.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505051355235740.jpg" alt="" />  <span style="font-size:14px;color:#ff0000;">',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505121527148741.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505131604276712.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505051409341749.jpg" alt="" />',"",$products['description']); 
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201510/heditor/201510081528064927.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201510/heditor/201510081528116996.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201510/heditor/201510081528164552.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201510/heditor/201510081528193065.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201510/heditor/201510081528298316.jpg" alt="" />',"",$products['description']); 
	 $products['description'] = str_replace('ps://des.chinabrands.com/uploads/2015/201507/heditor/201507311120146977.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507311120152434.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507311125249594.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507311125246258.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507311125069667.jpg" alt="" /> ',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507311120129318.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507311120129309.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507311120123617.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507311124455290.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507311120143961.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507311120144887.jpg" alt="" /> <img src="htt',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507161706364744.jpg" alt="" />  <img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507161706368930.jpg" alt="" />  <img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507161706366555.JPG" alt="" />  <img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507161706369673.jpg" alt="" />  <img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507161706377154.jpg" alt="" />  <img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507161706374306.jpg" alt="" />  <img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507161706372491.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509141846156071.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509141549472578.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507150934483969.jpg" alt="" />   <img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507150934485089.jpg" alt="" />   <img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507150934487041.jpg" alt="" />   <img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507150934482354.jpg" alt="" />   <img src="https://des.chinabrands.com/uploads/2015/201507/heditor/201507150934484800.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509251505586143.jpg" alt="" /><img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509251505591429.jpg" alt="" /><img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509251505598982.jpg" alt="" /><img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509251505592659.jpg" alt="" /><img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509251505591990.jpg" alt="" /><img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509251505598174.JPG" alt="" /><img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509251505594155.jpg" alt="" /><img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509251505593433.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509221801455346.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509221801459905.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509221801469031.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509221801464331.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201509/heditor/201509221801469992.JPG" alt="" />   <span style="font-size:16px;">',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506130907043015.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506130907047553.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506130907047186.jpg" alt="" /> <br / >',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506121513406807.jpg" alt="" />   <img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506121513402594.jpg" alt="" />   <img src="https://des.chinabrands.com/uploads/pdm-desc-pic/Distribution/image/2016/12/10/1481360465879169.jpg" title="1481360465879169.jpg" alt="201506121513405806.jpg"/>',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506121509466192.jpg" alt="" />   <img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506121509462003.jpg" alt="" /><img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506121509466777.jpg" alt="" /><img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506121509464119.jpg" alt="" />   <img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506121509474657.jpg" alt="" />   <img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506121509474702.jpg" alt="" />   <img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506121509477168.jpg" alt="" />   <img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506121509484392.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506031037041767.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506031037041665.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506031037522996.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506031037048831.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506031037048352.JPG" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505290926177859.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505290926173105.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505290958583819.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505290958583283.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505290958589004.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505290958587376.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505290958581785.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505290958588983.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505290958588981.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505060934102710.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505060929202874.gif" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506111420543605.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506111425265855.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506111422505628.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505061404487996.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505061406019553.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505061414527257.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505061415526539.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505061406014186.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505061406017406.jpg" alt="" /> <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505061406012139.jpg" alt="" />  <span style="font-size:14px;color:#ff0000;">Note: This picture is for illustration purpose only, please refer to the product description for  included in box <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505121450367134.jpg" alt="" />   The Allocacoc Powercube Socket Has Won The Red Dot Award in 2014   <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505131622295732.jpg" alt="" />  Super Mini Allocacoc Powercube Socket <img src="https://des.chinabrands.com/uploads/2015/201505/heditor/201505061422136798.jpg" alt="" />',"",$products['description']);
	 $products['description'] = str_replace('(<span style="font-family:Arial;font-size:12px;LINE-HEIGHT: 18px; PADDING-LEFT: 0px"> <a style="COLOR: #ff0000" href="http://ritech3d.com/apps/" rel="nofollow" target="_blank">Click here</a> to download video APP)',"",$products['description']);
	 $products['description'] = str_replace('<img src="https://des.chinabrands.com/uploads/2015/201506/heditor/201506111504039199.jpg" alt="" /><br / > <img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410180942352446.jpg" />  <img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410180942367581.jpg" /> <img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410180942362050.jpg" /> <img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410180942368302.jpg" /> <img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410180942367554.jpg" />  <img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410180942371170.jpg" /> <img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410180942372874.jpg" /> <img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410180943222261.jpg" /> <img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410180943228541.jpg" />  <img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410180943221971.jpg" /> <img alt="" src="https://des.chinabrands.com/uploads/2014/201410/heditor/201410180942372574.jpg" />',"",$products['description']);  
	 $count = substr_count($products['description'],"<img");
	 for($i=0;$i<$count;$i++){
    		$p1= strpos($products['description'],"<img");
    		$p2= strpos($products['description'],"/>");
    		$sub = substr($products['description'], $p1, $p2+2);
    		$products['description'] = str_replace($sub,"",$products['description']); 
    
	 }
	 $products['description'] = str_replace('<v>',"",$products['description']);
	 $products['description'] = str_replace('<br / >',"",$products['description']);
	 $products['description'] = str_replace('<div class="sf-adaption">',"",$products['description']);
	 $products['description'] = str_replace('<div class="self-adaption">',"",$products['description']);
	 $products['description'] = str_replace('<d class="self-adaption">',"",$products['description']);
	 $products['description'] = str_replace('<span style="color: rgb(255, 0, 0);">',"",$products['description']);
	 $products['description'] = str_replace('<span style="color:#000099;">',"",$products['description']);
	 $products['description'] = str_replace('<span style="font-size:12px;">',"",$products['description']);
	 $products['description'] = str_replace('<span style="font-size:10px;">',"",$products['description']);
	 $products['description'] = str_replace('<span style="color:#990000;">',"",$products['description']);
	 $products['description'] = str_replace('<font color="#FF0000">',"",$products['description']);
	 $products['description'] = str_replace('<span style="font-size: 18px;">',"",$products['description']);
	 $products['description'] = str_replace('</span style="color:#ff0000;">',"",$products['description']);
	 $products['description'] = str_replace('<div class="self-adaption" style="max-width:1200px">',"",$products['description']);
	 $products['description'] = str_replace('</br>',"",$products['description']);
	 $products['description'] = str_replace('<P />',"",$products['description']);
	 $products['description'] = str_replace('<p />',"",$products['description']);
	 $products['description'] = str_replace('<p >',"",$products['description']);
	 $products['description'] = str_replace('Get more information, please click here: <a href="http://download.appinthestore.com.s3.amazonaws.com/2016-globale-FAQ-data/Computer%20and%20Networking/User%20Manual/Hand%20Held%20Vacuum%20English%20User%20Manual.pdf" rel="nofollow" target="_blank"><span style="color:#3333ff;">Handheld Vacuum English User Manual </a>',"",$products['description']);
	 $products['description'] = str_replace('<span style="font-size:18px;">',"",$products['description']);
	 $products['description'] = str_replace('<span style="color:#CC0000;">',"",$products['description']);
	 $products['description'] = str_replace('<div class="self-adaption" style="margin-bottom:10px;">',"",$products['description']);
	 $products['description'] = str_replace('<span style="font-size:14px;color: rgb(255, 0, 0)">',"",$products['description']);
	 $products['description'] = str_replace('<div class="self-adaption" style="max-width:1000px">',"",$products['description']);
	 $products['description'] = str_replace('<div class="nomargin">',"",$products['description']);
	 $products['description'] = str_replace('< 5mW - Wavelength: 635 - 655nm - Power source: 3 x LR41 button cell batteries or equivalent ( not included )',"",$products['description']);
	 $products['description'] = str_replace('<span style="font-family:Arial;font-size:14px;LINE-HEIGHT: 18px; PADDING-LEFT: 0px"> <a style="COLOR: #ff0000" href="http://www.18gps.net/Skins/DefaultIndex/" rel="nofollow" target="_blank">click here</a> to log in and query',"",$products['description']);
	 $products['description'] = str_replace('mg src="https://des.chinabrands.com/uploads/pdm-desc-pic/Electronic/image/2016/01/10/1452411227382942.jpg" style="" title="1452411227382942.jpg"/',"",$products['description']);
	 $products['description'] = str_replace('<span style="font-size:13px;color:#FF0000;">',"",$products['description']);
	 $products['description'] = str_replace('<a href="https://des.chinabrands.com/uploads/2015/201509/heditor/201509161751565895.jpg" rel="nofollow" target="_blank"><span style="color:#CC0000;">Operating Instruction</a>',"",$products['description']);
	 $products['description'] = str_replace('<B style=”font-size:16px”>',"",$products['description']);
	 $products['description'] = str_replace('<Main',"",$products['description']);
	 $products['description'] = str_replace('</b >',"",$products['description']);
	 $products['description'] = str_replace('<u>',"",$products['description']);
	 $products['description'] = str_replace('</u>',"",$products['description']);
	 $products['description'] = str_replace('</ B>',"",$products['description']);
	 $products['description'] = str_replace('</Br>',"",$products['description']);
	 $products['description'] = str_replace('<span style="font-family:Arial;font-size:12px;LINE-HEIGHT: 18px; PADDING-LEFT: 0px"> <a style="COLOR: #ff0000" http://www.gearbest.com/blog/how-to/country-based-mobile-phone-network-frequency-bands-coverage-guide-1144" rel="nofollow" target="_blank">click here</a>',"",$products['description']);
	 $products['description'] = str_replace('<span style="font-family:Arial;font-size:12px;LINE-HEIGHT: 18px; PADDING-LEFT: 0px"><a style="COLOR: #ff0000" href="http://maps.mobileworldlive.com/" rel="nofollow" target="_blank">click here</a>',"",$products['description']);
	 $products['description'] = str_replace('<span style="font-family:Arial;font-size:12px;LINE-HEIGHT: 18px; PADDING-LEFT: 0px"> <a style="COLOR: #ff0000"  href="http://www.gearbest.com/blog/how-to/country-based-mobile-phone-network-frequency-bands-coverage-guide-1144" target="_blank">click here</a>',"",$products['description']);
	 $products['description'] = str_replace('<span style="font-family:Arial;font-size:12px;LINE-HEIGHT: 18px; PADDING-LEFT: 0px"> <a style="COLOR: #ff0000" href="https://www.gearbest.com/blog/how-to/country-based-mobile-phone-network-frequency-bands-coverage-guide-1144" target="_blank">click here</a>',"",$products['description']);
	 $products['description'] = str_replace('<span style="font-family:Arial;font-size:12px;LINE-HEIGHT: 18px; PADDING-LEFT: 0px"> <a style="COLOR: #ff0000" href="http://maps.mobileworldlive.com/" rel="nofollow" target="_bla nk">click here</a>',"",$products['description']);
	 $products['description'] = str_replace('<span style="font-family:Arial;font-size:12px;LINE-HEIGHT: 18px; PADDING-LEFT: 0px"> <a style="COLOR: #ff0000" href="http://maps.mobileworldlive.com/" rel="nofollow" target="_blank">click here</a>',"",$products['description']);
	 $products['description'] = str_replace('<span style="font-family: Arial, Helvetica; line-height: 25px;"><span style="font-size:14px;color:#000066;">',"",$products['description']);
	 $products['description'] = str_replace('<div class="xxkkk">',"",$products['description']);
	 $products['description'] = str_replace('Click the <a href="https://www.dropbox.com/sh/acmt8t84hh23ih2/AADNOz9JVmBj8WYKk7fHCJu9a?dl=0">Operating Instruction</a> to download the APP',"",$products['description']);
	 $products['description'] = str_replace('<span style="font-size: 18px; color: rgb(255, 0, 0);">',"",$products['description']);
        if(trim($products['description']) == '' || trim($products['description']) == '<div>' ){
	    $products['description'] = $products['name'];
	 }
	 $products['description'] = substr($products['description'],0,2000);
	 if($products['manufacturer'] == ''){
            $products['manufacturer'] = 'Tongkart';
        }
        if($category['name'] != 'Watches' && $category['name'] != "Men's Clothing" && $category['name'] != "Original Design-Women's Clothing" && $category['name'] != 'Jewelry' && $category['name'] != "Women's Clothing") {
        if (strpos($products['name'], 'gel') === false && strpos($products['name'], 'cream') === false && strpos($products['name'], 'nail print') === false && strpos($products['name'], 'cream') === false && strpos($products['name'], 'lotion') === false && strpos($products['name'], 'oil') === false && strpos($products['name'], 'sunscreen') === false && strpos($products['name'], 'facewash') === false && strpos($products['name'], 'wax') === false && strpos($products['name'], 'battery') === false && strpos($products['name'], 'blades') === false && strpos($products['name'], 'spinner') === false && strpos($products['name'], 'wi-fi') === false && strpos($products['name'], 'perfume') === false && strpos($products['name'], 'body mist') === false && strpos($products['name'], 'deodorant') === false && strpos($products['name'], 'face wash') === false && strpos($products['name'], 'moisturizer') === false) {
        $product_name = $this->getProductName($products['name'],$category['name']);
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
		   //$dimension .= '<MSRP currency="INR">'.$price_product.'</MSRP>'; 
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
            }
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
	 public function insertFeedId($array,$type,$product_array = array(),$account_id = 0) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "amazon_feeds SET feed_id = '" . $this->db->escape($array['SubmitFeedResult']['FeedSubmissionInfo']['FeedSubmissionId']) . "', type = '" . $this->db->escape($type) . "',account_id = '".$account_id."'");
            if($type == 'product'){
                foreach($product_array as $product){
                    $this->db->query("UPDATE " . DB_PREFIX . "product SET amazon_feed_id = '" . $this->db->escape($array['SubmitFeedResult']['FeedSubmissionInfo']['FeedSubmissionId']) . "', amazon_status = 'submited' where product_id = ".$product['product_id']);
                }
            } else if($type == 'image'){
		  foreach($product_array as $product){ 
                    $this->db->query("UPDATE " . DB_PREFIX . "product SET amazon_image_feed_id = '" . $this->db->escape($array['SubmitFeedResult']['FeedSubmissionInfo']['FeedSubmissionId']) . "' where product_id = ".$product['product_id']);
		  }
            }
	     else if($type == 'shipment'){
		  foreach($product_array as $product){ 
                    $this->db->query("UPDATE " . DB_PREFIX . "order_tracking SET amazon_feed_id = '" . $this->db->escape($array['SubmitFeedResult']['FeedSubmissionInfo']['FeedSubmissionId']) . "' where order_id = ".$product['order_id']);
		  }
            } else if($type == 'inventory'){
		  foreach($product_array as $product){ 
                    $this->db->query("UPDATE " . DB_PREFIX . "product SET amazon_inventory_feed_id = '" . $this->db->escape($array['SubmitFeedResult']['FeedSubmissionInfo']['FeedSubmissionId']) . "' where product_id = ".$product['product_id']);
		  }
            } else if($type == 'price'){
		  foreach($product_array as $product){ 
                    $this->db->query("UPDATE " . DB_PREFIX . "product SET amazon_price_feed_id = '" . $this->db->escape($array['SubmitFeedResult']['FeedSubmissionInfo']['FeedSubmissionId']) . "' where product_id = ".$product['product_id']);
		  }
            }
        } 
	 public function getFeedDetails($id = 0) {
	     if ($id == 0) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "amazon_feeds  WHERE status = 'submited' and account_id = 0");
            		if ($query->num_rows) {
                		return $query->rows;
            		}
            } else if ($id == 2) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "amazon_feeds  WHERE status = 'submited' and account_id = 2");
            		if ($query->num_rows) {
                		return $query->rows;
            		}
	     }
            
        }
	 public function updateFeed($response,$feed_id,$type) {
            if($type == 'product'){
                if(!isset($response['Error'])){
                    $this->db->query("UPDATE " . DB_PREFIX . "product SET amazon_status = 'listed', amazon_listed_date = now() where amazon_feed_id = ".$feed_id); 
                    $this->db->query("UPDATE " . DB_PREFIX . "amazon_feeds SET status = 'completed' where feed_id = ".$feed_id);  
                    if(isset($response['Message']['ProcessingReport']['Result'])){
			   $is_asso = $this->isAssoc($response['Message']['ProcessingReport']['Result']);
		          if($is_asso){
				$result = $response['Message']['ProcessingReport']['Result'];
			       if($result['ResultCode'] == 'Error'){
                                $this->db->query("UPDATE " . DB_PREFIX . "product SET amazon_error = CONCAT(amazon_error,'".$this->db->escape($result['ResultDescription'])."'), amazon_status = 'error' where model = '".$result['AdditionalInfo']['SKU']."'");
                            }
			   } else {
                        foreach($response['Message']['ProcessingReport']['Result'] as $result){ 
                            if($result['ResultCode'] == 'Error'){
                                $this->db->query("UPDATE " . DB_PREFIX . "product SET amazon_error = CONCAT(amazon_error,'".$this->db->escape($result['ResultDescription'])."'), amazon_status = 'error' where model = '".$result['AdditionalInfo']['SKU']."'");
                            }
                        }
			  }

                    }
                }
            } else if($type == 'image'){
                if(!isset($response['Error'])){
                    $this->db->query("UPDATE " . DB_PREFIX . "product SET amazon_image_update = '1' where amazon_image_feed_id = ".$feed_id);
                    $this->db->query("UPDATE " . DB_PREFIX . "amazon_feeds SET status = 'completed' where feed_id = ".$feed_id);
                }
            } else if($type == 'shipment'){
                if(!isset($response['Error'])){
		      $this->db->query("UPDATE " . DB_PREFIX . "amazon_feeds SET status = 'completed' where feed_id = ".$feed_id);
                    $this->db->query("UPDATE " . DB_PREFIX . "order_tracking SET tracking_status = '1' where amazon_feed_id = ".$feed_id);
                }
            } else if($type == 'price'){
                if(!isset($response['Error'])){
		      $this->db->query("UPDATE " . DB_PREFIX . "amazon_feeds SET status = 'completed' where feed_id = ".$feed_id);
                    $this->db->query("UPDATE " . DB_PREFIX . "product SET amazon_price_update = '1' where amazon_price_feed_id = ".$feed_id);
                }
            } else if($type == 'inventory'){
                if(!isset($response['Error'])){
		      $this->db->query("UPDATE " . DB_PREFIX . "amazon_feeds SET status = 'completed' where feed_id = ".$feed_id);
                    $this->db->query("UPDATE " . DB_PREFIX . "product SET inv_status = '0' where amazon_inventory_feed_id = ".$feed_id);
                }
            }
        }
	 public function insertOrder($orders,$seller_id) {
	     
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
                		$this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET temp_id = '".$f_id."', store_id = '0', amazon_seller_id = '".$seller_id."' store_name = 'Amazon', store_url = 'https://amazon.in',amazon_order_id = '".$order_array['AmazonOrderId']."', customer_id = '0', customer_group_id = '1', firstname = '" . $this->db->escape($order_array['BuyerName']) . "', lastname = '', email = '" . $this->db->escape($order_array['BuyerEmail']) . "', telephone = '" . $this->db->escape($order_array['ShippingAddress']['Phone']) . "', custom_field = '', payment_firstname = '" . $this->db->escape($order_array['BuyerName']) . "', payment_lastname = '', payment_company = '" . $this->db->escape($order_array['ShippingAddress']['CountryCode']) . "', payment_address_1 = '" . $this->db->escape($order_array['ShippingAddress']['AddressLine1']) . "', payment_address_2 = '".$order_array['ShippingAddress']['AddressLine2']."', payment_city = '" . $this->db->escape($order_array['ShippingAddress']['City']) . "', payment_postcode = '" . $this->db->escape($order_array['ShippingAddress']['PostalCode']) . "', payment_country = '" . $this->db->escape($order_array['ShippingAddress']['CountryCode']) . "', payment_country_id = '', payment_zone = '', payment_zone_id = '', payment_address_format = '', payment_custom_field = '', payment_method = '" . $this->db->escape($order_array['PaymentMethodDetails']['PaymentMethodDetail']) . "', payment_code = '', shipping_firstname = '" . $this->db->escape($order_array['BuyerName']) . "', shipping_lastname = '', shipping_company = '" . $this->db->escape($order_array['ShippingAddress']['CountryCode']) . "', shipping_address_1 = '" . $this->db->escape($order_array['ShippingAddress']['AddressLine1']) . "', shipping_address_2 = '".$order_array['ShippingAddress']['AddressLine2']."', shipping_city = '" . $this->db->escape($order_array['ShippingAddress']['City']) . "', shipping_postcode = '" . $this->db->escape($order_array['ShippingAddress']['PostalCode']) . "', shipping_country = '" . $this->db->escape($order_array['ShippingAddress']['CountryCode']) . "', shipping_country_id = '".$country_id."', shipping_zone = '', shipping_zone_id = '".$zone_id."', shipping_address_format = '', shipping_custom_field = '', shipping_method = '" . $this->db->escape($order_array['ShipmentServiceLevelCategory']) . "', shipping_code = '', comment = '', total = '" . (float)$order_array['OrderTotal']['Amount'] . "',order_status_id = '1', affiliate_id = '', commission = '', marketing_id = '', tracking = '', language_id = '1', currency_id = '4', currency_code = 'INR', currency_value = '1', ip = '', forwarded_ip = '', user_agent = '', accept_language = '', date_added = NOW(), date_modified = NOW()");		
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
                		$this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET temp_id = '".$f_id."',store_id = '0', amazon_seller_id = '".(int)$seller_id."', store_name = 'Amazon', store_url = 'https://amazon.in',amazon_order_id = '".$order_array['AmazonOrderId']."', customer_id = '0', customer_group_id = '1', firstname = '" . $this->db->escape($order_array['BuyerName']) . "', lastname = '', email = '" . $this->db->escape($order_array['BuyerEmail']) . "', telephone = '" . $this->db->escape($order_array['ShippingAddress']['Phone']) . "', custom_field = '', payment_firstname = '" . $this->db->escape($order_array['BuyerName']) . "', payment_lastname = '', payment_company = '" . $this->db->escape($order_array['ShippingAddress']['CountryCode']) . "', payment_address_1 = '" . $this->db->escape($order_array['ShippingAddress']['AddressLine1']) . "', payment_address_2 = '".$order_array['ShippingAddress']['AddressLine2']."', payment_city = '" . $this->db->escape($order_array['ShippingAddress']['City']) . "', payment_postcode = '" . $this->db->escape($order_array['ShippingAddress']['PostalCode']) . "', payment_country = '" . $this->db->escape($order_array['ShippingAddress']['CountryCode']) . "', payment_country_id = '', payment_zone = '', payment_zone_id = '', payment_address_format = '', payment_custom_field = '', payment_method = '" . $this->db->escape($order_array['PaymentMethodDetails']['PaymentMethodDetail']) . "', payment_code = '', shipping_firstname = '" . $this->db->escape($order_array['BuyerName']) . "', shipping_lastname = '', shipping_company = '" . $this->db->escape($order_array['ShippingAddress']['CountryCode']) . "', shipping_address_1 = '" . $this->db->escape($order_array['ShippingAddress']['AddressLine1']) . "', shipping_address_2 = '".$order_array['ShippingAddress']['AddressLine2']."', shipping_city = '" . $this->db->escape($order_array['ShippingAddress']['City']) . "', shipping_postcode = '" . $this->db->escape($order_array['ShippingAddress']['PostalCode']) . "', shipping_country = '" . $this->db->escape($order_array['ShippingAddress']['CountryCode']) . "', shipping_country_id = '".$country_id."', shipping_zone = '', shipping_zone_id = '".$zone_id."', shipping_address_format = '', shipping_custom_field = '', shipping_method = '" . $this->db->escape($order_array['ShipmentServiceLevelCategory']) . "', shipping_code = '', comment = '', total = '" . (float)$order_array['OrderTotal']['Amount'] . "',order_status_id = '1', affiliate_id = '', commission = '', marketing_id = '', tracking = '', language_id = '1', currency_id = '4', currency_code = 'INR', currency_value = '1', ip = '', forwarded_ip = '', user_agent = '', accept_language = '', date_added = NOW(), date_modified = NOW()");		
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
                		$this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET temp_id = '".$f_id."',store_id = '0', amazon_seller_id = '".(int)$seller_id."', store_name = 'Amazon', store_url = 'https://amazon.in',amazon_order_id = '".$order_array['AmazonOrderId']."', customer_id = '0', customer_group_id = '1', firstname = '" . $this->db->escape($order_array['BuyerName']) . "', lastname = '', email = '" . $this->db->escape($order_array['BuyerEmail']) . "', telephone = '" . $this->db->escape($order_array['ShippingAddress']['Phone']) . "', custom_field = '', payment_firstname = '" . $this->db->escape($order_array['BuyerName']) . "', payment_lastname = '', payment_company = '" . $this->db->escape($order_array['ShippingAddress']['CountryCode']) . "', payment_address_1 = '" . $this->db->escape($order_array['ShippingAddress']['AddressLine1']) . "', payment_address_2 = '".$order_array['ShippingAddress']['AddressLine2']."', payment_city = '" . $this->db->escape($order_array['ShippingAddress']['City']) . "', payment_postcode = '" . $this->db->escape($order_array['ShippingAddress']['PostalCode']) . "', payment_country = '" . $this->db->escape($order_array['ShippingAddress']['CountryCode']) . "', payment_country_id = '', payment_zone = '', payment_zone_id = '', payment_address_format = '', payment_custom_field = '', payment_method = '" . $this->db->escape($order_array['PaymentMethodDetails']['PaymentMethodDetail']) . "', payment_code = '', shipping_firstname = '" . $this->db->escape($order_array['BuyerName']) . "', shipping_lastname = '', shipping_company = '" . $this->db->escape($order_array['ShippingAddress']['CountryCode']) . "', shipping_address_1 = '" . $this->db->escape($order_array['ShippingAddress']['AddressLine1']) . "', shipping_address_2 = '".$order_array['ShippingAddress']['AddressLine2']."', shipping_city = '" . $this->db->escape($order_array['ShippingAddress']['City']) . "', shipping_postcode = '" . $this->db->escape($order_array['ShippingAddress']['PostalCode']) . "', shipping_country = '" . $this->db->escape($order_array['ShippingAddress']['CountryCode']) . "', shipping_country_id = '".$country_id."', shipping_zone = '', shipping_zone_id = '".$zone_id."', shipping_address_format = '', shipping_custom_field = '', shipping_method = '" . $this->db->escape($order_array['ShipmentServiceLevelCategory']) . "', shipping_code = '', comment = '', total = '" . (float)$order_array['OrderTotal']['Amount'] . "',order_status_id = '1', affiliate_id = '', commission = '', marketing_id = '', tracking = '', language_id = '1', currency_id = '4', currency_code = 'INR', currency_value = '1', ip = '', forwarded_ip = '', user_agent = '', accept_language = '', date_added = NOW(), date_modified = NOW()");		
	     		} else if ($order_array['OrderStatus'] == 'Canceled'){
	    			$this->db->query("UPDATE " . DB_PREFIX . "order SET order_status_id = '7' where amazon_order_id = '".$order_array['AmazonOrderId']."'");
	    		}
                    }

	    	} 
		}
             }
           }
	}
	public function getAmazonOrder($seller_id) {
            $query = $this->db->query("SELECT amazon_order_id FROM " . DB_PREFIX . "order WHERE order_status_id = '1' and store_name = 'Amazon' and amazon_seller_id = '".$seller_id."'");
	
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
	 			    $quantity_ordered = $product['ProductInfo']['NumberOfItems'];
			      		if (isset($product['QuantityOrdered']) && $product['QuantityOrdered'] > 0){
				  		$quantity_ordered = $product['QuantityOrdered'];
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
	 function isAssoc(array $arr) {
   	 if (array() === $arr) return false;
    		return array_keys($arr) !== range(0, count($arr) - 1);
	}
	public function getOrderTracking($seller_id) { 
            $query = $this->db->query("SELECT ot.*,o.amazon_order_id,o.shipping_method FROM " . DB_PREFIX . "order_tracking ot, " . DB_PREFIX . "order o WHERE o.order_id = ot.order_id and ot.tracking_status = '0' and seller_account_id = '".(int)$seller_id."'");
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
	 public function getSellerAccountId($product_id) {
            $query = $this->db->query("SELECT seller_id FROM " . DB_PREFIX . "seller_mapping WHERE product_id = '".$product_id."'");
            return $query->row['seller_id'];
        }
        public function getProductImages($product_id) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id =".$product_id);
            if ($query->num_rows) {
                return $query->rows;
            }
        }
	 public function getProductInventory($seller_id) {
	     if($seller_id == '1'){
            $query = $this->db->query("SELECT p.product_id,p.model,p.sku,p.quantity,p.amazon_status,p.inv_status FROM " . DB_PREFIX . "product p left join " . DB_PREFIX . "seller_mapping sm on(sm.product_id = p.product_id) WHERE sm.seller_id = ".$seller_id." and p.amazon_status ='listed' and p.inv_status = '1' limit 20000");
	     } else if($seller_id == '2'){
		$query = $this->db->query("SELECT p.product_id,p.model,p.sku,p.quantity,p.amazon_status,p.inv_status FROM " . DB_PREFIX . "product p left join " . DB_PREFIX . "seller_mapping sm on(sm.product_id = p.product_id) WHERE sm.seller_id = ".$seller_id." and p.amazon_status ='listed' limit 20000");

	     }
            if ($query->num_rows) {
                return $query->rows;
            }
        }
	 public function getProductName($name,$category) {
            if($category == 'Beauty & Health' || $category == 'Health & Beauty'){
                $listing_name = 'Segolike '.$name;
             } else if($category == 'Consumer Electronics' || $category == 'Video & Audio' || $category == 'Smart Device & Safety'){
		$listing_name = 'Jutek '.$name;
	     } else if($category == 'Computer & Office'){
	     	$listing_name = 'Jutek '.$name;
	     } else if($category == 'Automobiles & Motorcycles'){
	     	$listing_name = 'Jutek '.$name;
	     } else if($category == 'Sports & Entertainment' || $category == 'Sports & Outdoor'){
	     	$listing_name = 'Jutek '.$name;
	     } else if($category == 'Home & Garden' || $category == 'Test Equipment & Tools' || $category == 'Computer & Stationery'){
	     	$listing_name = 'Zenuss '.$name;
	     } else if($category == 'Watches'){
		$listing_name = 'Segolike '.$name;
	     } else if($category == 'Phones & Accessories' || $category == 'Cellphone & Accessories' || $category == 'Cameras & Photo Accessories'){
	     	$listing_name = 'Jutek '.$name;
	     } else if($category == 'Mother & Kids'){
	     	$listing_name = 'Segolike '.$name;
	     } else if($category == 'Toys & Hobbies'){
	     	$listing_name = 'Backgammon '.$name;
	     } else if($category == 'Bags'){
	     	$listing_name = 'Baggallini '.$name;
             } else if($category == 'Entertainment'){
	     	$listing_name = 'Jutek '.$name;
             } else if($category == 'Lights & Lighting'){
	     	$listing_name = 'Jutek '.$name;
	     } else if($category == 'Shoes'){
	     	$listing_name = 'Baggallini '.$name;
	     } else if($category == 'Jewelry' || $category == 'Apparel & Jewelry'){
	     	$listing_name = 'Segolike '.$name;
	     } else if($category == 'Automobiles & Motorcycles' || $category == 'Car Accessories'){
	     	$listing_name = 'Jutek '.$name;
             } else if($category == "Original Design-Women's Clothing"){
	     	$listing_name = 'Baggallini '.$name;
	     } else if($category == "Men's Clothing"){
	     	$listing_name = 'Baggallini '.$name;
             } else if($category == "Women's Clothing"){
	     	$listing_name = 'Baggallini '.$name;
	     } else if($category == "Tablets & Accessories"){
	     	$listing_name = 'Jutek '.$name;
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
	
}
