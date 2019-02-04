<?php
class ModelAccountNotListed extends Model {
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
        public function getListedProducts($customer_id) {
		$query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "amazon_product_listing where customer_id = ".$customer_id." and amazon_status = 'listed' or listing_status_id = '1'");

		return $query->rows;
	}
        public function getTotalNotListedProducts($customer_id) {
                $sql = "SELECT count(*) AS total FROM " . DB_PREFIX . "amazon_product_listing pl LEFT JOIN " . DB_PREFIX . "product_description pd ON (pl.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' and pl.amazon_status = 'error' and pl.customer_id =".$customer_id;
		$query = $this->db->query($sql);

		return $query->row['total'];
	}
        public function getNotListedProducts($data,$customer_id) {
                $sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "amazon_product_listing pl ON (p.product_id = pl.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' and pl.amazon_status = 'error' and pl.customer_id =".$customer_id;
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
                    } else {
                        $this->db->query("UPDATE " . DB_PREFIX . "amazon_product_listing SET amazon_status = '',listing_status_id = '1' WHERE customer_id = '" . (int)$customer_id . "' and product_id = '".$product."'");
                    }
                }
	}
}
