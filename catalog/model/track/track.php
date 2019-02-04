<?php
class ModelTrackTrack extends Model {
	
	
	
	public function getTrackingNumbers()
	{
		$query = $this->db->query("select tracking_number from oc_grandslam_shipments");
		
		return $query->rows;
	}
	
	public function insertPacketStatus($data) {
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "grandslam_packet_status SET AWBNo = '" . $data['AWBNo'] . "', 
		Msg = '" . $this->db->escape($data['Msg']) . "', Status = '" . $this->db->escape($data['Status']) . "',
		time = '" . $this->db->escape($data['Time']) . "', Memo = '" . $this->db->escape($data['Memo']) . "',
		Location = '" . $this->db->escape($data['Location']) . "'");
	}
	public function getCredentials() {
		$credentials_query = $this->db->query("SELECT * from oc_logistics_credentials where courior_name='GrandSlam'");
	return $credentials_query->row;
	}

			
}
