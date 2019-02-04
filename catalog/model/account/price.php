<?php
class ModelAccountPrice extends Model {
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
        public function getTotalProducts() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product");

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
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "amazon_price_settings where customer_id = ".$customer_id." and category_id = '".$data['category']."'");
                if ($query->num_rows){
                    $this->db->query("UPDATE `" . DB_PREFIX . "amazon_price_settings` SET sp = '" . $this->db->escape($data['sp']) . "', mrp = '" . $this->db->escape($data['mrp']) . "' , mirp = '" . $this->db->escape($data['mirp']) . "' where price_id =".$query->rows[0]['price_id']);
                } else{
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "amazon_price_settings` SET customer_id = '" . (int)$customer_id . "', category_id = '" . $this->db->escape($data['category']) . "', sp = '" . $this->db->escape($data['sp']) . "', mrp = '" . $this->db->escape($data['mrp']) . "', mirp = '" . $this->db->escape($data['mirp']) . "'");
                }
                $this->db->query("UPDATE `" . DB_PREFIX . "amazon_product_listing` SET amazon_price_update = '0' where customer_id =".$customer_id);

		//return $query->row['total'];
	}
        public function getSettings($customer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "amazon_price_settings where customer_id = ".$customer_id);

		return $query->rows;
	}
        public function getTotalPriceSet($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "amazon_price_settings where customer_id = ".$customer_id);

		return $query->row['total'];
	}
        public function getBrandSet($data,$customer_id) {
                $sql = "SELECT * FROM " . DB_PREFIX . "amazon_price_settings s left join " . DB_PREFIX . "category_description cd on (s.category_id = cd.category_id) where customer_id = ".$customer_id;
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
        public function getCategories() {
		$query = $this->db->query("SELECT c.category_id,cd.name FROM " . DB_PREFIX . "category c left join " . DB_PREFIX . "category_description cd on (c.category_id = cd.category_id) where c.parent_id = '0' and c.category_id != '0'");
                
		return $query->rows;
	}
}
