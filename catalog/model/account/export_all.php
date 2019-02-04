<?php
class ModelAccountExportAll extends Model {
	public function login($username, $key) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "api` WHERE `username` = '" . $this->db->escape($username) . "' `key` = '" . $this->db->escape($key) . "' AND status = '1'");

		return $query->row;
	}

	public function addApiSession($api_id, $session_id, $ip) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "api_session` SET api_id = '" . (int)$api_id . "', session_id = '" . $this->db->escape($session_id) . "', ip = '" . $this->db->escape($ip) . "', date_added = NOW(), date_modified = NOW()");

		return $this->db->getLastId();
	}

	public function getApiIps($api_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "api_ip` WHERE api_id = '" . (int)$api_id . "'");

		return $query->rows;
	}
        public function getTotalUpc($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "amazon_upc where customer_id =".$customer_id);

		return $query->row['total'];
	}
        public function getTotalUsedUpc($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "amazon_upc where customer_id =".$customer_id." and status = '1'");

		return $query->row['total'];
	}
        public function getTotalUnUsedUpc($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "amazon_upc where customer_id =".$customer_id." and status = '0'");

		return $query->row['total'];
	}
        public function getTotalNewProducts() {
                $current_date = gmdate("Y-m-d");
                $current_date = strtotime($current_date);
                $current_date = strtotime("-14 day", $current_date);
                $current_date = date("Y-m-d", $current_date);
                $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product where date_added > date ('".$current_date."')");

		return $query->row['total'];
	}
        public function getTotalListedProducts($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "amazon_product_listing where customer_id = ".$customer_id." and amazon_status = 'listed'");

		return $query->row['total'];
	}
        public function getTotalRestrictedProducts($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "amazon_product_listing where customer_id = ".$customer_id." and amazon_status = 'restricted'");

		return $query->row['total'];
	}
        public function getTotalErrorProducts($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "amazon_product_listing where customer_id = ".$customer_id." and amazon_status = 'error'");

		return $query->row['total'];
	}
        public function editSettings($data,$customer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "amazon_customer_settings where customer_id = ".$customer_id);
                if ($query->num_rows){
                    $this->db->query("UPDATE `" . DB_PREFIX . "amazon_customer_settings` SET  seller_id = '" . $this->db->escape($data['meta_title']) . "', auth_token = '" . $this->db->escape($data['meta_title_token']) . "' where setting_id =".$query->rows[0]['setting_id']);
                } else{
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "amazon_customer_settings` SET customer_id = '" . (int)$customer_id . "', seller_id = '" . $this->db->escape($data['meta_title']) . "', auth_token = '" . $this->db->escape($data['meta_title_token']) . "',status = '1'");
                }

		//return $query->row['total'];
	}
        public function getSettings($customer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "amazon_customer_settings where customer_id = ".$customer_id);

		return $query->rows;
	}
        public function deleteUpc($upc,$customer_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "amazon_upc where upc = '".$upc."' and customer_id = ".$customer_id);
	}
        public function insertUpc($data,$customer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "amazon_upc where upc = '".$data."' and customer_id = ".$customer_id);
                if ($query->num_rows){
                } else{
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "amazon_upc` SET customer_id = '" . (int)$customer_id . "', upc = '" . $this->db->escape($data) . "', date_added = now() ,status = '0'");
                }

		//return $query->row['total'];
	}
        public function getUpcExport($limit,$customer_id) {
                $sql = "SELECT * FROM " . DB_PREFIX . "amazon_upc where customer_id = ".$customer_id." and status = '0'";
                $sql .= " order by upc_id ASC";
                if (isset($limit) && $limit != ''){
                    $sql .= " limit ".$limit;
                }
		$query = $this->db->query($sql);

		return $query->rows;
	}
        public function getTotalMonitorStatus($customer_id) {
                $sql = "SELECT count(*) as total FROM " . DB_PREFIX . "amazon_upc_monitor where customer_id = ".$customer_id;
		$query = $this->db->query($sql);

		return $query->row['total'];
	}
        public function getMonitorStatus($data,$customer_id) {
                $sql = "SELECT * FROM " . DB_PREFIX . "amazon_upc_monitor where customer_id = ".$customer_id;
                $sql .= " order by monitor_id DESC";
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
        public function insertUpcExportMonitor($path,$customer_id) {
                    $current_date = date('Y-m-d H:i:s');
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "amazon_upc_monitor` SET customer_id = '" . (int)$customer_id . "', path = '" . $this->db->escape($path) . "',date_added = '".$current_date."'");
                
	}
        public function getDataExport($skus) { 
            $array_sku = array();
            foreach(preg_split("/((\r?\n)|(\r\n?))/", $skus) as $line){
                $array_sku[] = "'".$line."'";
            }
            $string_sku = implode(",",$array_sku);
            $sql = "SELECT p.*,pd.description,pd.amazon_description,pd.name,pd.color,pd.size FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
            $sql .= " and ( p.model IN (".$string_sku.") or p.sku IN (".$string_sku."))";
            $sql .= " GROUP BY p.product_id";
            $sql .= " ORDER BY p.product_id";
            $query = $this->db->query($sql);

            return $query->rows;
        }
        public function getProductImage($product_id) {
            $query = $this->db->query("SELECT image FROM " . DB_PREFIX . "product_image  WHERE product_id = '" . (int)$product_id . "' limit 8");
            if ($query->num_rows) {
                return $query->rows;
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
