<?php
class ModelQuarkQuark extends Model {
	
	public function insertCategoryList( $data ) {
		//echo '<pre>';
		//print_r($data);die;
		$countCategory = count($data->data);
		for($i=0;$i<$countCategory;$i++)
		{
			$sn = $this->db->query("SELECT * from " . DB_PREFIX . "category_description WHERE name = '" . $this->db->escape($data->data[$i]->name) . "'");
			if($sn->num_rows == 0){
					$this->db->query("INSERT INTO " . DB_PREFIX . "category SET category_id = '" . $this->db->escape($data->data[$i]->_id) . "', 
				parent_id = '" . $this->db->escape($data->data[$i]->parent_id) . "'");
				
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_description SET category_id = '" . $this->db->escape($data->data[$i]->_id) . "', 
				language_id = '1', name = '" . $this->db->escape($data->data[$i]->name) . "'");
			}
		}
		
	}
	
	public function insertProductListUpdate( $data ) {
		echo '<pre>';
		print_r($data);die;
		$countCategory = count($data->data);
		for($i=0;$i<$countCategory;$i++)
		{
			$sn = $this->db->query("SELECT * from " . DB_PREFIX . "product WHERE sku = '" . $this->db->escape($data->data[$i]->sku) . "'");
					if(!empty($data->data[$i]->first_category))
					{
						$category_name = $data->data[$i]->first_category;
						$category_id = $data->data[$i]->first_category_id;
					}
					else if(!empty($data->data[$i]->sub_category))
					{
						$category_name = $data->data[$i]->sub_category;
						$category_id = $data->data[$i]->sub_category_id;
					}
					else if(!empty($data->data[$i]->forth_category))
					{
						$category_name = $data->data[$i]->forth_category;
						$category_id = $data->data[$i]->forth_category_id;
					}
				
				foreach($sn->rows as $pro)
				{
					
					//echo $category_id;die;
					if($sn->num_rows == 0){
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . $this->db->escape($pro['product_id']) . "', 
									category_id = '" . $this->db->escape($category_id) . "'");
					}
					else
					{
						$this->db->query("UPDATE " . DB_PREFIX . "product_to_category SET category_id = '" . $this->db->escape($category_id) . "' 
										 where product_id = '".$pro['product_id']."'");
					}
					$snn = $this->db->query("SELECT * from " . DB_PREFIX . "category_description WHERE category_id = '" . $this->db->escape($category_id) . "'");
					if($sn->num_rows == 0){
							
						$this->db->query("INSERT INTO " . DB_PREFIX . "category_description SET category_id = '" . $this->db->escape($category_id) . "', 
						language_id = '1', name = '" . $this->db->escape($category_name) . "'");
					}
					else
					{
						$this->db->query("UPDATE " . DB_PREFIX . "category_description SET category_id = '" . $this->db->escape($category_id) . "', 
						language_id = '1', name = '" . $this->db->escape($category_name) . "' where category_id='".$this->db->escape($category_id)."'");
					}
				}
			
		}
		
	}
	
	public function getOrders()
	{
		$query = $this->db->query("select a.*,b.model from oc_order as a inner join oc_order_product as b on a.order_id=b.order_id
					where amazon_china_status = '0' and order_status_id != '7' and amazon_seller_id = '2'"); 
		return $query->rows; 
	}
	public function getSupplireOrders()
	{
		$query = $this->db->query("select supplire_order_id,wms_order_id from oc_supplire_orders where seller_id='2'"); 
		return $query->rows; 
	}
	public function insertSupplireOrderImport( $data,$supplire_orderId,$wms_OrderID ) {
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "supplire_order_import SET supplire_order_id = '" . $this->db->escape($supplire_orderId) . "', 
						item_id = '" . $this->db->escape($data->data[0]->item_id) . "',ship_confirm_date = '" . $this->db->escape($data->data[0]->ship_confirm_date) . "',
						order_no = '" . $this->db->escape($data->data[0]->order_no) . "',track_number = '" . $this->db->escape($data->data[0]->track_number) . "'
						,track_updated = '" . $this->db->escape($data->data[0]->track_updated) . "',order_refund_border = '" . $this->db->escape($data->data[0]->order_refund_border) . "',
						refund_verify_type = '" . $this->db->escape($data->data[0]->refund_verify_type) . "',return_date = '" . $this->db->escape($data->data[0]->return_date) . "',
						sale_status = '" . $this->db->escape($data->data[0]->sale_status) . "',shipping_company_id = '" . $this->db->escape($data->data[0]->shipping_company_id) . "',
						shipping_cost = '" . $this->db->escape($data->data[0]->shipping_cost) . "',shipping_type = '" . $this->db->escape($data->data[0]->shipping_type) . "',
						shipping_verify_date = '" . $this->db->escape($data->data[0]->shipping_verify_date) . "',shipping_weight = '" . $this->db->escape($data->data[0]->shipping_weight) . "',
						supp_order_id = '" . $this->db->escape($data->data[0]->order_id) . "',order_status = '" . $this->db->escape($data->data[0]->order_status) . "'
						,localTrackNumber = '" . $this->db->escape($data->data[0]->localTrackNumber) . "',is_register = '" . $this->db->escape($data->data[0]->is_register) . "',
						clientTrackNumber = '" . $this->db->escape($data->data[0]->clientTrackNumber) . "',sku = '" . $this->db->escape($data->data[0]->sku[0]) . "',
						sku_qty = '" . $this->db->escape($data->data[0]->sku_qty) . "',sku_type = '" . $this->db->escape($data->data[0]->sku_type) . "',
						spu = '" . $this->db->escape($data->data[0]->spu) . "',stock_id = '" . $this->db->escape($data->data[0]->stock_id) . "',
						stock_code = '" . $this->db->escape($data->data[0]->stock_code) . "',shipping_method = '" . $this->db->escape($data->data[0]->shipping_method) . "',
						shipping_method_en_name = '" . $this->db->escape($data->data[0]->shipping_method_en_name) . "',
						shipping_method_ch_name = '" . $this->db->escape($data->data[0]->shipping_method_ch_name) . "'");
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
		$query = $this->db->query("select * from oc_order_product where order_id = '".$order_id."'");
		$product_arr = array();
		foreach($query->rows as $product){
		     $query_product = $this->db->query("select * from oc_product where product_id = '".$product['product_id']."'");
		     $product_arr[] = array(
				'goods_sn' => $query_product->rows[0]['sku'],
				'goods_number' => $product['quantity']
		     ); 
		}
		return $product_arr;
	}
	
	public function insertProductList( $data ) {
		echo '<pre>';
		print_r($data);die;
		if(!empty($data->data))
		{
			$countProduct = count($data->data);
			if($countProduct > 0)
			{
				 
				$countp =0;
				for($i=0;$i<$countProduct;$i++)
				{
					
					$countp++;
					$modal = 'QS_'.$data->data[$i]->sku; 
					$man = $this->db->query("SELECT manufacturer_id,name from " . DB_PREFIX . "manufacturer WHERE name = '" . $this->db->escape($data->data[$i]->brand) . "'");
					if($man->num_rows != 0){
						$last_manu_id = $man->rows[0]['manufacturer_id'];
					} else{
						$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer SET name = '" . $this->db->escape($data->data[$i]->brand) . "'");
						$last_manu_id = $this->db->getLastId();
						$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_to_store SET manufacturer_id = '" . $last_manu_id . "',store_id='0'");
					}

					$product = $this->db->query("SELECT product_id,sku from " . DB_PREFIX . "product WHERE sku = '" . $this->db->escape($data->data[$i]->sku) . "'");
					if(empty($data->data[$i]->price)){
						$data->data[$i]->price = 0;
					}
					
					$price_original = $data->data[$i]->price;
					$price_store = $price_original*71.01;
					if($product->num_rows != 0){ 
						$this->db->query("UPDATE " . DB_PREFIX . "product SET original_price = '" . $price_original . "',price = '".$price_store."' where product_id = ".$product->rows[0]['product_id']);
						$this->db->query("UPDATE " . DB_PREFIX . "quark_spus SET status = '1' where warehouse_name='CN' and spus = '".$data->data[$i]->spu."'");
					} else{
						$this->insertCategoryList();
						$this->db->query("INSERT INTO " . DB_PREFIX . "product SET model = '" . $this->db->escape($modal) . "', sku = '" . $this->db->escape($data->data[$i]->sku) . "',quantity = '" . $this->db->escape($data->data[$i]->stock) . "',image = '" . $this->db->escape($data->data[$i]->image_main) . "',manufacturer_id = '" . $last_manu_id . "',original_price = '" . $this->db->escape($price_original) . "',price = '" . $this->db->escape($price_store) . "',weight = '" . $this->db->escape($data->data[$i]->weight) . "',length = '" . $this->db->escape($data->data[$i]->length) . "',width = '" . $this->db->escape($data->data[$i]->width) . "',height = '" . $this->db->escape($data->data[$i]->height) . "'");
					
						$last_id = $this->db->getLastId();	
						if(!empty($data->data[$i]->spu_items->color))
						{
							$color = $data->data[$i]->spu_items->color;
						}
						else{
							$color = "N/A";
						}
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . $this->db->escape($last_id) . "',language_id = '1',name = '" . $this->db->escape($data->data[$i]->product_name) . "',description = '" . $this->db->escape($data->data[$i]->product_description) . "',color = '" . $this->db->escape($color) . "'");
						
						if(empty($data->data[$i]->first_category_id))
						{
							$cat_sub_id = $data->data[$i]->sub_category_id;
						}
						else
						{
							$cat_sub_id = $data->data[$i]->first_category_id;
						}
						
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . $this->db->escape($last_id) . "', category_id = '" . $this->db->escape($cat_sub_id) . "'");
							
						$this->db->query("INSERT INTO " . DB_PREFIX . "seller_mapping SET product_id = '" . $this->db->escape($last_id) . "',seller_id = '2'");
							
						$imageCount = count($data->data[$i]->image_gallery);	
						for($j=0;$j<$imageCount;$j++)
						{
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . $this->db->escape($last_id) . "', image = '" . $this->db->escape($data->data[$i]->image_gallery[$j]) . "'");
						}
						$this->db->query("UPDATE " . DB_PREFIX . "quark_spus SET status = '1' where warehouse_name='CN' and spus = spus = '".$data->data[$i]->spu."'");
					}
				}
			} 
		}
		else
		{
			$exist_spu = "NULL";
			$not_exist_spu = "NULL";
			$brand_unauthorized_spu = "NULL";
			$countExistSpu = $data;
			if($data->status == 'fail' || $data->code == 402 || $data->code == 401)
			{
				$this->db->query("INSERT INTO " . DB_PREFIX . "quark_spus_fetch_error SET code='".$this->db->escape($data->code)."',status='".$this->db->escape($data->status)."', message='".$this->db->escape($data->message)."' ");
			}
			else
			{
				foreach($countExistSpu as $val){
					 $exist_spu = $val;
					 $this->db->query("INSERT INTO " . DB_PREFIX . "quark_spus_fetch_error SET exist_spu = '".$exist_spu."',not_exist_spu = '".$not_exist_spu."',brand_unauthorized_spu='".$exist_spu."',message='".$this->db->escape("Brand is unauthorized, please check.")."' ");
					 $this->db->query("UPDATE " . DB_PREFIX . "quark_spus SET status = '2' where warehouse_name='CN' and spus = '".$exist_spu."'");
				}
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
		$query = $this->db->query("select sku from oc_product where model LIKE '%QS%' ");
		return $query->rows;
	}
	
	public function getProductSpus()
	{
		$query = $this->db->query("select spus from oc_quark_spus where warehouse_name='CN' and status='0'");
		return $query->rows;
	}
	
	public function getProductSpusUpdate()
	{
		$query = $this->db->query("select spus from oc_quark_spus where warehouse_name='CN' and status='0'");
		return $query->rows;
	}
	
	
	public function updateProductStock( $data ) {
		if($data->code = 200)
		{
			if(!empty($data->data))
			{
				$countSku = count($data->data);
				for($i=0;$i<$countSku;$i++){
					$price_original = $data->data[$i]->price;
					$price_store = $price_original*71.01;
						
					if($data->code == 200){
						$sn = $this->db->query("SELECT quantity from " . DB_PREFIX . "product WHERE sku = '" . $data->data[$i]->sku . "'");
						if($sn->num_rows != 0 && $sn->rows[0]['quantity'] != $data->data[$i]->quantity){
								$this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = " . (int)$data->data[$i]->quantity . ",price = " . (int)$price_store . ",original_price = " . (int)$data->data[$i]->price . ",inv_status = '1' where sku = '".$data->data[$i]->sku."'"); 
						}
					}
				}
			}
		}
	} 
	public function updateOrder($data,$order_id,$originalOrder_id)
	{      $data = json_decode($data, true);
		//echo "<pre>";print_r($data);die;
		if(isset($data['details'][0]['status']) && $data['details'][0]['status'] == 'success'){
                $this->db->query("UPDATE " . DB_PREFIX . "order SET amazon_china_status = '1' where temp_id = '".$order_id."'"); 
                $this->db->query("INSERT INTO " . DB_PREFIX . "supplire_orders SET supplire_order_id = '".$data['details'][0]['orderNo']."',
                order_id = '".$originalOrder_id."',
                wms_order_id = '".$order_id."',info='".$data['details'][0]['info']."',status='".$data['details'][0]['status']."',seller_id='2'"); 
            }
		
	}		
}
