<?php
class ControllerCatalogProductExport extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/product_export');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->getList();
	}

	

	

	protected function getList() {

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/product_export', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['user_token'] = $this->session->data['user_token'];
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
                $this->response->setOutput($this->load->view('catalog/product_export', $data));
	}
        public function export() {
            $this->load->model('catalog/product_export');
            $details = $this->model_catalog_product_export->getProductDetails();
            require_once DIR_SYSTEM.'library/PHPExcel/Classes/PHPExcel.php';
            $objPHPExcel = new PHPExcel();

            $objPHPExcel->getProperties()->setCreator("fd");
            $objPHPExcel->getProperties()->setLastModifiedBy("dsf");
            $objPHPExcel->getProperties()->setTitle("fds");
            $objPHPExcel->getProperties()->setSubject("fds");
            $objPHPExcel->getProperties()->setDescription("dfs");

            // Add some data
            $objPHPExcel->setActiveSheetIndex(0);

            $objPHPExcel->getActiveSheet()->SetCellValue("A1","Product Id"); 
            $objPHPExcel->getActiveSheet()->SetCellValue("B1","Product Name");
            $objPHPExcel->getActiveSheet()->SetCellValue("C1","Listing SKU");
            $objPHPExcel->getActiveSheet()->SetCellValue("D1","Original SKU");
            $objPHPExcel->getActiveSheet()->SetCellValue("E1","Weight");
            $objPHPExcel->getActiveSheet()->SetCellValue("F1","Length");
            $objPHPExcel->getActiveSheet()->SetCellValue("G1","Height");
            $objPHPExcel->getActiveSheet()->SetCellValue("H1","Color");
            $objPHPExcel->getActiveSheet()->SetCellValue("I1","Size");
            $objPHPExcel->getActiveSheet()->SetCellValue("J1","Description");
            $objPHPExcel->getActiveSheet()->SetCellValue("K1","Image");
            $i=1;    
            $SrNo=1;
            foreach($details as $detail){

                $i++;
                $detail['description'] = html_entity_decode($detail['description']);
                $objPHPExcel->getActiveSheet()->SetCellValue("A$i","{$detail['product_id']}");
                $objPHPExcel->getActiveSheet()->SetCellValue("B$i","{$detail['name']}");
                $objPHPExcel->getActiveSheet()->SetCellValue("C$i","{$detail['model']}");
                $objPHPExcel->getActiveSheet()->SetCellValue("D$i","{$detail['sku']}");
                $objPHPExcel->getActiveSheet()->SetCellValue("E$i","{$detail['weight']}");
                $objPHPExcel->getActiveSheet()->SetCellValue("F$i","{$detail['length']}");
                $objPHPExcel->getActiveSheet()->SetCellValue("G$i","{$detail['width']}");
                $objPHPExcel->getActiveSheet()->SetCellValue("H$i","{$detail['color']}");
                $objPHPExcel->getActiveSheet()->SetCellValue("I$i","{$detail['size']}");
                $objPHPExcel->getActiveSheet()->SetCellValue("J$i","{$detail['description']}");
                $objPHPExcel->getActiveSheet()->SetCellValue("K$i","{$detail['image']}");
                $SrNo++;

            }
            $objPHPExcel->getActiveSheet()->setTitle('Simple');

            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(45);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(55);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'FFFF00'))));
            $styleArray = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $objPHPExcel->getDefaultStyle()->applyFromArray($styleArray);

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="ProductDetails.xls"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0

            ob_clean();
            ob_flush();

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit; 
        }
}