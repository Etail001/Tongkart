<?php

use Box\Spout\Common\Type;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Writer\WriterFactory;
class ControllerPaytmPaytm extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('cnwms/shipments');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('cnwms/shipments');
		$this->load->model('cnwms/order');

		$this->getListUpload();
	}
	
	
	public function getListUpload()
	{
		$curl = curl_init();
		$param = array(
						'username'=>'romaanventure27@gmail.com',
						'password'=>'paytm@2018',
						'state'=>'gurgaon',
						'notredirect'=>'true',
						'client_id'=>'merchant-integration',
						'response_type'=>'code',
				);

		$request = http_build_query($param);
		
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://persona.paytm.com/oauth2/authorize",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_SSL_VERIFYPEER => false,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS =>$request,
		  CURLOPT_HTTPHEADER => array(
			"cache-control: no-cache",
			"content-type: application/x-www-form-urlencoded",
		  ),
		));

		$response = curl_exec($curl);
		$result = json_decode($response);
		$code = $result->code;
		$state = $result->state;
		$param = array(
						'code'=>$code,
						'client_id'=>'merchant-integration',
						'client_secret'=>'1f9009680f0b950d502b7b59e6eb3e312344d137',
						'grant_type'=>'authorization_code',
						'state'=>$state,
				);

		$request = http_build_query($param);

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://persona.paytm.com/oauth2/token",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_SSL_VERIFYPEER => false,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS =>$request,
		  CURLOPT_HTTPHEADER => array(
			"cache-control: no-cache",
			"content-type: application/x-www-form-urlencoded",
		  ),
		));
		
		$response = curl_exec($curl);
		
		print_r($response);die;
		
		$result = json_decode($response);
		echo $auth_token = $result->access_token;
		die;

	}
	
	public function uploadScanDocument() {
		$this->load->language('cnwms/shipments');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('cnwms/shipments');
		
		$this->getUploadScanDocs();
	}
	
	public function trackShipments() {
		$this->load->language('cnwms/shipments');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('cnwms/shipments');
		
		$this->getTrackingStatus();
	}
	
	public function shipmentList() {
		$this->load->language('cnwms/cnwms');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('cnwms/shipments');
		$this->load->model('cnwms/order');

		$this->getShipmentList();
	}
	
	public function docsList() {
		$this->load->language('cnwms/cnwms');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('cnwms/shipments');
		$this->load->model('cnwms/order');

		$this->getDocsList();
	}
	
	
	protected function getList() {
		
		$url ='';
		$data['action'] = $this->url->link('cnwms/shipments/createshipments', 'user_token=' . $this->session->data['user_token'] . $url, true);
		
		$data['orders'] = array();
		
		$results = $this->model_cnwms_shipments->getWarehouse();
		foreach ($results as $result) {
			
			$data['orders'][] = array(
				'id'      => $result['id'],
				'warehouse'      => $result['warehouse_name'],
			);
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('cnwms/create_shipments', $data));
	}
	
	protected function getUploadScanDocs() {
		
		$url ='';
		$data['action'] = $this->url->link('cnwms/shipments/scandocument', 'user_token=' . $this->session->data['user_token'] . $url, true);
		
		$data['tracking_number'] = array();
		
		$results = $this->model_cnwms_shipments->getTrackingNumbers();
		foreach ($results as $result) {
			
			$data['tracking_number'][] = array(
				'tracking_number'      => $result['tracking_number'],
			);
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('cnwms/upload_scanDocs', $data));
	}
	
	protected function getShipmentList() {
		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = '';
		}

		if (isset($this->request->get['filter_customer'])) {
			$filter_customer = $this->request->get['filter_customer'];
		} else {
			$filter_customer = '';
		}

		if (isset($this->request->get['filter_order_status'])) {
			$filter_order_status = $this->request->get['filter_order_status'];
		} else {
			$filter_order_status = '';
		}
		
		if (isset($this->request->get['filter_order_status_id'])) {
			$filter_order_status_id = $this->request->get['filter_order_status_id'];
		} else {
			$filter_order_status_id = '';
		}
		
		if (isset($this->request->get['filter_total'])) {
			$filter_total = $this->request->get['filter_total'];
		} else {
			$filter_total = '';
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = '';
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = '';
		}
		
		if (isset($this->request->get['filter_order_supplires'])) {
			$filter_order_supplires = $this->request->get['filter_order_supplires'];
		} else {
			$filter_order_supplires = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'o.order_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}
	
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
			
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}
		
		if (isset($this->request->get['filter_order_supplires'])) {
			$url .= '&filter_order_supplires=' . $this->request->get['filter_order_supplires'];
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
			'href' => $this->url->link('firstmiles/firstmiles', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['invoice'] = $this->url->link('sale/order/invoice', 'user_token=' . $this->session->data['user_token'], true);
		$data['shipping'] = $this->url->link('sale/order/shipping', 'user_token=' . $this->session->data['user_token'], true);
		$data['action'] = $this->url->link('firstmiles/firstmiles/generateLabel', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['action_scan'] = $this->url->link('cnwms/shipments/scandocument', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['action_download'] = $this->url->link('cnwms/cnwms/download', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = str_replace('&amp;', '&', $this->url->link('sale/order/delete', 'user_token=' . $this->session->data['user_token'] . $url, true));

		$data['shipments'] = array();

		$filter_data = array(
			'filter_order_id'        => $filter_order_id,
			'filter_customer'	     => $filter_customer,
			'filter_order_status'    => $filter_order_status,
			'filter_order_status_id' => $filter_order_status_id,
			'filter_total'           => $filter_total,
			'filter_date_added'      => $filter_date_added,
			'filter_date_modified'   => $filter_date_modified,
			'filter_order_supplires'   => $filter_order_supplires,
			'sort'                   => $sort,
			'order'                  => $order,
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);

		$order_total = $this->model_cnwms_shipments->getTotalShipments($filter_data);

		$results = $this->model_cnwms_shipments->getShipments($filter_data);
		
		$awb = "";
		foreach ($results as $result) {
			if($result['tracking_number'] != "" || $result['tracking_number'] != null )
			{
				$awb = $result['tracking_number'];
			}
			
			$data['shipments'][] = array(
				'id'      => $result['id'],
				'shipment_order_id'      => $result['shipment_order_id'],
				'tracking_number'      => $awb,
				'weight'      => $result['weight'],
				'pcs'      => $result['pcs'],
				'track_status'      => $result['track_status'],
				'create_time'    => date($this->language->get('date_format_short'), strtotime($result['create_time'])),
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

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}
		
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
			
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}
		
		if (isset($this->request->get['filter_order_supplires'])) {
			$url .= '&filter_order_supplires=' . $this->request->get['filter_order_supplires'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_order'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . '&sort=o.order_id' . $url, true);
		$data['sort_customer'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . '&sort=customer' . $url, true);
		$data['sort_supplire'] = $this->url->link('cnwms/cnwms', 'user_token=' . $this->session->data['user_token'] . '&sort=supplire' . $url, true);
		$data['sort_status'] = $this->url->link('cnwms/cnwms', 'user_token=' . $this->session->data['user_token'] . '&sort=order_status' . $url, true);
		$data['sort_total'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . '&sort=o.total' . $url, true);
		$data['sort_date_added'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . '&sort=o.date_added' . $url, true);
		$data['sort_date_modified'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . '&sort=o.date_modified' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}
		
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
			
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}
		
		if (isset($this->request->get['filter_order_supplires'])) {
			$url .= '&filter_order_supplires=' . $this->request->get['filter_order_supplires'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('cnwms/cnwms', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['filter_order_id'] = $filter_order_id;
		$data['filter_customer'] = $filter_customer;
		$data['filter_order_status'] = $filter_order_status;
		$data['filter_order_status_id'] = $filter_order_status_id;
		$data['filter_total'] = $filter_total;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_date_modified'] = $filter_date_modified;
		$data['filter_order_supplires'] = $filter_order_supplires;

		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['order_supplires'] = array(
                    '1' => "China Brand",
                );

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		// API login
		$data['catalog'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;
		
		// API login
		$this->load->model('user/api');

		$api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

		if ($api_info && $this->user->hasPermission('modify', 'sale/order')) {
			$session = new Session($this->config->get('session_engine'), $this->registry);
			
			$session->start();
					
			$this->model_user_api->deleteApiSessionBySessonId($session->getId());
			
			$this->model_user_api->addApiSession($api_info['api_id'], $session->getId(), $this->request->server['REMOTE_ADDR']);
			
			$session->data['api_id'] = $api_info['api_id'];

			$data['api_token'] = $session->getId();
		} else {
			$data['api_token'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('cnwms/shipment_list', $data));
	}
	
	protected function getDocsList() {
		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = '';
		}

		if (isset($this->request->get['filter_customer'])) {
			$filter_customer = $this->request->get['filter_customer'];
		} else {
			$filter_customer = '';
		}

		if (isset($this->request->get['filter_order_status'])) {
			$filter_order_status = $this->request->get['filter_order_status'];
		} else {
			$filter_order_status = '';
		}
		
		if (isset($this->request->get['filter_order_status_id'])) {
			$filter_order_status_id = $this->request->get['filter_order_status_id'];
		} else {
			$filter_order_status_id = '';
		}
		
		if (isset($this->request->get['filter_total'])) {
			$filter_total = $this->request->get['filter_total'];
		} else {
			$filter_total = '';
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = '';
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = '';
		}
		
		if (isset($this->request->get['filter_order_supplires'])) {
			$filter_order_supplires = $this->request->get['filter_order_supplires'];
		} else {
			$filter_order_supplires = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'o.order_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}
	
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
			
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}
		
		if (isset($this->request->get['filter_order_supplires'])) {
			$url .= '&filter_order_supplires=' . $this->request->get['filter_order_supplires'];
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
			'href' => $this->url->link('firstmiles/firstmiles', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['invoice'] = $this->url->link('sale/order/invoice', 'user_token=' . $this->session->data['user_token'], true);
		$data['shipping'] = $this->url->link('sale/order/shipping', 'user_token=' . $this->session->data['user_token'], true);
		$data['action'] = $this->url->link('firstmiles/firstmiles/generateLabel', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['action_scan'] = $this->url->link('cnwms/shipments/scandocument', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['action_download'] = $this->url->link('cnwms/cnwms/download', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = str_replace('&amp;', '&', $this->url->link('sale/order/delete', 'user_token=' . $this->session->data['user_token'] . $url, true));

		$data['shipments'] = array();

		$filter_data = array(
			'filter_order_id'        => $filter_order_id,
			'filter_customer'	     => $filter_customer,
			'filter_order_status'    => $filter_order_status,
			'filter_order_status_id' => $filter_order_status_id,
			'filter_total'           => $filter_total,
			'filter_date_added'      => $filter_date_added,
			'filter_date_modified'   => $filter_date_modified,
			'filter_order_supplires'   => $filter_order_supplires,
			'sort'                   => $sort,
			'order'                  => $order,
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);

		$order_total = $this->model_cnwms_shipments->getTotalScanDocs($filter_data);

		$results = $this->model_cnwms_shipments->getScanDocs($filter_data);
		
		$awb = "";
		foreach ($results as $result) {
			if($result['awb'] != "" || $result['awb'] != null )
			{
				$awb = $result['awb'];
			}
			
			$data['shipments'][] = array(
				'id'      => $result['id'],
				'tracking_number'      => $awb,
				'msg'      => $result['msg'],
				'status'      => $result['status'],
				'create_time'    => date($this->language->get('date_format_short'), strtotime($result['create_time'])),
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

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}
		
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
			
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}
		
		if (isset($this->request->get['filter_order_supplires'])) {
			$url .= '&filter_order_supplires=' . $this->request->get['filter_order_supplires'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_order'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . '&sort=o.order_id' . $url, true);
		$data['sort_customer'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . '&sort=customer' . $url, true);
		$data['sort_supplire'] = $this->url->link('cnwms/cnwms', 'user_token=' . $this->session->data['user_token'] . '&sort=supplire' . $url, true);
		$data['sort_status'] = $this->url->link('cnwms/cnwms', 'user_token=' . $this->session->data['user_token'] . '&sort=order_status' . $url, true);
		$data['sort_total'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . '&sort=o.total' . $url, true);
		$data['sort_date_added'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . '&sort=o.date_added' . $url, true);
		$data['sort_date_modified'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . '&sort=o.date_modified' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}
		
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
			
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}
		
		if (isset($this->request->get['filter_order_supplires'])) {
			$url .= '&filter_order_supplires=' . $this->request->get['filter_order_supplires'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('cnwms/cnwms', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['filter_order_id'] = $filter_order_id;
		$data['filter_customer'] = $filter_customer;
		$data['filter_order_status'] = $filter_order_status;
		$data['filter_order_status_id'] = $filter_order_status_id;
		$data['filter_total'] = $filter_total;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_date_modified'] = $filter_date_modified;
		$data['filter_order_supplires'] = $filter_order_supplires;

		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['order_supplires'] = array(
                    '1' => "China Brand",
                );

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		// API login
		$data['catalog'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;
		
		// API login
		$this->load->model('user/api');

		$api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

		if ($api_info && $this->user->hasPermission('modify', 'sale/order')) {
			$session = new Session($this->config->get('session_engine'), $this->registry);
			
			$session->start();
					
			$this->model_user_api->deleteApiSessionBySessonId($session->getId());
			
			$this->model_user_api->addApiSession($api_info['api_id'], $session->getId(), $this->request->server['REMOTE_ADDR']);
			
			$session->data['api_id'] = $api_info['api_id'];

			$data['api_token'] = $session->getId();
		} else {
			$data['api_token'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('cnwms/scanDocs_list', $data));
	}
	
	protected function getTrackingStatus() {
		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = '';
		}

		if (isset($this->request->get['filter_customer'])) {
			$filter_customer = $this->request->get['filter_customer'];
		} else {
			$filter_customer = '';
		}

		if (isset($this->request->get['filter_order_status'])) {
			$filter_order_status = $this->request->get['filter_order_status'];
		} else {
			$filter_order_status = '';
		}
		
		if (isset($this->request->get['filter_order_status_id'])) {
			$filter_order_status_id = $this->request->get['filter_order_status_id'];
		} else {
			$filter_order_status_id = '';
		}
		
		if (isset($this->request->get['filter_total'])) {
			$filter_total = $this->request->get['filter_total'];
		} else {
			$filter_total = '';
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = '';
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = '';
		}
		
		if (isset($this->request->get['filter_order_supplires'])) {
			$filter_order_supplires = $this->request->get['filter_order_supplires'];
		} else {
			$filter_order_supplires = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'o.order_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}
	
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
			
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}
		
		if (isset($this->request->get['filter_order_supplires'])) {
			$url .= '&filter_order_supplires=' . $this->request->get['filter_order_supplires'];
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
			'href' => $this->url->link('firstmiles/firstmiles', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['invoice'] = $this->url->link('sale/order/invoice', 'user_token=' . $this->session->data['user_token'], true);
		$data['shipping'] = $this->url->link('sale/order/shipping', 'user_token=' . $this->session->data['user_token'], true);
		$data['action'] = $this->url->link('firstmiles/firstmiles/generateLabel', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['action_scan'] = $this->url->link('cnwms/shipments/scandocument', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['action_download'] = $this->url->link('cnwms/cnwms/download', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = str_replace('&amp;', '&', $this->url->link('sale/order/delete', 'user_token=' . $this->session->data['user_token'] . $url, true));

		$data['shipments'] = array();

		$filter_data = array(
			'filter_order_id'        => $filter_order_id,
			'filter_customer'	     => $filter_customer,
			'filter_order_status'    => $filter_order_status,
			'filter_order_status_id' => $filter_order_status_id,
			'filter_total'           => $filter_total,
			'filter_date_added'      => $filter_date_added,
			'filter_date_modified'   => $filter_date_modified,
			'filter_order_supplires'   => $filter_order_supplires,
			'sort'                   => $sort,
			'order'                  => $order,
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);

		$order_total = $this->model_cnwms_shipments->getTotalTrackingShipments($filter_data);

		$results = $this->model_cnwms_shipments->getTrackingShipments($filter_data);
		
		$awb = "";
		foreach ($results as $result) {
			if($result['AWBNo'] != "" || $result['AWBNo'] != null )
			{
				$awb = $result['AWBNo'];
			} 
			$data['shipments'][] = array(
				'id'      => $result['id'],
				'tracking_number'      => $awb,
				'status'      => $result['Status'],
				'time'      => $result['time'],
				'Memo'      => $result['Memo'],
				'Location'      => $result['Location'],
				'created_at'    => date($this->language->get('date_format_short'), strtotime($result['created_at'])),
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

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}
		
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
			
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}
		
		if (isset($this->request->get['filter_order_supplires'])) {
			$url .= '&filter_order_supplires=' . $this->request->get['filter_order_supplires'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_order'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . '&sort=o.order_id' . $url, true);
		$data['sort_customer'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . '&sort=customer' . $url, true);
		$data['sort_supplire'] = $this->url->link('cnwms/cnwms', 'user_token=' . $this->session->data['user_token'] . '&sort=supplire' . $url, true);
		$data['sort_status'] = $this->url->link('cnwms/cnwms', 'user_token=' . $this->session->data['user_token'] . '&sort=order_status' . $url, true);
		$data['sort_total'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . '&sort=o.total' . $url, true);
		$data['sort_date_added'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . '&sort=o.date_added' . $url, true);
		$data['sort_date_modified'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . '&sort=o.date_modified' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}
		
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
			
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}
		
		if (isset($this->request->get['filter_order_supplires'])) {
			$url .= '&filter_order_supplires=' . $this->request->get['filter_order_supplires'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('cnwms/cnwms', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['filter_order_id'] = $filter_order_id;
		$data['filter_customer'] = $filter_customer;
		$data['filter_order_status'] = $filter_order_status;
		$data['filter_order_status_id'] = $filter_order_status_id;
		$data['filter_total'] = $filter_total;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_date_modified'] = $filter_date_modified;
		$data['filter_order_supplires'] = $filter_order_supplires;

		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['order_supplires'] = array(
                    '1' => "China Brand",
                );

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		// API login
		$data['catalog'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;
		
		// API login
		$this->load->model('user/api');

		$api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

		if ($api_info && $this->user->hasPermission('modify', 'sale/order')) {
			$session = new Session($this->config->get('session_engine'), $this->registry);
			
			$session->start();
					
			$this->model_user_api->deleteApiSessionBySessonId($session->getId());
			
			$this->model_user_api->addApiSession($api_info['api_id'], $session->getId(), $this->request->server['REMOTE_ADDR']);
			
			$session->data['api_id'] = $api_info['api_id'];

			$data['api_token'] = $session->getId();
		} else {
			$data['api_token'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('cnwms/trackShipments', $data));
	}
	
	public function createshipments()
	{
		if($this->request->server['REQUEST_METHOD'] == 'POST') {
		
			$data = $this->request->post;
			//print_r($data);die;
			$this->load->model('cnwms/shipments');
			$this->load->model('logistics/logistics');
			$credentials = $this->model_logistics_logistics->getCredentials();
			$WarehouseDetails = $this->model_cnwms_shipments->getWarehouseDetails($data);
			$generateCustomOrder = $this->model_cnwms_shipments->generateCustomOrder();
			
			$pickupDate = date('d-M-Y');
		
			$soapUrl = "http://icms.grandslamexpress.in/V2/ClientAPI.asmx";
			$xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
			<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
			  <soap:Body>
				<PickUpEntry xmlns="http://grandslamexpress.in/">
				  <UserId>'.$credentials['user'].'</UserId>
				  <Password>'.$credentials['password'].'</Password>
				  <CCode>'.$credentials['user'].'</CCode>
				  <AWBNo></AWBNo>
				  <OrderNo>'.$generateCustomOrder.'</OrderNo>
				  <Origin>'.$WarehouseDetails['FromWH']['warehouse_name'].'</Origin>
				  <Destination>'.$WarehouseDetails['ToWH']['country'].'</Destination>
				  <Shipper_Name>'.$WarehouseDetails['FromWH']['warehouse_name'].'</Shipper_Name>
				  <Shipper_Add1>'.$WarehouseDetails['FromWH']['address_1'].'</Shipper_Add1>
				  <Shipper_Add2>'.$WarehouseDetails['FromWH']['address_2'].'</Shipper_Add2>
				  <Shipper_Add3></Shipper_Add3>
				  <Shipper_State>'.$WarehouseDetails['FromWH']['state'].'</Shipper_State>
				  <Shipper_Pin>'.$WarehouseDetails['FromWH']['zipcode'].'</Shipper_Pin>
				  <Shipper_Mobile>'.$WarehouseDetails['FromWH']['phone'].'</Shipper_Mobile>
				  <Shipper_Email>'.$WarehouseDetails['FromWH']['email'].'</Shipper_Email>
				  <Consignee_Name>'.$WarehouseDetails['ToWH']['warehouse_name'].'</Consignee_Name>
				  <Consignee_Contact>'.$WarehouseDetails['ToWH']['phone'].'</Consignee_Contact>
				  <Consignee_Add1>'.$WarehouseDetails['ToWH']['address_1'].'</Consignee_Add1>
				  <Consignee_Add2>'.$WarehouseDetails['ToWH']['address_2'].'</Consignee_Add2>
				  <Consignee_Add3></Consignee_Add3>
				  <Consignee_City>'.$WarehouseDetails['ToWH']['city'].'</Consignee_City>
				  <Consignee_State>'.$WarehouseDetails['ToWH']['state'].'</Consignee_State>
				  <Consignee_Pin>'.$WarehouseDetails['ToWH']['zipcode'].'</Consignee_Pin>
				  <Consignee_Mobile>'.$WarehouseDetails['ToWH']['phone'].'</Consignee_Mobile>
				  <Consignee_Email>'.$WarehouseDetails['ToWH']['email'].'</Consignee_Email>
				  <Pcs>'.$data['no_of_items'].'</Pcs>
				  <Weight>'.$data['total_weight'].'</Weight>
				  <Dox_Spx>ND</Dox_Spx>
				  <Shipment_Value>'.$data['total_value'].'</Shipment_Value>
				  <Shipment_Currency>INR</Shipment_Currency>
				  <COD_Amount>0</COD_Amount>
				  <PickupDate>'.$pickupDate.'</PickupDate>
				  <Instruction>'.$data['instruction'].'</Instruction>
				  <Mode>TEST</Mode>
				  <ClientSource>GS</ClientSource>
				  <PaymentMode>Prepaid</PaymentMode>
				</PickUpEntry>
			  </soap:Body>
			</soap:Envelope>';
		
		
			$headers = array(
			"POST /V2/ClientAPI.asmx HTTP/1.1",
			"Host: icms.grandslamexpress.in",
			"Content-Type: text/xml; charset=utf-8",
			"Content-Length: ".strlen($xml_post_string)
			); 
			
			$url = $soapUrl;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$response = curl_exec($ch);
			
			$page = file_put_contents('img/PickupEntryResponse.xml',$response);
			$page_xml = 'http://store.tongkart.com/tngkart/admin/img/PickupEntryResponse.xml';
			$dataFeed = file_get_contents($page_xml);
			$xml = preg_replace('/(<\/?)(\w+):([^>]*>)/', '$1$2$3', $dataFeed);
			$xml = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA);
			$json = json_encode($xml);
			$array = json_decode($json,TRUE);
			$paramsorder = base64_encode(serialize($xml_post_string));
			$result = serialize($array);
			$dataResponse = $array['soapBody']['PickUpEntryResponse'];
			$dataResult = array();
			if(empty($dataResponse['PickUpEntryResult']['Error']))
			{
				$dataResult['AWBNo'] = $dataResponse['PickUpEntryResult']['AWBNo'];
				$dataResult['Msg'] = $dataResponse['PickUpEntryResult']['Msg'];
				$dataResult['Status'] = $dataResponse['PickUpEntryResult']['Status'];
				$dataResult['OrderId'] = $generateCustomOrder;
				$dataResult['Request'] = $paramsorder;
				$dataResult['Response'] = $result;
				$dataResult['Weight'] = $data['total_weight'];
				$dataResult['Pcs'] = $data['no_of_items'];
				$dataResult['FromWarehouse'] = $data['shipments_from'];
				$dataResult['ToWarehouse'] = $data['shipments_to'];
				$dataResult['TrackStatus'] = $dataResponse['PickUpEntryResult']['Msg'];
				$this->model_logistics_logistics->insertPickupEntry($dataResult);
				//$this->model_cnwms_shipments->insertShipment($dataResult);
			}
			else
			{
				
			}
		}
		$url='';
		$this->response->redirect($this->url->link('cnwms/shipments/shipmentList', 'user_token=' . $this->session->data['user_token'] . $url, true));
	}
	
	public function scandocument()
	{
		$this->load->model('cnwms/shipments');

		$this->load->model('logistics/logistics');
		
		if($this->request->server['REQUEST_METHOD'] == 'POST') {
		
			$awb = $this->request->post['awb'];
			$file = $_FILES['file']['name'];
			$tmpFilePath = $_FILES['file']['tmp_name'];
			$opts = array('http' => array('header' => "User-Agent:MyAgent/1.0\r\n"));
			$context = stream_context_create($opts);
			$result = $tmpFilePath;
			$DocsContent = @file_get_contents($result, FALSE, $context);
			//print_r($result);die;
			$DocsContent = base64_encode($DocsContent);
			
			//print_r($DocsContent);die;
			//$awb= '925500178';
			$credentials = $this->model_logistics_logistics->getCredentials();
			
				//base_64_decode for document
				$soapUrl = "http://icms.grandslamexpress.in/V2/ClientAPI.asmx";
				$xml_post_string = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchemainstance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
										<soap:Body>
											<ScanDocumentList xmlns="http://grandslamexpress.in/">
												<UserId>'.$credentials['user'].'</UserId>
												<Password>'.$credentials['password'].'</Password>
												<AWBNo>'.$awb.'</AWBNo>
												<DocumentType>string</DocumentType>
												<Document>'.$DocsContent.'</Document>
											</ScanDocumentList>
										</soap:Body>
									</soap:Envelope>';

				$headers = array(
				"POST /V2/ClientAPI.asmx HTTP/1.1",
				"Host: icms.grandslamexpress.in",
				"Content-Type: text/xml; charset=utf-8",
				"Content-Length: ".strlen($xml_post_string)
				); 
				
				
				
				$url = $soapUrl;

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

				$response = curl_exec($ch);
				
				$page = file_put_contents('img/ScanDocumentList.xml',$response);
				$page_xml = 'http://store.tongkart.com/tngkart/admin/img/ScanDocumentList.xml';
				$data = file_get_contents($page_xml);
				$xml = preg_replace('/(<\/?)(\w+):([^>]*>)/', '$1$2$3', $data);
				$xml = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA);
				$json = json_encode($xml);
				$array = json_decode($json,TRUE);
				//print_r($array);die;
				$dataResponse = $array['soapBody']['ScanDocumentListResponse'];
				$dataResult = array();
				if(!empty($dataResponse['ScanDocumentListResult']['AWBNo']))
				{
					$dataResult['AWBNo'] = $dataResponse['ScanDocumentListResult']['AWBNo'];
					$dataResult['Msg'] = $dataResponse['ScanDocumentListResult']['Msg'];
					$dataResult['Status'] = $dataResponse['ScanDocumentListResult']['Status'];
					$dataResult['Docs'] = $DocsContent;
					$this->model_logistics_logistics->insertScanDocumentList($dataResult);
				}
		}
		$url='';
		$this->response->redirect($this->url->link('cnwms/shipments/docsList', 'user_token=' . $this->session->data['user_token'] . $url, true));
	}
}
