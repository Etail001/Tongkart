<?php
class ControllerAccountExportAll extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/dashboard', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/export_all');

		$this->document->setTitle($this->language->get('heading_title'));
                $this->load->model('account/export_all');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_newsletter'),
			'href' => $this->url->link('account/newsletter', '', true)
		);
                $url='';
                if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
                if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
                        $url .= '&limit='.$limit;
		} else {
			$limit = 10;
		}
                $filter_data = array(
			'start'           => ($page - 1) * $limit,
			'limit'           => $limit
		);

		$data['action'] = $this->url->link('account/export_all', '', true);
                $data['action_import'] = $this->url->link('account/export_all/import', '', true);
                $data['action_export'] = $this->url->link('account/export_all/export', '', true);

		$data['newsletter'] = $this->customer->getNewsletter();

		$data['back'] = $this->url->link('account/account', '', true);
                
                $data['price'] = $this->url->link('account/price');
                $data['brand'] = $this->url->link('account/brand');
                $data['export_all'] = $this->url->link('account/export_all');
                $data['export_special'] = $this->url->link('account/export_special');
                
                
                $data['api'] = $this->url->link('account/api');
                $data['upc'] = $this->url->link('account/upc');
                $data['account'] = $this->url->link('account/account');
                $data['catalog'] = $this->url->link('account/catalog');
                $data['dashboard'] = $this->url->link('account/dashboard');
                $data['new'] = $this->url->link('account/new');
                $data['total'] = $this->url->link('account/total');
                $data['listed'] = $this->url->link('account/listed');
                $data['not_listed'] = $this->url->link('account/not_listed');
                $data['monitor'] = $this->url->link('account/monitor');
                $data['sample_xls'] = HTTP_SERVER.'image/export_import/Upc.xlsx';
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['common'] = $this->load->controller('account/common');
                $total_export = $this->model_account_export_all->getTotalUpc($this->customer->getId());
                $data['total_export'] = $total_export;
                $total_used_export = $this->model_account_export_all->getTotalUsedUpc($this->customer->getId());
                $data['total_used_export'] = $total_used_export;
                $total_un_used_export = $this->model_account_export_all->getTotalUnUsedUpc($this->customer->getId());
                $data['total_un_used_export'] = $total_un_used_export;
                $settings = $this->model_account_export_all->getSettings($this->customer->getId());
                if (!empty($settings)){
                    $data['meta_title'] = $settings[0]['seller_id'];
                    $data['meta_title_token'] = $settings[0]['auth_token'];
                }
                if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
                if (isset($this->session->data['error_warning'])) {
			$data['error_warning'] = $this->session->data['error_warning'];

			unset($this->session->data['error_warning']);
		} else {
			$data['error_warning'] = '';
		}
                $total_export_monitor = $this->model_account_export_all->getTotalMonitorStatus($this->customer->getId());
                $export_monitor = $this->model_account_export_all->getMonitorStatus($filter_data,$this->customer->getId());
                $data['products'] = array();
                foreach($export_monitor as $list){
                    $data['products'][] = array(
					'monitor_id'  => $list['monitor_id'],
                                        'date_added'       => $list['date_added'],
                                        'path'           => HTTP_SERVER.'image/'.$list['path']
					
				);
                }
                $pagination = new Pagination();
                $pagination->total = $total_export_monitor;
                $pagination->page = $page;
                $pagination->limit = $limit;
                $pagination->url = $this->url->link('account/export',$url.'&page={page}');

                $data['pagination'] = $pagination->render();
                $data['results'] = sprintf($this->language->get('text_pagination'), ($total_export_monitor) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($total_export_monitor - $limit)) ? $total_export_monitor : ((($page - 1) * $limit) + $limit), $total_export_monitor, ceil($total_export_monitor / $limit));
		$this->response->setOutput($this->load->view('account/export_all', $data));
	}
        public function export() {
            ini_set('max_execution_time', 300000);
            ini_set('memory_limit', '1024M');
            $this->load->model('account/export_all');
            if ($this->request->server['REQUEST_METHOD'] == 'POST') {
                        $details = $this->model_account_export_all->getDataExport($this->request->post['skus']);
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
                        $objPHPExcel->getActiveSheet()->SetCellValue("J1","Price");
                        $objPHPExcel->getActiveSheet()->SetCellValue("K1","Category");
                        $objPHPExcel->getActiveSheet()->SetCellValue("L1","Description");
                        $objPHPExcel->getActiveSheet()->SetCellValue("M1","Amazon Description");
                        $objPHPExcel->getActiveSheet()->SetCellValue("N1","Main Image");
                        $objPHPExcel->getActiveSheet()->SetCellValue("O1","Additional Image");
                        $objPHPExcel->getActiveSheet()->SetCellValue("P1","Additional Image");
                        $objPHPExcel->getActiveSheet()->SetCellValue("Q1","Additional Image");
                        $objPHPExcel->getActiveSheet()->SetCellValue("R1","Additional Image");
                        $objPHPExcel->getActiveSheet()->SetCellValue("S1","Additional Image");
                        $objPHPExcel->getActiveSheet()->SetCellValue("T1","Additional Image");
                        $objPHPExcel->getActiveSheet()->SetCellValue("U1","Additional Image");
                        $objPHPExcel->getActiveSheet()->SetCellValue("V1","Additional Image");
                        $i=1;    
                        $SrNo=1;
                        foreach($details as $detail){
                            $image_details = $this->model_account_export_all->getProductImage($detail['product_id']);
                            $category_details = $this->model_account_export_all->getProductCategory($detail['product_id']);
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
                            $objPHPExcel->getActiveSheet()->SetCellValue("J$i","{$detail['price']}");
                            $objPHPExcel->getActiveSheet()->SetCellValue("K$i","{$category_details}");
                            $objPHPExcel->getActiveSheet()->SetCellValue("L$i","{$detail['description']}");
                            $objPHPExcel->getActiveSheet()->SetCellValue("M$i","{$detail['amazon_description']}");
                            $objPHPExcel->getActiveSheet()->SetCellValue("N$i","{$detail['image']}");
                            if(isset($image_details[0]['image']) && $image_details[0]['image'] != ''){
                                $objPHPExcel->getActiveSheet()->SetCellValue("O$i","{$image_details[0]['image']}");
                            }
                            if(isset($image_details[1]['image']) && $image_details[1]['image'] != ''){
                                $objPHPExcel->getActiveSheet()->SetCellValue("P$i","{$image_details[1]['image']}");
                            }
                            if(isset($image_details[2]['image']) && $image_details[2]['image'] != ''){
                                $objPHPExcel->getActiveSheet()->SetCellValue("Q$i","{$image_details[2]['image']}");
                            }
                            if(isset($image_details[3]['image']) && $image_details[3]['image'] != ''){
                                $objPHPExcel->getActiveSheet()->SetCellValue("R$i","{$image_details[3]['image']}");
                            }
                            if(isset($image_details[4]['image']) && $image_details[4]['image'] != ''){
                                $objPHPExcel->getActiveSheet()->SetCellValue("S$i","{$image_details[4]['image']}");
                            }
                            if(isset($image_details[5]['image']) && $image_details[5]['image'] != ''){
                                $objPHPExcel->getActiveSheet()->SetCellValue("T$i","{$image_details[5]['image']}");
                            }
                            if(isset($image_details[6]['image']) && $image_details[6]['image'] != ''){
                                $objPHPExcel->getActiveSheet()->SetCellValue("U$i","{$image_details[6]['image']}");
                            }
                            if(isset($image_details[7]['image']) && $image_details[7]['image'] != ''){
                                $objPHPExcel->getActiveSheet()->SetCellValue("V$i","{$image_details[7]['image']}");
                            }
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
                        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(30);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(30);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(30);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(30);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(30);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(30);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(30);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(30);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(30);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(30);
                        $objPHPExcel->getActiveSheet()->getStyle('A1:V1')->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'FFFF00'))));
                        $styleArray = array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN
                                )
                            )
                        );
                        $objPHPExcel->getDefaultStyle()->applyFromArray($styleArray);
                        $filename = strtotime("now").'export_product.xls';
                        $filepath = DIR_IMAGE.'export_export/'.$filename;
                        $temp_filepath = 'export_export/'.$filename;
                        //$this->model_account_export_all->insertUpcExportMonitor($temp_filepath,$this->customer->getId());
                        header('Content-Type: application/vnd.ms-excel');
                        header('Content-Disposition: attachment;filename="'.$filename.'"');
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
                        $this->response->redirect($this->url->link('account/export_all', '', true));
                        exit;

		}
        }
}