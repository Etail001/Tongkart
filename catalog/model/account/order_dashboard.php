<?php
class ModelAccountOrderDashboard extends Model {
	public function addAddress($customer_id, $data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$customer_id . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', company = '" . $this->db->escape($data['company']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', city = '" . $this->db->escape($data['city']) . "', zone_id = '" . (int)$data['zone_id'] . "', country_id = '" . (int)$data['country_id'] . "', custom_field = '" . $this->db->escape(isset($data['custom_field']['address']) ? json_encode($data['custom_field']['address']) : '') . "'");

		$address_id = $this->db->getLastId();

		if (!empty($data['default'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
		}

		return $address_id;
	}

	public function getTotalProducts() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product where amazon_status != 'restricted'");

		return $query->row['total'];
	}
        public function getTotalOrders($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order where customer_id = ".$customer_id);

		return $query->row['total'];
	}
        public function getTotalAwaitingPaymentOrders($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order where order_status_id = '20' and customer_id = ".$customer_id);

		return $query->row['total'];
	}
        public function getTotalProcessingOrders($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order where order_status_id = '2' and customer_id = ".$customer_id);

		return $query->row['total'];
	}
        public function getTotalPendingOrders($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order where order_status_id = '1' and customer_id = ".$customer_id);

		return $query->row['total'];
	}
        public function getTotalShippedOrders($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order where order_status_id = '15' and customer_id = ".$customer_id);

		return $query->row['total'];
	}
        public function getTotalCancelOrders($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order where order_status_id = '7' and customer_id = ".$customer_id);

		return $query->row['total'];
	}
        public function getTotalNewProducts() {
                $current_date = gmdate("Y-m-d");
                $current_date = strtotime($current_date);
                $current_date = strtotime("-14 day", $current_date);
                $current_date = date("Y-m-d", $current_date);
                $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd on (p.product_id = pd.product_id) where p.date_added > date ('".$current_date."')");

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
        public function getAccountDetails($customer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer where customer_id = ".$customer_id."");

		return $query->rows[0];
	}
}
