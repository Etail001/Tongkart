<?php
class ControllerAccountUpc extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/dashboard', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/upc');

		$this->document->setTitle($this->language->get('heading_title'));
                $this->load->model('account/upc');

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
			'href' => $this->url->link('account/dashboard', '', true)
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

		$data['action'] = $this->url->link('account/upc', '', true);
                $data['action_import'] = $this->url->link('account/upc/import', '', true);
                $data['action_export'] = $this->url->link('account/upc/export', '', true);

		$data['newsletter'] = $this->customer->getNewsletter();

		$data['back'] = $this->url->link('account/account', '', true);
                
                $data['price'] = $this->url->link('account/price');
                $data['brand'] = $this->url->link('account/brand');
                $data['export_all'] = $this->url->link('account/export_all');
                $data['export_special'] = $this->url->link('account/export_special');
                
                
                $data['api'] = $this->url->link('account/api');
                $data['upc'] = $this->url->link('account/upc');
                $data['account'] = $this->url->link('account/account');
                $data['dashboard'] = $this->url->link('account/dashboard');
                $data['catalog'] = $this->url->link('account/catalog');
                $data['new'] = $this->url->link('account/new');
                $data['total'] = $this->url->link('account/total');
                $data['listed'] = $this->url->link('account/listed');
                $data['not_listed'] = $this->url->link('account/not_listed');
                $data['monitor'] = $this->url->link('account/monitor');
                $data['sample_xls'] = HTTP_SERVER.'image/upc_import/Upc.xlsx';
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['common'] = $this->load->controller('account/common');
                $total_upc = $this->model_account_upc->getTotalUpc($this->customer->getId());
                $data['total_upc'] = $total_upc;
                $total_used_upc = $this->model_account_upc->getTotalUsedUpc($this->customer->getId());
                $data['total_used_upc'] = $total_used_upc;
                $total_un_used_upc = $this->model_account_upc->getTotalUnUsedUpc($this->customer->getId());
                $data['total_un_used_upc'] = $total_un_used_upc;
                $settings = $this->model_account_upc->getSettings($this->customer->getId());
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
                $total_upc_monitor = $this->model_account_upc->getTotalMonitorStatus($this->customer->getId());
                $upc_monitor = $this->model_account_upc->getMonitorStatus($filter_data,$this->customer->getId());
                $data['products'] = array();
                foreach($upc_monitor as $list){
                    $data['products'][] = array(
					'monitor_id'  => $list['monitor_id'],
                                        'date_added'       => $list['date_added'],
                                        'path'           => HTTP_SERVER.'image/'.$list['path']
					
				);
                }
                $pagination = new Pagination();
                $pagination->total = $total_upc_monitor;
                $pagination->page = $page;
                $pagination->limit = $limit;
                $pagination->url = $this->url->link('account/upc',$url.'&page={page}');

                $data['pagination'] = $pagination->render();
                $data['results'] = sprintf($this->language->get('text_pagination'), ($total_upc_monitor) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($total_upc_monitor - $limit)) ? $total_upc_monitor : ((($page - 1) * $limit) + $limit), $total_upc_monitor, ceil($total_upc_monitor / $limit));
		$this->response->setOutput($this->load->view('account/upc', $data));
	}
        public function import() {
            $this->load->model('account/upc');
            if ($this->request->server['REQUEST_METHOD'] == 'POST') {
                        require_once DIR_SYSTEM.'library/PHPExcel/Classes/PHPExcel.php';
                        $objPHPExcel = new PHPExcel();
                        $path = $this->request->files['import']['name'];
                        if(isset($path) && $path != '') {
                        $ext = pathinfo($path, PATHINFO_EXTENSION);
                        if($ext=="xlsx") {
                            $filepath = DIR_IMAGE.'upc_import/'. strtotime("now"). '_' .$this->request->files['import']['name'];
                            if (is_uploaded_file($this->request->files['import']['tmp_name'])) {
                                move_uploaded_file($this->request->files['import']['tmp_name'],$filepath);
                                $excelReader = PHPExcel_IOFactory::createReaderForFile($filepath);
                                $excelObj = $excelReader->load($filepath);
                                $sheet = $excelObj->getSheet(0); 
                                $highestRow = $sheet->getHighestDataRow();
                                $highestColumn = $sheet->getHighestDataColumn();
                                for ($row = 2; $row <= $highestRow; $row++){ 
                                    $rowData = $sheet->getCell('A' . $row)->getValue();
                                    $this->model_account_upc->insertUpc($rowData,$this->customer->getId());
                                }
                            } else {
                                    $content = false;
                            }
                            $this->session->data['success'] = 'Settings Succesfully Updated';

                            $this->response->redirect($this->url->link('account/upc', '', true));
                        } else {
                            $this->session->data['error_warning'] = 'Only .xlsx can be uploaded, please download sample file.';

                            $this->response->redirect($this->url->link('account/upc', '', true));
                        }
                    } else {
                        $this->session->data['error_warning'] = 'No File Selected';

                        $this->response->redirect($this->url->link('account/upc', '', true));
                    }
			
		}

                
        }
        public function export() {
            ini_set('max_execution_time', 300000);
            ini_set('memory_limit', '1024M');
            $this->load->model('account/upc');
            if ($this->request->server['REQUEST_METHOD'] == 'POST') {
                        $data = $this->model_account_upc->getUpcExport($this->request->post['upc'],$this->customer->getId());
                        require_once DIR_SYSTEM.'library/PHPExcel/Classes/PHPExcel.php';
                        $objPHPExcel = new PHPExcel();
                        $objPHPExcel->getProperties()->setCreator("fd");
                        $objPHPExcel->getProperties()->setLastModifiedBy("dsf");
                        $objPHPExcel->getProperties()->setTitle("fds");
                        $objPHPExcel->getProperties()->setSubject("fds");
                        $objPHPExcel->getProperties()->setDescription("dfs");

                        // Add some data
                        $objPHPExcel->setActiveSheetIndex(0);

                        $objPHPExcel->getActiveSheet()->SetCellValue("A1","UPC");
                        $i=1;    
                        $SrNo=1;
                        foreach($data as $detail){
                            $this->model_account_upc->deleteUpc($detail['upc'],$this->customer->getId());
                            $i++;
                            $objPHPExcel->getActiveSheet()->SetCellValue("A$i","{$detail['upc']}");
                            
                        }
                        $objPHPExcel->getActiveSheet()->setTitle('Simple');

                        $objPHPExcel->setActiveSheetIndex(0);
                        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'FFFF00'))));
                        $styleArray = array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN
                                )
                            )
                        );
                        $objPHPExcel->getDefaultStyle()->applyFromArray($styleArray);
                        $filename = strtotime("now").'upc_export.xls';
                        $filepath = DIR_IMAGE.'upc_export/'.$filename;
                        $temp_filepath = 'upc_export/'.$filename;
                        $this->model_account_upc->insertUpcExportMonitor($temp_filepath,$this->customer->getId());
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
                        $objWriter->save($filepath);
                        $this->response->redirect($this->url->link('account/upc', '', true));
                        exit;

		}
        }
}