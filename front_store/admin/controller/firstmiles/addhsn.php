<?php
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;

class ControllerFinanceAddhsn extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('marketing/marketing');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('marketing/marketing');

		$this->getList();
	}

	public function add() {
		$this->load->language('finance/addhsn');

		$this->document->setTitle($this->language->get('heading_title'));

		//$this->load->model('finance/finance');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_marketing_marketing->addMarketing($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_code'])) {
				$url .= '&filter_code=' . $this->request->get['filter_code'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->response->redirect($this->url->link('finance/addhsn/add', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}


	protected function getList() {
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_code'])) {
			$filter_code = $this->request->get['filter_code'];
		} else {
			$filter_code = '';
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_code'])) {
			$url .= '&filter_code=' . $this->request->get['filter_code'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('marketing/marketing', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('finance/addhsn/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('marketing/marketing/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['marketings'] = array();

		$filter_data = array(
			'filter_name'       => $filter_name,
			'filter_code'       => $filter_code,
			'filter_date_added' => $filter_date_added,
			'sort'              => $sort,
			'order'             => $order,
			'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'             => $this->config->get('config_limit_admin')
		);

		$marketing_total = $this->model_marketing_marketing->getTotalMarketings($filter_data);

		$results = $this->model_marketing_marketing->getMarketings($filter_data);

		foreach ($results as $result) {
			$data['marketings'][] = array(
				'marketing_id' => $result['marketing_id'],
				'name'         => $result['name'],
				'code'         => $result['code'],
				'clicks'       => $result['clicks'],
				'orders'       => $result['orders'],
				'date_added'   => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'edit'         => $this->url->link('marketing/marketing/edit', 'user_token=' . $this->session->data['user_token'] . '&marketing_id=' . $result['marketing_id'] . $url, true)
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_code'])) {
			$url .= '&filter_code=' . $this->request->get['filter_code'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('marketing/marketing', 'user_token=' . $this->session->data['user_token'] . '&sort=m.name' . $url, true);
		$data['sort_code'] = $this->url->link('marketing/marketing', 'user_token=' . $this->session->data['user_token'] . '&sort=m.code' . $url, true);
		$data['sort_date_added'] = $this->url->link('marketing/marketing', 'user_token=' . $this->session->data['user_token'] . '&sort=m.date_added' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_code'])) {
			$url .= '&filter_code=' . $this->request->get['filter_code'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $marketing_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('marketing/marketing', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($marketing_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($marketing_total - $this->config->get('config_limit_admin'))) ? $marketing_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $marketing_total, ceil($marketing_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_code'] = $filter_code;
		$data['filter_date_added'] = $filter_date_added;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('marketing/marketing_list', $data));
	}
	
	public function import() {
		$this->load->language('finance/addhsn');
		require_once './spout/src/Spout/Autoloader/autoload.php';
		require_once "./PHPExcel/Classes/PHPExcel.php";
		
		/*if (!empty($_FILES['import']['name'])) {
     
				// Get File extension eg. 'xlsx' to check file is excel sheet
				$pathinfo = pathinfo($_FILES["import"]["name"]);
				
				// check file has extension xlsx, xls and also check 
				// file is not empty
				if (($pathinfo['extension'] == 'xlsx' || $pathinfo['extension'] == 'csv') && $_FILES['import']['size'] > 0 ) {
						   
					// Temporary file name
					 $inputFileName = $_FILES['import']['tmp_name']; 
			
		   
				// Read excel file by using ReadFactory object.
				$reader = ReaderFactory::create(Type::CSV);
				
				// Open file
        $reader->open($inputFileName);
        $count = 1;
        $rows = array(); 
        $i = 1;
        $stk = 0;
        $prevOrderId = 0;
        $currentOrderId = 0;
		
        // Number of sheet in excel file
        foreach ($reader->getSheetIterator() as $sheet)
         {
			// echo "======================";die;
			     // It reads data after header. In the my excel sheet, 
                // header is in the first row. 
                foreach ($sheet->getRowIterator() as $row) {
					//echo "======================";die;
					 
						//echo "======================";die;
						echo $data['MerchantSKU'] 		= $row[1];die; 
					
				}
			}
			
				
				
			}
		}
		*/
		
		
		if(!empty($this->request->files['import']['tmp_name']))
		{
			
			$hsn_errrlog=array();
			$requiredHeaders = array('merchantsku', 'hsncode', 'tax1','tax2','multipleslab'); //headers we expect
			$name = $this->request->files['import']['tmp_name'];
			
			$tmp = explode(".", $name);
			$ext = end($tmp);
			
			if($ext != 'csv'){
				$msg = 'uploaded file type is not in csv format';// json_encode(array('status'=>'failed','message'=>'uploaded file type is not in csv format'));
			} 
			else
			{
				echo "================================";
				//$filename = WWW_ROOT. 'files'.DS.$this->request->data['FileSystem']['Import_file']['name'];
				//move_uploaded_file($this->request->data['FileSystem']['Import_file']['tmp_name'],$filename);  
				$name		=	 $this->request->files['import']['tmp_name'];
				$objPHPExcel = new PHPExcel();
				$objReader= PHPExcel_IOFactory::createReader('CSV');
				$objReader->setReadDataOnly(true);				
				$objPHPExcel=$objReader->load('files/'.$name);
				$objWorksheet=$objPHPExcel->setActiveSheetIndex(0);
				$lastRow = $objPHPExcel->getActiveSheet()->getHighestRow();
				$colString	=	 $highestColumn = $objPHPExcel->getActiveSheet()->getHighestColumn();
				$colNumber = PHPExcel_Cell::columnIndexFromString($colString);
				//print_r($colNumber);die;
				$count=0;
				$countalready=0;
				$header	=	array('sku', 'weight','lbh');
				$count	= 0;
				$alreadycount	=	0;
				$countbarcode	=	0;
				for($i=2;$i<=$lastRow;$i++) 
				{
					$today = date("Ymd");
					$rand = strtoupper(substr(uniqid(sha1(mt_rand())),0,4));
					echo $val = 'ILX'.$today . $rand;die;
					$MWMS_Order_Unique = $this->uniqueID($val);
					$productdata['sku']				=	$objWorksheet->getCellByColumnAndRow(0,$i)->getValue();
					$productdata['weight']				=	$objWorksheet->getCellByColumnAndRow(1,$i)->getValue();
					$productdata['lbh']			=	$objWorksheet->getCellByColumnAndRow(2,$i)->getValue();
					$this->Skutable->saveAll($productdata);
				}
				$this->Session->setFlash($i.' :- Sku Inserted', 'flash_danger');
			}
		}

		//$this->response->addHeader('Content-Type: application/json');
		//$this->response->setOutput(json_encode($json));
	}
	
	protected function getForm() {
		//echo "======================";die;
		$data['text_form'] = !isset($this->request->get['marketing_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['code'])) {
			$data['error_code'] = $this->error['code'];
		} else {
			$data['error_code'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_code'])) {
			$url .= '&filter_code=' . $this->request->get['filter_code'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('marketing/marketing', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['marketing_id'])) {
			$data['action'] = $this->url->link('finance/addhsn/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('finance/addhsn/edit', 'user_token=' . $this->session->data['user_token'] . '&marketing_id=' . $this->request->get['marketing_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('finance/addhsn', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['marketing_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$marketing_info = $this->model_marketing_marketing->getMarketing($this->request->get['marketing_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$data['store'] = HTTP_CATALOG;

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($marketing_info)) {
			$data['name'] = $marketing_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['description'])) {
			$data['description'] = $this->request->post['description'];
		} elseif (!empty($marketing_info)) {
			$data['description'] = $marketing_info['description'];
		} else {
			$data['description'] = '';
		}

		if (isset($this->request->post['code'])) {
			$data['code'] = $this->request->post['code'];
		} elseif (!empty($marketing_info)) {
			$data['code'] = $marketing_info['code'];
		} else {
			$data['code'] = uniqid();
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('finance/addhsn_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'marketing/marketing')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 32)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (!$this->request->post['code']) {
			$this->error['code'] = $this->language->get('error_code');
		}

		$marketing_info = $this->model_marketing_marketing->getMarketingByCode($this->request->post['code']);

		if (!isset($this->request->get['marketing_id'])) {
			if ($marketing_info) {
				$this->error['code'] = $this->language->get('error_exists');
			}
		} else {
			if ($marketing_info && ($this->request->get['marketing_id'] != $marketing_info['marketing_id'])) {
				$this->error['code'] = $this->language->get('error_exists');
			}
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'marketing/marketing')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
