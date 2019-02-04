<?php
class ModelCatalogProductExport extends Model {
	
        
        
        
        public function getProductDetails() { 
            $sql = "SELECT p.*,pd.description,pd.name,pd.color,pd.size FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' and p.product_id >82501 and p.amazon_data = '0' and p.amazon_status = '' and p.model like '%CB_%'";
            $sql .= " GROUP BY p.product_id";
            $sql .= " ORDER BY p.product_id";
            $sql .= " LIMIT 0,7500";
            $query = $this->db->query($sql);

            return $query->rows;
        }
}