<?php
class ModelReportSeller extends Model {
  public function getRecentCustomers() {
    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` ORDER BY date_added DESC LIMIT 5");
     //print_r($query);die;
    return $query->rows;
  }
}
