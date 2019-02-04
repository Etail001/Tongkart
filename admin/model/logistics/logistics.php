<?php
class ModelLogisticsLogistics extends Model {
	
	public function getCredentials() {
		$credentials_query = $this->db->query("SELECT * from oc_logistics_credentials where courior_name='GrandSlam'");
	return $credentials_query->row;
	}

	public function insertPickupEntry($data) {
		
		
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "grandslam_pickupentry SET awb = '" . $data['AWBNo'] . "', 
		msg = '" . $this->db->escape($data['Msg']) . "', status = '" . $this->db->escape($data['Status']) . "',order_id = '" . $this->db->escape($data['OrderId']) . "'");
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "grandslam_shipments SET shipment_order_id = '" . $data['OrderId'] . "', 
		from_warehouse = '" . $this->db->escape($data['FromWarehouse']) . "', to_warehouse = '" . $this->db->escape($data['ToWarehouse']) . "',
		tracking_number = '" . $this->db->escape($data['AWBNo']) . "',weight = '" . $this->db->escape($data['Weight']) . "',
		pcs = '" . $this->db->escape($data['Pcs']) . "',
		request_info = '" . $this->db->escape($data['Request']) . "',
		response_info = '" . $this->db->escape($data['Response']) . "',track_status = '" . $this->db->escape($data['TrackStatus']) . "'");
		
		//$this->db->query("UPDATE " . DB_PREFIX . "order SET awb = '" . $data['AWBNo'] . "',order_status_id='2' WHERE order_id = '" . $data['OrderId'] . "'");
	}
	
	public function getOrder($order_id) {
		$order_query = $this->db->query("SELECT awb,order_id from oc_order where awb !=''");
	return $order_query->row;
	}
	
	public function insertScanDocumentList($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "grandslam_scandocuments SET awb = '" . $data['AWBNo'] . "', 
		msg = '" . $this->db->escape($data['Msg']) . "', status = '" . $this->db->escape($data['Status']) . "',docs = '" . $data['Docs'] . "'");
		
		$this->db->query("UPDATE " . DB_PREFIX . "grandslam_shipments SET scan_docs = '" . $data['Msg'] . "' WHERE tracking_number = '" . $data['AWBNo'] . "'");
	}
	
	public function insertPacketStatus($data) {
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "grandslam_packet_status SET AWBNo = '" . $data['AWBNo'] . "', 
		Msg = '" . $this->db->escape($data['Msg']) . "', Status = '" . $this->db->escape($data['Status']) . "',
		time = '" . $this->db->escape($data['Time']) . "', Memo = '" . $this->db->escape($data['Memo']) . "',
		Location = '" . $this->db->escape($data['Location']) . "',order_id = '" . $this->db->escape($data['OrderId']) . "'");
	}
	
	public function getAwbPacketStatus($data = array()) {
		$sql = "SELECT * from oc_grandslam_packet_status as o";

		$sort_data = array(
			'o.order_id',
			'customer',
			'order_status',
			'o.date_added',
			'o.date_modified',
			'o.total'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY o.order_id";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
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
	public function getTotalAwbPacketStatus($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "grandslam_packet_status`";

		if (!empty($data['filter_order_id'])) {
			$sql .= " AND order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}
