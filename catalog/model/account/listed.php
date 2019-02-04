<?php
class ModelAccountListed extends Model {
	public function addAddress($customer_id, $data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$customer_id . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', company = '" . $this->db->escape($data['company']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', city = '" . $this->db->escape($data['city']) . "', zone_id = '" . (int)$data['zone_id'] . "', country_id = '" . (int)$data['country_id'] . "', custom_field = '" . $this->db->escape(isset($data['custom_field']['address']) ? json_encode($data['custom_field']['address']) : '') . "'");

		$address_id = $this->db->getLastId();

		if (!empty($data['default'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
		}

		return $address_id;
	}

	public function getTotalProducts() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product");

		return $query->row['total'];
	}
        public function getTotalListedProducts($data,$customer_id) {
                
                $sql = "SELECT count(*) as total FROM " . DB_PREFIX . "amazon_product_listing pl LEFT JOIN " . DB_PREFIX . "product p on (p.product_id = pl.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";
                if (isset($data['category_id']) && $data['category_id'] != ''){
                      $sql .= " LEFT JOIN " . DB_PREFIX . "product_to_category pc ON (p.product_id = pc.product_id)";
                }
                $sql .= "where pd.language_id = '" . (int)$this->config->get('config_language_id') . "' and pl.customer_id = ".$customer_id." and pl.amazon_status = 'listed'";
                if (isset($data['filter_search']) && $data['filter_search'] != ''){
                    $sql .= " and (p.model like ('%".$data['filter_search']."%') or pd.name like ('%".$data['filter_search']."%'))";
                }
                if (isset($data['filter_stock_start']) && $data['filter_stock_start'] != ''){
                $sql .= " and p.quantity >= ".$data['filter_stock_start'];
                }
                if (isset($data['filter_stock_end']) && $data['filter_stock_end'] != ''){
                    $sql .= " and p.quantity <= ".$data['filter_stock_end'];
                }
                if (isset($data['category_id']) && $data['category_id'] != ''){
                    $sql .= " and pc.category_id in (".$data['category_id'].")";
                }
		$query = $this->db->query($sql);
                

		return $query->row['total'];
	}
        public function getListedProducts($data,$customer_id) {
                $sql = "SELECT * FROM " . DB_PREFIX . "amazon_product_listing pl LEFT JOIN " . DB_PREFIX . "product p on (p.product_id = pl.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";
                if (isset($data['category_id']) && $data['category_id'] != ''){
                      $sql .= " LEFT JOIN " . DB_PREFIX . "product_to_category pc ON (p.product_id = pc.product_id)";
                }
                $sql .= "where pd.language_id = '" . (int)$this->config->get('config_language_id') . "' and pl.customer_id = ".$customer_id." and pl.amazon_status = 'listed'";
                if (isset($data['filter_search']) && $data['filter_search'] != ''){
                    $sql .= " and (p.model like ('%".$data['filter_search']."%') or pd.name like ('%".$data['filter_search']."%'))";
                }
                if (isset($data['filter_stock_start']) && $data['filter_stock_start'] != ''){
                $sql .= " and p.quantity >= ".$data['filter_stock_start'];
                }
                if (isset($data['filter_stock_end']) && $data['filter_stock_end'] != ''){
                    $sql .= " and p.quantity <= ".$data['filter_stock_end'];
                }
                if (isset($data['category_id']) && $data['category_id'] != ''){
                    $sql .= " and pc.category_id in (".$data['category_id'].")";
                }
                if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		$query = $this->db->query($sql);
                

		return $query->rows;
	}
        public function getTotalNotListedProducts($array) {
                $string_products = implode(",",$array);
                $sql = "SELECT count(*) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' and p.product_id NOT IN (".$string_products.")";
		$query = $this->db->query($sql);

		return $query->row['total'];
	}
        public function getNotListedProducts($data,$array) {
                $string_products = implode(",",$array);
                $sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' and p.product_id NOT IN (".$string_products.")";
                if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		$query = $this->db->query($sql);

		return $query->rows;
	}
        public function getTotalNewProducts($array) {
                $current_date = gmdate("Y-m-d");
                $current_date = strtotime($current_date);
                $current_date = strtotime("-114 day", $current_date);
                $current_date = date("Y-m-d", $current_date);
                $string_products = implode(",",$array);
                $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' and p.date_added > date ('".$current_date."') and p.product_id NOT IN (".$string_products.")";
                //echo $sql;die;
                $query = $this->db->query($sql);

		return $query->row['total'];
	}
        public function getAllNewProducts($data,$array) {
                $current_date = gmdate("Y-m-d");
                $current_date = strtotime($current_date);
                $current_date = strtotime("-114 day", $current_date);
                $current_date = date("Y-m-d", $current_date);
                $string_products = implode(",",$array);
                $sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' and p.date_added > date ('".$current_date."') and p.product_id NOT IN (".$string_products.")";
                if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
                //echo $sql;die;
                $query = $this->db->query($sql);

		return $query->rows;
	}
        public function getTotalRestrictedProducts($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "amazon_product_listing where customer_id = ".$customer_id." and amazon_status = 'restricted'");

