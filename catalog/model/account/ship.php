<?php
class ModelAccountShip extends Model {
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
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer where customer_id = ".$customer_id);
                if ($query->num_rows){
                    $this->db->query("UPDATE `" . DB_PREFIX . "customer` SET custom_company = '" . $this->db->escape($data['text']) . "' where customer_id =".$query->rows[0]['customer_id']);
                }

		//return $query->row['total'];
	}
        public function UpdateStatus($status,$customer_id) {
                if ($status){
                    $this->db->query("UPDATE `" . DB_PREFIX . "amazon_customer_settings` SET  status = '0' where customer_id =".$customer_id);
                } else{
                    $this->db->query("UPDATE `" . DB_PREFIX . "amazon_customer_settings` SET  status = '1' where customer_id =".$customer_id);
                }
	}
        public function getSettings($customer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer where customer_id = ".$customer_id);

		return $query->rows;
	}
}
