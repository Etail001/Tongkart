<?php
class ModelChinaboardChinaboard extends Model {
	
	public function insertCategoryList( $data ) {
		
		//echo '<pre>';
		//print_r($data);die;
		
		$countCategory = count($data['msg']);
		for($i=0;$i<$countCategory;$i++)
		{
			$this->db->query("INSERT INTO " . DB_PREFIX . "category SET category_id = '" . $this->db->escape($data['msg'][$i]['cat_id']) . "', 
				parent_id = '" . $this->db->escape($data['msg'][$i]['parent_id']) . "'");
				
			$this->db->query("INSERT INTO " . DB_PREFIX . "category_description SET category_id = '" . $this->db->escape($data['msg'][$i]['cat_id']) . "', 
				language_id = '1', name = '" . $this->db->escape($data['msg'][$i]['cat_name']) . "', meta_title = '" . $this->db->escape($data['msg'][$i]['cat_name']) . "'");
		}
	}
	
	public function insertDownloadList( $data ) {
		$countCategory = count($data['msg']['page_result']);
		for($i=0;$i<$countCategory;$i++)
		{		$sn = $this->db->query("SELECT * from " . DB_PREFIX . "chinabrand_good WHERE goods_sn = '" . $this->db->escape($data['msg']['page_result'][$i]['goods_sn']) . "'");
				if($sn->num_rows == 0){
					$this->db->query("INSERT INTO " . DB_PREFIX . "chinabrand_good SET goods_sn = '" . $this->db->escape($data['msg']['page_result'][$i]['goods_sn']) . "', 
				is_tort = '" . $this->db->escape($data['msg']['page_result'][$i]['is_tort']) . "'");
				}
			       
		}
	}
	
	public function getGoods()
	{
		$query = $this->db->query("select goods_sn from oc_chinabrand_good");
		
		return $query->rows;
	}
	public function getProductSku()
	{
		$query = $this->db->query("select sku from oc_product where amazon_status = 'listed'");
		
		return $query->rows;
	}
	public function getOrders()
	{
		$query = $this->db->query("select * from oc_order where amazon_china_status = '0' and order_status_id != '7' and amazon_seller_id = '1'"); 
		
		return $query->rows;
	}
	public function getOrdersAmount($order_id)
	{
		$query = $this->db->query("select * from oc_order_product where order_id = '".$order_id."'");
		$amount = 0;
		foreach($query->rows as $product){
		     $query_product = $this->db->query("select * from oc_product where product_id = '".$product['product_id']."'");
		     $amount += $query_product->rows[0]['original_price'];
		}
		return $amount;
	}
	public function getGoodsInfo($order_id)
	{	  
		  $product_arr = array();
		  $product_all = array();
		  //foreach($goods as $gn){
		  $query = $this->db->query("select * from oc_order_product where order_id = '".$order_id."'");
		  $product_arr = array();
			foreach($query->rows as $product){
		     		$query_product = $this->db->query("select * from oc_product where product_id = '".$product['product_id']."'");
		     		$product_arr[] = array(
				'goods_sn' => $query_product->rows[0]['sku'],
				'goods_number' => $product['quantity']
		     	);
			//array_push($product_all,$product_arr); 
			}
		//}
		
		return $product_arr;
	}
	
	public function insertProductInfo( $data ) {
		
		//echo '<pre>';
		//print_r($data);die;
		$countProduct = count($data['msg']);
		$countp =0;
		for($i=0;$i<$countProduct;$i++)
		{
			if(isset($data['msg'][$i]['status']) && $data['msg'][$i]['status'] == 1)
			{	
				 
				$countp++;
				$modal = 'CB_'.$data['msg'][$i]['sku']; 
				$man = $this->db->query("SELECT manufacturer_id,name from " . DB_PREFIX . "manufacturer WHERE name = '" . $this->db->escape($data['msg'][$i]['goods_brand']) . "'");
				if($man->num_rows != 0){
					$last_manu_id = $man->rows[0]['manufacturer_id'];
				} else{
					$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer SET name = '" . $this->db->escape($data['msg'][$i]['goods_brand']) . "'");
					$last_manu_id = $this->db->getLastId();
					$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_to_store SET manufacturer_id = '" . $last_manu_id . "',store_id='0'");
				}

				$product = $this->db->query("SELECT product_id,model from " . DB_PREFIX . "product WHERE model = '" . $this->db->escape($modal) . "'");
				if(empty($data['msg'][$i]['map'])){
				   $data['msg'][$i]['map'][0]['limit_price'] = 0;
				}
				if(isset($data['msg'][$i]['warehouse_list']['YB'])){
					$price_original = $data['msg'][$i]['warehouse_list']['YB']['price'];
				} else if(isset($data['msg'][$i]['warehouse_list']['ESTJWH'])){
					$price_original = $data['msg'][$i]['warehouse_list']['ESTJWH']['price'];
				} else if(isset($data['msg'][$i]['warehouse_list']['MXTJWH'])){
					$price_original = $data['msg'][$i]['warehouse_list']['MXTJWH']['price']; 
				} else {
					$price_original = 0.00;
				}
				$price_store = $price_original*71.01;
				if($product->num_rows != 0){ 
					$this->db->query("UPDATE " . DB_PREFIX . "product SET original_price = '" . $price_original . "',price = '".$price_store."' where product_id = ".$product->rows[0]['product_id']);
					$this->db->query("UPDATE " . DB_PREFIX . "product_description SET size = '" . $this->db->escape($data['msg'][$i]['size']) . "' where product_id = ".$product->rows[0]['product_id']." and language_id = '1'");
				} else{
					$this->db->query("INSERT INTO " . DB_PREFIX . "product SET model = '" . $this->db->escape($modal) . "', 
					sku = '" . $this->db->escape($data['msg'][$i]['sku']) . "',image = '" . $this->db->escape($data['msg'][$i]['original_img'][0]) . "',
					manufacturer_id = '" . $last_manu_id . "',original_price = '" . $this->db->escape($price_original) . "',price = '" . $this->db->escape($price_store) . "',weight = '" . $this->db->escape($data['msg'][$i]['volume_weight']) . "',
					length = '" . $this->db->escape($data['msg'][$i]['package_length']) . "',width = '" . $this->db->escape($data['msg'][$i]['package_width']) . "',
					height = '" . $this->db->escape($data['msg'][$i]['package_height']) . "'");
				
				$last_id = $this->db->getLastId();	
				
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . $this->db->escape($last_id) . "', 
					language_id = '1',name = '" . $this->db->escape($data['msg'][$i]['title']) . "',
					description = '" . $this->db->escape($data['msg'][$i]['goods_desc']) . "',color = '" . $this->db->escape($data['msg'][$i]['color']) . "',size = '" . $this->db->escape($data['msg'][$i]['size']) . ",
					meta_title = '" . $this->db->escape($data['msg'][$i]['title']) . "'");
				
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . $this->db->escape($last_id) . "', 
					category_id = '" . $this->db->escape($data['msg'][$i]['cat_id']) . "'");
					
				$imageCount = count($data['msg'][$i]['original_img']);	
				for($j=0;$j<$imageCount;$j++)
				{
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . $this->db->escape($last_id) . "', 
					image = '" . $this->db->escape($data['msg'][$i]['original_img'][$j]) . "'");
				}
				}
					
			}	
		}
		
	}
	public function updateProductStock( $data ) {
		$products_stock = $data['msg']['page_result'];
                foreach($products_stock as $stock){
                    if($stock['status'] == 1){
                        $sn = $this->db->query("SELECT quantity from " . DB_PREFIX . "product WHERE sku = '" . $this->db->escape($stock['goods_sn']) . "'");
                        if($sn->num_rows != 0 && $sn->rows[0]['quantity'] != $stock['goods_number']){
                                $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = " . (int)$stock['goods_number'] . ",inv_status = '1' where sku = '".$stock['goods_sn']."'"); 
                        }
                    }
                }
        
		
	} 
	public function updateOrder($data,$order_id)
        {   
	     if(isset($data['msg'][$order_id]['msg']) && $data['msg'][$order_id]['msg'] == 'success'){
                $this->db->query("UPDATE " . DB_PREFIX . "order SET amazon_china_status = '1' where temp_id = '".$order_id."'"); 
            }
		
	}
			
}