		return $query->row['total'];
	}
        public function getTotalErrorProducts($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "amazon_product_listing where customer_id = ".$customer_id." and amazon_status = 'error'");

		return $query->row['total'];
	}
        public function ListAmazon($products,$customer_id) {
                foreach($products as $product){
                    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "amazon_product_listing where product_id = '".$product."'");
                    if ($query->num_rows == 0){
                        $sql = "SELECT * FROM " . DB_PREFIX . "product where product_id = '".$product."'";
                        $query = $this->db->query($sql);
                        $this->db->query("INSERT INTO " . DB_PREFIX . "amazon_product_listing SET customer_id = '" . (int)$customer_id . "', product_id = '" . (int)$query->row['product_id'] . "', model = '" . $this->db->escape($query->row['model']) . "', sku = '" . $this->db->escape($query->row['sku']) . "', price = '" . $query->row['price'] . "', listing_status_id = '1', date_added = now()");
                    }
                }
	}
        public function getProductCategory($product_id) {
            $query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "product_to_category p2c  WHERE product_id = '" . (int)$product_id . "'");
            $category_name = '';
            if ($query->num_rows) {
                $data = $query->row;
                $category_array = array();
                 $query1 = $this->db->query("SELECT c.category_id,c.parent_id,cd.name FROM " . DB_PREFIX . "category c, " . DB_PREFIX . "category_description cd  WHERE c.category_id = cd.category_id and c.category_id = ".$data['category_id']);
                    if ($query1->num_rows) {
                        $category_name = $query1->row['name'];
                        if($query1->row['parent_id'] == 0){
                            $category_array = array(
                            'category_id' => $query1->row['category_id'],
                            'parent_id' => $query1->row['parent_id'],
                            'name' => $query1->row['name'],
                        );
                        } else{
                             $query2 = $this->db->query("SELECT c.category_id,c.parent_id,cd.name FROM " . DB_PREFIX . "category c, " . DB_PREFIX . "category_description cd  WHERE c.category_id = cd.category_id and c.category_id = ".$query1->row['parent_id']);
                                if ($query2->num_rows) {
                                    $category_name = $query2->row['name'].' >> '.$category_name;
                                    if($query2->row['parent_id'] == 0){
                                        $category_array = array(
                                        'category_id' => $query2->row['category_id'],
                                        'parent_id' => $query2->row['parent_id'],
                                        'name' => $query2->row['name'],
                                    );
                                    } else{
                                        $query3 = $this->db->query("SELECT c.category_id,c.parent_id,cd.name FROM " . DB_PREFIX . "category c, " . DB_PREFIX . "category_description cd  WHERE c.category_id = cd.category_id and c.category_id = ".$query2->row['parent_id']); 
                                        if ($query3->num_rows) {
                                            $category_name = $query3->row['name'].' >> '.$category_name;
                                            if($query3->row['parent_id'] == 0){
                                                $category_array = array(
                                                'category_id' => $query3->row['category_id'],
                                                'parent_id' => $query3->row['parent_id'],
                                                'name' => $query3->row['name'],
                                            );
                                            } else{
                                                $query4 = $this->db->query("SELECT c.category_id,c.parent_id,cd.name FROM " . DB_PREFIX . "category c, " . DB_PREFIX . "category_description cd  WHERE c.category_id = cd.category_id and c.category_id = ".$query3->row['parent_id']);     
                                                if ($query4->num_rows) {
                                                    $category_name = $query4->row['name'].' >> '.$category_name;
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
            //return $category_array;
             return $category_name;
        }
}
