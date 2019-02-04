<?php
class ControllerAccountAwaitingPayment extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/dashboard', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/awaiting_payment');
                $this->load->model('tool/image');

		$this->document->setTitle($this->language->get('heading_title'));
                $this->load->model('account/awaiting_payment');

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
                $querry = 0;
                if (isset($this->request->get['filter_search'])) {
			$filter_search = $this->request->get['filter_search'];
                        $data['filter_search'] = $filter_search;
                        $querry = 1;
                        $url .= '&filter_search=' . urlencode(html_entity_decode($this->request->get['filter_search'], ENT_QUOTES, 'UTF-8'));
		} else {
			$filter_search = '';
		}
                if (isset($this->request->get['filter_date_from'])) {
			$filter_date_from = $this->request->get['filter_date_from'];
                        $data['filter_date_from'] = $filter_date_from;
                        $querry = 1;
                        $url .= '&filter_date_from=' . urlencode(html_entity_decode($this->request->get['filter_date_from'], ENT_QUOTES, 'UTF-8'));
		} else {
			$filter_date_from = '';
		}
                if (isset($this->request->get['filter_date_to'])) {
			$filter_date_to = $this->request->get['filter_date_to'];
                        $data['filter_date_to'] = $filter_date_to;
                        $url .= '&filter_date_to=' . urlencode(html_entity_decode($this->request->get['filter_date_to'], ENT_QUOTES, 'UTF-8'));
                        $querry = 1;
		} else {
			$filter_date_to = '';
		}
                if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
                        $url .= '&limit='.$limit;
		} else {
			$limit = 10;
		}
                $filter_data = array(
			'start'           => ($page - 1) * $limit,
			'limit'           => $limit,
                        'filter_search'  => $filter_search,
                        'filter_date_from'=> $filter_date_from,
                        'filter_date_to'  => $filter_date_to
		);
                

		$data['action'] = $this->url->link('account/awaiting_payment', '', true);

		$data['newsletter'] = $this->customer->getNewsletter();

		$data['back'] = $this->url->link('account/account', '', true);
                
                $data['price'] = $this->url->link('account/price');
                $data['brand'] = $this->url->link('account/brand');
                $data['catalog'] = $this->url->link('account/catalog');
                $data['export_all'] = $this->url->link('account/export_all');
                $data['export_special'] = $this->url->link('account/export_special');
                $data['api'] = $this->url->link('account/api');
                $data['upc'] = $this->url->link('account/upc');
                $data['account'] = $this->url->link('account/account');
                $data['orders_total'] = $this->url->link('account/orders_total');
                $data['pending'] = $this->url->link('account/pending');
                $data['awaiting_payment'] = $this->url->link('account/awaiting_payment');
                $data['odr_shipped'] = $this->url->link('account/odr_shipped');
                $data['new'] = $this->url->link('account/new');
                $data['total'] = $this->url->link('account/total');
                $data['listed'] = $this->url->link('account/listed');
                $data['not_listed'] = $this->url->link('account/not_listed');
                $data['monitor'] = $this->url->link('account/monitor');
                $data['dashboard'] = $this->url->link('account/dashboard');
                $data['url'] = $this->url->link('account/awaiting_payment',$url);
                $data['page'] = $page;
                $data['limit'] = $limit;
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['common'] = $this->load->controller('account/common');
                $data['dashboard'] = $this->url->link('account/dashboard');
                $data['customer_id'] = $this->customer->getId();
                $total_order_count = $this->model_account_awaiting_payment->getTotalOrders($filter_data,$this->customer->getId());
                $total_orders = $this->model_account_awaiting_payment->getorders($filter_data,$this->customer->getId());
                
                $data['all_orders'] = array();
                foreach($total_orders as $list){
                    if ($list['order_status_id'] == 15){
                        $status = 'Shipped';
                    } else if ($list['order_status_id'] == 1){
                        $status = 'Pending';
                    } else if ($list['order_status_id'] == 7){
                        $status = 'Canceled';
                    } else if ($list['order_status_id'] == 19){
                        $status = 'Under Reveiw';
                    } else{
                        $status = 'Processing';
                    }
                    $tongkart_price = $this->model_account_awaiting_payment->getTongkartOrderPrice($list['order_id']);
                    $price = $this->currency->format($this->tax->calculate($tongkart_price, 0, $this->config->get('config_tax')), 'INR');
                    $data['all_orders'][] = array(
					'order_id'  => $list['order_id'],
                                        'temp_id'       => $list['temp_id'],
                                        'marketplace_order_id'       => $list['amazon_order_id'],
					'name'        => $list['firstname'].' '.$list['lastname'],
					'total'       => $price,
                                        'status'       => $status,
                                        'marketplace'       => $list['store_name'],
                                        'date_added'       => $list['date_added'],
                                        'link'        => $this->url->link('account/order/info', 'order_id='.$list['order_id'])
					//End
					//'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'] . $url)
				);
                }
                $pagination = new Pagination();
                $pagination->total = $total_order_count;
                $pagination->page = $page;
                $pagination->limit = $limit;
                $pagination->url = $this->url->link('account/awaiting_payment',$url.'&page={page}');

                $data['pagination'] = $pagination->render();
                $data['payment'] = $this->url->link('account/awaiting_payment/payment');
                $data['results'] = sprintf($this->language->get('text_pagination'), ($total_order_count) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($total_order_count - $limit)) ? $total_order_count : ((($page - 1) * $limit) + $limit), $total_order_count, ceil($total_order_count / $limit));
                
                if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
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
		$this->response->setOutput($this->load->view('account/awaiting_payment', $data));
	}
        public function export() {
            ini_set('max_execution_time', 300000);
            ini_set('memory_limit', '1024M');
            $this->load->model('account/awaiting_payment');
            $querry = 0;
            $url = '';
            if (isset($this->request->get['filter_search'])) {
                    $filter_search = $this->request->get['filter_search'];
                    $data['filter_search'] = $filter_search;
                    $querry = 1;
                    $url .= '&filter_search=' . urlencode(html_entity_decode($this->request->get['filter_search'], ENT_QUOTES, 'UTF-8'));
            } else {
                    $filter_search = '';
            }
            if (isset($this->request->get['filter_date_from'])) {
                    $filter_date_from = $this->request->get['filter_date_from'];
                    $data['filter_date_from'] = $filter_date_from;
                    $querry = 1;
                    $url .= '&filter_date_from=' . urlencode(html_entity_decode($this->request->get['filter_date_from'], ENT_QUOTES, 'UTF-8'));
            } else {
                    $filter_date_from = '';
            }
            if (isset($this->request->get['filter_date_to'])) {
                    $filter_date_to = $this->request->get['filter_date_to'];
                    $data['filter_date_to'] = $filter_date_to;
                    $url .= '&filter_date_to=' . urlencode(html_entity_decode($this->request->get['filter_date_to'], ENT_QUOTES, 'UTF-8'));
                    $querry = 1;
            } else {
                    $filter_date_to = '';
            }
            $filter_data = array(
                        'filter_search'  => $filter_search,
                        'filter_date_from'=> $filter_date_from,
                        'filter_date_to'  => $filter_date_to
		);
            
            $export_product = $this->model_account_awaiting_payment->getAllorders($filter_data,$this->customer->getId());
            require_once DIR_SYSTEM.'library/PHPExcel/Classes/PHPExcel.php';
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()->setCreator("fd");
            $objPHPExcel->getProperties()->setLastModifiedBy("dsf");
            $objPHPExcel->getProperties()->setTitle("fds");
            $objPHPExcel->getProperties()->setSubject("fds");
            $objPHPExcel->getProperties()->setDescription("dfs");

            // Add some data
            $objPHPExcel->setActiveSheetIndex(0);

            $objPHPExcel->getActiveSheet()->SetCellValue("A1","Tongkart Order Id"); 
            $objPHPExcel->getActiveSheet()->SetCellValue("B1","Marketplace Order Id");
            $objPHPExcel->getActiveSheet()->SetCellValue("C1","Total");
            $objPHPExcel->getActiveSheet()->SetCellValue("D1","Name");
            $objPHPExcel->getActiveSheet()->SetCellValue("E1","Marketplace");
            $objPHPExcel->getActiveSheet()->SetCellValue("F1","Date");
            $i=1;    
            $SrNo=1;
            foreach($export_product as $detail){
                $i++;
                $tongkart_price = $this->model_account_awaiting_payment->getTongkartOrderPrice($detail['order_id']);
                $name = $detail['firstname']." ".$detail['lastname'];
                $objPHPExcel->getActiveSheet()->SetCellValue("A$i","{$detail['temp_id']}");
                $objPHPExcel->getActiveSheet()->SetCellValue("B$i","{$detail['amazon_order_id']}");
                $objPHPExcel->getActiveSheet()->SetCellValue("C$i","{$tongkart_price}");
                $objPHPExcel->getActiveSheet()->SetCellValue("D$i","{$name}");
                $objPHPExcel->getActiveSheet()->SetCellValue("E$i","{$detail['store_name']}");
                $objPHPExcel->getActiveSheet()->SetCellValue("F$i","{$detail['date_added']}");
                
                $SrNo++;

            }
            $objPHPExcel->getActiveSheet()->setTitle('Simple');

            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'FFFF00'))));
            $styleArray = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $objPHPExcel->getDefaultStyle()->applyFromArray($styleArray);
            $filename = strtotime("now").'export_shipped_order.xls';
            $filepath = DIR_IMAGE.'export_export/'.$filename;
            $temp_filepath = 'export_export/'.$filename;
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
            exit;
        }
        public function payment() {

		$this->load->model('account/awaiting_payment');
		if (isset($this->request->post['selected'])) {
                    $result = $this->model_account_awaiting_payment->pay($this->request->post['selected'],$this->customer->getId());
                    if ($result == 'success'){
                        $this->session->data['success'] = 'Succesfully Paid from your Wallet. Orders will be processed soon.';
                        $this->response->redirect($this->url->link('account/awaiting_payment'));
                    } else {
                        $this->session->data['error_warning'] = 'Not Enough Amount on your Wallet. Please Add more.';
                        $this->response->redirect($this->url->link('account/awaiting_payment'));
                    }
                        
			
                } else{
                    $this->session->data['error_warning'] = 'No Order Selected';
                    $this->response->redirect($this->url->link('account/awaiting_payment'));
                }
	}
}